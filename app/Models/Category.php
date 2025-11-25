<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // These fields are allowed to be mass-assigned (security)
    protected $fillable = ['name', 'slug', 'description'];

    // RELATIONSHIP: A Category has many Products
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
