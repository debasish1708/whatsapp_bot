<?php

namespace Database\Seeders;

use App\Models\Sustainability;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SustainabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'We offer food just before the expiration date',
            'We use returnable or compostable packaging',
            'We cooperate with food banks'
        ];

        foreach ($data as $value) {
            Sustainability::create([
                'id' => Str::uuid(),
                'value' => $value
            ]);
        }
    }
}
