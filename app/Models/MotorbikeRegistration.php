<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotorbikeRegistration extends Model
{
    protected $fillable = [
        'motorbike_id',
        'registration_number',
        'start_date',
        'end_date',
    ];

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }
}
