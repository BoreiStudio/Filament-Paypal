<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paypal_orders', function (Blueprint $table) {
            $table->text('approval_url')->nullable()->after('paypal_response');
        });
    }

    public function down(): void
    {
        Schema::table('paypal_orders', function (Blueprint $table) {
            $table->dropColumn('approval_url');
        });
    }
};
