<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreMenuItemRequest;
use App\Http\Requests\Business\Restaurant\UpdateMenuItemRequest;
use App\Models\RestaurantMenuCategory;
use App\Models\RestaurantMenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MenuItemController extends Controller
{
    public function index(Request $request){
        try {
            $restaurant = Auth::user()->restaurant;
            // Shepherd tour logic for first visit
            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_already_visited = $visited_pages->contains('menu-items');
            if(!$is_already_visited){
                $visited_pages->add('menu-items');
                $restaurant->update([
                    'visited_pages'=>json_encode($visited_pages)
                ]);
            }
            $menu_items = RestaurantMenuItem::where('restaurant_id', $restaurant->id)
                ->orderByDesc('created_at');
            $restaurant_categories = RestaurantMenuCategory::all();

            if($request->ajax()){
                return DataTables::of($menu_items)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($menu_item) {
                        return view('content.business.restaurants.menu-items.actions', compact('menu_item'));
                    })
                    ->editColumn('tags', function ($menu_item) {
                        $tags_array = collect(json_decode($menu_item->tags, true))->toArray();
                        return $tags_array;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }

            return view('content.business.restaurants.menu-items.index',compact('restaurant_categories', 'is_already_visited'));

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function create(){

    }

    public function store(StoreMenuItemRequest $request){
        try {
            // dd($request->all());
            $restaurant = Auth::user()->restaurant;
            $tags = collect(json_decode($request->tags,true))->pluck('value')->toArray();
            // dd($tags);
            $images = [];
            foreach($request->images as $image){
                $file_name = $image->store('public/restaurants/menu-items', 's3');
                $images[] = ["file_name" => basename($file_name)];
            }
            // dd($images);
            DB::transaction(function () use($restaurant, $tags, $request, $images) {
                $item=$restaurant->items()->create([
                    'menu_category_id'=>$request->category_id,
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'price'=>$request->price,
                    'tags'=>json_encode($tags)
                ]);

                $item->images()->createMany($images);
            });
            // return redirect()->back()->with('menu_items', [
            //     'message'=>'Item added successfully',
            //     'type'=>'success'
            // ]);
            return redirect()->back()->with('success', __('Item added successfully.'));
        } catch (\Throwable $th) {
            // return redirect()->back()->with('menu_items', [
            //     'message'=>$th->getMessage(),
            //     'type'=>'error'
            // ]);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show(RestaurantMenuItem $menu_item){
        try {
            $menu_item->load(['category', 'images']);
            return view('content.business.restaurants.menu-items.show', compact('menu_item'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function edit(RestaurantMenuItem $menu_item){
        try {
            return response()->json([
                'data'=>$menu_item,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage(),
            ], 400);
        }
    }

    public function update(UpdateMenuItemRequest $request, RestaurantMenuItem $menu_item){
        try {
            $restaurant = Auth::user()->restaurant;
            $tags = collect(json_decode($request->tags,true))->pluck('value')->toArray();
            // dd($tags);
            $images = [];
            if($request->exists('images')){
                foreach($request->images as $image){
                    $file_name = $image->store('public/restaurants/menu-items', 's3');
                    $images[] = ["file_name" => basename($file_name)];
                }
            }

            DB::transaction(function () use($restaurant, $menu_item, $tags, $request, $images) {
                $menu_item->update([
                    'menu_category_id'=>$request->category_id,
                    'name'=>$request->name,
                    'description'=>$request->description,
                    'price'=>$request->price,
                    'tags'=>json_encode($tags)
                ]);

                if(count($images)>0){
                    $menu_item->images()->createMany($images);
                }
            });
            return redirect()->back()->with('success', __('Item updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    public function destroy(RestaurantMenuItem $menu_item){
        try {
            $menu_item->offers()->detach();
            $menu_item->delete();
            return response()->json([
                'message'=>__("Item deleted successfully."),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>__("Something went wrong."),
            ], 200);
        }
    }
}
