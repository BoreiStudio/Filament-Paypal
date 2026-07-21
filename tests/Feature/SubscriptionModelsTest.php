<?php

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Plan;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Product;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;

it('can create a product', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $product = Product::create([
        'account_id' => $account->id,
        'paypal_product_id' => 'PROD-001',
        'name' => 'Premium Plan',
        'type' => 'SERVICE',
        'status' => 'CREATED',
    ]);

    expect($product->name)->toBe('Premium Plan')
        ->and($product->plans)->toBeEmpty();
});

it('can create a plan linked to product', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $product = Product::create([
        'account_id' => $account->id,
        'paypal_product_id' => 'PROD-002',
        'name' => 'Pro',
        'type' => 'SERVICE',
    ]);

    $plan = Plan::create([
        'account_id' => $account->id,
        'product_id' => $product->id,
        'paypal_plan_id' => 'PLAN-001',
        'name' => 'Monthly Pro',
        'amount' => 29.99,
        'currency_code' => 'USD',
        'billing_frequency' => 'MONTH',
        'billing_cycles' => 12,
        'status' => 'ACTIVE',
    ]);

    expect($plan->product->id)->toBe($product->id)
        ->and((float) $plan->amount)->toBe(29.99);
});

it('can create a subscription linked to plan', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $product = Product::create([
        'account_id' => $account->id,
        'paypal_product_id' => 'PROD-003',
        'name' => 'Basic',
        'type' => 'SERVICE',
    ]);

    $plan = Plan::create([
        'account_id' => $account->id,
        'product_id' => $product->id,
        'paypal_plan_id' => 'PLAN-002',
        'name' => 'Monthly Basic',
        'amount' => 9.99,
        'currency_code' => 'USD',
        'billing_frequency' => 'MONTH',
        'billing_cycles' => 0,
        'status' => 'ACTIVE',
    ]);

    $subscription = Subscription::create([
        'account_id' => $account->id,
        'plan_id' => $plan->id,
        'paypal_subscription_id' => 'SUB-001',
        'status' => 'ACTIVE',
        'subscriber_email' => 'sub@example.com',
    ]);

    expect($subscription->plan->id)->toBe($plan->id)
        ->and($subscription->isActive())->toBeTrue()
        ->and($subscription->isSuspended())->toBeFalse();
});

it('subscription status colors', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'client',
        'production_client_secret' => 'secret',
        'status' => 'connected',
    ]);

    $product = Product::create([
        'account_id' => $account->id,
        'paypal_product_id' => 'PROD-004',
        'name' => 'Test',
        'type' => 'SERVICE',
    ]);

    $plan = Plan::create([
        'account_id' => $account->id,
        'product_id' => $product->id,
        'paypal_plan_id' => 'PLAN-003',
        'name' => 'Test Plan',
        'amount' => 5.00,
        'currency_code' => 'USD',
        'billing_frequency' => 'MONTH',
        'billing_cycles' => 1,
        'status' => 'ACTIVE',
    ]);

    $colors = [
        'ACTIVE' => 'success',
        'SUSPENDED' => 'warning',
        'CANCELLED' => 'danger',
        'APPROVAL_PENDING' => 'warning',
    ];

    foreach ($colors as $status => $expected) {
        $sub = Subscription::create([
            'account_id' => $account->id,
            'plan_id' => $plan->id,
            'paypal_subscription_id' => 'SUB-'.$status,
            'status' => $status,
        ]);

        expect($sub->status->getColor())->toBe($expected);
    }
});
