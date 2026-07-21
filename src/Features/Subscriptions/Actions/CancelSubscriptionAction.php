<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class CancelSubscriptionAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Subscription $subscription, ?string $reason = null): Subscription
    {
        $http = $this->client->http();

        $payload = [];
        if ($reason) {
            $payload['reason'] = $reason;
        }

        $response = $http->post(
            "/v1/billing/subscriptions/{$subscription->paypal_subscription_id}/cancel",
            $payload
        );
        $response->throw();

        $subscription->update([
            'status' => 'CANCELLED',
        ]);

        return $subscription->fresh();
    }
}
