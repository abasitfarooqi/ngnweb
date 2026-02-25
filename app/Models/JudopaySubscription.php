<?php

namespace App\Models;

use App\Casts\EncryptCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JudopaySubscription extends Model
{
    use HasFactory;
    use CrudTrait;

    protected $table = 'judopay_subscriptions';

    protected $fillable = [
        'judopay_onboarding_id',
        'date',
        'subscribable_id',
        'subscribable_type',
        'billing_frequency',
        'billing_day',
        'amount',
        'opening_balance',
        'start_date',
        'end_date',
        'status',
        'consumer_reference',
        'card_token',
        'receipt_id',
        // Payment success details (non-PCI compliant data from webhook)
        'judopay_receipt_id',
        'acquirer_transaction_id',
        'auth_code',
        'merchant_name',
        'statement_descriptor',
        // Non-sensitive card details (safe to store)
        'card_last_four',
        'card_funding',
        'card_category',
        'card_country',
        'issuing_bank',
        // Compliance & security data
        'billing_address',
        'risk_assessment',
        'three_d_secure',
        'audit_log',
    ];

    protected $casts = [
        'date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
        'amount' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        // JSON fields for payment details
        'billing_address' => 'array',
        'risk_assessment' => 'array',
        'three_d_secure' => 'array',
        'audit_log' => 'array',
        // PCI sensitive field - encrypted
        'card_token' => EncryptCast::class,
    ];

    /**
     * A individual could have multiple subscriptions
     * but that individual require to represents single
     * onboarding id.
     */
    public function judopayOnboarding(): BelongsTo
    {
        return $this->belongsTo(JudopayOnboarding::class);
    }

    /**
     * A client / Package user might have multiple services
     * like rental, finance, etc... And obviously those might
     * maintained in different tables. So, this morphs to
     * subscribable used.
     */
    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    public function citPaymentSessions(): HasMany
    {
        return $this->hasMany(JudopayCitPaymentSession::class, 'subscription_id');
    }

    public function citAccesses(): HasMany
    {
        return $this->hasMany(JudopayCitAccess::class, 'subscription_id');
    }

    public function mitPaymentSessions(): HasMany
    {
        return $this->hasMany(JudopayMitPaymentSession::class, 'subscription_id');
    }

    public function paymentSessionOutcomes(): HasMany
    {
        return $this->hasMany(JudopayPaymentSessionOutcome::class, 'subscription_id');
    }

    /**
     * Get active consent version for this subscription
     */
    public function getActiveConsentVersion(): string
    {
        try {
            $latestSession = $this->citPaymentSessions()
                ->where('status', 'success')
                ->latest()
                ->first();
            
            return $latestSession 
                ? ($latestSession->consent_terms_version ?? 'v1.0-judopay-cit')
                : config('judopay.consent.current_version', 'v1.0-judopay-cit');
        } catch (\Exception $e) {
            \Log::error('Failed to get active consent version', ['error' => $e->getMessage()]);
            return 'v1.0-judopay-cit';
        }
    }

    /**
     * Check if subscription has active consent
     */
    public function hasActiveConsent(): bool
    {
        try {
            return $this->citPaymentSessions()
                ->where('status', 'success')
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function ngnMitQueues(): HasMany
    {
        return $this->hasMany(NgnMitQueue::class, 'subscribable_id');
    }

    public static function getActiveSubscriptions(){
        return static::where('status', 'active')
            ->with(['subscribable'])
            ->get()
            ->filter(function($subscription) {
                if ($subscription->subscribable_type === RentingBooking::class) {
                    return $subscription->subscribable && $subscription->subscribable->is_posted;
                }
                if ($subscription->subscribable_type === FinanceApplication::class) {
                    return $subscription->subscribable && $subscription->subscribable->is_posted && 
                           (!$subscription->subscribable->is_cancelled || $subscription->subscribable->is_cancelled === null);
                }
                return false;
            });
    }
}
