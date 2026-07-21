# Subscriptions

Recurring billing using PayPal's Subscriptions API v1 and Catalog API.

## Architecture

Subscriptions follow a three-level hierarchy:

```
Product → Plan → Subscription
```

| Level | Description | Example |
|---|---|---|
| **Product** | A catalog item (service or good) | "Premium Plan" |
| **Plan** | Billing rules for a product | "$29.99/month" |
| **Subscription** | A subscriber's active/recurring billing | "John's subscription" |

## Create a Product

1. Go to **PayPal → Products**
2. Click **Create Product**
3. Enter name, description, and type (Service/Physical/Digital)
4. The product is created in PayPal's catalog and stored locally

## Create a Plan

1. Go to **PayPal → Plans**
2. Click **Create Plan**
3. Select a product, enter name, amount, billing frequency, and cycles
4. The plan is created in PayPal and linked to the product

## Create a Subscription

1. Go to **PayPal → Subscriptions**
2. Click **Create Subscription**
3. Select a plan and enter the subscriber's email
4. The subscription is created in PayPal and the subscriber receives an approval link

## Manage Subscriptions

- **Cancel** — Active subscriptions can be cancelled from the table row
- Statuses: APPROVAL_PENDING, APPROVED, ACTIVE, SUSPENDED, CANCELLED, EXPIRED
