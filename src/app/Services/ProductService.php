<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getFilteredProducts(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Product::with(['category', 'images']);

        $query->where('stock', '>', 0);

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Search by name or description
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Filter by featured products
        if (isset($filters['featured']) && $filters['featured'] !== null) {
            $query->where('featured', (bool)$filters['featured']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    public function getProductWithRelations(Product $product): Product
    {
        return $product->load(['category', 'images']);
    }

    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'] ?? \Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'category_id' => $data['category_id'],
                'featured' => $data['featured'] ?? false, // ✅ ADD THIS
                'specifications' => $data['specifications'] ?? null // ✅ ADD THIS
            ]);

            // Handle images if provided
            if (!empty($data['images'])) {
                foreach ($data['images'] as $index => $imageData) {
                    $product->images()->create([
                        'url' => $imageData['url'],
                        'is_primary' => $imageData['is_primary'] ?? ($index === 0)
                    ]);
                }
            }

            return $product->load(['category', 'images']);
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'] ?? $product->name,
                'slug' => $data['slug'] ?? $product->slug,
                'description' => $data['description'] ?? $product->description,
                'price' => $data['price'] ?? $product->price,
                'stock' => $data['stock'] ?? $product->stock,
                'category_id' => $data['category_id'] ?? $product->category_id,
                'featured' => $data['featured'] ?? $product->featured, // ✅ ADD THIS
                'specifications' => $data['specifications'] ?? $product->specifications // ✅ ADD THIS
            ]);

            // Handle image updates if provided
            if (isset($data['images'])) {
                // Delete existing images
                $product->images()->delete();
                
                // Add new images
                foreach ($data['images'] as $index => $imageData) {
                    $product->images()->create([
                        'url' => $imageData['url'],
                        'is_primary' => $imageData['is_primary'] ?? ($index === 0)
                    ]);
                }
            }

            return $product->fresh(['category', 'images']);
        });
    }

    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            // Delete associated images
            $product->images()->delete();
            
            // Delete the product
            $product->delete();
        });
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new \Exception("Insufficient stock for product: {$product->name}");
        }

        $product->decrement('stock', $quantity);
    }
}