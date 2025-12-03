<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

 public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0', 
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'featured' => 'nullable|boolean',
            'specifications' => 'nullable|array',
            
            'images' => 'nullable|array',
            'images.*.url' => 'required_with:images|string',
            'images.*.is_primary' => 'nullable|boolean'
        ];
    }
}