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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('label_id')->nullable()->constrained('product_labels')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->string('sku')->unique()->index();
            $table->string('barcode')->nullable()->unique()->index();
            $table->string('type')->default('simple'); // simple, variable, grouped, bundle, digital, gift_card, subscription
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->json('highlights')->nullable(); // bullet highlights points
            $table->json('specifications')->nullable(); // technical specifications parameters
            $table->text('wash_care')->nullable();
            $table->string('origin_country')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->integer('sort_order')->default(0)->index();

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('path');
            $table->string('type')->default('image'); // image, video, 360
            $table->string('alt_text')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('sku')->unique()->index();
            $table->string('barcode')->nullable()->unique()->index();
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();

            // Core attributes relations
            $table->foreignId('size_id')->nullable()->constrained('sizes')->onDelete('set null');
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');

            $table->json('attributes')->nullable(); // custom attributes (e.g. material, sleeve, fit, fabric)
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('product_category', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->primary(['product_id', 'category_id']);
        });

        Schema::create('product_collection', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('collection_id')->constrained('collections')->onDelete('cascade');
            $table->primary(['product_id', 'collection_id']);
        });

        Schema::create('product_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('related_id')->constrained('products')->onDelete('cascade');
            $table->string('type')->default('related'); // related, cross-sell, upsell, bought-together
            $table->timestamps();

            $table->unique(['product_id', 'related_id', 'type'], 'prod_rel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_relationships');
        Schema::dropIfExists('product_collection');
        Schema::dropIfExists('product_category');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('products');
    }
};
