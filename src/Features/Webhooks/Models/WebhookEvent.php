<?php

namespace BoreiStudio\FilamentPayPal\Features\Webhooks\Models;

use BoreiStudio\FilamentPayPal\Features\Webhooks\Enums\WebhookEventStatus;
use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $account_id
 * @property string|null $paypal_event_id
 * @property string $event_type
 * @property string|null $resource_type
 * @property string|null $resource_id
 * @property string|null $summary
 * @property bool|null $signature_valid
 * @property \BoreiStudio\FilamentPayPal\Features\Webhooks\Enums\WebhookEventStatus $status
 * @property string|null $error
 * @property array $raw_payload
 * @property \DateTime|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class WebhookEvent extends Model
{
    protected $table = 'paypal_webhook_events';

    protected $fillable = [
        'account_id',
        'paypal_event_id',
        'event_type',
        'resource_type',
        'resource_id',
        'summary',
        'signature_valid',
        'status',
        'error',
        'raw_payload',
        'processed_at',
    ];

    protected $casts = [
        'status' => WebhookEventStatus::class,
        'signature_valid' => 'boolean',
        'raw_payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function isPending(): bool
    {
        return $this->status === WebhookEventStatus::Pending;
    }

    public function isProcessed(): bool
    {
        return $this->status === WebhookEventStatus::Processed;
    }

    public function isFailed(): bool
    {
        return $this->status === WebhookEventStatus::Failed;
    }
}
