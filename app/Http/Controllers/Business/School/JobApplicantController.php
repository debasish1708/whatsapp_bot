<?php

namespace App\Http\Controllers\Business\School;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class JobApplicantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // try{
        //     if($request->ajax()){
        //         $school = auth()->user()->school;
        //         $data = $school->jobApplications()->latest()->get();
        //         return DataTables::of($data)
        //                ->addIndexColumn()
        //                ->addColumn('actions', function ($row) {
        //                    return view('content.business.school.job-offers.actions', [
        //                        'job_application' => $row
        //                    ])->render();
        //                 })
        //                ->editColumn('created_at', function($row){
        //                    return $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d M Y') : 'N/A';
        //                })
        //                ->rawColumns(['actions'])
        //                ->make(true);
        //     }
        //     return view('content.business.school.job-offers.show');
        // }catch(\Exception $e){
        //     return response()->json(['error' => $e->getMessage()], 500);
        // }
    }

    public function applicants(Request $request,JobOffer $jobOffer)
    {
        try{
            if($request->ajax()){
                $data = $jobOffer->jobApplications()->latest()->get();
                return DataTables::of($data)
                       ->addIndexColumn()
                       ->addColumn('name',function($row){
                           return $row->first_name.' '.$row->last_name;
                       })
                       ->addColumn('actions', function ($row) {
                           return view('content.business.school.job-offers.applicants.actions', [
                               'jobApplication' => $row
                           ])->render();
                        })
                       ->editColumn('resume',function($row){
                           return '<a href="' . asset($row->resume) . '" target="_blank">
                                        <i class="fas fa-file-pdf text-danger me-1"></i> Resume
                                   </a>';
                       })
                       ->rawColumns(['name','resume','actions'])
                       ->make(true);
            }
            return view('content.business.school.job-offers.applicants.index',compact('jobOffer'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $jobApplicant)
    {
        try{
            if (request()->ajax()) {
                return response()->json([
                    'id' => $jobApplicant->id,
                    'name' => $jobApplicant->first_name.' '.$jobApplicant->last_name,
                    'email' => $jobApplicant->email,
                    'mobile_nuumber' => $jobApplicant->mobile_number,
                    'address' => $jobApplicant->address,
                    'gender' => $jobApplicant->gender,
                    'dob' => $jobApplicant->date_of_birth,
                    'city' => $jobApplicant->city,
                    'resume' => $jobApplicant->resume,
                ]);
            }
            return view('content.business.school.job-offers.applicants.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplication $jobApplicant)
    {
        try{
            $jobApplicant->delete();
            return response()->json([
                'message'=>__('Applicant Deleted successfully.'),
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message'=>__('Something went wrong.'),
            ], 200);
        }
    }
}
