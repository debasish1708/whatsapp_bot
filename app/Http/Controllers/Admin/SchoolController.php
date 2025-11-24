<?php

namespace App\Http\Controllers\Admin;

use App\Dialog360\Dialog360;
use App\Enums\BusinessStatus;
use App\Http\Controllers\Controller;
use App\Mail\ApproveBusiness;
use App\Mail\RejectBusiness;
use App\Models\School;
use App\Notifications\RejectBusinessNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use MailerSend\Exceptions\MailerSendException;
use Yajra\DataTables\DataTables;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        try {
            $schools = School::query()->withWhereHas('user', function ($query) {
                $query->where('status', '<>', 'rejected');
            })->orderByDesc('created_at')->get();
                // ->whereHas('user', function ($query) {
                //     $query->where('status', 'pending');
                // });


            if($request->ajax()){
                return DataTables::of($schools)
                    ->addIndexColumn()
                    ->editColumn('logo', function ($school) {
                        if(isset($school->logo)){
                            return "<img src='{$school->logo}' alt='' width='90' height='90'/>";
                        }
                        return "<img src='" . asset('assets/img/placeholder.jpg') . "' alt='' width='90' height='90'/>";
                    })
                    ->addColumn('actions', function ($school) {
                        return view('content.admin.schools.actions', compact('school'));
                    })
                    ->editColumn('created_at', function ($school) {
                        return Carbon::parse($school->created_at)->format('Y-m-d H:i:s');
                    })
                    ->rawColumns(['logo', 'actions'])
                    ->make(true);
            }
            // dd($restaurants->get());
            return view('content.admin.schools.index');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
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
    public function show(School $school)
    {
        try {
            $school->load('user');
            $user = $school->user;
            $subscription = $user->subscription(config('constant.subscription_plans.type'));
            return view('content.admin.schools.show', compact('school', 'subscription'));
        } catch (\Throwable $th) {
            // return $th->getMessage();
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
    public function destroy(School $school)
    {
        try {
            $school->delete();
            return response()->json([
                'message'=>"School deleted successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ]);
        }
    }

    public function approve(School $school){
        try {
            $user = $school->user;
            $user->update([
                'status' => BusinessStatus::APPROVED->value
            ]);
            Mail::to($user->email)->send(new ApproveBusiness($user));
            $school_data_json = [
                'school_name' => $user->name
            ];
            // dispatch(new \App\Jobs\Send360TemplateMessageJob($user,'school_welcome',$school_data_json, [
            //   'school_id' => $school->id
            // ]));
            (new Dialog360())->sendSchoolModuleButtons($user);
            return response()->json([
                'message'=>"School approved successfully"
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

    public function reject(Request $request,School $school){
        $request->validate([
            'reason' => 'required|string'
        ]);
        try {
            $user = $school->user;
            $user->update([
                'status' => BusinessStatus::REJECTED->value,
                'reject_reason' => $request->reason
            ]);
            $user->notify(new RejectBusinessNotification());
            // Mail::to($user->email)->send(new RejectBusiness($user, $request->reason));
            // Mail::raw($request->reason, function ($message) use ($user) {
            //     $message->to($user->email)
            //         ->subject('Reject Business');
            // });
            return response()->json([
                'message'=>"School rejected successfully"
            ], 200);
        } catch(MailerSendException $e) {
            info('MailerSendException Email Reject School: ' . $e->getMessage());
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }
}
