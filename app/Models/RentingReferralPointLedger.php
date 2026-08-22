<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentingReferralPointLedger extends Model
{
    public const DIRECTION_CREDIT = 'credit';

    public const DIRECTION_DEBIT = 'debit';

    public const STATUS_PENDING = 'pending';

    public const STATUS_AVAILABLE = 'available';

    public const STATUS_REDEEMED = 'redeemed';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_REVERSED = 'reversed';

    protected $table = 'renting_referral_point_ledger';

    protected $fillable = [
        'customer_id',
        'referral_id',
        'direction',
        'status',
        'points',
        'available_from',
        'approved_by',
        'approved_at',
        'released_early_by',
        'released_early_at',
        'release_reason',
        'original_available_from',
        'redeemed_booking_id',
        'redeemed_invoice_id',
        'redeemed_transaction_id',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'approved_at' => 'datetime',
        'released_early_at' => 'datetime',
        'original_available_from' => 'datetime',
        'points' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(RentingReferral::class, 'referral_id');
    }

    public function redeemedInvoice(): BelongsTo
    {
        return $this->belongsTo(BookingInvoice::class, 'redeemed_invoice_id');
    }

    public function isSpendable(): bool
    {
        if ($this->direction !== self::DIRECTION_CREDIT || $this->status !== self::STATUS_AVAILABLE) {
            return false;
        }

        if ($this->released_early_at) {
            return true;
        }

        if ($this->available_from === null) {
            return true;
        }

        return $this->available_from->lte(now());
    }
}
