<?php

use BoreiStudio\FilamentPayPal\Features\Payouts\Models\Payout;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;

it('can create a payout', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $payout = Payout::create([
        'account_id' => $account->id,
        'paypal_batch_id' => 'BATCH-001',
        'status' => 'PENDING',
        'amount' => 200.00,
        'currency_code' => 'USD',
        'recipient_type' => 'EMAIL',
        'recipient_value' => 'payee@example.com',
    ]);

    expect($payout->isPending())->toBeTrue()
        ->and($payout->isCompleted())->toBeFalse()
        ->and($payout->status->getColor())->toBe('warning');
});

it('payout status colors', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $colors = [
        'PENDING' => 'warning',
        'SUCCESS' => 'success',
        'DENIED' => 'danger',
        'CANCELLED' => 'gray',
        'FAILED' => 'danger',
    ];

    foreach ($colors as $status => $expected) {
        $payout = Payout::create([
            'account_id' => $account->id,
            'paypal_batch_id' => 'BATCH-' . $status,
            'status' => $status,
            'amount' => 10.00,
            'currency_code' => 'USD',
            'recipient_type' => 'EMAIL',
            'recipient_value' => 'test@example.com',
        ]);

        expect($payout->status->getColor())->toBe($expected);
    }
});
