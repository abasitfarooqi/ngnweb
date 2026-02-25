<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorbikeMaintenanceLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'motorbike_id',
        'booking_id',
        'user_id',
        'cost',
        'serviced_at',
        'description',
        'note',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost' => 'decimal:2',
        'serviced_at' => 'datetime',
    ];

    /**
     * Get the motorbike associated with this maintenance log.
     */
    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class);
    }

    /**
     * Get the booking associated with this maintenance log.
     */
    public function booking()
    {
        return $this->belongsTo(RentingBooking::class);
    }

    /**
     * Get the user who logged this maintenance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
