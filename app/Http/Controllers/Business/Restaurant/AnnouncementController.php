<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreAnnouncementRequest;
use App\Http\Requests\Business\Restaurant\UpdateAnnouncementRequest;
use App\Models\Annoucement;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class AnnouncementController extends Controller
{
    public function index(Request $request){
        try {
            $restaurant = Auth::user()->restaurant;

            // Shepherd tour logic for first visit
            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_already_visited = $visited_pages->contains('announcements');
            if(!$is_already_visited){
                $visited_pages->add('announcements');
                $restaurant->update([
                    'visited_pages'=>json_encode($visited_pages)
                ]);
            }

            // $announcements = RestaurantAnnouncement::where('restaurant_id', $restaurant->id)
            //     ->orderByDesc('created_at');

            $announcements = $restaurant->announcements()
                ->whereDate('end_date', '>=', Carbon::now())
                ->orderByDesc('created_at');

            if($request->ajax()){
                return DataTables::of($announcements)
                    ->addIndexColumn()
                    ->editColumn('start_date', function ($announcement) {
                        return Carbon::parse($announcement->start_date)->format('d M Y') ?? 'N/A';
                    })
                    ->editColumn('end_date', function ($announcement) {
                        return Carbon::parse($announcement->end_date)->format('d M Y') ?? 'N/A';
                    })
                    ->editColumn('title',function($announcement){
                        return Str::limit($announcement->title,15,'...');
                    })
                    ->editColumn('description',function($announcement){
                        return Str::limit($announcement->description,30,'...');
                    })
                    ->addColumn('actions', function ($announcement) {
                        return view('content.business.restaurants.announcements.actions', compact('announcement'));
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.business.restaurants.announcements.index', compact('is_already_visited'));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function create(){

    }

    public function store(StoreAnnouncementRequest $request){
        try {
            $data = $request->only(['title', 'description', 'start_date', 'end_date', 'type']);
            $restaurant = Auth::user()->restaurant;
            $announcement = $restaurant->announcements()->create($data);
            $restaurant_users = User::where('is_verified', true)
                ->whereHas('businessUsers', function ($query) use ($restaurant) {
                    $query->where('businessable_type', Restaurant::class)
                        ->where('businessable_id', $restaurant->id)
                        ->where('added_by', '!=', 'search');
                })->get();

            if(!$restaurant_users->isEmpty()){
                $announcement_data = [
                    'school_name' => Auth::user()->name,
                    'type' => $announcement->type,
                    'title' => $announcement->title,
                    'description' => $announcement->description,
                    'start_date' => $announcement->start_date,
                    'end_date' => $announcement->end_date,
                ];
                dispatch(new \App\Jobs\Send360TemplateMessageJob(
                    $restaurant_users,
                    'school_announcement',
                    $announcement_data,
                    [
                        'announcement_id' => $announcement->id
                    ]
                ));
            }

            return redirect()->back()->with('success', __('Announcement created successfully!'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    public function show(Annoucement $announcement){
        try{
            $announcement_type = config('constant.restaurant_announcements');
            info($announcement);
            if(request()->ajax()){
                return response()->json([
                    'id'=>$announcement->id,
                    'title'=>$announcement->title,
                    'description'=>$announcement->description,
                    'start_date'=>Carbon::parse($announcement->start_date)->format("Y-m-d"),
                    'end_date'=>Carbon::parse($announcement->end_date)->format("Y-m-d"),
                    'type'=>__($announcement_type[$announcement->type] ?? $announcement_type['offer'] ?? 'N/A'),
                    'created_at'=>Carbon::parse($announcement->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            return view('content.business.restaurants.announcements.index');
        }catch(\Throwable $th){
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function edit(Annoucement $announcement){
        try {
            // $restaurant = Auth::user()->restaurant;
            if(request()->ajax()){
                return response()->json([
                    'id'=>$announcement->id,
                    'title'=>$announcement->title,
                    'description'=>$announcement->description,
                    'start_date'=>Carbon::parse($announcement->start_date)->format("Y-m-d"),
                    'end_date'=>Carbon::parse($announcement->end_date)->format("Y-m-d"),
                    'type'=>$announcement->type,
                    'created_at'=>Carbon::parse($announcement->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function update(UpdateAnnouncementRequest $request, Annoucement $announcement){
        try {
            $data = $request->only(['title', 'description', 'start_date', 'end_date', 'type']);
            $announcement->update($data);
            return redirect()->back()->with('success', __('Announcement updated successfully!'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    public function destroy(Annoucement $announcement){
        try {
            $announcement->delete();
            return response()->json([
                'message'=>__('Announcement deleted successfully.'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 200);
        }
    }
}
