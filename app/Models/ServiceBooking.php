<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type',
        'description',
        'requires_schedule',
        'booking_date',
        'booking_time',
        'status',
        'fullname',
        'phone',
        'reg_no',
        'email',
    ];
}
