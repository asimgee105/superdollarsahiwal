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
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->text('short_description')->nullable();
            $table->longText('body');
            $table->string('image_url')->nullable();
            $table->integer('reading_time')->default(5); // in minutes
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();

            // SEO Meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();
        });

        Schema::create('post_category_pivot', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('post_categories')->onDelete('cascade');
            $table->primary(['post_id', 'category_id']);
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('general')->index();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_title')->nullable(); // e.g. Fashion Blogger or VIP Customer
            $table->string('avatar_url')->nullable();
            $table->text('comment');
            $table->tinyInteger('rating')->default(5);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('lookbooks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->string('image_url');
            $table->text('description')->nullable();
            $table->json('tagged_product_ids')->nullable(); // Tag products on image coordinates
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lookbooks');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('post_category_pivot');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_categories');
    }
};
