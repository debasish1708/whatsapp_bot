<?php

namespace App\Http\Controllers\Business\School;

use App\Dialog360\Dialog360;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\CreateAnnouncementRequest;
use App\Http\Requests\Business\School\UpdateAnnouncementRequest;
use App\Models\Annoucement;
use App\Models\School;
use App\Models\User;
use App\OpenAI\OpenAI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AnnouncementController extends Controller
{
  private $dialog360Object, $openAIObject;
  public function __construct()
  {
    $this->dialog360Object = new Dialog360();
    $this->openAIObject = new OpenAI();
  }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $user=auth()->user();
            $school = $user->school;

            $visited_pages = collect(json_decode($school->visited_pages));
            $is_visited = $visited_pages->contains('announcements');

            if(!$is_visited){
                $visited_pages->add('announcements');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            if($request->ajax()){

                $data = $school->announcements()
                             ->latest()
                             ->where('end_date', '>=', Carbon::now())
                             ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('start_date', function ($row) {
                        return \Carbon\Carbon::parse($row->start_date)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->editColumn('end_date', function ($row) {
                        return \Carbon\Carbon::parse($row->end_date)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->editColumn('title',function($announcement){
                        return Str::limit($announcement->title,15,'...');
                    })
                    ->editColumn('description',function($announcement){
                        return Str::limit($announcement->description,30,'...');
                    })
                    ->addColumn('actions', function ($row) {
                        // Pass the whole model as 'announcement' to the view
                        return view('content.business.school.announcements.actions', [
                            'announcement' => $row
                        ])->render();
                    })
                    ->rawColumns(['start_date', 'end_date', 'actions'])
                    ->make(true);
            }
            return view('content.business.school.announcements.index', compact('is_visited'));
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAnnouncementRequest $request)
    {
       try{
        $data=$request->validated();
        $user = auth()->user();
        $school = $user->school;
        $announcements = $school->announcements()->create($data);

        $users = User::where('is_verified', true)
          ->whereHas('businessUsers', function ($q) use ($school) {
            $q->where('businessable_type', School::class)
              ->where('businessable_id', $school->id)
              ->where('added_by', '!=', 'search');
          })
          ->get();

        if (!$users->isEmpty()) {
          $announcement_json = [
            'school_name' => $user->name,
            'type' => $announcements->type,
            'title' => $announcements->title,
            'description' => $announcements->description,
            'start_date' => $announcements->start_date,
            'end_date' => $announcements->end_date,
          ];
          dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'school_announcement', $announcement_json, [
            'announcement_id' => $announcements->id
          ]));
        }

        return redirect()->back()->with('success', __('Announcement created successfully!'));
       }catch(\Exception $e){

        return redirect()->back()->with('error', 'Something went wrong');
       }
    }

    /**
     * Display the specified resource.
     */
    public function show(Annoucement $announcement)
    {
        try{
            if (request()->ajax()) {
                return response()->json([
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'description' => $announcement->description,
                    'start_date'=>Carbon::parse($announcement->start_date)->format("d M Y h:i A"),
                    'end_date'=>Carbon::parse($announcement->end_date)->format("d M Y h:i A"),
                    'type' => $announcement->type,
                    'created_at'=>Carbon::parse($announcement->created_at)->format('d M Y h:i A'),
                ]);
            }
            return view('content.business.school.announcements.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Annoucement $announcement)
    {
        try{
             if (request()->ajax()) {
                return response()->json([
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'description' => $announcement->description,
                    'start_date'=>Carbon::parse($announcement->start_date)->format("Y-m-d h:i A"),
                    'end_date'=>Carbon::parse($announcement->end_date)->format("Y-m-d h:i A"),
                    'type' => $announcement->type,
                    'created_at'=>Carbon::parse($announcement->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            return view('content.business.school.announcements.index');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }

        // dd($announcement);
        // return view('')
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Annoucement $announcement)
    {
        try{
            $data = $request->validated();
            $announcement->update($data);
            $user = auth()->user();
            $school = $user->school;
            $users = User::where('is_verified', true)
              ->whereHas('businessUsers', function ($q) use ($school) {
                $q->where('businessable_type', School::class)
                  ->where('businessable_id', $school->id);
              })
              ->get();

            if (!$users->isEmpty()) {
              $announcement_json = [
                'school_name' => $user->name,
                'type' => $announcement->type,
                'title' => $announcement->title,
                'description' => $announcement->description,
                'start_date' => $announcement->start_date,
                'end_date' => $announcement->end_date,
              ];
              dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'school_announcement', $announcement_json, [
                'announcement_id' => $announcement->id
              ]));
          }
            return redirect()->back()->with('success', __('Announcement updated successfully!'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Annoucement $announcement)
    {
        try {
            $announcement->delete();
            return response()->json([
                'message'=>__('Announcement deleted successfully.'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 200);
        }
    }

}
