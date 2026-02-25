<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class VehicleNotification extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'reg_no',
        'phone',
        'notify_email',
        'notify_phone',
        'enable',
    ];

    protected $table = 'veh_notifications';

    protected $casts = [
        'notify_email' => 'boolean',
        'notify_phone' => 'boolean',
        'enable' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // public function getNotifyEmailAttribute()
    // {
    //     return $this->notify_email ? 'Yes' : 'No';
    // }

    // public function getNotifyPhoneAttribute()
    // {
    //     return $this->notify_phone ? 'Yes' : 'No';
    // }

    public function getCreatedAtAttribute($value)
    {
        return date('d/m/Y H:i', strtotime($value));
    }
}
