<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\StoreCustomerRequest;
use App\Http\Requests\Business\Restaurant\UpdateCustomerRequest;
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
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request){
        try {
            $user = Auth::user();
            $restaurant = $user->restaurant;
            // Shepherd tour logic for first visit
            $visited_pages = collect(json_decode($restaurant->visited_pages));
            $is_already_visited = $visited_pages->contains('customers');
            if(!$is_already_visited){
                $visited_pages->add('customers');
                $restaurant->update([
                    'visited_pages'=>json_encode($visited_pages)
                ]);
            }
            $users = $restaurant->businessUsers()
                ->with('user')
                ->whereHas('user')
                ->get();

            if($request->ajax()){
                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('actions', function ($user) {
                        return view('content.business.restaurants.customers.actions', ['user'=>$user]);
                    })
                    ->editColumn('name', function ($user) {
                        return Str::limit($user->user->name, 20, '...');
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }
            return view('content.business.restaurants.customers.index', compact('is_already_visited'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function store(StoreCustomerRequest $request){
        try {
            DB::transaction(function () use ($request) {
                $restaurant = Auth::user()->restaurant;
                $user = User::where('mobile_number', $request->mobile_number)->first();
                $role = Role::where('slug', 'user')->first();

                if(!$user){
                    $user = User::create([
                        'role_id'=>$role->id,
                        'name'=>$request->name,
                        'mobile_number'=>$request->mobile_number,
                        'is_verified'=>true,
                        'verified_at'=>Carbon::now(),
                        'status'=>'approved'
                    ]);
                }else{
                    $businessUser = $restaurant->businessUsers()
                        ->where('user_id', $user->id)
                        ->whereIn('added_by', ['restaurant', 'search'])
                        ->first();

                    if ($businessUser) {
                        if ($businessUser->added_by === 'restaurant') {
                            throw new Exception(__('User already exists'));
                        }

                        // If added_by is 'search', update it to 'restaurant'
                        $businessUser->update(['added_by' => 'restaurant']);
                        return;
                    }
                }

                $restaurant->businessUsers()->firstOrCreate([
                    'user_id'=>$user->id,
                    'added_by'=>'restaurant'
                ]);
            });
            return redirect()->back()->with('success', __('Customer added successfully.'));
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
            return redirect()->back()->with('success', __('Customers imported successfully.'));
        }catch(\Exception $e){
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(BusinessUser $customer){
        try {
            if(request()->ajax()){
                return response()->json([
                    'id'=>$customer->id,
                    'name'=>$customer->user->name,
                    'mobile_number'=>$customer->user->mobile_number
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 200);
        }
    }

    public function update(UpdateCustomerRequest $request, BusinessUser $customer){
        try {
            $user = $customer->user;
            $user->update([
                'name'=>$request->name,
                'mobile_number'=>$request->mobile_number
            ]);
            return redirect()->back()->with('success', __('Customer updated successfully.'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function destroy(User $customer){
        try {
            DB::transaction(function () use ($customer) {
                // Delete the student from the school_user table
                // if($customer->businessMembers()->exists()){
                //     $customer->businessMembers()->delete();
                // }
                $customer->businessUsers()
                        ->where('businessable_id','=',Auth::user()->restaurant->id)
                        ->delete();
                if(!in_array($customer->role->slug, ['restaurant_member', 'school_member'])){
                    $customer->update([
                        'role_id'=>Role::where('slug', 'user')->first()->id
                    ]);
                }
            });

            return response()->json([
                'message'=>__("Customer deleted successfully")
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>$th->getMessage()
            ], 400);
        }
    }
}
