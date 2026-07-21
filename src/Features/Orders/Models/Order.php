<?php

namespace BoreiStudio\FilamentPayPal\Features\Orders\Models;

use BoreiStudio\FilamentPayPal\Features\Orders\Enums\OrderStatus;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string $paypal_order_id
 * @property string $intent
 * @property OrderStatus $status
 * @property string|null $payer_email
 * @property string|null $payer_id
 * @property string|null $payer_name
 * @property string $currency_code
 * @property float $amount
 * @property string|null $description
 * @property string|null $external_reference
 * @property string|null $source
 * @property array|null $purchase_units
 * @property array|null $paypal_response
 * @property string|null $approval_url
 * @property \DateTime|null $approved_at
 * @property \DateTime|null $captured_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Order extends Model
{
    protected $table = 'paypal_orders';

    protected $fillable = [
        'account_id',
        'paypal_order_id',
        'intent',
        'status',
        'payer_email',
        'payer_id',
        'payer_name',
        'currency_code',
        'amount',
        'description',
        'external_reference',
        'source',
        'purchase_units',
        'paypal_response',
        'approval_url',
        'approved_at',
        'captured_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'amount' => 'decimal:2',
        'purchase_units' => 'array',
        'paypal_response' => 'array',
        'approved_at' => 'datetime',
        'captured_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function getApprovalUrl(): ?string
    {
        if ($this->approval_url) {
            return $this->approval_url;
        }

        $response = $this->paypal_response;
        if (! $response || ! isset($response['links'])) {
            return null;
        }

        foreach ($response['links'] as $link) {
            if (($link['rel'] ?? '') === 'approve') {
                return $link['href'] ?? null;
            }
        }

        return null;
    }

    public function isCreated(): bool
    {
        return $this->status === OrderStatus::Created;
    }

    public function isApproved(): bool
    {
        return $this->status === OrderStatus::Approved;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed;
    }

    public function isVoided(): bool
    {
        return $this->status === OrderStatus::Voided;
    }
}
