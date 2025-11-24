<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $role = Auth::user()->role->slug;

        $user = User::where('mobile_number', $row['mobile_number'])->first();

        $user_role = Role::where('slug', 'user')->first();

        if(!$user){
            $user = User::create([
                'role_id'=>$user_role->id,
                'name' => $row['name'],
                'mobile_number' => $row['mobile_number'],
                'is_verified'=>true,
                'verified_at'=>Carbon::now(),
                'status'=>'approved'
            ]);
        }

        if($role=='school'){
            $school = Auth::user()->school;
            $school->businessUsers()->firstOrCreate([
                'user_id'=>$user->id,
                'added_by'=>'school'
            ]);
        }
        if($role=='restaurant'){
            $restaurant = Auth::user()->restaurant;
            $restaurant->businessUsers()->firstOrCreate([
                'user_id'=>$user->id,
                'added_by'=>'restaurant'
            ]);
        }

        return $user;
        
    }
}
