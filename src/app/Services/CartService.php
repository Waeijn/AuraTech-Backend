<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get or create user's cart
     */
    public function getUserCart(User $user): Cart
    {
        $cart = Cart::with(['items.product.images', 'items.product.category'])
            ->firstOrCreate(['user_id' => $user->id]);
        
        return $cart;
    }

    /**
     * Add item to cart
     */
    public function addToCart(User $user, array $data): CartItem
    {
        return DB::transaction(function () use ($user, $data) {
            $product = Product::findOrFail($data['product_id']);
            
            // Check stock availability
            if ($product->stock < $data['quantity']) {
                throw new \Exception("Insufficient stock. Only {$product->stock} items available.");
            }

            $cart = $this->getUserCart($user);
            
            // Check if product already exists in cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();
            
            if ($cartItem) {
                // Update quantity if item already exists
                $newQuantity = $cartItem->quantity + $data['quantity'];
                
                if ($product->stock < $newQuantity) {
                    throw new \Exception("Insufficient stock. Only {$product->stock} items available.");
                }
                
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $data['quantity']
                ]);
            }
            
            return $cartItem->load(['product.images', 'product.category']);
        });
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItemQuantity(User $user, int $cartItemId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($user, $cartItemId, $quantity) {
            $cart = $this->getUserCart($user);
            
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('id', $cartItemId)
                ->firstOrFail();
            
            // Check stock availability
            if ($cartItem->product->stock < $quantity) {
                throw new \Exception("Insufficient stock. Only {$cartItem->product->stock} items available.");
            }
            
            if ($quantity <= 0) {
                throw new \Exception("Quantity must be greater than 0");
            }
            
            $cartItem->update(['quantity' => $quantity]);
            
            return $cartItem->load(['product.images', 'product.category']);
        });
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(User $user, int $cartItemId): void
    {
        $cart = $this->getUserCart($user);
        
        CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->delete();
    }

    /**
     * Clear all items from cart
     */
    public function clearCart(User $user): void
    {
        $cart = $this->getUserCart($user);
        $cart->clearItems();
    }

    /**
     * Validate cart items stock before checkout
     */
    public function validateCartStock(Cart $cart): array
    {
        $unavailableItems = [];
        
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->quantity) {
                $unavailableItems[] = [
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'requested_quantity' => $item->quantity,
                    'available_stock' => $item->product->stock
                ];
            }
        }
        
        return $unavailableItems;
    }
}