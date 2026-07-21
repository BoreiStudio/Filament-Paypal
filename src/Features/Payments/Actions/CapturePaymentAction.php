<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Actions;

use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;

class CapturePaymentAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(string $paypalCaptureId): Payment
    {
        $paymentsController = $this->client->getPaymentsController();
        $response = $paymentsController->getCapturedPayment([
            'captureId' => $paypalCaptureId,
        ]);
        $result = $response->getResult();
        $data = json_decode(json_encode($result), true);
        $amount = $data['amount'] ?? [];

        $payment = Payment::updateOrCreate(
            ['paypal_capture_id' => $paypalCaptureId],
            [
                'status' => ! empty($data['status']) ? $data['status'] : 'COMPLETED',
                'status_detail' => ! empty($data['status']) ? $data['status'] : 'COMPLETED',
                'amount' => $amount['value'] ?? '0',
                'currency_code' => $amount['currency_code'] ?? 'USD',
                'paypal_response' => $data,
                'captured_at' => now(),
            ]
        );

        return $payment;
    }
}
