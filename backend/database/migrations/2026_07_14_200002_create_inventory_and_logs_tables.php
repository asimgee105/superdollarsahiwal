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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->index();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity')->default(0); // Available stock
            $table->integer('reserved')->default(0); // Held in pending carts/orders
            $table->integer('incoming')->default(0); // Stock on purchase order
            $table->integer('damaged')->default(0);  // Defective stock
            $table->integer('returned')->default(0); // Returned customer items
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamps();

            $table->unique(['warehouse_id', 'variant_id']);
        });

        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->string('type'); // adjustment, movement, receipt, sale, return, damage
            $table->integer('quantity_changed');
            $table->string('reference')->nullable(); // Order number or PO number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('warehouses');
    }
};
