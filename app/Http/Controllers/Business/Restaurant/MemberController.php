<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Dialog360\Dialog360;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreMemberRequest;
use App\Http\Requests\Business\Restaurant\UpdateMemberRequest;
use App\Models\BusinessMember;
use App\Models\RestaurantMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $members = auth()->user()->restaurant->members()->with('user')->get();
            if($request->ajax()) {
                return DataTables::of($members)
                    ->addIndexColumn()
                    ->addColumn('name', function ($member) {
                        return $member->user ? $member->user->name : 'N/A';
                    })
                    ->addColumn('mobile_number', function ($member) {
                        return $member->user ? $member->user->mobile_number : 'N/A';
                    })
                    ->addColumn('actions', function ($member) {
                           return view('content.business.restaurants.members.actions', compact('member'));
                    })
                    ->make(true);
            }
            return view('content.business.restaurants.members.index', compact('members'));
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
    public function store(StoreMemberRequest $request)
    {
        try{
            $data = $request->validated();
            $user = User::where('mobile_number', $data['mobile_number'])->first();
            if ($user && $user->businessMembers()->exists()) {
                return redirect()->back()->with(
                    'error',
                    'This user already exists under a business.'
                );
            }
            $role = Role::where('slug','restaurant_member')->first();

            DB::transaction (function () use ($data, $role, &$user) {
                if (!$user) {
                    $user = User::create([
                        'name' => $data['name'],
                        'mobile_number' => $data['mobile_number'],
                        'role_id' => $role->id,
                    ]);
                } else {
                    $user->update([
                        'role_id' => $role->id,
                    ]);
                }

                // auth()->user()->restaurant->members()->create([
                //     'user_id' => $user->id,
                //     'name' => $data['name'],
                //     'mobile_number' => $data['mobile_number'],
                // ]);
                auth()->user()->restaurant->members()->create([
                    'user_id' => $user->id,
                ]);
            });

            (new Dialog360())->sendRestaurantModuleButtons($user);
            return redirect()->back()->with('success', __('Member added successfully.'));
        } catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessMember $member)
    {
        try{
            return response()->json([
                'id' => $member->id,
                'name' => $member->user->name,
                'mobile_number' => $member->user->mobile_number,
                'created_at' => $member->created_at?->toDateTimeString(),
            ]);
        }catch(\Exception $e){
            return response()->json(['message' => 'Failed to fetch member.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessMember $member)
    {
        try{
            return response()->json([
                'id' => $member->id,
                'user_id' => $member->user_id,
                'name' => $member->user->name,
                'mobile_number' => $member->user->mobile_number,
                'created_at' => $member->created_at?->toDateTimeString(),
            ]);
        }catch(\Exception $e){
            return response()->json(['message' => 'Failed to fetch member.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, BusinessMember $member)
    {
        try{
            $data = $request->validated();
            $user = User::where('mobile_number', $data['mobile_number'])->first();

            DB::transaction (function () use ($data, &$user, $member) {
                $role = Role::where('slug','user')->first();
                $member->user()->update([
                    'role_id' => $role->id
                ]);
                if($user) {
                    $restaurantMember = Role::where('slug','restaurant_member')->first();
                    $user->update([
                        'name' => $data['name'],
                        'mobile_number' => $data['mobile_number'],
                        'role_id' => $restaurantMember->id
                    ]);
                } else {
                     $restaurantMember = Role::where('slug','restaurant_member')->first();
                     $user = User::create([
                        'name' => $data['name'],
                        'mobile_number' => $data['mobile_number'],
                        'role_id' => $restaurantMember->id,
                    ]);
                }
                $member->update([
                    'user_id' => $user->id,
                ]);
            });

            return redirect()->back()->with('success', __('Member updated successfully.'));
        }catch(\Illuminate\Validation\ValidationException $e){
            return redirect()->back()->withErrors($e->validator)->withInput();
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessMember $member)
    {
        try{
            $role = Role::where('slug','user')->first();
            $member->user()->update([
                'role_id' => $role->id
            ]);
            $member->delete();
            return response()->json([
                'message'=>__("Member deleted successfully."),
            ], 200);
        }catch(\Exception $e){
            return response()->json([
                'message'=>__("Error deleting member."),
            ], 500);
        }
    }
}
