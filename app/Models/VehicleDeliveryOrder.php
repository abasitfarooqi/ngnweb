<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class VehicleDeliveryOrder
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $quote_date
 * @property \Illuminate\Support\Carbon $pickup_date
 * @property float $total_distance
 * @property float $surcharge
 * @property int $delivery_vehicle_type_id
 * @property int $branch_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class VehicleDeliveryOrder extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_delivery_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quote_date',
        'pickup_date',
        'total_distance',
        'surcharge',
        'delivery_vehicle_type_id',
        'branch_id',
        'user_id',
        'vrm',
        'full_name',
        'phone_number',
        'email',
        'notes',
    ];

    /**
     * Get the delivery vehicle type associated with the order.
     */
    public function deliveryVehicleType(): BelongsTo
    {
        return $this->belongsTo(DeliveryVehicleType::class, 'delivery_vehicle_type_id');
    }

    public function motorbikeDeliveryOrderEnquiry(): BelongsTo
    {
        return $this->belongsTo(MotorbikeDeliveryOrderEnquiries::class, 'delivery_vehicle_type_id');
    }

    /**
     * Get the branch associated with the order.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the order items for the delivery order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(VehicleDeliveryOrderItem::class, 'vehicle_delivery_order_id');
    }
}
