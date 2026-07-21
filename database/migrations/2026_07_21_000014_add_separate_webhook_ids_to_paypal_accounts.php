<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paypal_accounts', function (Blueprint $table) {
            $table->renameColumn('webhook_id', 'production_webhook_id');
            $table->text('sandbox_webhook_id')->nullable()->after('production_webhook_id');
        });
    }

    public function down(): void
    {
        Schema::table('paypal_accounts', function (Blueprint $table) {
            $table->renameColumn('production_webhook_id', 'webhook_id');
            $table->dropColumn('sandbox_webhook_id');
        });
    }
};
