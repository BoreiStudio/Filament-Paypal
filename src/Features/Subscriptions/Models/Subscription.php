<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Models;

use BoreiStudio\FilamentPayPal\Features\Subscriptions\Enums\SubscriptionStatus;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property int|null $plan_id
 * @property string $paypal_subscription_id
 * @property SubscriptionStatus $status
 * @property string|null $subscriber_email
 * @property string|null $subscriber_name
 * @property \DateTime|null $start_time
 * @property \DateTime|null $next_billing_time
 * @property \DateTime|null $last_payment_time
 * @property float|null $last_payment_amount
 * @property int $failed_payments_count
 * @property array|null $paypal_response
 * @property string|null $approval_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Subscription extends Model
{
    protected $table = 'paypal_subscriptions';

    protected $fillable = [
        'account_id',
        'plan_id',
        'paypal_subscription_id',
        'status',
        'subscriber_email',
        'subscriber_name',
        'start_time',
        'next_billing_time',
        'last_payment_time',
        'last_payment_amount',
        'failed_payments_count',
        'paypal_response',
        'approval_url',
    ];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'last_payment_amount' => 'decimal:2',
        'failed_payments_count' => 'integer',
        'start_time' => 'datetime',
        'next_billing_time' => 'datetime',
        'last_payment_time' => 'datetime',
        'paypal_response' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
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

    public function isApprovalPending(): bool
    {
        return $this->status === SubscriptionStatus::ApprovalPending;
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active;
    }

    public function isSuspended(): bool
    {
        return $this->status === SubscriptionStatus::Suspended;
    }

    public function isCancelled(): bool
    {
        return $this->status === SubscriptionStatus::Cancelled;
    }
}
