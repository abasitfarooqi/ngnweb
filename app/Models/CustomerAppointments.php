<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class CustomerAppointments extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'appointment_date',
        'customer_name',
        'registration_number',
        'contact_number',
        'is_resolved',
        'email',
        'booking_reason',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    protected $table = 'customer_appointments';

    protected $guarded = ['id'];
}
