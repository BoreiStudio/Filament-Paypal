<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->string('paypal_order_id')->unique();
            $table->string('intent')->default('CAPTURE');
            $table->string('status');
            $table->string('payer_email')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('currency_code')->default('USD');
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('external_reference')->nullable();
            $table->string('source')->nullable();
            $table->json('purchase_units')->nullable();
            $table->json('paypal_response')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_orders');
    }
};
