# API Reference

## Core Classes

| Class | Purpose |
|---|---|
| `PayPalPlugin` | Filament plugin registration |
| `PayPalServiceProvider` | Package service provider |
| `PayPalClient` | Wraps PayPal Server SDK + HTTP calls |

## Actions

| Action | Input | Output |
|---|---|---|
| `CreateOrderAction` | `array $data` | `Order` |
| `CaptureOrderAction` | `Order $order` | `Order` |
| `AuthorizeOrderAction` | `Order $order` | `Order` |
| `SyncOrderFromApiAction` | `string $paypalOrderId` | `Order` |
| `CapturePaymentAction` | `string $paypalCaptureId` | `Payment` |
| `SyncPaymentFromApiAction` | `string $paypalCaptureId` | `Payment` |
| `CreateRefundAction` | `Payment $payment, array $data` | `Refund` |
| `CreateProductAction` | `array $data` | `Product` |
| `CreatePlanAction` | `Product $product, array $data` | `Plan` |
| `CreateSubscriptionAction` | `Plan $plan, array $data` | `Subscription` |
| `ActivateSubscriptionAction` | `Subscription $subscription` | `Subscription` |
| `CancelSubscriptionAction` | `Subscription $subscription` | `Subscription` |
| `CreatePayoutAction` | `array $data` | `Payout` |
| `SyncPayoutFromApiAction` | `Payout $payout` | `Payout` |

## Models

| Model | Table | Key Fields |
|---|---|---|
| `PaypalAccount` | `paypal_accounts` | client_id, client_secret, sandbox_mode |
| `Order` | `paypal_orders` | paypal_order_id, status, amount, approval_url |
| `Payment` | `paypal_payments` | paypal_capture_id, status, amount |
| `Refund` | `paypal_refunds` | paypal_refund_id, amount, status |
| `WebhookEvent` | `paypal_webhook_events` | event_type, status, raw_payload |
| `Product` | `paypal_products` | paypal_product_id, name, type |
| `Plan` | `paypal_plans` | paypal_plan_id, name, amount |
| `Subscription` | `paypal_subscriptions` | paypal_subscription_id, status |
| `Payout` | `paypal_payouts` | paypal_batch_id, status, amount |

## Enums

All status enums implement `HasLabel`, `HasColor`, `HasIcon`:

- `OrderStatus`
- `PaymentStatus`
- `RefundStatus`
- `SubscriptionStatus`
- `PayoutStatus`
- `WebhookEventStatus`
