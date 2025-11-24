<?php

namespace Database\Seeders;

use App\Models\Accessibility;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccessibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'basic' => [
                'Wheelchair access',
                'Free Wi-Fi',
                'Mobile phone charger',
                'Pet-friendly (dogs allowed)'
            ],
            'senior' => [
                'Quiet zone or separate space',
                'Highlighted and easy-to-read menu',
                'Possibility of personal assistance when ordering',
                'Special prices / discounts 65+'
            ],
            'student' => [
                'Student menu at a discounted price',
                'Possibility of payment by ISIC card',
                'Quick meals in under 5 minutes',
                'Quiet study corner / laptop sockets'
            ],
            'child' => [
                'Children\'s menu',
                'High chairs / changing table',
                'Play corner',
                'Possibility of heating baby food',
                'Coloring books or interactive entertainment'
            ]
        ];

        foreach ($data as $category => $values) {
            foreach ($values as $value) {
                Accessibility::create([
                    'id' => Str::uuid(),
                    'category' => $category,
                    'value' => $value
                ]);
            }
        }
    }
}
