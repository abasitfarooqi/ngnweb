<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class DeliveryVehicleType
 *
 *
 * @property int $id
 * @property string $name
 * @property string $cc_range
 * @property float $additional_fee
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class DeliveryVehicleType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'delivery_vehicle_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'cc_range',
        'additional_fee',
    ];

    /**
     * Get the delivery orders for the vehicle type.
     */
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(VehicleDeliveryOrder::class, 'delivery_vehicle_type_id');
    }

    public function motorbikeDeliveryOrderEnquiry(): HasMany
    {
        return $this->hasMany(MotorbikeDeliveryOrderEnquiries::class, 'delivery_vehicle_type_id');
    }
}
