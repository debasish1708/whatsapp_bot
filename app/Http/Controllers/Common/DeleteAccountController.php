<?php

namespace App\Http\Controllers\Common;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DeleteAccountController extends Controller
{

    public function index(Request $request){
        try {
            $users = User::with('role')->where('is_deletion_requested', true)->get();

            if($request->ajax()){
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($user) {
                        return view('content.admin.account-deletion-requests.actions', compact('user'));
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }

            return view('content.admin.account-deletion-requests.index');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function requestDeletion(Request $request){
        try {
            $user = Auth::user();
            $user->update([
                'is_deletion_requested'=>true
            ]);

            return response()->json([
                'message'=>'Deletion request submitted. Account will be deleted when the admin confirms the request.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }

    public function show(User $account){
        try {

            $role = $account->role->slug;
            $subscription = $account->subscription('business');
            if($role == 'school'){
                $school = $account->school;
                return view('content.admin.account-deletion-requests.schools-show', compact('account', 'school', 'subscription'));
            }
            if($role == 'restaurant'){
                $restaurant = $account->restaurant;
                return view('content.admin.account-deletion-requests.restaurants-show', compact('account', 'restaurant', 'subscription'));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function approve(User $account){
        try {
            $role = $account->role->slug;

            DB::transaction(function () use($account, $role) {
                if($role==UserRole::School->value){
                    $account->school()->delete();
                }
                if($role==UserRole::Restaurant->value){
                    $account->restaurant()->delete();
                }
                $account->delete();
            });

            return response()->json([
                'message'=>'Account deleted successfully.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }

    public function reject(User $account){
        try {
            $account->update([
                'is_deletion_requested'=>false
            ]);
            return response()->json([
                'message'=>'Deletion request rejected successfully.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }
}
