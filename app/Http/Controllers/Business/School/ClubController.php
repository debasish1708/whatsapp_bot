<?php

namespace App\Http\Controllers\Business\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\CreateClubRequest;
use App\Http\Requests\Business\School\UpdateClubRequest;
use App\Models\SchoolClub;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class ClubController extends Controller
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
            $is_visited = $visited_pages->contains('clubs');

            if(!$is_visited){
                $visited_pages->add('clubs');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }

            if($request->ajax()){
                $data = $school
                             ->clubs()
                             ->latest()
                             ->get();
                return DataTables::of($data)
                     ->addIndexColumn()
                     ->addColumn('actions', function ($row) {
                        return view('content.business.school.club-activities.actions', [
                            'club' => $row
                        ])->render();
                    })
                    ->editColumn('meeting_time', function($row){
                        return optional($row->meeting_time)->format('d M Y h:i A') ?? 'N/A';
                    })
                    ->editColumn('name',function($row){
                        return Str::limit($row->name,30,'...');
                    })
                    ->editColumn('description',function($row){
                        return Str::limit($row->description,30,'...');
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.business.school.club-activities.index', compact('is_visited'));
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
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
    public function store(CreateClubRequest $request)
    {
        try{
           $data=$request->validated();
           $school=auth()->user()->school;
           $school->clubs()->create($data);
        //    return redirect()->back()->with('club_status', [
        //     'message' => 'Club created successfully!',
        //     'type' => 'success'
        //    ]);
            return redirect()->back()->with('success', __('Club created successfully!'));
        }catch(\Exception $e){
            // return redirect()->back()->with('club_status', [
            // 'message' => 'Something went wrong! Please try again.',
            // 'type' => 'error'
            // ]);
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClub $clubActivity)
    {
        try{
            if (request()->ajax()) {
                return response()->json([
                    'id' => $clubActivity->id,
                    'name' => $clubActivity->name,
                    'description' => $clubActivity->description,
                    'meeting_time' => Carbon::parse($clubActivity->meeting_time)->format('Y-m-d H:i:s'),
                    'location' => $clubActivity->location,
                    'contact_person' => $clubActivity->contact_person,
                    'contact_phone' => $clubActivity->contact_phone,
                    'created_at'=>Carbon::parse($clubActivity->created_at)->format('Y-m-d H:i:s')
                ]);
            }
            return view('content.business.school.club-activities.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchoolClub $clubActivity)
    {
        try{
             if (request()->ajax()) {
                return response()->json([
                    'id' => $clubActivity->id,
                    'name' => $clubActivity->name,
                    'description' => $clubActivity->description,
                    'meeting_time' => $clubActivity->meeting_time,
                    'location' => $clubActivity->location,
                    'contact_person' => $clubActivity->contact_person,
                    'contact_phone' => $clubActivity->contact_phone,
                ]);
            }
            return view('content.business.school.club-activities.index');
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClubRequest $request, SchoolClub $clubActivity)
    {
        try{
            $data=$request->validated();
            $clubActivity->update($data);
            return redirect()->back()->with('success', __('Club updated successfully!'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchoolClub $clubActivity)
    {
        try {
            $clubActivity->delete();
            return response()->json([
                'message'=>__('Club Deleted successfully.'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 200);
        }
    }
}
