# Public Checkout

A public payment page that allows customers to pay without accessing the admin panel.

## URL

```
https://yourdomain.com/paypal/checkout
```

## Flow

1. Customer visits `/paypal/checkout`
2. Enters amount, currency, and description
3. Clicks **Pay with PayPal**
4. Redirected to PayPal to log in and approve
5. After approval, redirected back to a result page

## Return URLs

After the customer approves on PayPal, they are redirected to:

```
https://yourdomain.com/paypal/checkout/return?token=ORD-XXXXXXXXXX
```

The result page shows:
- **Success**: Order ID and amount
- **Failure**: Error message with try-again button

## Prerequisites

- A PayPal account must be configured in the admin settings
- The account must have **Return URLs** configured in your PayPal app settings
