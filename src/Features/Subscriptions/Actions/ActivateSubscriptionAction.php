<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class ActivateSubscriptionAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Subscription $subscription): Subscription
    {
        $http = $this->client->http();

        $response = $http->post("/v1/billing/subscriptions/{$subscription->paypal_subscription_id}/activate");
        $response->throw();

        $subscription->update([
            'status' => 'ACTIVE',
        ]);

        return $subscription->fresh();
    }
}
