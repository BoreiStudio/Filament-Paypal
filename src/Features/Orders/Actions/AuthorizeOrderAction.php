<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Actions;

use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class AuthorizeOrderAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Order $order): Order
    {
        $ordersController = $this->client->getOrdersController();
        $response = $ordersController->authorizeOrder([
            'id' => $order->paypal_order_id,
            'prefer' => 'return=representation',
        ]);
        $result = $response->getResult();
        $data = json_decode(json_encode($result), true);

        $order->update([
            'status' => ! empty($data['status']) ? $data['status'] : $order->status->value,
            'intent' => 'AUTHORIZE',
            'paypal_response' => $data,
            'approved_at' => now(),
        ]);

        return $order->fresh();
    }
}
