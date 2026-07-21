<?php

namespace BoreiStudio\FilamentPayPal\Features\Refunds\Models;

use BoreiStudio\FilamentPayPal\Features\Payments\Models\Payment;
use BoreiStudio\FilamentPayPal\Features\Refunds\Enums\RefundStatus;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property int $payment_id
 * @property string $paypal_refund_id
 * @property float $amount
 * @property \BoreiStudio\FilamentPayPal\Features\Refunds\Enums\RefundStatus $status
 * @property string|null $status_detail
 * @property string|null $invoice_id
 * @property string|null $note_to_payer
 * @property array|null $paypal_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Refund extends Model
{
    protected $table = 'paypal_refunds';

    protected $fillable = [
        'account_id',
        'payment_id',
        'paypal_refund_id',
        'amount',
        'status',
        'status_detail',
        'invoice_id',
        'note_to_payer',
        'paypal_response',
    ];

    protected $casts = [
        'status' => RefundStatus::class,
        'amount' => 'decimal:2',
        'paypal_response' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === RefundStatus::Completed;
    }

    public function isPending(): bool
    {
        return $this->status === RefundStatus::Pending;
    }
}
