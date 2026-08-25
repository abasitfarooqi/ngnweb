<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentingReferral extends Model
{
    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_MATCHED = 'matched';

    public const STATUS_QUALIFYING = 'qualifying';

    public const STATUS_REVIEW = 'review';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_ATTRIBUTION_STATUSES = [
        self::STATUS_MATCHED,
        self::STATUS_QUALIFYING,
        self::STATUS_REVIEW,
        self::STATUS_APPROVED,
    ];

    public const SOURCE_PORTAL = 'portal';

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_LINK = 'link';

    protected $table = 'renting_referrals';

    protected $fillable = [
        'referral_code',
        'referrer_customer_id',
        'submitted_name',
        'submitted_phone',
        'submitted_email',
        'referred_customer_id',
        'status',
        'source',
        'referrer_qualifying_booking_id',
        'referred_qualifying_booking_id',
        'referred_qualifying_invoice_id',
        'matched_at',
        'qualified_at',
        'reviewed_at',
        'reviewed_by',
        'review_reason',
        'created_by',
        'warnings',
    ];

    protected $casts = [
        'matched_at' => 'datetime',
        'qualified_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'warnings' => 'array',
    ];

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referrer_customer_id');
    }

    public function referred(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referred_customer_id');
    }

    public function referredQualifyingBooking(): BelongsTo
    {
        return $this->belongsTo(RentingBooking::class, 'referred_qualifying_booking_id');
    }

    public function referredQualifyingInvoice(): BelongsTo
    {
        return $this->belongsTo(BookingInvoice::class, 'referred_qualifying_invoice_id');
    }

    public function referrerQualifyingBooking(): BelongsTo
    {
        return $this->belongsTo(RentingBooking::class, 'referrer_qualifying_booking_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(RentingReferralLog::class, 'referral_id')->orderByDesc('id');
    }

    public function ledger(): HasMany
    {
        return $this->hasMany(RentingReferralPointLedger::class, 'referral_id');
    }

    public function awards(): HasMany
    {
        return $this->hasMany(RentingFreeWeekAward::class, 'referral_id');
    }

    public function credit(): ?RentingReferralPointLedger
    {
        return $this->ledger->firstWhere('direction', RentingReferralPointLedger::DIRECTION_CREDIT);
    }

    public function shareUrl(): string
    {
        return url('/rentals/refer/'.$this->referral_code);
    }

    public function friendlyStatus(): string
    {
        $credit = $this->relationLoaded('ledger') ? $this->credit() : $this->ledger()->where('direction', 'credit')->first();

        if ($credit?->status === RentingReferralPointLedger::STATUS_REDEEMED) {
            return 'Reward used';
        }

        if ($credit?->status === RentingReferralPointLedger::STATUS_AVAILABLE && $credit->isSpendable()) {
            return 'Reward available';
        }

        return match ($this->status) {
            self::STATUS_SUBMITTED,
            self::STATUS_MATCHED,
            self::STATUS_QUALIFYING,
            self::STATUS_REVIEW => 'Sent',
            self::STATUS_APPROVED => $credit?->isSpendable() ? 'Reward available' : 'Sent',
            self::STATUS_REJECTED, self::STATUS_CANCELLED => 'Not eligible',
            default => 'Sent',
        };
    }

    public function hasWarning(): bool
    {
        return is_array($this->warnings) && $this->warnings !== [];
    }

    public function staffStatusLabel(): string
    {
        $credit = $this->relationLoaded('ledger') ? $this->credit() : $this->ledger()->where('direction', 'credit')->first();

        if ($credit?->status === RentingReferralPointLedger::STATUS_REDEEMED) {
            return 'Redeemed';
        }

        return match ($this->status) {
            self::STATUS_APPROVED => $credit?->isSpendable() ? 'Ready' : 'Waiting',
            self::STATUS_REVIEW => 'Review',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            default => ucfirst((string) $this->status),
        };
    }

    public function staffStatusTone(): string
    {
        return match ($this->staffStatusLabel()) {
            'Redeemed', 'Ready' => 'green',
            'Rejected', 'Cancelled' => 'red',
            default => 'orange',
        };
    }

    public function pointsStatusLabel(): string
    {
        $credit = $this->relationLoaded('ledger') ? $this->credit() : $this->ledger()->where('direction', 'credit')->first();
        if (! $credit) {
            return 'No points yet';
        }

        $points = (int) $credit->points;

        return match ($credit->status) {
            RentingReferralPointLedger::STATUS_REDEEMED => $points.' spent',
            RentingReferralPointLedger::STATUS_PENDING => $points.' pending',
            RentingReferralPointLedger::STATUS_AVAILABLE => $credit->isSpendable()
                ? $points.' unused'
                : $points.' waiting',
            RentingReferralPointLedger::STATUS_REJECTED => $points.' refused',
            RentingReferralPointLedger::STATUS_REVERSED => $points.' reversed',
            default => $points.' '.$credit->status,
        };
    }

    public function pointsStatusTone(): string
    {
        $credit = $this->relationLoaded('ledger') ? $this->credit() : $this->ledger()->where('direction', 'credit')->first();

        return match ($credit?->status) {
            RentingReferralPointLedger::STATUS_REDEEMED => 'green',
            RentingReferralPointLedger::STATUS_AVAILABLE => $credit->isSpendable() ? 'green' : 'orange',
            RentingReferralPointLedger::STATUS_REJECTED,
            RentingReferralPointLedger::STATUS_REVERSED => 'red',
            default => 'orange',
        };
    }
}
