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
        Schema::create('platform_automation_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_event')->index(); // user_registered, order_created, order_paid
            $table->json('conditions')->nullable();    // if/else checks
            $table->json('actions')->nullable();       // email, sms, coupon, webhook
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('platform_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('platform_automation_workflows')->onDelete('cascade');
            $table->string('status')->default('success')->index(); // success, failed
            $table->text('output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_workflow_logs');
        Schema::dropIfExists('platform_automation_workflows');
    }
};
