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
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique()->index(); // main_header, footer_quick_links, etc.
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('navigation_menus')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->onDelete('cascade');
            $table->string('title');
            $table->string('url')->default('#');
            $table->string('type')->default('link'); // link, mega-menu, category
            $table->integer('sort_order')->default(0)->index();
            $table->json('settings')->nullable(); // holds badges, colors, icons, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigation_menus');
    }
};
