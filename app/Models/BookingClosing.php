<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// renting_bookings

class BookingClosing extends Model
{
    use HasFactory;

    protected $table = 'booking_closing';

    protected $fillable = [
        'booking_id',
        'notice_details',
        'notice_checked',
        'collect_details',
        'collect_date',
        'collect_time',
        'collect_checked',
        'damages_checked',
        'pcn_checked',
        'pending_checked',
        'deposit_checked',
        'collect_proceeded_anyway_user_id',
        'collect_proceeded_anyway_at',
    ];

    protected $casts = [
        'collect_proceeded_anyway_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(RentingBooking::class);
    }

    public function collectProceededAnywayUser()
    {
        return $this->belongsTo(User::class, 'collect_proceeded_anyway_user_id');
    }

    public function getNoticeCheckedAttribute($value)
    {
        return (bool) $value;
    }

    public function getCollectCheckedAttribute($value)
    {
        return (bool) $value;
    }
}
