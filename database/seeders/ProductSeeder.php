<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $laptops = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'High performance notebooks and ultrabooks'
        ]);

        $smartphones = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Android and iOS mobile phones'
        ]);

        $audio = Category::create([
            'name' => 'Audio',
            'slug' => 'audio',
            'description' => 'Headphones, speakers, and earbuds'
        ]);

        Product::create([
            'category_id' => $laptops->id,
            'name' => 'AuraBook Pro 15',
            'slug' => 'aurabook-pro-15',
            'description' => 'M3 Processor, 16GB RAM, 1TB SSD.',
            'price' => 1299.99,
            'stock' => 15,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $smartphones->id,
            'name' => 'AuraPhone X',
            'slug' => 'auraphone-x',
            'description' => '6.7 inch OLED display, 5G connectivity.',
            'price' => 899.00,
            'stock' => 50,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $audio->id,
            'name' => 'NoiseCancel Pods',
            'slug' => 'noisecancel-pods',
            'description' => 'Wireless earbuds with active noise cancellation.',
            'price' => 149.99,
            'stock' => 200,
            'is_active' => true
        ]);

        Product::create([
            'category_id' => $audio->id,
            'name' => 'BassBoom Speaker',
            'slug' => 'bassboom-speaker',
            'description' => 'Portable waterproof bluetooth speaker.',
            'price' => 59.99,
            'stock' => 75,
            'is_active' => true
        ]);
    }
}
