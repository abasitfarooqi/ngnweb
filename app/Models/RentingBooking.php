<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class RentingBooking extends Model
{
    use HasFactory;

    protected $table = 'renting_bookings';

    protected $fillable = [
        'customer_id',
        'user_id',
        'deposit',
        'start_date',
        'due_date',
        'state',
        'is_posted',
    ];

    protected $casts = [
        'deposit' => 'decimal:2',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'is_posted' => 'boolean',
    ];

    public function judopaySubscription(): MorphOne
    {
        return $this->morphOne(JudopaySubscription::class, 'subscribable');
    }

    public static function getActiveRentals()
    {
        return static::where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($items) => $items->whereNull('end_date'))
            ->with(['rentingBookingItems' => function ($items) {
                $items->whereNull('end_date')
                    ->with('motorbike:id,reg_no,make,model');
            }]);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions($invoiceNumber = null)
    {
        $query = $this->hasMany(RentingTransaction::class, 'booking_id');

        if ($invoiceNumber !== null) {
            $query->where('invoice_id', $invoiceNumber);
        }

        return $query;
    }

    public function bookingInvoices()
    {
        return $this->hasMany(BookingInvoice::class, 'booking_id');
    }

    public function rentingBookingItems()
    {
        return $this->hasMany(RentingBookingItem::class, 'booking_id');
    }

    public function activeItems()
    {
        return $this->hasMany(RentingBookingItem::class, 'booking_id')->whereNull('end_date');
    }

    public function scopeActive($query)
    {
        return $query->where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($q) => $q->whereNull('end_date'));
    }

    public function invoices()
    {
        return $this->hasMany(BookingInvoice::class, 'booking_id');
    }

    public function bookingItems()
    {
        return $this->hasMany(RentingBookingItem::class, 'booking_id');
    }

    public function getCustomerNameAndBookingIdAttribute()
    {
        $customer = $this->customer;
        $customerName = $customer ? ($customer->first_name.' '.$customer->last_name) : '';

        return 'Customer: '.trim($customerName).' Booking ID: '.$this->id;
    }
}
