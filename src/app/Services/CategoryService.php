<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function getAllCategories(): Collection
    {
        return Category::withCount('products')
            ->orderBy('name')
            ->get();
    }

    public function getCategoryWithProducts(Category $category): Category
    {
        return $category->load(['products' => function ($query) {
            $query->where('stock', '>', 0)
                  ->with('images')
                  ->orderBy('name');
        }]);
    }

    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'slug' => $data['slug'] ?? \Str::slug($data['name']),
            'description' => $data['description'] ?? null
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'] ?? $category->name,
            'slug' => $data['slug'] ?? $category->slug,
            'description' => $data['description'] ?? $category->description
        ]);

        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            throw new \Exception('Cannot delete category with associated products');
        }

        $category->delete();
    }
}