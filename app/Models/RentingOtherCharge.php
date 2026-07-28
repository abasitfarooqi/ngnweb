<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentingOtherCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'description',
        'amount',
        'is_paid',
        'is_whatsapp_sent',
        'whatsapp_last_reminder_sent_at',
        'email_last_reminder_sent_at',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_whatsapp_sent' => 'boolean',
        'whatsapp_last_reminder_sent_at' => 'datetime',
        'email_last_reminder_sent_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(RentingBooking::class);
    }

    public function getAmountAttribute($value)
    {
        return number_format($value, 2);
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = str_replace(',', '', $value);
    }

    public function getIsPaidAttribute($value)
    {
        return $value ? 'Yes' : 'No';
    }
}
