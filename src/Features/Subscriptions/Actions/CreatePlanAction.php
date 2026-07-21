<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Actions;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Plan;
use BoreiStudio\FilamentPayPal\Features\Subscriptions\Models\Product;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class CreatePlanAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Product $product, array $data): Plan
    {
        $http = $this->client->http();

        $billingCycles = [
            [
                'frequency' => [
                    'interval_unit' => $data['billing_frequency'] ?? 'MONTH',
                    'interval_count' => 1,
                ],
                'tenure_type' => 'REGULAR',
                'sequence' => 1,
                'total_cycles' => $data['billing_cycles'] ?? 12,
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => number_format((float) $data['amount'], 2, '.', ''),
                        'currency_code' => $data['currency_code'] ?? 'USD',
                    ],
                ],
            ],
        ];

        $response = $http->post('/v1/billing/plans', [
            'product_id' => $product->paypal_product_id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status' => 'ACTIVE',
            'billing_cycles' => $billingCycles,
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => '0.00',
                    'currency_code' => $data['currency_code'] ?? 'USD',
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3,
            ],
        ]);

        $response->throw();
        $result = $response->json();

        throw_if(empty($result['id']), new \RuntimeException(
            'PayPal API returned unexpected response: '.json_encode($result)
        ));

        return Plan::create([
            'account_id' => $product->account_id,
            'product_id' => $product->id,
            'paypal_plan_id' => $result['id'],
            'name' => $result['name'] ?? $data['name'],
            'description' => $data['description'] ?? null,
            'status' => $result['status'] ?? 'ACTIVE',
            'currency_code' => $data['currency_code'] ?? 'USD',
            'amount' => $data['amount'],
            'billing_frequency' => $data['billing_frequency'] ?? 'MONTH',
            'billing_cycles' => $data['billing_cycles'] ?? 12,
            'paypal_response' => $result,
        ]);
    }
}
