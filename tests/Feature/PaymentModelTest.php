<?php

use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Features\Refunds\Models\Refund;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;

it('can create a payment', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $payment = Payment::create([
        'account_id' => $account->id,
        'paypal_capture_id' => 'CAP-12345',
        'status' => 'COMPLETED',
        'amount' => 75.00,
        'currency_code' => 'USD',
        'payer_email' => 'buyer@example.com',
    ]);

    expect($payment->isCompleted())->toBeTrue()
        ->and($payment->isRefunded())->toBeFalse()
        ->and($payment->status->getColor())->toBe('success');
});

it('payment can track refunds', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $payment = Payment::create([
        'account_id' => $account->id,
        'paypal_capture_id' => 'CAP-REFUND',
        'status' => 'COMPLETED',
        'amount' => 100.00,
        'currency_code' => 'USD',
    ]);

    Refund::create([
        'account_id' => $account->id,
        'payment_id' => $payment->id,
        'paypal_refund_id' => 'REF-001',
        'amount' => 30.00,
        'status' => 'COMPLETED',
    ]);

    expect($payment->getRefundedAmount())->toBe(30.00)
        ->and($payment->getAvailableForRefund())->toBe(70.00);
});

it('payment status colors', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $cases = [
        'COMPLETED' => 'success',
        'PENDING' => 'warning',
        'REFUNDED' => 'danger',
        'PARTIALLY_REFUNDED' => 'danger',
        'DECLINED' => 'danger',
        'FAILED' => 'danger',
    ];

    foreach ($cases as $status => $color) {
        $payment = Payment::create([
            'account_id' => $account->id,
            'paypal_capture_id' => 'CAP-' . $status,
            'status' => $status,
            'amount' => 10.00,
            'currency_code' => 'USD',
        ]);

        expect($payment->status->getColor())->toBe($color);
    }
});
