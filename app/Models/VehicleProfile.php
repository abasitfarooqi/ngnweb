<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleProfile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_internal'];

    protected $table = 'vehicle_profiles';

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function motorbike()
    {
        return $this->hasMany(Motorbike::class, 'vehicle_profile_id');
    }
}
