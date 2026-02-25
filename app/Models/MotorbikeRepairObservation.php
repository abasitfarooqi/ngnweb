<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotorbikeRepairObservation extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'motorbike_repair_observations';

    protected $fillable = [
        'motorbike_repair_id',
        'observation_description',
    ];

    public function motorbikeRepair()
    {
        return $this->belongsTo(MotorbikeRepair::class);
    }
}
