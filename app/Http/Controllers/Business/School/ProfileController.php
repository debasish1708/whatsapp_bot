<?php

namespace App\Http\Controllers\Business\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\ProfileCreateRequest;
use App\Http\Requests\Business\School\ProfileUpdateRequest;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('restrict.school.profile.setup')->except(['store']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $school = auth()->user()->school;
            $school->name = auth()->user()->name;
            $school->email = auth()->user()->email;
            if (!$school) {
                return redirect()->back()->with('error', 'School profile not found. Please complete your profile setup.');
            }
            return view('content.business.school.profile.show',compact('school'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while fetching the school profile: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       try{
            $school = auth()->user()->school;
            $user=auth()->user();
            $categories=SchoolCategory::all();
            if (!$school) {
                return redirect()->back()->with('error', 'School profile not found. Please complete your profile setup.');
            }
            return view('content.business.school.profile.edit', compact('school','user','categories'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'An error occurred while fetching the school profile: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfileCreateRequest $request)
    {
        try{
            $data=$request->validated();
            $user_data=$data['user'];
            $school_data=$data['school'];
            DB::transaction(function () use ($user_data, $school_data,$request) {
                $user = auth()->user();
                $school = $user->school;
                $user->update($user_data);
                $schoolRole = Role::where('slug', 'school')->first();
                $updatedUser = User::where('mobile_number', $school_data['mobile_number'])->first();
                if($updatedUser) {
                    $updatedUser->role_id = $schoolRole->id;
                    $updatedUser->save();
                }

                if (isset($school_data['logo'])) {
                    $path=$school_data['logo']->store('public/school/logo', 's3');
                    $school_data['logo'] = basename($path);
                }

                $school_data['mobile_number'] = str_replace(' ','', $school_data['mobile_number']);
                $school->update($school_data);
                $user->update([
                    'mobile_number' => $school_data['mobile_number']
                ]);
                $school->update(['is_profile_completed'=>true]);

                $school->categories()->sync($school_data['categories']);
            });
            // return redirect()->route('school.dashboard')->with('school_profile',[
            //     'message' => 'Profile Created Successful!.',
            //     'type' => 'success'
            // ]);
            return redirect()->route('school.dashboard')->with('success', 'Profile Created Successful!.');
        }catch(\Exception $e){
            // return redirect()->back()->with('school_profile',[
            //     'message' => 'Something Went Wrong!.',
            //     'type' => 'error'
            // ]);
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //]
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $profile)
    {
        try{
            $school = $profile;
            $user=auth()->user();
            $categories=SchoolCategory::all();
            $flag='update';
            $schoolDetail = $profile;
            if (!$school) {
                return redirect()->back()->with('error', 'School profile not found. Please complete your profile setup.');
            }
            return view('content.business.school.profile.edit', compact('school','categories','user','flag', 'schoolDetail'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', 'An error occurred while fetching the school profile: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request, School $profile)
    {
        try{
            $data=$request->validated();
            $user_data=$data['user'];
            $school_data=$data['school'];
            // $school_data['name'] = $user_data['name'];
            DB::transaction (function () use ($request, $profile, $user_data, $school_data) {
            //   $userRole = Role::where('slug','user')->first();
            //   $existUser = User::where('mobile_number', $school_data['mobile_number'])
            //     ->where('role_id', '=', $userRole->id)
            //     ->first();
            //     if ($existUser){
            //       $existUser->delete();
            //     }
                $user = auth()->user();
                if(isset($user->mobile_number)){
                    $user->mobile_number = null;
                    $user->save();
                }
                $profile->user->update($user_data);

                if (isset($school_data['logo'])) {
                    $path=$school_data['logo']->store('public/school/logo', 's3');
                    $school_data['logo'] = basename($path);
                }

                $school_data['mobile_number'] = str_replace(' ','', $school_data['mobile_number']);
                // $profile->user->update([
                //     'mobile_number' => $school_data['mobile_number']
                // ]);
                $profile->update($school_data);
                $profile->categories()->sync($school_data['categories']);

                // $schoolRole = Role::where('slug', 'school')->first();
                // $updatedUser = User::where('mobile_number', $school_data['mobile_number'])->first();
                // if($updatedUser) {
                //     $updatedUser->role_id = $schoolRole->id;
                //     $updatedUser->save();
                // }
            });
            return redirect()->back()->with('success', 'Profile Updated Successful!.');
        }catch(\Exception $e){
            info('Error in Update School Profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
