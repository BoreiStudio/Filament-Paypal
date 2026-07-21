# Multi-Tenant Support

The plugin supports both single-tenant and multi-tenant configurations.

## Single-Tenant Mode (Default)

One PayPal account for the entire application. Configure credentials in **Settings → PayPal**.

## Multi-Tenant Mode

Each tenant configures their own PayPal credentials scoped to their tenant.

### Enable via Plugin Toggle

```php
PayPalPlugin::make()
    ->tenancy(true)
    // ... other features
```

Also set the tenant model in `config/paypal.php`:

```php
'tenant_model' => App\Models\Team::class,
```

### How It Works

The `CredentialResolverInterface` is bound to either:
- `SingleTenantCredentialResolver` — looks for accounts without tenant scope
- `MultiTenantCredentialResolver` — scopes to the current Filament tenant

When `->tenancy(true)` is set, the plugin automatically switches to multi-tenant mode. Each tenant's credentials are stored with their `tenant_id` and `tenant_type` in the `paypal_accounts` table.

### Alternative (via .env)

You can also control the mode via environment variable:

```
PAYPAL_MODE=multi_tenant
```
