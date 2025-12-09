<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Get all products belonging to this category
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope to get categories with product count
     */
    public function scopeWithProductCount($query)
    {
        return $query->withCount('products');
    }

    /**
     * Scope to get only categories that have products
     */
    public function scopeHasProducts($query)
    {
        return $query->has('products');
    }
}