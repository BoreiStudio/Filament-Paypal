# Orders

The Orders feature uses PayPal's **Orders API v2** to create, authorize, capture, and manage orders.

## Create an Order

1. Go to **PayPal → Orders**
2. Click **Create Order** (top toolbar)
3. Fill in the form:
   - **Account**: Select the connected PayPal account
   - **Currency** (USD, EUR, GBP)
   - **Description** (optional)
   - **External Reference** (optional, for your tracking)
   - **Items**: Add one or more items with name, amount, and quantity
4. Submit — the order is created in PayPal and saved locally

## Order Statuses

| Status | Description |
|---|---|
| CREATED | Order created, awaiting buyer approval |
| APPROVED | Buyer approved on PayPal, ready to capture |
| COMPLETED | Payment captured successfully |
| VOIDED | Order voided/cancelled |

## Order Actions

From the table row action group:

| Action | Description |
|---|---|
| **View** | Opens a slide-over with full order details |
| **Open Payment Link** | Opens the PayPal approval URL in a new tab |
| **Copy Payment Link** | Copies the approval URL to clipboard |
| **Capture** | Captures payment (only for APPROVED orders) |
| **Sync** | Refreshes order data from PayPal API |

## Payment Link

When an order is created, PayPal returns an approval URL. This is the link you send to the buyer so they can approve and complete the payment. The link is shown in the order detail slide-over and can be copied.

## API

The Orders feature uses the `CreateOrderAction`, `CaptureOrderAction`, `AuthorizeOrderAction`, and `SyncOrderFromApiAction` classes.
