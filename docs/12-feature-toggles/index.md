# Feature Toggles

Each feature can be independently enabled or disabled when registering the plugin.

## Usage

```php
use BoreiStudio\FilamentPayPal\PayPalPlugin;

PayPalPlugin::make()
    ->orders(true)             // Orders & Payments (default: true)
    ->refunds(true)            // Refunds (default: true)
    ->subscriptions(false)     // Subscriptions (default: false)
    ->payouts(false)           // Payouts (default: false)
    ->webhooks(true)           // Webhook events (default: true)
    ->dashboard(true)          // Dashboard widget (default: true)
    ->documentation(true)      // In-panel documentation (default: true)
    ->tenancy(false)           // Multi-tenant mode (default: false)
    ->settingsRole('super_admin') // Restrict Settings/Docs to a Shield role (default: null)
    ->navigationGroup('PayPal'); // Custom navigation group
```

## Feature Defaults

| Feature | Default | Description |
|---|---|---|
| orders | `true` | Orders & Payments management |
| refunds | `true` | Refund management |
| subscriptions | `false` | Products, plans, recurring billing |
| payouts | `false` | Mass payout sending |
| webhooks | `true` | Webhook event viewer |
| dashboard | `true` | Stats widget |
| documentation | `true` | Built-in documentation viewer |
| tenancy | `false` | Multi-tenant mode |
| settingsRole | `null` | Restrict Settings/Docs access by Shield role |

## Custom navigation group

```php
PayPalPlugin::make()
    ->navigationGroup('E-commerce')
    ->orders();
```

Only the main cluster (Orders, Payments, Refunds, etc.) is affected. The configuration cluster (Settings, Documentation) stays in "Settings".

## Restrict Settings access

```php
PayPalPlugin::make()
    ->settingsRole('super_admin')   // solo super_admin puede ver Settings + Docs
    ->orders();
```

If a role is set, only users with that Shield role can access the Settings and Documentation pages. If not set (`null`), access falls back to `super_admin` or users with `viewAny` permission on `PaypalAccount`.

## Multi-tenant

```php
PayPalPlugin::make()
    ->tenancy(true)
    ->orders();
```
