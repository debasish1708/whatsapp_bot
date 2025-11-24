<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                "name"=>"Restaurant",
                "slug"=>"restaurant",
            ],
            [
                "name"=>"Museum",
                "slug"=>"museum",
            ],
            [
                "name"=>"Hotel",
                "slug"=>"hotel",
            ],
            [
                "name"=>"School",
                "slug"=>"school",
            ],
            [
                "name"=>"Admin",
                "slug"=>"admin",
            ],
            [
                "name" => "Restaurant Member",
                "slug" => "restaurant_member"
            ],
            [
                "name" => "School Member",
                "slug" => "school_member"
            ]
        ];

        foreach($roles as $role){
            Role::create($role);
        }
    }
}
