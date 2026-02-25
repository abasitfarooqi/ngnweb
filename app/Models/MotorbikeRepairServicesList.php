<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorbikeRepairServicesList extends Model
{
    use CrudTrait, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    // MotorbikeRepairServicesList.php
    public function updates()
    {
        return $this->belongsToMany(
            MotorbikeRepairUpdate::class,
            'repair_update_service',
            'service_id',
            'update_id'
        )->withTimestamps();
    }
}
