<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class BookingInvoice extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $table = 'booking_invoices';

    protected $attributes = [
        'is_paid' => false,
    ];

    protected $fillable = [
        'booking_id',
        'user_id',
        'invoice_date',
        'amount',
        'deposit',
        'is_posted',
        'is_paid',
        'paid_date',
        'state',
        'notes',
        'is_whatsapp_sent',
        'whatsapp_last_reminder_sent_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'is_posted' => 'boolean',
        'is_paid' => 'boolean',
        'paid_date' => 'date',
        'is_whatsapp_sent' => 'boolean',
        'whatsapp_last_reminder_sent_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(RentingBooking::class, 'booking_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(RentingTransaction::class, 'invoice_id');
    }

    public function getBookingSummary($bookingId)
    {
        $booking = \App\Models\RentingBooking::with(['rentingBookingItems', 'bookingInvoices'])->findOrFail($bookingId);

        $start = $booking->start_date;
        $end = $booking->due_date;
        $now = now();

        // Use end date if set, otherwise now
        $effectiveEnd = $end ?? $now;
        $weeks = ceil($start->diffInDays($effectiveEnd) / 7);

        // Get all invoices for this booking (paid only)
        $totalIncome = $booking->bookingInvoices()->where('is_paid', true)->sum('amount');

        // Get the first motorbike_id
        $motorbike_id = optional($booking->rentingBookingItems->first())->motorbike_id;

        // Get all maintenance logs for this booking/motorbike
        $totalCost = \App\Models\MotorbikeMaintenanceLog::where('booking_id', $bookingId)
            ->where('motorbike_id', $motorbike_id)
            ->sum('cost');

        // Get current weekly rent for this motorbike
        $currentPricing = \App\Models\RentingPricing::where('motorbike_id', $motorbike_id)
            ->where('iscurrent', true)
            ->first();

        return response()->json([
            'start_date' => $start ? $start->format('Y-m-d') : null,
            'end_date' => $end ? $end->format('Y-m-d') : null,
            'weeks' => $weeks,
            'total_income' => $totalIncome,
            'total_cost' => $totalCost,
            'net_profit' => $totalIncome - $totalCost,
            'current_weekly_rent' => $currentPricing ? $currentPricing->weekly_price : null,
        ]);
    }
}
