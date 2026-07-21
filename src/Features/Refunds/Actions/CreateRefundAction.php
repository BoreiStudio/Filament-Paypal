<?php

namespace BoreiStudio\FilamentPayPal\Features\Refunds\Actions;

use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Features\Refunds\Models\Refund;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\RefundRequestBuilder;

class CreateRefundAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(Payment $payment, array $data): Refund
    {
        $money = MoneyBuilder::init(
            $payment->currency_code,
            number_format((float) $data['amount'], 2, '.', '')
        )->build();

        $refundRequest = RefundRequestBuilder::init()
            ->amount($money)
            ->invoiceId($data['invoice_id'] ?? null)
            ->noteToPayer($data['note_to_payer'] ?? null)
            ->build();

        $paymentsController = $this->client->getPaymentsController();
        $response = $paymentsController->refundCapturedPayment([
            'captureId' => $payment->paypal_capture_id,
            'body' => $refundRequest,
            'prefer' => 'return=representation',
        ]);
        $result = $response->getResult();
        $responseData = json_decode(json_encode($result), true);
        $responseAmount = $responseData['amount'] ?? [];

        $refund = Refund::create([
            'account_id' => $payment->account_id,
            'payment_id' => $payment->id,
            'paypal_refund_id' => $responseData['id'] ?? '',
            'amount' => $responseAmount['value'] ?? $data['amount'],
            'status' => ! empty($responseData['status']) ? $responseData['status'] : 'COMPLETED',
            'status_detail' => ! empty($responseData['status']) ? $responseData['status'] : 'COMPLETED',
            'invoice_id' => $data['invoice_id'] ?? null,
            'note_to_payer' => $data['note_to_payer'] ?? null,
            'paypal_response' => $responseData,
        ]);

        $payment->refresh();
        $totalRefunded = $payment->refunds()
            ->where('status', 'COMPLETED')
            ->sum('amount');

        if ((float) $totalRefunded >= (float) $payment->amount) {
            $payment->update(['status' => 'REFUNDED']);
        } else {
            $payment->update(['status' => 'PARTIALLY_REFUNDED']);
        }

        return $refund;
    }
}
