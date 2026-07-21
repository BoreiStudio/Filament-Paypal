<?php

use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use BoreiStudio\FilamentPayPal\Support\Credentials\PayPalAccountNotConnectedException;
use BoreiStudio\FilamentPayPal\Support\Credentials\SingleTenantCredentialResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

it('can create a paypal account', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'test-prod-id',
        'production_client_secret' => 'test-prod-secret',
        'sandbox_mode' => true,
        'status' => 'connected',
    ]);

    expect($account->production_client_id)->toBe('test-prod-id')
        ->and($account->isConnected())->toBeTrue();
});

it('can scope by tenant', function () {
    $tenant = new class extends Model
    {
        protected $table = 'tenants';
    };
    $tenant->setAttribute('id', 1);

    $account = PaypalAccount::create([
        'tenant_id' => 1,
        'tenant_type' => $tenant->getMorphClass(),
        'production_client_id' => 'tenant-id',
        'production_client_secret' => 'secret',
        'sandbox_mode' => true,
        'status' => 'connected',
    ]);

    $found = PaypalAccount::byTenant($tenant)->first();

    expect($found->id)->toBe($account->id);
});

it('single tenant resolver throws when no account exists', function () {
    $resolver = new SingleTenantCredentialResolver;

    $resolver->resolve();
})->throws(PayPalAccountNotConnectedException::class);

it('single tenant resolver returns sandbox credentials when sandbox_mode is true', function () {
    PaypalAccount::create([
        'production_client_id' => 'prod-id',
        'production_client_secret' => 'prod-secret',
        'sandbox_client_id' => 'my-sandbox-id',
        'sandbox_client_secret' => 'my-sandbox-secret',
        'sandbox_mode' => true,
        'status' => 'connected',
    ]);

    $resolver = new SingleTenantCredentialResolver;
    $credentials = $resolver->resolve();

    expect($credentials->getClientId())->toBe('my-sandbox-id')
        ->and($credentials->getClientSecret())->toBe('my-sandbox-secret')
        ->and($credentials->isSandbox())->toBeTrue();
});

it('single tenant resolver returns production credentials when sandbox_mode is false', function () {
    PaypalAccount::create([
        'production_client_id' => 'my-prod-id',
        'production_client_secret' => 'my-prod-secret',
        'sandbox_mode' => false,
        'status' => 'connected',
    ]);

    $resolver = new SingleTenantCredentialResolver;
    $credentials = $resolver->resolve();

    expect($credentials->getClientId())->toBe('my-prod-id')
        ->and($credentials->getClientSecret())->toBe('my-prod-secret')
        ->and($credentials->isSandbox())->toBeFalse();
});

it('single tenant resolver returns empty array when no account', function () {
    $resolver = new SingleTenantCredentialResolver;

    expect($resolver->applicationCredentials())->toBe([]);
});

it('paypal account has correct casts', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'id',
        'production_client_secret' => 'secret',
        'sandbox_mode' => true,
        'status' => 'connected',
        'last_verified_at' => now(),
    ]);

    expect($account->sandbox_mode)->toBeTrue()
        ->and($account->last_verified_at)->toBeInstanceOf(Carbon::class);
});

it('getActiveClientId returns sandbox credentials in sandbox mode', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'prod-id',
        'production_client_secret' => 'prod-secret',
        'sandbox_client_id' => 'sandbox-id',
        'sandbox_client_secret' => 'sandbox-secret',
        'sandbox_mode' => true,
        'status' => 'connected',
    ]);

    expect($account->getActiveClientId())->toBe('sandbox-id');
});

it('getActiveClientId returns production credentials in production mode', function () {
    $account = PaypalAccount::create([
        'production_client_id' => 'prod-id',
        'production_client_secret' => 'prod-secret',
        'sandbox_client_id' => 'sandbox-id',
        'sandbox_client_secret' => 'sandbox-secret',
        'sandbox_mode' => false,
        'status' => 'connected',
    ]);

    expect($account->getActiveClientId())->toBe('prod-id');
});
