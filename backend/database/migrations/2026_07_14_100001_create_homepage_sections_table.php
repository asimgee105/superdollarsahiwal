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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layout_id')->constrained('homepage_layouts')->onDelete('cascade');
            $table->string('section_key')->index(); // hero_slider, categories, trust_badges, etc.
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('background_type')->default('color'); // color, image, video
            $table->string('background_color')->nullable();
            $table->string('background_image')->nullable();
            $table->string('background_video')->nullable();
            $table->string('padding')->nullable(); // py-12, py-16
            $table->string('margin')->nullable();
            $table->string('width')->default('container'); // container, full
            $table->string('animation')->nullable(); // fade, slide, none
            $table->string('button_text')->nullable();
            $table->string('button_url')->nullable();
            $table->string('layout_variation')->default('default'); // grid, carousel, list

            // Visibility & Scheduling rules
            $table->boolean('is_enabled')->default(true)->index();
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('show_on_desktop')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->integer('sort_order')->default(0)->index();
            $table->json('settings')->nullable(); // holds sliders info, lists of product IDs, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
