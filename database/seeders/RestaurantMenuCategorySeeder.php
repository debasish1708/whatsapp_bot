<?php

namespace Database\Seeders;

use App\Models\RestaurantMenuCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantMenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name"=>"starter"
            ],
            [
                "name"=>"Main course"
            ],
            [
                "name"=>'Dessert'
            ]
        ];

        foreach($categories as $category){
            RestaurantMenuCategory::create($category);
        }
    }
}
