<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreJobOfferRequest;
use App\Http\Requests\Business\Restaurant\UpdateJobOfferRequest;
use App\Models\JobOffer;
use App\Models\RestaurantJobOffer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class JobOfferController extends Controller
{
    public function index(Request $request)
    {
        try{
            $restaurant = auth()->user()->restaurant;
            // Shepherd tour logic for first visit
            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_already_visited = $visited_pages->contains('job-offers');
            if(!$is_already_visited){
                $visited_pages->add('job-offers');
                $restaurant->update([
                    'visited_pages'=>json_encode($visited_pages)
                ]);
            }
            if($request->ajax()){
                $school = auth()->user()->school;
                $restaurant = auth()->user()->restaurant;

                $restaurant->jobOffers()
                    ->whereIn('status', ['active', 'inactive'])
                    ->where('expiry_date', '<', now())
                    ->update(['status' => 'expired']);

                $data = RestaurantJobOffer::where('restaurant_id', $restaurant->id)
                    ->orderByDesc('created_at');

                $data = $restaurant->jobOffers()->latest()->get();
                return DataTables::of($data)
                       ->addIndexColumn()
                        ->addColumn('applicants', function ($data) {
                           return count($data->jobApplications);
                        })
                       ->addColumn('actions', function ($job_offer) {
                           return view('content.business.restaurants.job-offers.actions', compact('job_offer'));
                        })
                       ->editColumn('expiry_date', function($job_offer){
                           return $job_offer->expiry_date ? Carbon::parse($job_offer->expiry_date)->format('d M Y') : 'N/A';
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
            return view('content.business.restaurants.job-offers.index', compact('is_already_visited'));
        }catch(\Throwable $th){
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(StoreJobOfferRequest $request)
    {
        try{
            $restaurant = auth()->user()->restaurant;
            $data=$request->only(['position', 'description', 'salary', 'location', 'contact_email', 'contact_number', 'expiry_date', 'status']);
            $restaurant->jobOffers()->create($data);
            // return redirect()->back()->with('job_offer_status', [
            //     'message' => 'Job Offer created successfully!',
            //     'type' => 'success'
            // ]);
            return redirect()->back()->with('success', __('Job Offer created successfully!'));
        }catch(\Throwable $th){
            // return redirect()->back()->with('job_offer_status', [
            //     'message' => 'Job Offer creation failed!',
            //     'type' => 'error'
            // ]);
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JobOffer $job_offer)
    {
        try{
            if (request()->ajax()) {
                return response()->json([
                    'id' => $job_offer->id,
                    'position' => $job_offer->position,
                    'description' => $job_offer->description,
                    'salary' => $job_offer->salary,
                    'location' => $job_offer->location,
                    'contact_phone' => $job_offer->contact_number,
                    'contact_email' => $job_offer->contact_email,
                    'expiry_date' => $job_offer->expiry_date,
                    'status' => $job_offer->status,
                    'created_at' => Carbon::parse($job_offer->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            return view('content.business.restaurants.job-offers.index');
        }catch(\Throwable $th){
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobOffer $job_offer)
    {
        try{
             if (request()->ajax()) {
                return response()->json([
                    'id' => $job_offer->id,
                    'position' => $job_offer->position,
                    'description' => $job_offer->description,
                    'salary' => $job_offer->salary,
                    'location' => $job_offer->location,
                    'contact_phone' => $job_offer->contact_number,
                    'contact_email' => $job_offer->contact_email,
                    'expiry_date' => $job_offer->expiry_date,
                    'status' => $job_offer->status,
                    'created_at' => Carbon::parse($job_offer->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            return view('content.business.school.job-offer');
        }catch(\Throwable $th){
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobOfferRequest $request, JobOffer $job_offer)
    {
        try{
            $data=$request->only(['position', 'description', 'salary', 'location', 'contact_email', 'contact_number', 'expiry_date', 'status']);
            $job_offer->update($data);
            return redirect()->back()->with('success', __('Job Offer updated successfully!'));
        }catch(\Throwable $th){
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobOffer $job_offer)
    {
        try{
            $job_offer->delete();
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
