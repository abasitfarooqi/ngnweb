<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleEstimator extends Model
{
    use HasFactory;

    protected $fillable = [
        'referer_id',
        'make',
        'model',
        'vrm',
        'engine_size',
        'mileage',
        'vehicle_year',
        'condition',
        'base_price',
        'calculated_value',
        'like',
    ];

    protected $casts = [
        'engine_size' => 'integer',
        'mileage' => 'integer',
        'condition' => 'integer',
        'base_price' => 'decimal:2',
        'calculated_value' => 'decimal:2',
        'like' => 'boolean',
    ];

    public function getConditionAttribute()
    {
        return $this->condition;
    }

    public function getCalculatedValueAttribute()
    {
        return $this->calculated_value;
    }

    public function getLikeAttribute()
    {
        return $this->like;
    }

    public function getVehicleYearAttribute()
    {
        return $this->vehicle_year;
    }

    public function getVrmAttribute()
    {
        return $this->vrm;
    }

    public function getMakeAttribute()
    {
        return $this->make;
    }

    public function getModelAttribute()
    {
        return $this->model;
    }
}
