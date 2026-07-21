<?php

namespace BoreiStudio\FilamentPayPal\Features\Payouts\Actions;

use BoreiStudio\FilamentPayPal\Features\Payouts\Models\Payout;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class SyncPayoutFromApiAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Payout $payout): Payout
    {
        if (! $payout->paypal_batch_id) {
            return $payout;
        }

        $result = $this->client->getPayoutBatch($payout->paypal_batch_id);

        $payout->update([
            'status' => $result['batch_header']['batch_status'] ?? $payout->status,
            'paypal_response' => $result,
            'completed_at' => in_array($result['batch_header']['batch_status'] ?? '', ['SUCCESS', 'DENIED'])
                ? now()
                : $payout->completed_at,
        ]);

        return $payout->fresh();
    }
}
