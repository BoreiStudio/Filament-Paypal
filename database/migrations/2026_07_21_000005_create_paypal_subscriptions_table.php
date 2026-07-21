<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('paypal_plans')->cascadeOnDelete();
            $table->string('paypal_subscription_id')->unique();
            $table->string('status');
            $table->string('subscriber_email')->nullable();
            $table->string('subscriber_name')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('next_billing_time')->nullable();
            $table->timestamp('last_payment_time')->nullable();
            $table->decimal('last_payment_amount', 10, 2)->nullable();
            $table->integer('failed_payments_count')->default(0);
            $table->json('paypal_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_subscriptions');
    }
};
