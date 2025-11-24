<?php

namespace Database\Seeders;

use App\Models\RestaurantCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                "name"=>"Restaurants",
            ],
            [
                "name"=>"Pizzeria",
            ],
            [
                "name"=>"Fast food (McDonald's, KFC, Burger King...)",
            ],
            [
                "name"=>"Pub / beer hall",
            ],
            [
                "name"=>"Wine bar / wine shop",
            ],
            [
                "name"=>"Bistro",
            ],
            [
                "name"=>"Soup house / homemade meals",
            ],
            [
                "name"=>"Street food / food trucks",
            ],
            [
                "name"=>"Cafe",
            ],
            [
                "name"=>"Pastry shop / ice cream parlor",
            ],
            [
                "name"=>"Bakery",
            ],
            [
                "name"=>"Supermarket (Kaufland, Lidl...)",
            ],
            [
                "name"=>"Grocery / convenience store",
            ],
            [
                "name"=>"Healthy food / fitness cuisine",
            ],
            [
                "name"=>"Vegetarian/vegan",
            ],
            [
                "name"=>"Zero-waste / pre-consumption sales",
            ],
            [
                "name"=>"Nonstop / 24/7 operation",
            ],
            [
                "name"=>"Local/regional specialty",
            ],
            // [
            //     "name"=>"Chain (with the option to select a brand from the list)",
            // ],
        ];

        foreach($categories as $category){
            RestaurantCategory::create($category);
        }
    }
}
