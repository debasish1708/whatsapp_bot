<?php

namespace App\Http\Controllers\Business\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\School\StoreStudentRequest;
use App\Http\Requests\Business\School\UpdateStudentRequest;
use App\Imports\StudentsImport;
use App\Models\BusinessUser;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class StudentController extends Controller
{
    public function index(Request $request){
        try {
            $user = Auth::user();
            $school = $user->school;
            $visited_pages = collect(json_decode($school->visited_pages));
            $is_visited = $visited_pages->contains('students');
            if(!$is_visited){
                $visited_pages->add('students');
                $school->update([
                    'visited_pages'=>$visited_pages
                ]);
            }
            // $users = User::withWhereHas('businessUser', function ($query) use ($school) {
            //     $query->where('businessable_id', $school->id);
            // });
            $users = $school->businessUsers()
                ->with('user')
                ->whereHas('user')
                ->get();
            // info('Total Users ' . json_encode($users, JSON_PRETTY_PRINT));
            if($request->ajax()){
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($user) {
                        return view('content.business.school.students.actions', ['user'=>$user]);
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.business.school.students.index', compact('is_visited'));
        } catch (\Throwable $th) {
            // return $th;
            return redirect()->back()->with('error', __("Something went wrong."));
        }
    }

    public function store(StoreStudentRequest $request){
        try {
            DB::transaction(function () use ($request) {
                $school = Auth::user()->school;
                $user = User::where('mobile_number', $request->mobile_number)->first();
                $role = Role::where('slug', 'user')->first();
                if(!$user){
                    $user = User::create([
                        'role_id' => $role->id,
                        'name'=>$request->name,
                        'mobile_number'=>$request->mobile_number,
                        'is_verified'=>true,
                        'verified_at'=>Carbon::now(),
                        'status'=>'approved'
                    ]);
                } else {
                    // throw new \Exception(__('student already exists'));
                    // return redirect()->back()->with('error',__('student already exists'));
                    $businessUser = $school->businessUsers()
                        ->where('user_id', $user->id)
                        ->whereIn('added_by', ['school', 'search'])
                        ->first();

                    if ($businessUser) {
                        if ($businessUser->added_by === 'school') {
                            throw new Exception(__('User already exists'));
                        }

                        // Update directly if added_by is 'search'
                        $businessUser->update(['added_by' => 'school']);
                        return;
                    }
                }

                $school->businessUsers()->firstOrCreate([
                    'user_id'=>$user->id,
                    'added_by'=>'school'
                ]);
            });
            return redirect()->back()->with('success', __('student added successfully.'));
        } catch (\Throwable $th) {
            // return $th;
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function import(Request $request)
    {
        try{
            $request->validate([
                'file' => 'required|file|mimetypes:text/csv,text/plain,application/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:2048',
            ]);
            Excel::import(new StudentsImport, request()->file('file'));
            return redirect()->back()->with('success', __('Students imported successfully.'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', __('Please upload a valid CSV.'));
        }
    }

    public function edit(User $student){
        try {
            info('Editing Student: ' . json_encode($student, JSON_PRETTY_PRINT));
            if(request()->ajax()){
                return response()->json([
                    'id'=>$student->id,
                    'name'=>$student->name,
                    'mobile_number'=>$student->mobile_number
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>__("Something went wrong.")
            ], 200);
        }
    }

    public function update(UpdateStudentRequest $request, User $student){
        try {
            // $is_mobile_exist = User::where('mobile_number', $request->mobile_number)->first();
            // if($is_mobile_exist){
            //     return redirect()->back()->with('warning',__('Student Already Exists'));
            // }
            $student->update([
                'name'=>$request->name,
                'mobile_number'=>$request->mobile_number
            ]);
            return redirect()->back()->with('success', __('student updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __("Something went wrong."));
        }
    }

    public function destroy(User $student){
        try {
            DB::transaction(function () use ($student) {
                // Delete the student from the school_user table
                // if($student->businessMembers()->exists()){
                //     $student->businessMembers()->delete();
                // }
                $student->businessUsers()
                        ->where('businessable_id','=',Auth::user()->school->id)
                        ->delete();
                if(!in_array($student->role->slug, ['school_member', 'restaurant_member'])){
                    $student->update([
                        'role_id'=>Role::where('slug', 'user')->first()->id
                    ]);
                }
            });

            return response()->json([
                'message'=> __('Student deleted successfully.')
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>__("Something went wrong.")
            ], 400);
        }
    }
}
