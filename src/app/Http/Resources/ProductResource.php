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
            'price' => $this->price, // Return as integer (cents)
            'image' => $this->when(
                $this->relationLoaded('images'),
                fn() => $this->images->first()?->url ?? '/img/products/placeholder.png'
            ),
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn() => $this->category->name),
            'stock' => $this->stock,
            'featured' => $this->featured,
            'specifications' => $this->specifications ?? new \stdClass(), 
            
            // Additional fields for detail view
            'slug' => $this->when($request->routeIs('products.show'), $this->slug),
            'category_id' => $this->when($request->routeIs('products.show'), $this->category_id),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->when($request->routeIs('products.show'), $this->created_at?->toISOString()),
            'updated_at' => $this->when($request->routeIs('products.show'), $this->updated_at?->toISOString())
        ];
    }
}