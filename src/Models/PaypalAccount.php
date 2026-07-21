<?php

namespace BoreiStudio\FilamentPayPal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $tenant_id
 * @property string|null $tenant_type
 * @property string|null $production_client_id
 * @property string|null $production_client_secret
 * @property string|null $production_webhook_id
 * @property string|null $sandbox_client_id
 * @property string|null $sandbox_client_secret
 * @property string|null $sandbox_webhook_id
 * @property bool $sandbox_mode
 * @property string $status
 * @property \DateTime|null $last_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PaypalAccount extends Model
{
    protected $table = 'paypal_accounts';

    protected $fillable = [
        'tenant_id',
        'tenant_type',
        'production_client_id',
        'production_client_secret',
        'production_webhook_id',
        'sandbox_client_id',
        'sandbox_client_secret',
        'sandbox_webhook_id',
        'sandbox_mode',
        'status',
        'last_verified_at',
    ];

    protected $casts = [
        'production_client_id' => 'encrypted',
        'production_client_secret' => 'encrypted',
        'production_webhook_id' => 'encrypted',
        'sandbox_client_id' => 'encrypted',
        'sandbox_client_secret' => 'encrypted',
        'sandbox_webhook_id' => 'encrypted',
        'sandbox_mode' => 'boolean',
        'last_verified_at' => 'datetime',
    ];

    public function scopeByTenant(Builder $query, Model $tenant): Builder
    {
        return $query
            ->where('tenant_type', $tenant->getMorphClass())
            ->where('tenant_id', $tenant->getKey());
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function getActiveClientId(): string
    {
        return $this->sandbox_mode
            ? ($this->sandbox_client_id ?? '')
            : ($this->production_client_id ?? '');
    }

    public function getActiveClientSecret(): string
    {
        return $this->sandbox_mode
            ? ($this->sandbox_client_secret ?? '')
            : ($this->production_client_secret ?? '');
    }
}
