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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index(); // e.g. order_confirmed, order_shipped
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->json('channels')->nullable(); // ['email', 'sms', 'push', 'whatsapp']
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('channel')->index(); // email, sms, push, whatsapp
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status')->default('sent')->index(); // sent, failed, retried
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_templates');
    }
};
