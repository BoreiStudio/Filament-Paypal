<?php

namespace BoreiStudio\FilamentPayPal\Settings;

use Spatie\LaravelSettings\Settings;

class PayPalCredentialsSettings extends Settings
{
    public string $production_client_id;

    public string $production_client_secret;

    public string $webhook_id;

    public bool $sandbox_mode = true;

    public static function group(): string
    {
        return 'paypal-app';
    }
}
