<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

// use App\Http\Controllers\Admin\MotorbikeDeliveryOrderEnquiriesCrudController;

class MotorbikeDeliveryOrderEnquiries extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'motorbike_delivery_order_enquiries';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $fillable = [
        'order_id',
        'pickup_address',
        'dropoff_address',
        'vrm',
        'moveable',
        'documents',
        'keys',
        'pick_up_datetime',
        'distance',
        'note',
        'full_name',
        'phone',
        'email',
        'customer_address',
        'customer_postcode',
        'total_cost',
        'vehicle_type',
        'branch_name',
        'branch_id',
        'is_dealt',
        'dealt_by_user_id',
        'notes',
        'pickup_postcode',
        'dropoff_postcode',
        'vehicle_type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dealt_by_user_id');
    }

    public function vehicleType()
    {
        return $this->belongsTo(DeliveryVehicleType::class, 'vehicle_type_id');
    }

    public function getBranchNameAttribute()
    {
        return $this->branch ? $this->branch->name : null;
    }

    public function getVehicleTypeNameAttribute()
    {
        return $this->vehicleType ? $this->vehicleType->name : null;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function dealtByUser()
    {
        return $this->belongsTo(User::class, 'dealt_by_user_id');
    }
}
