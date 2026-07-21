# Payouts

Send mass payments to multiple recipients via PayPal's Payouts API v1.

## Create a Payout

1. Go to **PayPal → Payouts**
2. Click **Create Payout**
3. Fill in:
   - **Account**: Select the connected PayPal account
   - **Recipient Type**: EMAIL, PHONE, or PayPal ID
   - **Recipient Value**: The email/phone/ID of the receiver
   - **Amount**: The amount to send
   - **Currency**: USD, EUR, or GBP
   - **Note** (optional)
   - **Email Subject** (optional)
4. Submit

The payout is sent to PayPal and a batch record is created locally.

## Payout Statuses

| Status | Description |
|---|---|
| PENDING | Payout pending processing |
| SUCCESS | Payout sent successfully |
| DENIED | Payout denied |
| CANCELLED | Payout cancelled |
| FAILED | Payout failed |

## Limitations

- PayPal sandbox requires the recipient to be a valid sandbox test user
- Minimum and maximum amounts vary by currency and country
- Recipient must have a verified PayPal account
