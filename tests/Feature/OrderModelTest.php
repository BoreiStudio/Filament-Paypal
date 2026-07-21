<?php

use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;

it('can create an order', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $order = Order::create([
        'account_id' => $account->id,
        'paypal_order_id' => 'ORD-12345',
        'intent' => 'CAPTURE',
        'status' => 'CREATED',
        'currency_code' => 'USD',
        'amount' => 100.00,
        'description' => 'Test order',
    ]);

    expect($order->paypal_order_id)->toBe('ORD-12345')
        ->and($order->isCreated())->toBeTrue()
        ->and($order->isApproved())->toBeFalse()
        ->and($order->status->getColor())->toBe('info');
});

it('order belongs to account', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $order = Order::create([
        'account_id' => $account->id,
        'paypal_order_id' => 'ORD-67890',
        'intent' => 'CAPTURE',
        'status' => 'COMPLETED',
        'currency_code' => 'USD',
        'amount' => 50.00,
    ]);

    expect($order->account->id)->toBe($account->id)
        ->and($order->isCompleted())->toBeTrue()
        ->and($order->status->getColor())->toBe('success');
});

it('casts amounts as decimal', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $order = Order::create([
        'account_id' => $account->id,
        'paypal_order_id' => 'ORD-CAST',
        'intent' => 'CAPTURE',
        'status' => 'CREATED',
        'currency_code' => 'USD',
        'amount' => '99.99',
    ]);

    expect((float) $order->amount)->toBe(99.99);
});
