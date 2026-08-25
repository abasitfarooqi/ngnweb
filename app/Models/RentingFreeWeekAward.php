<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentingFreeWeekAward extends Model
{
    public const SOURCE_PROGRAMME = 'programme';

    public const SOURCE_DIRECT = 'direct';

    public const ELIGIBILITY_FALLBACK = 'staff will explain why this referrer is eligible to get the free week';

    protected $table = 'renting_free_week_awards';

    protected $fillable = [
        'source',
        'referral_id',
        'awarded_booking_id',
        'awarded_invoice_id',
        'awarded_transaction_id',
        'amount',
        'hirer_customer_id',
        'selected_referrer_customer_id',
        'selected_referrer_booking_id',
        'selected_paid_invoices',
        'eligibility_note',
        'staff_proof',
        'applied_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'selected_paid_invoices' => 'array',
    ];

    public function referral(): BelongsTo
    {
        return $this->belongsTo(RentingReferral::class, 'referral_id');
    }

    public function awardedBooking(): BelongsTo
    {
        return $this->belongsTo(RentingBooking::class, 'awarded_booking_id');
    }

    public function awardedInvoice(): BelongsTo
    {
        return $this->belongsTo(BookingInvoice::class, 'awarded_invoice_id');
    }

    public function awardedTransaction(): BelongsTo
    {
        return $this->belongsTo(RentingTransaction::class, 'awarded_transaction_id');
    }

    public function hirer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'hirer_customer_id');
    }

    public function selectedReferrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'selected_referrer_customer_id');
    }

    public function selectedReferrerBooking(): BelongsTo
    {
        return $this->belongsTo(RentingBooking::class, 'selected_referrer_booking_id');
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }
}
