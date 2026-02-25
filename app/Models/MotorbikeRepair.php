<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class MotorbikeRepair extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'motorbikes_repair';

    protected $fillable = [
        'arrival_date',
        'motorbike_id',
        'notes',
        'is_repaired',
        'repaired_date',
        'is_returned',
        'returned_date',
        'fullname',
        'email',
        'phone',
        'branch_id',
        'user_id',
    ];

    protected $casts = [
        'is_repaired' => 'boolean',
        'is_returned' => 'boolean',
        'arrival_date' => 'datetime',
        'repaired_date' => 'datetime',
        'returned_date' => 'datetime',
    ];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    public function updates()
    {
        return $this->hasMany(MotorbikeRepairUpdate::class)->with('services');
    }

    public function observations()
    {
        return $this->hasMany(MotorbikeRepairObservation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
