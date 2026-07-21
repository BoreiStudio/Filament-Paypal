# Installation & Configuration

## Install

```bash
composer require boreistudio/filament-paypal
php artisan migrate
```

## Register the plugin

In your `PanelProvider`:

```php
use BoreiStudio\FilamentPayPal\PayPalPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(PayPalPlugin::make());
}
```

## Configuration

### Mode: single-tenant vs multi-tenant

The plugin auto-detects the mode from your panel's tenancy configuration.

- **Single-tenant**: one PayPal account for the whole app.
- **Multi-tenant**: each tenant configures their own PayPal credentials.

You can override in `.env`:

```
PAYPAL_MODE=multi_tenant
```

### Sandbox mode

By default the plugin operates in sandbox mode. Configure in `.env`:

```
PAYPAL_SANDBOX=true
```

### API Credentials

After installation, go to **Settings → PayPal → PayPal Settings** in the Filament panel.

| Field | Description |
|---|---|
| Client ID | From your PayPal Developer application |
| Client Secret | From your PayPal Developer application |
| Sandbox Mode | Toggle between sandbox and production |

Get your credentials at [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/applications).

### Webhook URL

Configure in your PayPal Developer application:

```
https://yourdomain.com/paypal/webhooks
```

The webhook route is automatically excluded from CSRF protection.

### Public Checkout URL

The public checkout page is available at:

```
https://yourdomain.com/paypal/checkout
```
