<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class JudopayCitAccess extends Model
{
    use CrudTrait;
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'customer_id',
        'passcode',
        'expires_at',
        'subscription_id',
        'admin_form_data',
        // Customer interaction tracking
        'last_accessed_at',
        'access_ip_address',
        'sms_requested_at',
        'sms_request_count',
        'sms_sids',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'admin_form_data' => 'array',
        // Customer interaction timestamps
        'last_accessed_at' => 'datetime',
        'sms_requested_at' => 'datetime',
        // SMS SIDs array
        'sms_sids' => 'array',
    ];

    protected $appends = ['link_html', 'contract_type', 'status', 'is_expired'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subscription()
    {
        return $this->belongsTo(JudopaySubscription::class, 'subscription_id');
    }

    public function getLinkHtmlAttribute()
    {
        $url = $this->getLink();

        return '<a href="'.$url.'" target="_blank">'.$url.'</a>';
    }

    public function getLink()
    {
        return route('payment.authorize', [
            'customer_id' => $this->customer_id,
            'passcode' => $this->passcode,
            'subscription_id' => $this->subscription_id,
        ]);
    }

    public function getContractTypeAttribute()
    {
        if (! $this->subscription) {
            return 'Unknown';
        }

        $subscribableType = $this->subscription->subscribable_type;

        if ($subscribableType === 'App\Models\RentingBooking') {
            return 'Rental';
        } elseif ($subscribableType === 'App\Models\FinanceApplication') {
            return 'Finance';
        }

        return 'Unknown';
    }

    public function getStatusAttribute()
    {
        return $this->isExpired() ? 'Expired' : 'Active';
    }

    public function getIsExpiredAttribute()
    {
        return $this->isExpired();
    }

    public function isExpired()
    {
        return $this->expires_at < now();
    }

    public function isValid()
    {
        return ! $this->isExpired();
    }

    /**
     * Generate a unique passcode for this access
     */
    public static function generatePasscode(): string
    {
        do {
            $passcode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT).str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('passcode', $passcode)->exists());

        return $passcode;
    }

    /**
     * Find valid access by customer_id, passcode, and subscription_id
     */
    public static function findValid($customerId, $passcode, $subscriptionId)
    {
        return self::where('customer_id', $customerId)
            ->where('passcode', $passcode)
            ->where('subscription_id', $subscriptionId)
            ->where('expires_at', '>', now())
            ->with(['customer', 'subscription.subscribable'])
            ->first();
    }

    /**
     * Create a new CIT access with default expiry (24 hours)
     */
    public static function createAccess($customerId, $subscriptionId, $expiresInHours = 24, $adminFormData = null): self
    {
        return self::create([
            'customer_id' => $customerId,
            'subscription_id' => $subscriptionId,
            'passcode' => self::generatePasscode(),
            'expires_at' => now()->addHours($expiresInHours),
            'admin_form_data' => $adminFormData,
        ]);
    }

    /**
     * Redact PII data for GDPR compliance when retiring the link
     */
    public function redactPiiData(): void
    {
        if ($this->admin_form_data) {
            $redactedData = $this->admin_form_data;
            
            // Redact PII fields
            if (isset($redactedData['customer_email'])) {
                $email = $redactedData['customer_email'];
                $redactedData['customer_email'] = substr($email, 0, 2) . '***@***' . substr($email, strrpos($email, '.'));
            }
            
            if (isset($redactedData['customer_mobile'])) {
                $mobile = $redactedData['customer_mobile'];
                $redactedData['customer_mobile'] = substr($mobile, 0, 3) . '***' . substr($mobile, -2);
            }
            
            if (isset($redactedData['customer_name'])) {
                $redactedData['customer_name'] = '[REDACTED]';
            }
            
            if (isset($redactedData['card_holder_name'])) {
                $redactedData['card_holder_name'] = '[REDACTED]';
            }
            
            if (isset($redactedData['address1'])) {
                $redactedData['address1'] = '[REDACTED]';
            }
            
            if (isset($redactedData['address2'])) {
                $redactedData['address2'] = '[REDACTED]';
            }
            
            if (isset($redactedData['city'])) {
                $redactedData['city'] = '[REDACTED]';
            }
            
            if (isset($redactedData['postcode'])) {
                $redactedData['postcode'] = '[REDACTED]';
            }
            
            // Add redaction timestamp
            $redactedData['redacted_at'] = now()->toISOString();
            $redactedData['redacted_reason'] = 'GDPR PII redaction on link retirement';
            
            $this->update(['admin_form_data' => $redactedData]);
            
            \Log::channel('judopay')->info('PII data redacted for GDPR compliance', [
                'access_id' => $this->id,
                'customer_id' => $this->customer_id,
                'subscription_id' => $this->subscription_id,
                'redacted_at' => now(),
            ]);
        }
    }
}
