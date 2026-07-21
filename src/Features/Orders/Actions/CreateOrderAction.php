<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Actions;

use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;

class CreateOrderAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(array $data): Order
    {
        $items = $data['items'] ?? [];
        $totalAmount = '0.00';
        $currencyCode = $data['currency_code'] ?? 'USD';

        if (! empty($items)) {
            $total = array_sum(array_column($items, 'amount'));
            $totalAmount = number_format($total, 2, '.', '');
        } elseif (isset($data['amount'])) {
            $totalAmount = number_format((float) $data['amount'], 2, '.', '');
        }

        $amount = AmountWithBreakdownBuilder::init($currencyCode, $totalAmount)->build();

        $purchaseUnit = PurchaseUnitRequestBuilder::init($amount)
            ->description($data['description'] ?? null)
            ->customId($data['external_reference'] ?? null)
            ->invoiceId($data['invoice_id'] ?? null)
            ->build();

        $orderRequest = OrderRequestBuilder::init(
            CheckoutPaymentIntent::CAPTURE,
            [$purchaseUnit]
        )->build();

        $ordersController = $this->client->getOrdersController();
        $response = $ordersController->createOrder([
            'body' => $orderRequest,
            'prefer' => 'return=representation',
        ]);
        $result = $response->getResult();
        $resultData = json_decode(json_encode($result), true);

        $approvalUrl = null;
        $links = $resultData['links'] ?? [];
        foreach ($links as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                $approvalUrl = $link['href'] ?? null;
                break;
            }
        }

        $order = Order::create([
            'account_id' => $data['account_id'],
            'paypal_order_id' => $resultData['id'] ?? '',
            'intent' => 'CAPTURE',
            'status' => $resultData['status'] ?? '',
            'currency_code' => $currencyCode,
            'amount' => $totalAmount,
            'description' => $data['description'] ?? null,
            'external_reference' => $data['external_reference'] ?? null,
            'source' => $data['source'] ?? 'filament',
            'approval_url' => $approvalUrl,
            'purchase_units' => $resultData['purchase_units'] ?? [],
            'paypal_response' => $resultData,
        ]);

        return $order;
    }
}
