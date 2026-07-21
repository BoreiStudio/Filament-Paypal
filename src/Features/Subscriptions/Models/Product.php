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
 * @property string $paypal_product_id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property string|null $category
 * @property string|null $image_url
 * @property string|null $home_url
 * @property string $status
 * @property array|null $paypal_response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Product extends Model
{
    protected $table = 'paypal_products';

    protected $fillable = [
        'account_id',
        'paypal_product_id',
        'name',
        'description',
        'type',
        'category',
        'image_url',
        'home_url',
        'status',
        'paypal_response',
    ];

    protected $casts = [
        'paypal_response' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaypalAccount::class, 'account_id');
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class, 'product_id');
    }
}
