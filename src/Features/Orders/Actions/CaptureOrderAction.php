<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Actions;

use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class CaptureOrderAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Order $order): Order
    {
        $ordersController = $this->client->getOrdersController();
        $response = $ordersController->captureOrder([
            'id' => $order->paypal_order_id,
            'prefer' => 'return=representation',
        ]);
        $result = $response->getResult();
        $data = json_decode(json_encode($result), true);

        $order->update([
            'status' => ! empty($data['status']) ? $data['status'] : $order->status->value,
            'paypal_response' => $data,
            'captured_at' => now(),
        ]);

        $purchaseUnits = $data['purchase_units'] ?? [];
        if (! empty($purchaseUnits)) {
            $payments = $purchaseUnits[0]['payments'] ?? [];
            $captures = $payments['captures'] ?? [];

            foreach ($captures as $capture) {
                $amount = $capture['amount'] ?? [];
                Payment::create([
                    'account_id' => $order->account_id,
                    'order_id' => $order->id,
                    'paypal_capture_id' => $capture['id'] ?? '',
                    'status' => $capture['status'] ?? '',
                    'status_detail' => $capture['status'] ?? '',
                    'amount' => $amount['value'] ?? '0',
                    'currency_code' => $amount['currency_code'] ?? 'USD',
                    'payer_email' => $order->payer_email,
                    'external_reference' => $order->external_reference,
                    'source' => $order->source,
                    'paypal_response' => $capture,
                    'captured_at' => now(),
                ]);
            }
        }

        return $order->fresh();
    }
}
