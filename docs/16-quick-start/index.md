# Quick Start

Get from zero to a working payment in 5 minutes.

## 1. Install

```bash
composer require boreistudio/filament-paypal
php artisan migrate
```

## 2. Register the plugin

In your `PanelProvider`:

```php
use BoreiStudio\FilamentPayPal\PayPalPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(PayPalPlugin::make()
            ->subscriptions(true)
            ->payouts(true)
            ->tenancy(false));
}
```

## 3. Configure credentials

1. Go to **Settings → PayPal → PayPal Settings**
2. Enter your **Client ID** and **Client Secret** from [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/applications)
3. Toggle **Sandbox Mode** as needed
4. Click **Save**

## 4. Create an order

1. Go to **PayPal → Orders**
2. Click **Create Order**
3. Enter an amount (e.g., 10.00 USD)
4. Submit

## 5. Share the payment link

1. Click the eye icon on the order
2. Copy the **Payment Link** (approval URL)
3. Share it with a buyer to complete payment

---

**Next steps:**
- Set up [webhooks](06-webhooks/) for automatic status updates
- Create [subscriptions](07-subscriptions/) for recurring billing
- Enable the [public checkout](09-checkout/) page
- Explore [feature toggles](12-feature-toggles/) to customize the plugin
