<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Plan;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Subscription;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class CreateSubscriptionAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Plan $plan, array $data): Subscription
    {
        $http = $this->client->http();

        $payload = [
            'plan_id' => $plan->paypal_plan_id,
            'start_time' => $data['start_time'] ?? now()->addHour()->toIso8601String(),
            'subscriber' => [
                'name' => [
                    'given_name' => $data['given_name'] ?? 'Subscriber',
                    'surname' => $data['surname'] ?? '',
                ],
                'email_address' => $data['email_address'] ?? '',
            ],
            'application_context' => [
                'brand_name' => $data['brand_name'] ?? config('app.name'),
                'locale' => 'en-US',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action' => 'SUBSCRIBE_NOW',
                'payment_method' => [
                    'payer_selected' => 'PAYPAL',
                    'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                ],
                'return_url' => $data['return_url'] ?? url('/paypal/subscription/approval'),
                'cancel_url' => $data['cancel_url'] ?? url('/paypal/subscription/cancel'),
            ],
        ];

        $response = $http->post('/v1/billing/subscriptions', $payload);
        $response->throw();
        $result = $response->json();

        throw_if(empty($result['id']), new \RuntimeException(
            'PayPal API returned unexpected response: '.json_encode($result)
        ));

        $approvalUrl = null;
        foreach ($result['links'] ?? [] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approvalUrl = $link['href'] ?? null;
                break;
            }
        }

        return Subscription::create([
            'account_id' => $plan->account_id,
            'plan_id' => $plan->id,
            'paypal_subscription_id' => $result['id'],
            'status' => $result['status'] ?? 'APPROVAL_PENDING',
            'subscriber_email' => $data['email_address'] ?? null,
            'subscriber_name' => ($data['given_name'] ?? '').' '.($data['surname'] ?? ''),
            'start_time' => $data['start_time'] ?? null,
            'approval_url' => $approvalUrl,
            'paypal_response' => $result,
        ]);
    }
}
