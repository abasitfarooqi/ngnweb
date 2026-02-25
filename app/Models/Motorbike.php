<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Motorbike extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;
    // use SoftDeletes;

    protected $table = 'motorbikes';

    protected $fillable = [
        'vin_number',
        'model',
        'make',
        'year',
        'engine',
        'co2_emissions',
        'fuel_type',
        'marked_for_export',
        'color',
        'deleted_at',
        'type_approval',
        'wheel_plan',
        'month_of_first_registration',
        'reg_no',
        'branch_id',
        'vehicle_profile_id',
        'date_of_last_v5c_issuance',
        'accessories',
        'is_ebike',
    ];

    protected $primaryKey = 'id';

    public static function getMotorbikeIdByRegNo($reg_no)
    {
        return Motorbike::where('reg_no', $reg_no)->first()->id ?? null;
    }

    public function vehicleProfile()
    {
        return $this->belongsTo(VehicleProfile::class);
    }

    public static function isRegNoExists($reg_no)
    {
        // remove spaces
        $reg_no = str_replace(' ', '', $reg_no);

        return Motorbike::where('reg_no', $reg_no)->exists();
    }

    public static function getMotorbikeModelById($id)
    {
        return Motorbike::where('id', $id)->first()->model;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    protected $casts = [
        'marked_for_export' => 'integer',
    ];

    protected $hidden = ['deleted_at'];

    public function registrations()
    {
        return $this->hasMany(MotorbikeRegistration::class);
    }

    public function repairs()
    {
        return $this->hasMany(MotorbikeRepair::class);
    }

    public function annualCompliances()
    {
        return $this->hasMany(MotorbikeAnnualCompliance::class);
    }

    public function motNotifiers()
    {
        return $this->hasMany(NgnMotNotifier::class);
    }

    public function motorbikeCatB()
    {
        return $this->hasMany(MotorbikeCatB::class);
    }

    public function images()
    {
        return $this->hasMany(MotorbikeImage::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MotorbikeMaintenanceLog::class, 'motorbike_id');
    }

    public function scopeByMake($query, $make)
    {
        return $query->where('make', $make);
    }

    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByEngine($query, $engine)
    {
        return $query->where('engine', $engine);
    }

    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    public function scopeExternal($query)
    {
        \Log::info('External scope called');

        return $query->where('vehicle_profile_id', 2);
    }

    public function getFilteredRegNoAttribute()
    {
        \Log::info('getFilteredRegNoAttribute called');
        if ($this->vehicle_profile_id == 2) {
            return $this->reg_no;
        }

        return null;
    }

    public function rentingPricings()
    {
        return $this->hasMany(RentingPricing::class);
    }

    public function rentingBookingItems()
    {
        return $this->hasMany(RentingBookingItem::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    //

    public function getDetailAttribute()
    {
        return $this->reg_no.' | '.$this->make.' '.$this->model.' | '.$this->year.' | '.$this->vin_number;
    }
}
