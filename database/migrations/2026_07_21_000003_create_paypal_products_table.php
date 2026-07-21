<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('paypal_accounts')->cascadeOnDelete();
            $table->string('paypal_product_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('SERVICE');
            $table->string('category')->nullable();
            $table->string('image_url')->nullable();
            $table->string('home_url')->nullable();
            $table->string('status')->default('CREATED');
            $table->json('paypal_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_products');
    }
};
