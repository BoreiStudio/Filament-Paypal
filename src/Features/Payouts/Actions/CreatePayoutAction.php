<?php

namespace BoreiStudio\FilamentPayPal\Features\Payouts\Actions;

use BoreiStudio\FilamentPayPal\Features\Payouts\Models\Payout;
use BoreiStudio\FilamentPayPal\Support\Http\PayPalClient;
use Illuminate\Support\Str;

class CreatePayoutAction
{
    public function __construct(
        private readonly PayPalClient $client,
    ) {}

    public function execute(array $data): Payout
    {
        $payoutData = [
            'sender_batch_header' => [
                'sender_batch_id' => $data['sender_batch_id'] ?? 'BATCH-'.Str::random(20),
                'email_subject' => $data['email_subject'] ?? 'You have a payout!',
            ],
            'items' => [
                [
                    'recipient_type' => $data['recipient_type'] ?? 'EMAIL',
                    'receiver' => $data['recipient_value'],
                    'note' => $data['note'] ?? 'Payout from '.config('app.name'),
                    'sender_item_id' => $data['sender_item_id'] ?? 'ITEM-'.Str::random(10),
                    'amount' => [
                        'currency' => $data['currency_code'] ?? 'USD',
                        'value' => number_format((float) $data['amount'], 2, '.', ''),
                    ],
                ],
            ],
        ];

        $result = $this->client->createPayout($payoutData);

        return Payout::create([
            'account_id' => $data['account_id'],
            'paypal_batch_id' => $result['batch_header']['payout_batch_id'] ?? null,
            'status' => $result['batch_header']['batch_status'] ?? 'PENDING',
            'amount' => $data['amount'],
            'currency_code' => $data['currency_code'] ?? 'USD',
            'recipient_type' => $data['recipient_type'] ?? 'EMAIL',
            'recipient_value' => $data['recipient_value'],
            'recipient_name' => $data['recipient_name'] ?? null,
            'sender_item_id' => $data['sender_item_id'] ?? null,
            'note' => $data['note'] ?? null,
            'email_subject' => $data['email_subject'] ?? null,
            'items' => $payoutData['items'],
            'paypal_response' => $result,
        ]);
    }
}
