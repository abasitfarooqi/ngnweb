<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentingOtherCharge extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'description', 'amount', 'is_paid'];

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
