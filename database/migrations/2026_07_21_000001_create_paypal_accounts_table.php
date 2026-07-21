<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_accounts', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('tenant');
            $table->text('client_id');
            $table->text('client_secret');
            $table->boolean('sandbox_mode')->default(true);
            $table->string('status')->default('connected');
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_accounts');
    }
};
