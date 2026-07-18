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
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_url')->unique()->index();
            $table->string('target_url');
            $table->integer('status_code')->default(301); // 301 Permanent or 302 Temporary
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('redirect_404_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url')->index();
            $table->string('referrer')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('hit_count')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redirect_404_logs');
        Schema::dropIfExists('redirects');
    }
};
