# Dashboard Widget

The plugin includes a stats widget that shows payment totals for the current period.

## Stats Cards

| Card | Description |
|---|---|
| **Today** | Total amount of COMPLETED payments today |
| **This Week** | Total amount for the current week |
| **This Month** | Total amount for the current month |

The widget queries the local `paypal_payments` table, so it only reflects payments that have been captured and stored.

## Enable/Disable

```php
PayPalPlugin::make()
    ->dashboard(true)  // or false to hide the widget
```
