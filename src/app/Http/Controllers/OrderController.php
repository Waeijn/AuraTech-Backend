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
     * Get orders (Admin sees all, User sees their own)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();


        // 1. CHECK: Is the user an Admin?
        // We check both 'role' string and 'is_admin' boolean to be safe
        $isAdmin = ($user->role === 'admin' || $user->is_admin == 1);


        if ($isAdmin) {
            // ADMIN MODE: Fetch ALL orders from the database
            // We load 'user' too so the admin knows who bought it
            $orders = Order::with(['items.product', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // CUSTOMER MODE: Fetch only THEIR orders
            $orders = $user->orders()
                ->with(['items.product'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }


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
        // Allow Admin to view ANY order
        $isAdmin = ($request->user()->role === 'admin' || $request->user()->is_admin == 1);


        if ($order->user_id !== $request->user()->id && !$isAdmin) {
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
     * UPDATE STATUS (For Admin Order Review)
     * This was missing!
     */
    public function update(Request $request, Order $order)
    {
        // Validate Status
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);


        // Update the status
        $order->status = $request->status;
        $order->save();


        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'data' => new OrderResource($order)
        ]);
    }


    public function destroy(Order $order)
    {
        //
    }
}
