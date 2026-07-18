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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('rating')->unsigned(); // 1 to 5
            $table->string('title')->nullable();
            $table->text('comment');
            $table->string('status')->default('pending')->index(); // pending, approved, rejected
            $table->boolean('is_verified')->default(false);
            $table->integer('helpful_votes')->default(0);
            $table->timestamps();
        });

        Schema::create('product_review_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
            $table->string('path');
            $table->string('type')->default('image'); // image, video
            $table->timestamps();
        });

        Schema::create('product_review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('reply');
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_key')->nullable()->index(); // For guests
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
            $table->unique(['session_key', 'product_id']);
        });

        Schema::create('recently_viewed', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_key')->nullable()->index(); // For guests
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('filter_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade'); // Null means global config
            $table->string('filter_key')->index(); // brand, size, color, price, material, fit
            $table->string('label');
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_enabled')->default(true)->index();
            $table->string('style')->default('checkbox'); // checkbox, swatch, radio, range
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filter_configs');
        Schema::dropIfExists('recently_viewed');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('product_review_replies');
        Schema::dropIfExists('product_review_media');
        Schema::dropIfExists('product_reviews');
    }
};
