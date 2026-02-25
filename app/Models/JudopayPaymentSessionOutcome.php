<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JudopayPaymentSessionOutcome extends Model
{
    use HasFactory;

    protected $table = 'judopay_payment_session_outcomes';

    protected $fillable = [
        'session_id',
        'session_type',
        'subscription_id',
        'status',
        'source',
        'judopay_receipt_id',
        'judopay_payment_reference',
        'payment_network_transaction_id',
        'acquirer_transaction_id',
        'auth_code',
        'external_bank_response_code',
        'appears_on_statement_as',
        'card_last_four',
        'card_funding',
        'card_category',
        'card_country',
        'issuing_bank',
        'billing_address',
        'risk_assessment',
        'three_d_secure',
        'type',
        'amount',
        'your_payment_reference',
        'your_consumer_reference',
        'payload',
        'message',
        'occurred_at',
        // NEW compliance fields
        'merchant_name',
        'judo_id',
        'net_amount',
        'original_amount',
        'amount_collected',
        'locator_id',
        'disable_network_tokenisation',
        'allow_increment',
        'risk_score',
        'recurring_payment_type',
        'bank_response_category',
        'is_retryable',
        'judopay_created_at',
        'timezone',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'amount_collected' => 'decimal:2',
        'payload' => 'array',
        'billing_address' => 'array',
        'risk_assessment' => 'array',
        'three_d_secure' => 'array',
        'occurred_at' => 'datetime',
        'judopay_created_at' => 'datetime',
        'disable_network_tokenisation' => 'boolean',
        'allow_increment' => 'boolean',
        'is_retryable' => 'boolean',
        'risk_score' => 'integer',
    ];

    /**
     * This will store the response/outcome of payment session
     * in the reponse of MIT, or CIT session.
     * The expceted response always from JudoPay and can be
     * more than one responses represents a CIT or MIT session.
     * The minimum expected are success post, or webhook post.
     */
    public function session(): MorphTo
    {
        return $this->morphTo();
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(JudopaySubscription::class, 'subscription_id');
    }

    /**
     * Enquiry records related to this outcome
     * This is where we store API enquiry responses for verification
     */
    public function enquiryRecords(): HasMany
    {
        return $this->hasMany(JudopayEnquiryRecord::class, 'payment_session_outcome_id');
    }

    /**
     * Get the most recent enquiry record
     */
    public function latestEnquiry()
    {
        return $this->hasOne(JudopayEnquiryRecord::class, 'payment_session_outcome_id')->latest('enquired_at');
    }

    /**
     * Check if this outcome has been enquired about
     */
    public function hasBeenEnquired(): bool
    {
        return $this->enquiryRecords()->exists();
    }

    /**
     * Get the appropriate enquiry identifier (receiptId for MIT, reference for CIT)
     */
    public function getEnquiryIdentifierAttribute(): ?string
    {
        // For MIT: use judopay_receipt_id
        if ($this->recurring_payment_type === 'mit' || $this->session_type === 'App\Models\JudopayMitPaymentSession') {
            return $this->judopay_receipt_id;
        }

        // For CIT: use your_payment_reference (this is the reference)
        if ($this->recurring_payment_type === 'cit' || $this->session_type === 'App\Models\JudopayCitPaymentSession') {
            return $this->your_payment_reference;
        }

        return null;
    }

    /**
     * Get the appropriate enquiry type (transaction for MIT, webpayment for CIT)
     */
    public function getEnquiryTypeAttribute(): ?string
    {
        // For MIT: use transaction endpoint
        if ($this->recurring_payment_type === 'mit' || $this->session_type === 'App\Models\JudopayMitPaymentSession') {
            return 'transaction';
        }

        // For CIT: use webpayment endpoint
        if ($this->recurring_payment_type === 'cit' || $this->session_type === 'App\Models\JudopayCitPaymentSession') {
            return 'webpayment';
        }

        return null;
    }

    /**
     * Check if this outcome is eligible for enquiry
     */
    public function isEnquiryEligible(): bool
    {
        return ! empty($this->enquiry_identifier) && ! empty($this->enquiry_type);
    }
}
