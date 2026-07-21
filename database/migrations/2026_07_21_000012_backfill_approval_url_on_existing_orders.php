<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('paypal_orders')
            ->whereNull('approval_url')
            ->whereNotNull('paypal_response')
            ->eachById(function ($order) {
                $response = json_decode($order->paypal_response, true);
                if ($response && isset($response['links'])) {
                    foreach ($response['links'] as $link) {
                        if (($link['rel'] ?? '') === 'approve') {
                            DB::table('paypal_orders')
                                ->where('id', $order->id)
                                ->update(['approval_url' => $link['href'] ?? null]);
                            break;
                        }
                    }
                }
            });
    }

    public function down(): void {}
};
