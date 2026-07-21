# Filament PayPal Plugin

A Filament v5 plugin that integrates PayPal payments into your Laravel application. Supports Orders (PayPal Checkout), Subscriptions, Webhooks, Refunds, Payouts, public checkout page, and in-panel documentation.

## Requirements

- PHP 8.3+
- Laravel 12+ / 13+
- Filament 5.x

## Installation

```bash
composer require boreistudio/filament-paypal
php artisan migrate
```

Register the plugin in your `PanelProvider`:

```php
use BoreiStudio\FilamentPayPal\PayPalPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(PayPalPlugin::make());
}
```

## Configuration

A **Settings → PayPal → PayPal Settings** page appears in the Filament panel to configure the API credentials (`Client ID`, `Client Secret`, Sandbox mode). All sensitive values are stored encrypted.

The plugin supports two modes:

- **Single-tenant**: one PayPal account for the whole app.
- **Multi-tenant**: each tenant configures their own PayPal credentials.

The mode is auto-detected from your panel's tenancy configuration.

## Features

| Feature | Description | API |
|---|---|---|
| **Orders** | Create, capture, authorize PayPal orders | Orders API v2 |
| **Payments** | View captured payments, track refunds | Payments API v2 |
| **Refunds** | Issue full and partial refunds | Payments API v2 |
| **Webhooks** | Event notifications with signature validation | Webhooks API |
| **Subscriptions** | Products, billing plans, recurring subscriptions | Catalog + Subscriptions API |
| **Payouts** | Send mass payments to email/phone/PayPal ID | Payouts API v1 |
| **Checkout** | Public payment page for customers | Orders API v2 |
| **Dashboard** | Stats widget with payment totals | — |

## Feature Toggles

```php
PayPalPlugin::make()
    ->orders(true)           // Orders & Payments (default: true)
    ->refunds(true)          // Refunds (default: true)
    ->subscriptions(false)   // Subscriptions (default: false)
    ->payouts(false)         // Payouts (default: false)
    ->webhooks(true)         // Webhook events (default: true)
    ->dashboard(true)        // Dashboard widget (default: true)
    ->documentation(true)    // In-panel documentation (default: true)
    ->tenancy(false)         // Multi-tenant mode (default: false)
    ->navigationGroup('PayPal'); // Navigation group name (default: null, uses cluster default)
```

## Documentation

Full documentation is available in the `docs/` directory and also accessible from the Filament panel at **Settings → PayPal → Documentation**.

## Security

- All credentials (`client_secret`) are stored encrypted in the database.
- Webhook notifications are validated via PayPal's verify-webhook-signature endpoint.
- Refund amounts are validated server-side before calling the PayPal API.
- No sensitive data is logged.
- See `SECURITY.md` for reporting vulnerabilities.

## Testing

```bash
./vendor/bin/pest
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).
