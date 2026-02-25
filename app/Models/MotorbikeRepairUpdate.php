<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorbikeRepairUpdate extends Model
{
    use CrudTrait, HasFactory;

    protected $table = 'motorbike_repair_updates';

    protected $fillable = [
        'motorbike_repair_id',
        'job_description',
        'price',
        'note',
    ];

    public function motorbikeRepair()
    {
        return $this->belongsTo(MotorbikeRepair::class);
    }

    public function services()
    {
        return $this->belongsToMany(
            MotorbikeRepairServicesList::class,
            'repair_update_service',
            'update_id',
            'service_id'
        )->withTimestamps();
    }

    // public function getServicesAttribute($value)
    // {
    //     return $this->services ? $this->services->pluck('id')->toArray() : [];
    // }
    public function getServicesIdsAttribute()
    {
        return $this->services()->pluck('id')->toArray();
    }
}
