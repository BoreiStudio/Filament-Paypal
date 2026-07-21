<?php

namespace BoreiStudio\FilamentPayPal\Support\Credentials;

use BoreiStudio\FilamentPayPal\Contracts\CredentialResolverInterface;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Filament\Facades\Filament;

class MultiTenantCredentialResolver implements CredentialResolverInterface
{
    public function resolve(): PayPalCredentialsDTO
    {
        $tenant = Filament::getTenant();

        $account = $tenant
            ? PaypalAccount::byTenant($tenant)->where('status', 'connected')->first()
            : PaypalAccount::whereNull('tenant_id')
                ->whereNull('tenant_type')
                ->where('status', 'connected')
                ->first();

        if (! $account) {
            throw new PayPalAccountNotConnectedException;
        }

        return new PayPalCredentialsDTO(
            clientId: $account->getActiveClientId(),
            clientSecret: $account->getActiveClientSecret(),
            sandbox: $account->sandbox_mode,
        );
    }

    public function applicationCredentials(): array
    {
        $tenant = Filament::getTenant();

        $account = $tenant
            ? PaypalAccount::byTenant($tenant)->where('status', 'connected')->first()
            : PaypalAccount::whereNull('tenant_id')
                ->whereNull('tenant_type')
                ->where('status', 'connected')
                ->first();

        if (! $account) {
            return [];
        }

        return [
            'production_client_id' => $account->production_client_id,
            'production_client_secret' => $account->production_client_secret,
            'production_webhook_id' => $account->production_webhook_id,
            'sandbox_client_id' => $account->sandbox_client_id,
            'sandbox_client_secret' => $account->sandbox_client_secret,
            'sandbox_webhook_id' => $account->sandbox_webhook_id,
            'sandbox_mode' => $account->sandbox_mode,
            'webhook_id' => $account->sandbox_mode
                ? $account->sandbox_webhook_id
                : $account->production_webhook_id,
        ];
    }
}
