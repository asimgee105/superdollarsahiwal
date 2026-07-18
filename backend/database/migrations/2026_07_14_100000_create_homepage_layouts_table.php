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
        Schema::create('homepage_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('header_style')->default('classic'); // classic, minimal, center
            $table->string('hero_style')->default('slider');    // slider, split, video
            $table->string('category_style')->default('grid');  // grid, carousel
            $table->string('product_card_style')->default('card'); // card, overlay
            $table->string('footer_style')->default('default'); // default, simple
            $table->json('colors')->nullable(); // primary, secondary, backgrounds
            $table->json('typography')->nullable(); // font family, sizes
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_layouts');
    }
};
