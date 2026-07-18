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
        Schema::create('platform_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action')->index(); // login, logout, order_create, settings_change
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('platform_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('event')->index(); // order.created, user.registered
            $table->string('secret')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('platform_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained('platform_webhooks')->onDelete('cascade');
            $table->json('payload')->nullable();
            $table->integer('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->boolean('is_success')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_webhook_logs');
        Schema::dropIfExists('platform_webhooks');
        Schema::dropIfExists('platform_activity_logs');
    }
};
