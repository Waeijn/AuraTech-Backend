<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private CartService $cartService)
    {
    }

    /**
     * Process checkout and create order from cart
     */
    public function processCheckout(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = $this->cartService->getUserCart($user);

            // Validate cart is not empty
            if ($cart->items->isEmpty()) {
                throw new \Exception('Cart is empty');
            }

            // Validate stock availability
            $unavailableItems = $this->cartService->validateCartStock($cart);
            if (!empty($unavailableItems)) {
                $message = "Some items are out of stock:\n";
                foreach ($unavailableItems as $item) {
                    $message .= "- {$item['product_name']}: Requested {$item['requested_quantity']}, Available {$item['available_stock']}\n";
                }
                throw new \Exception($message);
            }

            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $tax = $this->calculateTax($subtotal);
            $shipping = $this->calculateShipping($data);
            $total = $subtotal + $tax + $shipping;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $data['payment_method'],
                'payment_status' => 'unpaid',
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['shipping_email'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_state' => $data['shipping_state'],
                'shipping_zip' => $data['shipping_zip'],
                'shipping_country' => $data['shipping_country'] ?? 'Philippines',
                'notes' => $data['notes'] ?? null
            ]);

            // Create order items and deduct stock
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_price' => $cartItem->product->price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->product->price * $cartItem->quantity
                ]);

                // Deduct stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Process payment simulation
            if ($data['payment_method'] !== 'cash_on_delivery') {
                $this->processPaymentSimulation($order, $data);
            }

            // Clear cart after successful order
            $this->cartService->clearCart($user);

            return $order;
        });
    }

    /**
     * Cancel order and restore stock
     */
    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->items as $orderItem) {
                $orderItem->product->increment('stock', $orderItem->quantity);
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded'
            ]);
        });
    }

    /**
     * Calculate tax (12% for Philippines)
     */
    private function calculateTax(int $subtotal): int
    {
        return (int) ($subtotal * 0.12);
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShipping(array $data): int
    {
        // Flat rate shipping for now
        return 5000; 
    }

    /**
     * Simulate payment processing
     */
    private function processPaymentSimulation(Order $order, array $data): void
    {
        // Simulate payment gateway integration
        // In production, integrate with real payment gateways (PayMaya, GCash, etc.)
        
        $paymentSuccess = true; // Simulate success

        if ($paymentSuccess) {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'processing'
            ]);
        } else {
            throw new \Exception('Payment failed. Please try again.');
        }
    }

    /**
     * Update order status (Admin only)
     */
    public function updateOrderStatus(Order $order, string $status): Order
    {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception('Invalid order status');
        }

        $order->update(['status' => $status]);

        return $order->fresh();
    }
}