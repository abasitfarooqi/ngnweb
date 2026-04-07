<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotTaxAlertSubscription extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'vehicle_registration',
        'notify_email',
        'notify_sms',
        'enable_deals',
    ];

    protected function casts(): array
    {
        return [
            'notify_email' => 'boolean',
            'notify_sms' => 'boolean',
            'enable_deals' => 'boolean',
        ];
    }
}
