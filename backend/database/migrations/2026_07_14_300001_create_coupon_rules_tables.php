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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->string('type')->default('percentage'); // flat, percentage, free_shipping
            $table->decimal('value', 12, 2);
            $table->decimal('min_cart_value', 12, 2)->default(0.00);
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_user')->default(1);
            $table->integer('used_count')->default(0);
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('applicable_categories')->nullable(); // array of category IDs
            $table->json('applicable_brands')->nullable();     // array of brand IDs
            $table->json('applicable_products')->nullable();   // array of product IDs
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
