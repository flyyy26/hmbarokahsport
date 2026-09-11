<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = [
            ['name' => 'S', 'label' => 'Small', 'sort_order' => 1],
            ['name' => 'M', 'label' => 'Medium', 'sort_order' => 2],
            ['name' => 'L', 'label' => 'Large', 'sort_order' => 3],
            ['name' => 'XL', 'label' => 'Extra Large', 'sort_order' => 4],
            ['name' => 'XXL', 'label' => 'Double Extra Large', 'sort_order' => 5],
            ['name' => '3XL', 'label' => 'Triple Extra Large', 'sort_order' => 6],
            ['name' => '4XL', 'label' => '4X Large', 'sort_order' => 7],
            ['name' => '5XL', 'label' => '5X Large', 'sort_order' => 8],
            ['name' => '6XL', 'label' => '6X Large', 'sort_order' => 9],
            ['name' => '7XL', 'label' => '7X Large', 'sort_order' => 10],
            ['name' => '8XL', 'label' => '8X Large', 'sort_order' => 11],
            ['name' => '9XL', 'label' => '9X Large', 'sort_order' => 12],
            ['name' => '10XL', 'label' => '10X Large', 'sort_order' => 13],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}