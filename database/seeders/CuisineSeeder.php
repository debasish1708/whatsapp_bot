<?php

namespace Database\Seeders;

use App\Models\Cuisine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cuisines = [
            ['name' => 'Italian'],
            ['name' => 'Chinese'],
            ['name' => 'Indian'],
            ['name' => 'Mexican'],
            ['name' => 'Thai'],
            ['name' => 'Japanese'],
            ['name' => 'Mediterranean'],
            ['name' => 'French'],
            ['name' => 'American'],
            ['name' => 'Greek'],
        ];

        foreach($cuisines as $cuisine){
            Cuisine::create($cuisine);
        }
    }
}
