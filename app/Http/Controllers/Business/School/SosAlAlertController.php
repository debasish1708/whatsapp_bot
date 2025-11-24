<?php

namespace App\Http\Controllers\Business\School;

use App\Dialog360\Dialog360;
use App\Enums\SchoolSosAleart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\CreateSosAleartRequest;
use App\Http\Requests\Business\School\UpdateSosAleartRequest;
use App\Models\School;
use App\Models\SchoolSosAlert;
use App\Models\User;
use App\OpenAI\OpenAI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SosAlAlertController extends Controller
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
            $is_visited = $visited_pages->contains('sos');

            if(!$is_visited){
                $visited_pages->add('sos');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            if($request->ajax()){
                $data = $school
                            ->sosAlerts()
                            ->latest()
                            ->get();
                return DataTables::of($data)
                     ->addIndexColumn()
                     ->addColumn('actions', function ($row) {
                        return view('content.business.school.sos-alerts.actions', [
                            'sos_alert' => $row
                        ])->render();
                    })
                    ->editColumn('type', function($row){
                        return ucwords(str_replace('_', ' ', $row->type));
                    })
                    ->editColumn('created_at', function($row){
                        return \Carbon\Carbon::parse($row->created_at)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->editColumn('title',function($row){
                        return Str::limit($row->title,15,'...');
                    })
                    ->editColumn('message',function($row){
                        return Str::limit($row->message,30,'...');
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.business.school.sos-alerts.index', compact('is_visited'));
        }catch(\Exception $e){
            // return redirect()->back()->with('sos_status', [
            // 'message' => 'Something went wrong! Please try again.',
            // 'type' => 'error'
            // ]);
            return redirect()->back()->with('error', __("Something went wrong."));
        }
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
    public function store(CreateSosAleartRequest $request)
    {
        try{
            $data=$request->validated();
            $school=auth()->user()->school;
            $sos =  $school->sosAlerts()->create($data);
            $user = auth()->user();

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
                'title' => $sos->title,
                'description' => $sos->message,
                'type' => SchoolSosAleart::safeFrom($sos->type) ?? '',
             ];
              dispatch(new \App\Jobs\Send360TemplateMessageJob($users,'alert_notification', $announcement_json, [
                'sos_id' => $sos->id
              ]));
            }

        //     return redirect()->back()->with('sos_status', [
        //     'message' => 'SOS Alert created successfully!',
        //     'type' => 'success'
        //    ]);
            return redirect()->back()->with('success', __('SOS Alert created successfully!'));
        }catch(\Exception $e){

        //     return redirect()->back()->with('sos_status', [
        //     'message' => 'Something went wrong! Please try again.',
        //     'type' => 'error'
        //    ]);
            return redirect()->back()->with('error', __("Something went wrong."));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolSosAlert $sosAlert)
    {
        try{
            if(request()->ajax()){
                return response()->json([
                    'id'=>$sosAlert->id,
                    'title'=>$sosAlert->title,
                    'message'=>$sosAlert->message,
                    'type'=>$sosAlert->type,
                    'created_at'=>Carbon::parse($sosAlert->created_at)->format('Y-m-d H:i:s')
                ]);
            }
            return view('content.business.school.sos-alerts.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolSosAlert $sosAlert)
    {
        try{
            if(request()->ajax()){
                return response()->json([
                    'id'=>$sosAlert->id,
                    'title'=>$sosAlert->title,
                    'message'=>$sosAlert->message,
                    'type'=>$sosAlert->type,
                    'created_at'=>Carbon::parse($sosAlert->created_at)->format('Y-m-d H:i:s')
                ]);
            }
            return view('content.business.school.sos-alerts.index');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong! Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSosAleartRequest $request, SchoolSosAlert $sosAlert)
    {
        try{
            $data=$request->validated();
            $sosAlert->update($data);
            return redirect()->back()->with('success', __('SOS Alert updated successfully!'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolSosAlert $sosAlert)
    {
        try {
            $sosAlert->delete();
             return response()->json([
                'message'=> __('SosAlert deleted successfully.'),
            ], 200);
        } catch (\Exception $e) {
             return response()->json([
                'message'=> __('Something went wrong.'),
            ], 200);
        }
    }
}
