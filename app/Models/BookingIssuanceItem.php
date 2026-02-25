<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class BookingIssuanceItem extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'booking_issuance_items';

    protected $fillable = [
        'booking_item_id',
        'issued_by_user_id',
        'is_insured',
        'current_mileage',
        'is_video_recorded',
        'accessories_checked',
        'issuance_branch',
        'notes',
    ];

    public function bookingItem()
    {
        return $this->belongsTo(RentingBookingItem::class, 'booking_item_id');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    public function booking()
    {
        if ($this->bookingItem) {
            return $this->bookingItem->booking();
        }

        return null;
    }
}
