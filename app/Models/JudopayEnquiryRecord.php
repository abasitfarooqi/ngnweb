<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JudopayEnquiryRecord extends Model
{
    use HasFactory;

    protected $table = 'judopay_enquiry_records';

    protected $fillable = [
        'payment_session_outcome_id',
        'user_id',
        'enquiry_type',
        'enquiry_identifier',
        'endpoint_used',
        'api_status',
        'http_status_code',
        'api_response',
        'api_headers',
        'judopay_status',
        'current_state',
        'matches_local_record',
        'discrepancy_notes',
        'external_bank_response_code',
        'amount_collected_remote',
        'remote_message',
        'is_retryable',
        'enquired_at',
        'enquiry_reason',
    ];

    protected $casts = [
        'api_response' => 'array',
        'api_headers' => 'array',
        'enquired_at' => 'datetime',
        'matches_local_record' => 'boolean',
        'is_retryable' => 'boolean',
        'amount_collected_remote' => 'decimal:2',
    ];

    /**
     * The outcome record this enquiry is related to (source of truth)
     */
    public function paymentSessionOutcome(): BelongsTo
    {
        return $this->belongsTo(\App\Models\JudopayPaymentSessionOutcome::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session (CIT or MIT) through the outcome relationship
     */
    public function getSessionAttribute()
    {
        return $this->paymentSessionOutcome->session ?? null;
    }

    /**
     * Get the subscription through the outcome relationship
     */
    public function getSubscriptionAttribute()
    {
        return $this->paymentSessionOutcome->subscription ?? null;
    }

    /**
     * Determine if this enquiry is for a CIT or MIT transaction
     */
    public function getTransactionTypeAttribute(): string
    {
        return $this->enquiry_type === 'webpayment' ? 'CIT' : 'MIT';
    }

    /**
     * Check if the enquiry was successful
     */
    public function isSuccessful(): bool
    {
        return $this->api_status === 'success' && $this->http_status_code === 200;
    }

    /**
     * Check if there are discrepancies between local and remote data
     */
    public function hasDiscrepancies(): bool
    {
        return $this->matches_local_record === false;
    }

    /**
     * Check if this enquiry indicates the payment was actually successful
     */
    public function isRemotelySuccessful(): bool
    {
        return $this->judopay_status === 'Success' && $this->amount_collected_remote > 0;
    }

    /**
     * Check if this enquiry indicates the payment was declined
     */
    public function isRemotelyDeclined(): bool
    {
        return $this->judopay_status === 'Declined' || $this->amount_collected_remote == 0;
    }

    /**
     * Get the bank response category based on the external bank response code
     */
    public function getBankResponseCategoryAttribute(): ?string
    {
        if (! $this->external_bank_response_code) {
            return null;
        }

        return config("judopay.bank_response_codes.{$this->external_bank_response_code}", 'UNKNOWN');
    }

    /**
     * Get a human-readable summary of the enquiry
     */
    public function getSummaryAttribute(): string
    {
        $type = $this->transaction_type;
        $identifier = $this->enquiry_identifier;
        $status = $this->judopay_status ?? $this->api_status;
        $bankCode = $this->external_bank_response_code ? " (Bank: {$this->external_bank_response_code})" : '';

        return "{$type} enquiry for {$identifier}: {$status}{$bankCode}";
    }

    /**
     * Get detailed analysis summary
     */
    public function getAnalysisSummaryAttribute(): string
    {
        $parts = [];

        if ($this->isSuccessful()) {
            $parts[] = '✅ API Success';
            $parts[] = "Status: {$this->judopay_status}";

            if ($this->amount_collected_remote !== null) {
                $parts[] = "Collected: £{$this->amount_collected_remote}";
            }

            if ($this->external_bank_response_code) {
                $category = $this->bank_response_category;
                $parts[] = "Bank: {$this->external_bank_response_code} ({$category})";
            }

            if ($this->matches_local_record === true) {
                $parts[] = '✅ Matches Local';
            } elseif ($this->matches_local_record === false) {
                $parts[] = '⚠️ Discrepancy';
            }

            if ($this->is_retryable === true) {
                $parts[] = '🔄 Retryable';
            } elseif ($this->is_retryable === false) {
                $parts[] = '🚫 Not Retryable';
            }
        } else {
            $parts[] = '❌ API Failed';
            $parts[] = "HTTP: {$this->http_status_code}";
        }

        return implode(' | ', $parts);
    }

    /**
     * Scope for successful enquiries
     */
    public function scopeSuccessful($query)
    {
        return $query->where('api_status', 'success')->where('http_status_code', 200);
    }

    /**
     * Scope for failed enquiries
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('api_status', ['failed', 'timeout', 'error']);
    }

    /**
     * Scope for enquiries with discrepancies
     */
    public function scopeWithDiscrepancies($query)
    {
        return $query->where('matches_local_record', false);
    }

    /**
     * Scope for retryable enquiries
     */
    public function scopeRetryable($query)
    {
        return $query->where('is_retryable', true);
    }

    /**
     * Scope for CIT enquiries
     */
    public function scopeCit($query)
    {
        return $query->where('enquiry_type', 'webpayment');
    }

    /**
     * Scope for MIT enquiries
     */
    public function scopeMit($query)
    {
        return $query->where('enquiry_type', 'transaction');
    }

    /**
     * Scope for recent enquiries
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('enquired_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for specific bank response codes
     */
    public function scopeWithBankCode($query, string $code)
    {
        return $query->where('external_bank_response_code', $code);
    }

    /**
     * Scope for specific enquiry reasons
     */
    public function scopeForReason($query, string $reason)
    {
        return $query->where('enquiry_reason', $reason);
    }
}
