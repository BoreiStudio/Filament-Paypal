# Testing

The plugin includes a test suite built with Pest and Orchestra Testbench.

## Running Tests

```bash
cd vendor/boreistudio/filament-paypal
vendor/bin/pest
```

## Test Structure

```
tests/
  Feature/
    PaypalAccountTest.php       # Account creation, scoping, credential resolvers
    OrderModelTest.php          # Order model, status helpers
    PaymentModelTest.php        # Payment model, refund tracking
    PayoutModelTest.php         # Payout model
    SubscriptionModelsTest.php  # Product, Plan, Subscription models
    WebhookEventTest.php        # Webhook event model
```

## Writing Tests

Tests use the `TestCase` class that sets up:
- SQLite in-memory database
- All plugin migrations
- Filament service providers
- Spatie Laravel Settings

```php
use function Pest\Livewire\livewire;

it('creates an order', function () {
    // Test your action or page here
});
```
