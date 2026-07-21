<?php

use BoreiStudio\FilamentPayPal\Features\Webhooks\Models\WebhookEvent;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;

it('can create a webhook event', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $event = WebhookEvent::create([
        'account_id' => $account->id,
        'paypal_event_id' => 'WH-001',
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource_type' => 'capture',
        'resource_id' => 'CAP-001',
        'status' => 'pending',
        'raw_payload' => ['key' => 'value'],
    ]);

    expect($event->isPending())->toBeTrue()
        ->and($event->isProcessed())->toBeFalse()
        ->and($event->status->getColor())->toBe('warning');
});

it('webhook event belongs to account', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $event = WebhookEvent::create([
        'account_id' => $account->id,
        'event_type' => 'CHECKOUT.ORDER.APPROVED',
        'status' => 'processed',
        'processed_at' => now(),
        'raw_payload' => [],
    ]);

    expect($event->account->id)->toBe($account->id)
        ->and($event->isProcessed())->toBeTrue();
});

it('casts raw_payload to array', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $payload = ['event' => 'test', 'data' => ['id' => 123]];

    $event = WebhookEvent::create([
        'account_id' => $account->id,
        'event_type' => 'TEST',
        'status' => 'pending',
        'raw_payload' => $payload,
    ]);

    expect($event->raw_payload)->toBeArray()
        ->and($event->raw_payload['event'])->toBe('test');
});
