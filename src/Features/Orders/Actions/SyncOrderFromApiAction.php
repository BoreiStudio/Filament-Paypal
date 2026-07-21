<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Actions;

use BoreiStudio\FilamentPayPal\Features\Orders\Enums\OrderStatus;
use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class SyncOrderFromApiAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(string $paypalOrderId, ?int $accountId = null): Order
    {
        $ordersController = $this->client->getOrdersController();
        $response = $ordersController->getOrder([
            'id' => $paypalOrderId,
        ]);
        $result = $response->getResult();
        $data = json_decode(json_encode($result), true);

        $payer = $data['payer'] ?? [];
        $purchaseUnits = $data['purchase_units'] ?? [];
        $amount = $purchaseUnits[0]['amount'] ?? [];

        $order = Order::updateOrCreate(
            ['paypal_order_id' => $paypalOrderId],
            [
                'account_id' => $accountId,
                'status' => ! empty($data['status'])
                    ? (OrderStatus::tryFrom($data['status'])?->value ?? 'CREATED')
                    : 'CREATED',
                'payer_email' => $payer['email_address'] ?? null,
                'payer_id' => $payer['payer_id'] ?? null,
                'payer_name' => isset($payer['name'])
                    ? ($payer['name']['given_name'] ?? '') . ' ' . ($payer['name']['surname'] ?? '')
                    : null,
                'amount' => $amount['value'] ?? '0',
                'currency_code' => $amount['currency_code'] ?? 'USD',
                'purchase_units' => $purchaseUnits,
                'paypal_response' => $data,
            ]
        );

        return $order;
    }
}
