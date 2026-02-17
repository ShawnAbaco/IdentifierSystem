<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Trees',
                'slug' => 'trees',
                'description' => 'Woody plants with a single main stem or trunk, typically reaching significant heights.',
                'icon' => 'fa-tree'
            ],
            [
                'name' => 'Flowers',
                'slug' => 'flowers',
                'description' => 'Beautiful blooming plants known for their colorful and fragrant flowers.',
                'icon' => 'fa-seedling'
            ],
            [
                'name' => 'Shrubs',
                'slug' => 'shrubs',
                'description' => 'Woody plants smaller than trees, with multiple stems.',
                'icon' => 'fa-leaf'
            ],
            [
                'name' => 'Vines',
                'slug' => 'vines',
                'description' => 'Plants with trailing or climbing stems.',
                'icon' => 'fa-tree'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
