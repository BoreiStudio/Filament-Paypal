<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->foreignId('payment_id')->constrained('paypal_payments')->cascadeOnDelete();
            $table->string('paypal_refund_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->string('status_detail')->nullable();
            $table->string('invoice_id')->nullable();
            $table->text('note_to_payer')->nullable();
            $table->json('paypal_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_refunds');
    }
};
