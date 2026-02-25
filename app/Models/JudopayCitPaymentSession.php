<?php

namespace App\Models;

use App\Casts\EncryptCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class JudopayCitPaymentSession extends Model
{
    use HasFactory;

    protected $table = 'judopay_cit_payment_sessions';

    protected $fillable = [
        'subscription_id',
        'user_id',
        'judopay_payment_reference',
        'amount',
        'customer_email',
        'customer_mobile',
        'customer_name',
        'card_holder_name',
        'address1',
        'address2',
        'city',
        'postcode',
        'judopay_reference',
        'judopay_receipt_id',
        'judopay_paylink_url',
        'expiry_date',
        'status',
        'is_active', // Is active is link alive or not. (Not related to card or subscription)
        'card_token',
        'judopay_response',
        'judopay_webhook_data',
        'judopay_session_status', // Extra get request toward judopay to enquire (receiptId or Reference)
        'payment_completed_at',
        'link_generated_at',
        'customer_accessed_at', // UI Redirect to Judopay from Hostapplication
        'failure_reason',
        'status_score',
        // Consent tracking fields
        'consent_given_at',
        'consent_ip_address',
        'consent_terms_version',
        'sms_verification_sid',
        'sms_verified_at',
        'consent_content_sha256',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expiry_date' => 'datetime',
        'payment_completed_at' => 'datetime',
        'link_generated_at' => 'datetime',
        'customer_accessed_at' => 'datetime',
        'is_active' => 'boolean',
        'judopay_response' => 'array',
        'judopay_webhook_data' => 'array',
        'judopay_session_status' => 'array',
        'status_score' => 'integer',
        // Consent tracking timestamps
        'consent_given_at' => 'datetime',
        'sms_verified_at' => 'datetime',
        // GDPR/PII Encrypted fields
        'customer_email' => EncryptCast::class,
        'customer_mobile' => EncryptCast::class,
        'customer_name' => EncryptCast::class,
        'card_holder_name' => EncryptCast::class,
        'address1' => EncryptCast::class,
        'address2' => EncryptCast::class,
        'city' => EncryptCast::class,
        'postcode' => EncryptCast::class,
        'card_token' => EncryptCast::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(JudopaySubscription::class, 'subscription_id');
    }

    public function paymentSessionOutcomes(): MorphMany
    {
        return $this->morphMany(JudopayPaymentSessionOutcome::class, 'session');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get consent version details with fallback
     */
    public function getConsentVersionDetails()
    {
        try {
            $version = $this->consent_terms_version ?? 'v1.0-judopay-cit';
            $versions = config('judopay.consent.versions', []);
            $versionConfig = $versions[$version] ?? null;
            
            if (!$versionConfig) {
                return [
                    'blade_file' => 'judopay-authorisation-concent-form-v1',
                    'effective_date' => '2025-10-07',
                    'hash' => null,
                    'description' => 'Default consent version',
                ];
            }
            
            return $versionConfig;
        } catch (\Exception $e) {
            \Log::error('Failed to get consent version details', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
