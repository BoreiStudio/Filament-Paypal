# Application Credentials

## Getting Started

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/applications)
2. Click **Create App**
3. Choose **My Apps & Credentials** under REST API apps
4. Select **Create App**
5. Name your app and click **Create App**

## Credentials

After creating the app, you'll see:

- **Client ID** — Public identifier for your app
- **Secret** — Confidential key (keep this safe!)

Copy these values into **Settings → PayPal → PayPal Settings** in the Filament panel.

## Sandbox vs Production

| Mode | API URL | Use Case |
|---|---|---|
| Sandbox | `https://api-m.sandbox.paypal.com` | Testing with test accounts |
| Production | `https://api-m.paypal.com` | Live payments |

Toggle **Sandbox Mode** in the settings page to switch between environments.

### Sandbox Test Accounts

Create test accounts in the [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/accounts):

- **Buyer account**: for testing payments
- **Seller account**: to receive payments (your app's credentials)

## Webhook ID

To enable webhook signature verification, set up a webhook in your PayPal Developer application and copy the **Webhook ID** into the settings page.

This allows the plugin to verify that incoming webhook notifications genuinely came from PayPal.
