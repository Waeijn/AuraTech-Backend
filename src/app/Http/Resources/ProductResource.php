<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'featured' => (bool) $this->featured,
            'description' => $this->description,

            // 👇 CRITICAL FIX 1: Always send category_id (Removed the 'when' condition)
            'category_id' => $this->category_id,

            // 👇 CRITICAL FIX 2: Send the full Category Object (Not just the name string)
            // This allows your frontend to read product.category.name if needed
            'category' => new CategoryResource($this->whenLoaded('category')),

            'specifications' => $this->specifications ?? new \stdClass(),

            'slug' => $this->slug,

            // Handle Images
            'image' => $this->when(
                $this->relationLoaded('images'),
                fn() => $this->images->first()?->url ?? '/img/products/placeholder.png'
            ),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString()
        ];
    }
}
