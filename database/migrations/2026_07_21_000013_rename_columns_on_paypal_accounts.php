<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paypal_accounts', function (Blueprint $table) {
            $table->renameColumn('client_id', 'production_client_id');
            $table->renameColumn('client_secret', 'production_client_secret');
        });

        Schema::table('paypal_accounts', function (Blueprint $table) {
            $table->text('sandbox_client_id')->nullable()->after('production_client_secret');
            $table->text('sandbox_client_secret')->nullable()->after('sandbox_client_id');
            $table->text('webhook_id')->nullable()->after('sandbox_client_secret');
        });
    }

    public function down(): void
    {
        Schema::table('paypal_accounts', function (Blueprint $table) {
            $table->renameColumn('production_client_id', 'client_id');
            $table->renameColumn('production_client_secret', 'client_secret');
            $table->dropColumn(['sandbox_client_id', 'sandbox_client_secret', 'webhook_id']);
        });
    }
};
