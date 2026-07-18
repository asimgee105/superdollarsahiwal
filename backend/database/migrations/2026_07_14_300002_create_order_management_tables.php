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
            $table->string('order_number')->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('status')->default('pending')->index(); // pending, confirmed, processing, packed, ready_to_ship, shipped, out_for_delivery, delivered, cancelled, returned, refunded, failed

            // Addresses
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country')->default('Pakistan');

            $table->string('billing_name')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country')->default('Pakistan');

            // Financial breakdown
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('shipping_cost', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);

            // Payments
            $table->string('payment_method')->default('cod'); // cod, stripe, paypal, razorpay, bank_transfer
            $table->string('payment_status')->default('pending')->index(); // pending, paid, failed, refunded

            // Additional Configuration options
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->onDelete('set null');
            $table->boolean('gift_wrap')->default(false);
            $table->text('gift_message')->nullable();
            $table->text('order_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->string('sku')->index();
            $table->integer('quantity');
            $table->decimal('price', 12, 2);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });

        Schema::create('order_timeline', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('status')->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_method');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending')->index(); // pending, success, failed, reversed
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
            $table->string('type')->default('return'); // return, exchange, replacement
            $table->string('reason'); // defective, sizing_wrong, not_matching_desc, other
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->json('media_paths')->nullable(); // array of uploaded images/videos URLs
            $table->string('status')->default('pending')->index(); // pending, approved, rejected, completed
            $table->string('pickup_status')->default('pending')->index(); // pending, assigned, picked, details
            $table->decimal('refund_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('order_transactions');
        Schema::dropIfExists('order_timeline');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
