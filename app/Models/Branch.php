<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class Branch extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'branches';

    // Add 'address' to the fillable attributes
    protected $fillable = ['name', 'address', 'latitude', 'longitude', 'postal_code', 'city'];

    public function stockMovements()
    {
        return $this->hasMany(NgnStockMovement::class, 'branch_id');
    }

    public function appointments()
    {
        return $this->hasMany(MOTBooking::class);
    }

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function motorbikes()
    {
        return $this->hasMany(Motorbike::class);
    }

    public function scopeWithAppointments($query)
    {
        return $query->with('appointments');
    }

    public function scopeWithPendingAppointments($query)
    {
        return $query->with(['appointments' => function ($query) {
            $query->pending();
        }]);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(VehicleDeliveryOrder::class, 'branch_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(EcommerceOrder::class, 'branch_id');
    }

    /**
     * Get the delivery order items where this branch is the drop branch.
     */
    public function dropOrderItems(): HasMany
    {
        return $this->hasMany(VehicleDeliveryOrderItem::class, 'drop_branch_id');
    }
}
