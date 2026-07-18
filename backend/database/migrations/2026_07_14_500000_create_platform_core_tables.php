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
        Schema::create('platform_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('version')->default('1.0.0');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(true)->index();
            $table->json('dependencies')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('version')->default('1.0.0');
            $table->boolean('is_enabled')->default(false)->index();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('slug')->unique()->index();
            $table->boolean('is_active')->default(false)->index();
            $table->json('variables')->nullable(); // primary_color, font_family, button_style
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('disk')->default('local');
            $table->bigInteger('size_bytes')->default(0);
            $table->string('status')->default('success')->index(); // success, failed
            $table->timestamps();
        });

        Schema::create('platform_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key')->unique();
            $table->string('domain')->nullable();
            $table->string('status')->default('active')->index(); // active, expired, suspended
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_licenses');
        Schema::dropIfExists('platform_backups');
        Schema::dropIfExists('platform_themes');
        Schema::dropIfExists('platform_plugins');
        Schema::dropIfExists('platform_modules');
    }
};
