<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->string('paypal_batch_id')->nullable()->unique();
            $table->string('payout_type')->default('BATCH');
            $table->string('status');
            $table->decimal('amount', 10, 2);
            $table->string('currency_code')->default('USD');
            $table->string('recipient_type')->default('EMAIL');
            $table->string('recipient_value');
            $table->string('recipient_name')->nullable();
            $table->string('sender_item_id')->nullable();
            $table->text('note')->nullable();
            $table->string('email_subject')->nullable();
            $table->json('items')->nullable();
            $table->json('paypal_response')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_payouts');
    }
};
