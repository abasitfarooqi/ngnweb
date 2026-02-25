<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentingBookingItem extends Model
{
    use HasFactory;

    protected $table = 'renting_booking_items';

    protected $fillable = [
        'booking_id',
        'motorbike_id',
        'user_id',
        'weekly_rent',
        'start_date',
        'due_date',
        'end_date',
        'is_posted',
    ];

    protected $casts = [
        'weekly_rent' => 'decimal:2',
        'start_date' => 'date',
        'due_date' => 'date',
        'end_date' => 'date',
        'is_posted' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(RentingBooking::class, 'booking_id');
    }

    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(RentingTransaction::class, 'booking_item_id');
    }
}
