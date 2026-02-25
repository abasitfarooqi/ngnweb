<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDeliveryOrderItem extends Model
{
    use HasFactory;

    protected $table = 'vehicle_delivery_orders_items';

    protected $fillable = [
        'vehicle_delivery_order_id',
        'pickup_point_coordinates_lat',
        'pickup_point_coordinates_lon',
        'drop_branch_id',
    ];

    public function vehicleDeliveryOrder(): BelongsTo
    {
        return $this->belongsTo(VehicleDeliveryOrder::class, 'vehicle_delivery_order_id');
    }

    public function dropBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'drop_branch_id');
    }
}
