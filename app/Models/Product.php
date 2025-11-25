<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'is_active'
    ];

    // RELATIONSHIP: A Product belongs to one Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // RELATIONSHIP: A Product has many Images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
