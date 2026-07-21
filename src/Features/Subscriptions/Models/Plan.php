<?php

namespace BoreiStudio\FilamentPayPal\Features\Subscriptions\Models;

use BoreiStudio\FilamentPayPal\Models\PaypalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property int $product_id
 * @property string $paypal_plan_id
 * @property string $name
 * @property string|null $description
 * @property string $status
 * @property string $currency_code
 * @property float $amount
 * @property string $billing_frequency
 * @property int $billing_cycles
 * @property string|null $payment_preference
 * @property array|null $paypal_response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Plan extends Model
{
    protected $table = 'paypal_plans';

    protected $fillable = [
        'account_id',
        'product_id',
        'paypal_plan_id',
        'name',
        'description',
        'status',
        'currency_code',
        'amount',
        'billing_frequency',
        'billing_cycles',
        'payment_preference',
        'paypal_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_cycles' => 'integer',
        'paypal_response' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
