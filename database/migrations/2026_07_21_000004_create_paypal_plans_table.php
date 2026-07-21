<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('paypal_products')->cascadeOnDelete();
            $table->string('paypal_plan_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('CREATED');
            $table->string('currency_code')->default('USD');
            $table->decimal('amount', 10, 2);
            $table->string('billing_frequency')->default('MONTH');
            $table->integer('billing_cycles');
            $table->string('payment_preference')->nullable();
            $table->json('paypal_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_plans');
    }
};
