<?php

namespace App\Http\Controllers\Business\Restaurant;

use App\GooglePlace\Services\GooglePlaceService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Business\Restaurant\UpdateProfileRequest;
use App\Models\Accessibility;
use App\Models\Cuisine;
use App\Models\RestaurantCategory;
use App\Models\Role;
use App\Models\Sustainability;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function show(){
        try {
            $user = Auth::user();
            $user->load([
                'restaurant.timings' => function ($query) {
                    $query->orderBy('created_at');
                },
                'restaurant.cuisines' => function ($query) {
                    $query->orderBy('created_at');
                },
                'restaurant.categories'
            ]);
            return view('content.business.restaurants.profile.show', compact('user'));
        } catch (\Throwable $th) {
            // return redirect()->back()->with('restaurant-profile',[
            //     'message' => $th->getMessage(),
            //     'type' => 'error'
            // ]);
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }

    public function edit(){
        try {
            $user = Auth::user();
            $user->load([
                'restaurant.timings',
                'restaurant.cuisines' => function ($query) {
                    $query->orderBy('created_at');
                },
                'restaurant.categories'
            ]);
            // dd($user->restaurant->logo);
            $restaurant_categories = RestaurantCategory::all();
            $cuisines = Cuisine::all();
            $flag = 'update';
            if($user->restaurant->place_id){
                $data = (new GooglePlaceService())->getPlaceDetails($user->restaurant->place_id);
            }else{
                $data = null;
            }
            $flag = 'edit';

             // Group accessibilities by category
            $sustainabilities = Sustainability::all();
            $accessibilities = Accessibility::all()->groupBy('category');
            return view('content.business.restaurants.profile.edit', [
                'user' => $user,
                'cuisines' => $cuisines,
                'restaurant_categories' => $restaurant_categories,
                'sustainabilities' => $sustainabilities,
                'accessibilities' => $accessibilities,
                'data' => $data,
                'flag' => $flag,
                'restaurant_data' => $user->restaurant
            ]);
            //  return view(
            //     'content.business.restaurants.profile.edit',
            //     compact('user', 'cuisines', 'data', 'restaurant_categories','flag')
            // );
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something Went Wrong');
        }
    }

    public function update(UpdateProfileRequest $request){
        try {
            $user = Auth::user();
            $business_data = $request->only(['restaurant_name']);
            //if restaurant is rejected, then on resubmiting, the status will change to pending
            if($user->status=='rejected'){
                $business_data['status'] = 'pending';
            }

            $restaurant_data = $request->only([
                'address', 'address_link', 'city', 'pincode', 'mobile_number', 'country'
            ]);
            // $business_data['mobile_number'] = $restaurant_data['mobile_number'];

            // if(!$request->exists('accessibilities')){
            //     $restaurant_data['accessibilities'] = [];
            // }
            // $restaurant_data['name'] = $business_data['restaurant_name'];
            $cuisines = $request->cuisine_type;
            $categories = $request->restaurant_categories;
            $timings = $request->timings;
            $sustainabilities = $request->sustainabilities ?? [];
            $accessibilities  = $request->accessibilities ?? [];

            if($request->exists('restaurant_logo')){
                // dd("Hello");
                $file_name=$request->file('restaurant_logo')->store('public/restaurants/logo', 's3');
                $restaurant_data['logo'] = basename($file_name);
            }
            $flag = $user->restaurant->is_profile_completed ?? false;
            $restaurant_data['is_profile_completed'] = true;
            DB::transaction(function () use ($user, $business_data, $restaurant_data, $cuisines, $timings, $categories, $sustainabilities, $accessibilities ) {
                // $userRole = Role::where('slug','user')->first();
                // $user->role_id = $userRole->id;
                // $existUser = User::where('mobile_number', $business_data['mobile_number'])
                //   ->where('role_id', '=', $userRole->id)
                //   ->first();
                // if ($existUser){
                //   $existUser->delete();
                // }
                if(isset($user->mobile_number)){
                    $user->mobile_number = null;
                    $user->save();
                }
                $user->update($business_data);
                // $restaurantRole = Role::where('slug', 'restaurant')->first();
                // $updatedUser = User::where('mobile_number', $business_data['mobile_number'])->first();
                // if($updatedUser) {
                //     $updatedUser->role_id = $restaurantRole->id;
                //     $updatedUser->save();
                // }

                $restaurant=$user->restaurant()->updateOrCreate(
                    ['user_id' => $user->id],
                    $restaurant_data
                );
                $restaurant->cuisines()->sync($cuisines);
                $restaurant->categories()->sync($categories);
                $restaurant->sustainability()->sync($sustainabilities);
                $restaurant->accessibility()->sync($accessibilities);

                foreach($timings as $timing){
                    $restaurant->timings()->updateOrCreate(
                        ['day'=>$timing['day']],
                        [
                            'start_time' => $timing['start_time'] ?? null,
                            'end_time' => $timing['end_time'] ?? null,
                            'is_closed' => isset($timing['is_closed']) ? true : false,
                        ]
                    );
                }
            });
            if($flag){
                return redirect()->back()->with('success',__('Restaurant Profile Updated Successfully'));
            }
            return redirect()->route('restaurant.dashboard')->with('success',__('Restaurant Profile Created Successfully'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }
}
