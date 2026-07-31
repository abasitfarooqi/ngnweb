<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class RentalTerminateAccess extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'customer_id',
        'booking_id',
        'passcode',
        'expire_at',
    ];

    protected $table = 'rental_terminate_accesses';

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function bookings(): BelongsTo
    {
        return $this->belongsTo(RentingBooking::class, 'booking_id', 'id');
    }

    public function customerBookingUrl(): string
    {
        return route('rental.termination.show', [
            'customer_id' => $this->customer_id,
            'booking_id' => $this->booking_id,
            'passcode' => $this->passcode,
        ]);
    }
}
