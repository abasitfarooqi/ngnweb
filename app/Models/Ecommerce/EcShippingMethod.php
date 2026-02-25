<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcShippingMethod extends Model
{
    protected $table = 'ec_shipping_methods';

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'link_url',
        'description',
        'shipping_amount',
        'is_enabled',
        'in_store_pickup',
    ];

    protected $casts = [
        'shipping_amount' => 'decimal:2',
        'is_enabled' => 'boolean',
        'in_store_pickup' => 'boolean',
    ];

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(EcOrder::class, 'shipping_method_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopePickup($query)
    {
        return $query->where('in_store_pickup', true);
    }

    public function scopeDelivery($query)
    {
        return $query->where('in_store_pickup', false);
    }
}
