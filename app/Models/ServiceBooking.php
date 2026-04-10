<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceBooking extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_auth_id',
        'conversation_id',
        'submission_context',
        'enquiry_type',
        'service_type',
        'subject',
        'description',
        'requires_schedule',
        'booking_date',
        'booking_time',
        'status',
        'fullname',
        'phone',
        'reg_no',
        'email',
        'is_dealt',
        'dealt_by_user_id',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dealt_by_user_id');
    }

    public function conversation()
    {
        return $this->belongsTo(SupportConversation::class, 'conversation_id');
    }

    public static function inferEnquiryType(?string $serviceType, ?string $description): string
    {
        $haystack = Str::lower(trim((string) $serviceType.' '.(string) $description));

        return match (true) {
            Str::contains($haystack, ['e-bike', 'ebike', 'e bike', 'pedal-assist', 'pedal assist', 'electric bicycle']) => 'e_bike',
            Str::contains($haystack, ['rental', 'rent']) => 'rental',
            Str::contains($haystack, ['used bike']) => 'used_bike',
            Str::contains($haystack, ['new bike']) => 'new_bike',
            Str::contains($haystack, ['finance']) => 'finance',
            Str::contains($haystack, ['mot']) => 'mot',
            Str::contains($haystack, ['repair', 'service booking']) => 'service',
            Str::contains($haystack, ['delivery', 'recovery']) => 'recovery_delivery',
            default => 'general',
        };
    }

    /**
     * Rows that count as rental enquiries (stored enquiry_type, or legacy text in service/subject/description).
     */
    public function scopeWhereRentalEnquiry(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->where('enquiry_type', 'rental')
                ->orWhere('service_type', 'like', '%Rental%')
                ->orWhere('subject', 'like', '%Rental%')
                ->orWhere('description', 'like', '%rental%')
                ->orWhere('description', 'like', '%Rental%');
        });
    }

    /**
     * Same customer matching for portal lists: auth id, customer_id, profile id, and email fallback.
     *
     * @param  \App\Models\CustomerAuth|\Illuminate\Contracts\Auth\Authenticatable|null  $customerAuth
     */
    public function scopeForPortalCustomer(Builder $query, $customerAuth): Builder
    {
        if (! $customerAuth) {
            return $query->whereRaw('1 = 0');
        }

        $customerId = $customerAuth->customer_id ?? null;
        $customerProfileId = $customerAuth->customer?->id ?? null;
        $customerEmail = trim((string) ($customerAuth->email ?? ''));

        return $query->where(function (Builder $q) use ($customerAuth, $customerId, $customerProfileId, $customerEmail): void {
            if ($customerId) {
                $q->orWhere('customer_id', $customerId);
            }
            if ($customerProfileId && (int) $customerProfileId !== (int) $customerId) {
                $q->orWhere('customer_id', $customerProfileId);
            }
            if ($customerAuth->id) {
                $q->orWhere('customer_auth_id', $customerAuth->id);
            }
            if ($customerEmail !== '') {
                $q->orWhereRaw('LOWER(email) = ?', [strtolower($customerEmail)]);
            }
        });
    }
}
