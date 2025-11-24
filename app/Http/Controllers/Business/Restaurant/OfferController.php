<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Enums\RestaurantOfferType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreOfferRequest;
use App\Http\Requests\Business\Restaurant\UpdateOfferRequest;
use App\Models\Restaurant;
use App\Models\RestaurantOffer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $restaurant = Auth::user()->restaurant;

            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_visited = $visited_pages->contains('offers');

            if(!$is_visited){
                $visited_pages->add('offers');
                $restaurant->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            $inValidOffers = $restaurant->offers()->with('applicableItems')->where('ends_at', '<', now())->get();

            // Remove pivot records from offer_menuitem for the fetched offers
            foreach ($inValidOffers as $offer) {
                $offer->applicableItems()->detach();
            }
            // Get menu items that are NOT attached to any offer
            $attached_item_ids = $restaurant->offers()->with('applicableItems')->get()
                ->pluck('applicableItems')
                ->flatten()
                ->pluck('id')
                ->unique();

            $menu_items = $restaurant->items()->whereNotIn('id', $attached_item_ids)->get();
            $offers = $restaurant->offers()->with('applicableItems')->latest()->get();
            // dd($offers);

            // dd($menu_items);

            if($request->ajax()){
                return DataTables::of($offers)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($offer) {
                        return view('content.business.restaurants.offers.actions', compact('offer'));
                    })
                    ->addColumn('Items', function ($offer) {
                        $items = $offer->applicableItems;
                        $totalCount = $items->count();

                        if ($totalCount == 0) {
                            return '<span class="text-muted">No items</span>';
                        }

                        $visibleItems = $items->take(3)->map(function ($item) {
                            return '<span class="badge bg-info bg-opacity-10 text-info">'.$item->name.'</span>';
                        })->implode(' ');

                        $moreCount = $totalCount - 3;

                        if ($moreCount > 0) {
                            $allItemsJson = htmlspecialchars(json_encode($items->pluck('name')->toArray()), ENT_QUOTES, 'UTF-8');
                            return '<div class="d-flex flex-wrap gap-2 align-items-center">' .
                                $visibleItems .
                                ' <span class="badge bg-secondary cursor-pointer" onclick="showAllItems(this)" data-items=\''.$allItemsJson.'\' data-offer-title="'.htmlspecialchars($offer->title, ENT_QUOTES, 'UTF-8').'">+' . $moreCount . ' more</span>' .
                                '</div>';
                        }

                        return '<div class="d-flex flex-wrap gap-2">' . $visibleItems . '</div>';
                    })
                    ->addColumn('status', function ($offer) {
                        if ($offer->ends_at < Carbon::now()) {
                            return '<span class="badge bg-danger bg-opacity-10 text-danger">'.__('Expired').'</span>';
                        }
                        return '<span class="badge bg-success bg-opacity-10 text-success">'.__('Active').'</span>';
                    })
                    ->editColumn('starts_from', function ($offer) {
                        return Carbon::parse($offer->starts_from)->format('d M Y');
                    })
                    ->editColumn('ends_at', function ($offer) {
                        return Carbon::parse($offer->ends_at)->format('d M Y');
                    })
                    ->editColumn('title',function($offer){
                        return Str::limit($offer->title,15,'...');
                    })
                    ->editColumn('description',function($offer){
                        return Str::limit($offer->description,30,'...');
                    })
                    ->rawColumns(['actions','starts_from','ends_at','status','Items'])
                    ->make(true);
            }

            return view('content.business.restaurants.offers.index', compact('menu_items', 'is_visited'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function getAvailableItems(Restaurant $restaurant)
    {
        // Step 1: Get valid offers only (filter expired at query level)
        $validOffers = $restaurant->offers()
            ->where('ends_at', '>=', now())
            ->get();

        // Step 2: Get all items attached to valid offers
        $attached_item_ids = $validOffers
            ->pluck('applicableItems')
            ->flatten()
            ->pluck('id')
            ->unique();

        // Step 3: Get available (not attached) items
        $menu_items = $restaurant->items()
            ->whereNotIn('id', $attached_item_ids)
            ->get(['id', 'name']);

        return response()->json($menu_items);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOfferRequest $request)
    {
        try {
            // dd($request->all());
            // dd($request->validated());
            $user = Auth::user();
            DB::transaction(function () use ($request, $user) {
                $offer_data = $request->only(['title', 'description', 'discount_type', 'discount', 'starts_from', 'ends_at']);
                $restaurant = $user->restaurant;
                $offer = $restaurant->offers()->create($offer_data);
                $offer->applicableItems()->attach($request->applicable_items);

                $users = User::where('is_verified', true)
                ->whereHas('businessUsers', function ($q) use ($restaurant) {
                $q->where('businessable_type', Restaurant::class)
                    ->where('businessable_id', $restaurant->id)
                    ->where('added_by', '!=', 'search');
                })
                ->get();

                if (!$users->isEmpty()) {
                    $typeEnum = RestaurantOfferType::from($offer->discount_type);
                    $restaurant_offer_json = [
                        'restaurant_id' => $restaurant->id,
                        'restaurant_name' => $user->name,
                        'title' => $offer->title,
                        'discount' => $offer->discount,
                        'discount_type' => $offer->discount_type,
                        'description' => $offer->description,
                        'valid_from' => $offer->starts_from,
                        'valid_until' => $offer->ends_at,
                        'applicable_items' => $offer->applicableItems->pluck('name')->implode(', '),
                    ];
                    // info($announcement_json);
                    info('Restaurant Offer JSON', ['offer' => $restaurant_offer_json]);
                    // dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'restaurant_offers', $restaurant_offer_json, [
                    //   'offer_id' => $offer->id
                    // ]));
                    dispatch(new \App\Jobs\SendRestaurantOfferMessageJob($users, $restaurant_offer_json));
                }

            });
            return redirect()->back()->with('success', __('offer added successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
            // return $th->getMessage();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RestaurantOffer $offer)
    {
        try {
            $offer->load(['applicableItems']);

            return response()->json([
                'data'=>$offer,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage(),
                'line'=>$th->getLine(),
                'code'=>$th->getCode()
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RestaurantOffer $offer)
    {
        try {
            $offer->load(['applicableItems']);
            return response()->json([
                'data'=>$offer,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage(),
                'line'=>$th->getLine(),
                'code'=>$th->getCode()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOfferRequest $request, RestaurantOffer $offer)
    {
        try {
            $user = Auth::user();
            DB::transaction(function () use ($request, $offer) {
                $offer_data = $request->only(['title', 'description', 'discount_type', 'discount', 'starts_from', 'ends_at']);
                $offer->update($offer_data);
                $offer->applicableItems()->sync($request->applicable_items);
            });
            return redirect()->back()->with('success', __('offer updated successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RestaurantOffer $offer)
    {
        try {
            $restaurantId = $offer->restaurant->id;
            $offer->applicableItems()->detach();
            $offer->delete();
            return response()->json([
                'message'=>__('Offer deleted successfully.'),
                'restaurantId'=> $restaurantId
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 400);
        }
    }
}
