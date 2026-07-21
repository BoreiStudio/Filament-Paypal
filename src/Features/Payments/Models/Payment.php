<?php

namespace BoreiStudio\FilamentPayPal\Features\Payments\Models;

use BoreiStudio\FilamentPayPal\Features\Orders\Models\Order;
use BoreiStudio\FilamentPayPal\Features\Payments\Enums\PaymentStatus;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $account_id
 * @property int|null $order_id
 * @property string $paypal_capture_id
 * @property \BoreiStudio\FilamentPayPal\Features\Payments\Enums\PaymentStatus $status
 * @property string|null $status_detail
 * @property float $amount
 * @property string $currency_code
 * @property string|null $payment_method
 * @property string|null $payer_email
 * @property string|null $payer_id
 * @property string|null $external_reference
 * @property string|null $source
 * @property array|null $paypal_response
 * @property \DateTime|null $captured_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Payment extends Model
{
    protected $table = 'paypal_payments';

    protected $fillable = [
        'account_id',
        'order_id',
        'paypal_capture_id',
        'status',
        'status_detail',
        'amount',
        'currency_code',
        'payment_method',
        'payer_email',
        'payer_id',
        'external_reference',
        'source',
        'paypal_response',
        'captured_at',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'paypal_response' => 'array',
        'captured_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(\BoreiStudio\FilamentPayPal\Features\Refunds\Models\Refund::class, 'payment_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === PaymentStatus::Completed;
    }

    public function isRefunded(): bool
    {
        return $this->status === PaymentStatus::Refunded;
    }

    public function isPartiallyRefunded(): bool
    {
        return $this->status === PaymentStatus::PartiallyRefunded;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::Pending;
    }

    public function getRefundedAmount(): float
    {
        return (float) $this->refunds()
            ->where('status', 'COMPLETED')
            ->sum('amount');
    }

    public function getAvailableForRefund(): float
    {
        return (float) $this->amount - $this->getRefundedAmount();
    }
}
