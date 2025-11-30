<?php

namespace App\Http\Controllers;

use App\Http\Requests\Order\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
     public function __construct(private OrderService $orderService)
    {
    }

    /**
     * Get all orders for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product'])
            ->recent()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total()
            ]
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
    public function store(Request $request)
    {
        //
    }

     /**
     * Get single order details
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== $request->user()->id && !$request->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $order->load(['items.product', 'user']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

     /**
     * Process checkout and create order
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->processCheckout(
                $request->user(),
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => new OrderResource($order->load(['items.product']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel an order (only if pending)
     */
    public function cancel(Request $request, Order $order): JsonResponse
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if (!$order->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled'
            ], 422);
        }

        $this->orderService->cancelOrder($order);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => new OrderResource($order->fresh(['items.product']))
        ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}