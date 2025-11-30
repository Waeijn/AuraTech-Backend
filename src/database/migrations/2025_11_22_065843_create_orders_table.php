<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Order totals
            $table->integer('subtotal'); // Total before any adjustments (in cents)
            $table->integer('tax')->default(0); // Tax amount (in cents)
            $table->integer('shipping')->default(0); // Shipping cost (in cents)
            $table->integer('total'); // Final total (in cents)
            
            // Order status
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            
            // Payment information
            $table->enum('payment_method', ['cash_on_delivery', 'credit_card', 'gcash', 'paymaya'])->default('cash_on_delivery');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed', 'refunded'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            
            // Shipping information
            $table->string('shipping_name');
            $table->string('shipping_email');
            $table->string('shipping_phone');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_zip');
            $table->string('shipping_country')->default('Philippines');
            
            // Additional information
            $table->text('notes')->nullable();
            
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
