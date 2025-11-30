<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id'
    ];

    /**
     * Get the user that owns the cart
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in the cart
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get cart items with product details
     */
    public function itemsWithProducts()
    {
        return $this->items()->with(['product.images', 'product.category']);
    }

    /**
     * Calculate total price of all items in cart
     */
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }

    /**
     * Get total number of items in cart
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Clear all items from cart
     */
    public function clearItems()
    {
        $this->items()->delete();
    }
}