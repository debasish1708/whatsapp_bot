<?php

namespace App\Http\Controllers\Business\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\CreatePsychologicalSupportRequest;
use App\Http\Requests\Business\School\UpdatePsychologicalSupportRequest;
use App\Models\PsychologicalSupportContact;
use App\Models\SchoolPsychologicalSupport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PsychologicalSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
          $user = auth()->user();
          $school = $user->school;
          $visited_pages = collect(json_decode($school->visited_pages));
          $is_visited = $visited_pages->contains('psychological_supports');
          if(!$is_visited){
            $visited_pages->add('psychological_supports');
            $school->update([
              'visited_pages'=>$visited_pages
            ]);
          }
          if($request->ajax()){
            $data = $school
                         ->psychologicalSupports()
                         ->with('officeHours')
                         ->latest()
                         ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('office_hours', function ($row) {
                    if ($row->officeHours->isEmpty()) {
                        return 'N/A';
                    }

                    // Display as a list of days with time range
                    return $row->officeHours->map(function ($hour) {
                       try {
                            $start = $hour->start_time->format('g:i A');
                            $end = $hour->end_time->format('g:i A');
                            return "{$hour->day}: {$start} - {$end}";
                        } catch (\Exception $e) {
                            return "{$hour->day}: Invalid Time";
                        }
                    })->implode('<br>'); // Return HTML line breaks between entries
                })
                ->editColumn('name',function($row){
                    return Str::limit($row->name,30,'...');
                })
                ->addColumn('actions', function ($row) {
                    return view('content.business.school.psychological-supports.actions', [
                        'psychological_support' => $row
                    ])->render();
                })
                ->rawColumns(['office_hours','actions'])
                ->make(true);
          }
          $dayColors=[];
          $timeColors=[];
          foreach(\App\Enums\SchoolPsychologicalOfficeHour::cases() as $day){
            $dayColors[strtolower($day->value)] = $day->dayColor();
            $timeColors[strtolower($day->value)] = $day->timeColor();
          }
          return view('content.business.school.psychological-supports.index',compact('dayColors','timeColors', 'is_visited'));
        }catch(\Exception $e){
          return redirect()->back()->with('psychological_support_status', [
                'message' => 'Something went wrong!',
                'type' => 'error'
          ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.business.school.psychological-support-old.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePsychologicalSupportRequest $request)
    {
        try{
            $data=$request->validated();
            DB::transaction(function () use ($data) {
                $school = auth()->user()->school;
                $psych = $school->psychologicalSupports()->create(Arr::only($data, ['name', 'mobile_number']));
                // Filter out days where is_closed is set
                $officeHours = collect($data['office_hours'])
                    ->filter(function ($day) {
                        return !($day['is_closed'] ?? false); // exclude if is_closed is true
                    })
                    ->values() // reindex the array (optional but cleaner)
                    ->toArray();
                $psych->officeHours()->createMany($officeHours);
            });
        //     return redirect()->back()->with('psychological_support_status', [
        //         'message' => 'Psychological support contact created successfully!',
        //         'type' => 'success'
        //    ]);
            return redirect()->back()->with('success', __('Contact created successfully!'));
        }catch(\Exception $e){
        //     return redirect()->back()->with('psychological_support_status', [
        //         'message' => 'Something went wrong!',
        //         'type' => 'error'
        //    ]);
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolPsychologicalSupport $psychologicalSupport)
    {
        try{
            $office_hours = $psychologicalSupport->officeHours->map(function ($hour) {
                return [
                    'day' => $hour->day,
                    'start_time' => $hour->start_time->format('H:i'),
                    'end_time' => $hour->end_time->format('H:i'),
                    'is_closed' => false,

                ];
            });
            if (request()->ajax()) {
                return response()->json([
                    'id' => $psychologicalSupport->id,
                    'name' => $psychologicalSupport->name,
                    'mobile_number' => $psychologicalSupport->mobile_number,
                    'office_hours' => $office_hours,
                    'created_at' => Carbon::parse($psychologicalSupport->created_at)->format('Y-m-d H:i:s')
                ]);
            }
            // return view('content.business.school.psychological-support', [
            //     'psychological_support' => $psychologicalSupport
            // ]);
            return view('content.business.school.psychological-supports.index');
        }catch(\Exception $e){
            // return redirect()->back()->with('psychological_support_status', [
            //     'message' => 'Something went wrong!',
            //     'type' => 'error'
            // ]);
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolPsychologicalSupport $psychologicalSupport)
    {
        try{
            $office_hours = $psychologicalSupport->officeHours->map(function ($hour) {
                return [
                    'day' => $hour->day,
                    'start_time' => $hour->start_time->format('H:i'),
                    'end_time' => $hour->end_time->format('H:i'),
                    'is_closed' => false
                ];
            });
            if (request()->ajax()) {
                return response()->json([
                    'id' => $psychologicalSupport->id,
                    'name' => $psychologicalSupport->name,
                    'mobile_number' => $psychologicalSupport->mobile_number,
                    'office_hours' => $office_hours,
                ]);
            }
            return view('content.business.school.psychological-supports.index');
            // return view('content.business.school.psychological-support.psychological-support-edit',
                                // compact('psychologicalSupport','office_hours'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePsychologicalSupportRequest $request, SchoolPsychologicalSupport $psychologicalSupport)
    {
        try{
            $data=$request->validated();
            DB::transaction(function () use ($data,$psychologicalSupport) {
                $psychologicalSupport->update(Arr::only($data, ['name', 'mobile_number']));
                $psychologicalSupport->officeHours()->delete();
                $officeHours = collect($data['office_hours'])
                    ->filter(function ($day) {
                        return !($day['is_closed'] ?? false); // exclude if is_closed is true
                    })
                    ->values() // reindex the array (optional but cleaner)
                    ->toArray();

                $psychologicalSupport->officeHours()->createMany($officeHours);
            });
        //     return redirect()->route('school.psychological-support.index')->with('psychological_support_status', [
        //     'message' => 'Psychological support contact updated successfully!',
        //     'type' => 'success'
        //    ]);
           return redirect()->route('school.psychological-support.index')->with('success', __('Contact updated successfully!'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolPsychologicalSupport $psychologicalSupport)
    {
        try{
            $psychologicalSupport->officeHours()->delete();
            $psychologicalSupport->delete();
            return response()->json([
                'message'=>__('Contact deleted successfully!'),
            ], 200);
        }catch(\Exception $e){
             return response()->json([
                'message'=> __('Something went wrong.'),
            ], 200);
        }
    }
}
