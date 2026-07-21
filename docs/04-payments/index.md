# Payments

The Payments feature manages captured payments via PayPal's **Payments API v2**.

## View Payments

1. Go to **PayPal → Payments**
2. See all captured payments with status, amount, payer, and date

## Payment Statuses

| Status | Description |
|---|---|
| COMPLETED | Payment successfully captured |
| PENDING | Payment pending processing |
| REFUNDED | Fully refunded |
| PARTIALLY_REFUNDED | Partially refunded |
| DECLINED | Payment declined |
| FAILED | Payment failed |

## Payment Details

Click the eye icon on any payment to see a slide-over with:
- PayPal Capture ID
- Status and amount
- Payment method
- Payer email
- Order reference
- Refunded amount and available for refund

## Sync from API

Use the **Sync** button in the action group to refresh payment data from PayPal's API.

## Refunds

See the [Refunds](05-refunds) section for issuing refunds on captured payments.
