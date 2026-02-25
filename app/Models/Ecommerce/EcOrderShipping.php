<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EcOrderShipping extends Model
{
    protected $table = 'ec_order_shippings';

    // Fulfillment method constants to match database enum
    const FULFILLMENT_CARRIER = 'carrier';

    const FULFILLMENT_PICKUP = 'pickup';

    protected $fillable = [
        'order_id',
        'fulfillment_method',
        'status',
        'processing_at',
        'ready_at',
        'shipped_at',
        'completed_at',
        'return_method',
        'return_initiated_at',
        'return_shipped_at',
        'return_received_at',
        'carrier',
        'tracking_number',
        'tracking_url',
        'notes',
    ];

    protected $attributes = [
        'fulfillment_method' => self::FULFILLMENT_CARRIER,
        'status' => 'processing',
    ];

    protected $casts = [
        'processing_at' => 'datetime',
        'ready_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'return_initiated_at' => 'datetime',
        'return_shipped_at' => 'datetime',
        'return_received_at' => 'datetime',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(EcOrder::class, 'order_id');
    }

    // Helper methods for status checks
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isReadyForCarrier(): bool
    {
        return $this->status === 'ready_for_carrier';
    }

    public function isReadyForPickup(): bool
    {
        return $this->status === 'ready_for_pickup';
    }

    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isPickedUp(): bool
    {
        return $this->status === 'picked_up';
    }

    public function isCollected(): bool
    {
        return $this->status === 'collected';
    }

    public function isStorePickup(): bool
    {
        return $this->fulfillment_method === self::FULFILLMENT_PICKUP;
    }

    public function isCarrierDelivery(): bool
    {
        return $this->fulfillment_method === self::FULFILLMENT_CARRIER;
    }
}
