<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gaming Laptops',
                'slug' => 'gaming-laptops',
                'description' => 'High-performance laptops for gaming and professional work'
            ],
            [
                'name' => 'Smartphones',
                'slug' => 'smartphones',
                'description' => 'Latest smartphones with cutting-edge technology'
            ],
            [
                'name' => 'Smartwatches',
                'slug' => 'smartwatches',
                'description' => 'Smart wearable devices for fitness and connectivity'
            ],
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'description' => 'Headphones, speakers, and audio equipment'
            ],
            [
                'name' => 'Mouse & Keyboards',
                'slug' => 'mouse-keyboards',
                'description' => 'Gaming and productivity input devices'
            ],
            [
                'name' => 'Monitors',
                'slug' => 'monitors',
                'description' => 'High-resolution displays for gaming and work'
            ],
            [
                'name' => 'PC Components',
                'slug' => 'pc-components',
                'description' => 'Computer hardware and components'
            ],
            [
                'name' => 'Cameras',
                'slug' => 'cameras',
                'description' => 'Action cameras and recording devices'
            ],
            [
                'name' => 'Stands & Mounts',
                'slug' => 'stands-mounts',
                'description' => 'Accessories for organizing your setup'
            ],
            [
                'name' => 'Game Consoles',
                'slug' => 'game-consoles',
                'description' => 'Next-gen gaming consoles'
            ],
            [
                'name' => 'Gaming Chairs',
                'slug' => 'gaming-chairs',
                'description' => 'Ergonomic chairs for extended gaming sessions'
            ],
            [
                'name' => 'Microphones',
                'slug' => 'microphones',
                'description' => 'Professional microphones for streaming and recording'
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Various gaming and tech accessories'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}