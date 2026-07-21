<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->string('paypal_event_id')->nullable()->unique();
            $table->string('event_type');
            $table->string('resource_type')->nullable();
            $table->string('resource_id')->nullable();
            $table->string('summary')->nullable();
            $table->boolean('signature_valid')->nullable();
            $table->string('status')->default('pending');
            $table->text('error')->nullable();
            $table->json('raw_payload');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_webhook_events');
    }
};
