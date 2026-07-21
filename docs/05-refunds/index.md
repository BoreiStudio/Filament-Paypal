# Refunds

Issue full or partial refunds on captured PayPal payments.

## Issue a Refund

1. Go to **PayPal → Payments**
2. Click the eye icon on a COMPLETED payment
3. Click **Issue Refund**
4. Enter the amount and an optional note
5. Submit

The refund is sent to PayPal via the Payments API and stored locally. The payment status updates to `REFUNDED` or `PARTIALLY_REFUNDED` depending on the amount.

## View Refunds

1. Go to **PayPal → Refunds**
2. See all issued refunds with status, amount, and payment reference

## Refund Statuses

| Status | Description |
|---|---|
| COMPLETED | Refund successfully processed |
| PENDING | Refund pending processing |
| FAILED | Refund failed |
| CANCELLED | Refund cancelled |

## API

The refund system uses `CreateRefundAction` which calls PayPal's `captures/refund` endpoint.
