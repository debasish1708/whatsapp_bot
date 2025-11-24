<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BusinessStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Mail\ApproveBusiness;
use App\Models\Restaurant;
use App\Models\User;
use App\Notifications\RejectBusinessNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
use MailerSend\Exceptions\MailerSendException;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Restaurant::query()->withWhereHas('user', function ($query) {
                $query->where('status', '<>', 'rejected');
            })->orderByDesc('created_at')->get();

            if($request->ajax()){
                return DataTables::of($query)
                    ->addIndexColumn()
                    ->editColumn('logo', function ($restaurant) {
                        if(isset($restaurant->logo)){
                            return "<img src='{$restaurant->logo}' alt='' width='90' height='90'/>";
                        }
                        return "<img src='" . asset('assets/img/restaurant_placeholder.png') . "' alt='' width='90' height='90'/>";
                    })
                    ->addColumn('actions', function ($restaurant) {
                        // dd($row);
                        return view('content.admin.restaurants.actions', compact('restaurant'));
                    })
                    ->editColumn('created_at', function ($restaurant) {
                        return Carbon::parse($restaurant->created_at)->format('Y-m-d H:i:s');
                    })
                    ->rawColumns(['logo', 'actions'])
                    ->make(true);
            }
            // dd($restaurants->get());
            return view('content.admin.restaurants.index');
        } catch (\Throwable $th) {
            // return $th->getMessage();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

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
    public function show(Restaurant $restaurant)
    {
        try {
            $restaurant->load(['user', 'timings', 'cuisines', 'categories']);
            $user = $restaurant->user;
            $subscription = $user->subscription(config('constant.subscription_plans.type'));
            return view('content.admin.restaurants.show', compact('restaurant', 'subscription'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
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
    public function destroy(Restaurant $restaurant)
    {
        try {
            $restaurant->delete();
            return response()->json([
                'message'=>"Restaurant deleted successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ]);
        }
    }

    public function approve(Restaurant $restaurant){
        try {
            $user = $restaurant->user;
            $user->update([
                'status' => BusinessStatus::APPROVED->value
            ]);
            Mail::to($user->email)->send(new ApproveBusiness($user));
            return response()->json([
                'message'=>"Restaurant approved successfully"
            ], 200);
        } catch(MailerSendException $e) {
            info('MailerSendException Email Approve School: ' . $e->getMessage());
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }

    public function reject(Request $request,Restaurant $restaurant){
        $request->validate([
            'reason' => 'required|string'
        ]);
        try {
            $user = $restaurant->user;
            $user->update([
                'status' => BusinessStatus::REJECTED->value,
                'reject_reason' => $request->reason
            ]);
            $user->notify(new RejectBusinessNotification());
            return response()->json([
                'message'=>"Restaurant rejected successfully"
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }
}
