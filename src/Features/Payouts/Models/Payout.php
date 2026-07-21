<?php

namespace BoreiStudio\FilamentPayPal\Features\Payouts\Models;

use BoreiStudio\FilamentPayPal\Features\Payouts\Enums\PayoutStatus;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string|null $paypal_batch_id
 * @property string $payout_type
 * @property PayoutStatus $status
 * @property float $amount
 * @property string $currency_code
 * @property string $recipient_type
 * @property string $recipient_value
 * @property string|null $recipient_name
 * @property string|null $sender_item_id
 * @property string|null $note
 * @property string|null $email_subject
 * @property array|null $items
 * @property array|null $paypal_response
 * @property \DateTime|null $completed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Payout extends Model
{
    protected $table = 'paypal_payouts';

    protected $fillable = [
        'account_id',
        'paypal_batch_id',
        'payout_type',
        'status',
        'amount',
        'currency_code',
        'recipient_type',
        'recipient_value',
        'recipient_name',
        'sender_item_id',
        'note',
        'email_subject',
        'items',
        'paypal_response',
        'completed_at',
    ];

    protected $casts = [
        'status' => PayoutStatus::class,
        'amount' => 'decimal:2',
        'items' => 'array',
        'paypal_response' => 'array',
        'completed_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function isPending(): bool
    {
        return $this->status === PayoutStatus::Pending;
    }

    public function isCompleted(): bool
    {
        return $this->status === PayoutStatus::Success;
    }
}
