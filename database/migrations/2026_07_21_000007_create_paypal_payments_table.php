<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('paypal_orders')->nullOnDelete();
            $table->string('paypal_capture_id')->unique();
            $table->string('status');
            $table->string('status_detail')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency_code')->default('USD');
            $table->string('payment_method')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('external_reference')->nullable();
            $table->string('source')->nullable();
            $table->json('paypal_response')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_payments');
    }
};
