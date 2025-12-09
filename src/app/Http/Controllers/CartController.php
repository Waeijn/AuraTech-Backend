<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartService->getUserCart($request->user());
        
        return response()->json([
            'success' => true,
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(AddToCartRequest $request): JsonResponse
    {
        $cartItem = $this->cartService->addToCart(
            $request->user(),
            $request->validated()
        );
        
        $cart = $this->cartService->getUserCart($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'data' => new CartResource($cart)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, int $cartItemId): JsonResponse
    {
        $this->cartService->updateCartItemQuantity(
            $request->user(),
            $cartItemId,
            $request->input('quantity')
        );
        
        $cart = $this->cartService->getUserCart($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $cartItemId): JsonResponse
    {
        $this->cartService->removeFromCart($request->user(), $cartItemId);
        
        $cart = $this->cartService->getUserCart($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully',
            'data' => new CartResource($cart)
        ]);
    }

     /**
     * Clear entire cart
     */
    public function clear(Request $request): JsonResponse
    {
        $this->cartService->clearCart($request->user());
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'data' => [
                'items' => [],
                'total' => 0,
                'total_items' => 0
            ]
        ]);
    }
}
