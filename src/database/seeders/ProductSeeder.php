<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first category
        $category = Category::first();

        if ($category) {
            Product::create([
                'category_id' => $category->id,
                'name' => 'Super Gaming Laptop 3000',
                'slug' => Str::slug('Super Gaming Laptop 3000'),
                'description' => 'High performance laptop.',
                'price' => 1500.00,
                'stock' => 10,
            ]);
        }
    }
}
