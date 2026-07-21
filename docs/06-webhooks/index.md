# Webhooks

Receive and process PayPal event notifications automatically.

## Setup

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/applications)
2. Select your app
3. Go to **Webhooks**
4. Add a webhook endpoint:

```
https://yourdomain.com/paypal/webhooks
```

5. Subscribe to the following events:
   - `CHECKOUT.ORDER.APPROVED`
   - `CHECKOUT.ORDER.COMPLETED`
   - `PAYMENT.CAPTURE.COMPLETED`
   - `PAYMENT.CAPTURE.REFUNDED`
   - `PAYMENT.CAPTURE.DENIED`
6. Copy the **Webhook ID** and save it in **Settings → PayPal → PayPal Settings**

## How It Works

1. PayPal sends a POST request to `/paypal/webhooks`
2. The plugin validates the signature (if Webhook ID is configured)
3. Creates a `WebhookEvent` record with the raw payload
4. Dispatches a queued job to process the event:
   - `CHECKOUT.ORDER.*` → `ProcessOrderWebhookJob` → syncs the order
   - `PAYMENT.CAPTURE.*` → `ProcessPaymentWebhookJob` → syncs the payment

## View Webhook Events

1. Go to **PayPal → Webhook Events**
2. See all received events with type, status, and processing result
3. Click the eye icon to see full event details and raw payload

## Testing

Use the Artisan command to simulate a webhook:

```bash
php artisan paypal:webhook-simulate --event-type=PAYMENT.CAPTURE.COMPLETED --resource-id=CAP-12345
```
