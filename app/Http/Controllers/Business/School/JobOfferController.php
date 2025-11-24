<?php

namespace App\Http\Controllers\Business\School;

use App\Dialog360\Dialog360;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\CreateJobOfferRequest;
use App\Http\Requests\Business\School\UpdateJobOfferRequest;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class JobOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $school = auth()->user()->school;
            $visited_pages = collect(json_decode($school->visited_pages));
            $is_visited = $visited_pages->contains('job_offers');
            if(!$is_visited){
                $visited_pages->add('job_offers');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }
            if($request->ajax()){
                $school->jobOffers()
                    ->whereIn('status', ['active', 'inactive'])
                    ->where('expiry_date', '<', now())
                    ->update(['status' => 'expired']);

                $data = $school->jobOffers()->latest()->get();
                return DataTables::of($data)
                       ->addIndexColumn()
                       ->addColumn('applicants', function ($row) {
                           return count($row->jobApplications);
                        })
                       ->addColumn('actions', function ($row) {
                           return view('content.business.school.job-offers.actions', [
                               'job_offer' => $row
                           ])->render();
                        })
                       ->editColumn('expiry_date', function($row){
                           return $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date)->format('d M Y') : 'N/A';
                       })
                       ->editColumn('description',function($job_offer){
                            return Str::limit($job_offer->description,30,'...');
                        })
                        ->editColumn('location',function($job_offer){
                            return Str::limit($job_offer->location,6,'...');
                        })
                        ->editColumn('position',function($job_offer){
                            return Str::limit($job_offer->position,30,'...');
                        })
                       ->rawColumns(['actions'])
                       ->make(true);
            }
            return view('content.business.school.job-offers.index', compact('is_visited'));
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
    public function store(CreateJobOfferRequest $request)
    {
        try{
            $school = auth()->user()->school;
            $data=$request->validated();
            $school->jobOffers()->create($data);
        //     return redirect()->back()->with('job_offer_status', [
        //     'message' => 'Job Offer created successfully!',
        //     'type' => 'success'
        //    ]);
            return redirect()->back()->with('success', __('Job Offer created successfully!'));
        }catch(\Exception $e){
        //     return redirect()->back()->with('job_offer_status', [
        //     'message' => 'Job Offer creation failed!',
        //     'type' => 'error'
        //    ]);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    // public function storeApplication(StoreJobApplicationRequest $request)
    // {
    //     try{
    //         $data = $request->validated();
    //         $user = User::where('mobile_number', $request->mobile_number)
    //                     ->first();
    //         $data['user_id'] = $user->id;
    //         $jobOffer = JobOffer::find($request->job_offer_id);
    //         if (isset($data['resume'])) {
    //             $path = $data['resume']->store('public/job_application/resume', 's3');
    //             $data['resume'] = basename($path);
    //         }
    //         $jobOffer->jobApplications()->create($data);
    //         return redirect()->back()->with('modal_success', 'Application submitted successfully!');
    //     }catch(\Exception $e){
    //         return redirect()->back()->with('modal_error', 'Application submission failed!');
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function show(JobOffer $jobOffer)
    {
        try{
            if (request()->ajax()) {
                return response()->json([
                    'id' => $jobOffer->id,
                    'position' => $jobOffer->position,
                    'description' => $jobOffer->description,
                    'salary' => $jobOffer->salary,
                    'location' => $jobOffer->location,
                    'contact_phone' => $jobOffer->contact_number,
                    'contact_email' => $jobOffer->contact_email,
                    'expiry_date' => $jobOffer->expiry_date,
                    'status' => $jobOffer->status,
                    'created_at' => $jobOffer->created_at
                ]);
            }
            return view('content.business.school.job-offers.index');
        }catch(\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // public function showForm(string $id){
    //     try{
    //         $jobOffer = JobOffer::where('id',$id)->first();
    //         $business = $jobOffer->businessable;
    //         if (!$jobOffer) {
    //           return redirect()->back()->with('error', 'Job not found.');
    //         }
    //         $pageConfigs = ['myLayout' => 'blank'];
    //         return view('content.business.common.job.form', [
    //             'job_offer' => $jobOffer,
    //             'pageConfigs' => $pageConfigs,
    //             'business' => $business,
    //             'school' => $business,
    //         ]);
    //     }catch(\Exception $e){
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobOffer $jobOffer)
    {
        try{
             if (request()->ajax()) {
                return response()->json([
                    'id' => $jobOffer->id,
                    'position' => $jobOffer->position,
                    'description' => $jobOffer->description,
                    'salary' => $jobOffer->salary,
                    'location' => $jobOffer->location,
                    'contact_number' => $jobOffer->contact_number,
                    'contact_email' => $jobOffer->contact_email,
                    'expiry_date' => $jobOffer->expiry_date,
                    'status' => $jobOffer->status
                ]);
            }
            return view('content.business.school.job-offers.index');
        }catch(\Exception $e){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobOfferRequest $request, JobOffer $jobOffer)
    {
        try{
            $data = $request->validated();
            $jobOffer->update($data);
            return redirect()->back()->with('success', __('Job Offer updated successfully!'));
        }catch(\Exception $e){
            return redirect()->back()->with('error',__('Something went wrong'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobOffer $jobOffer)
    {
        try{
            $jobOffer->delete();
            return response()->json([
                'message'=> __('Job Offer Deleted successfully.'),
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message'=>"Something went wrong. ",
            ], 200);
        }
    }
}
