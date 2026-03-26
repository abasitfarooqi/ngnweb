<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    protected $table = 'customer_profiles';

    protected $fillable = [
        'customer_auth_id',
        'first_name',
        'last_name',
        'phone',
        'whatsapp',
        'dob',
        'nationality',
        'license_number',
        'license_expiry_date',
        'license_issuance_authority',
        'license_issuance_date',
        'address',
        'postcode',
        'city',
        'country',
        'emergency_contact',
        'preferred_branch_id',
        'verification_status',
        'verified_at',
        'verification_expires_at',
        'locked_fields',
        'reputation_note',
        'rating',
        'is_register',
    ];

    protected $casts = [
        'dob' => 'date',
        'license_expiry_date' => 'date',
        'license_issuance_date' => 'date',
        'verified_at' => 'datetime',
        'verification_expires_at' => 'datetime',
        'locked_fields' => 'array',
        'emergency_contact' => 'array',
        'is_register' => 'boolean',
    ];

    public function customerAuth(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'customer_auth_id');
    }

    public function isFieldLocked(string $field): bool
    {
        $locked = $this->locked_fields ?? [];
        if (! is_array($locked)) {
            $locked = [];
        }

        return in_array($field, $locked, true);
    }

    /**
     * Rentals for the linked customers row (customer_auths.customer_id).
     */
    public function rentingBookings()
    {
        $cid = $this->customerAuth?->customer_id;
        if (! $cid) {
            return RentingBooking::query()->whereRaw('0 = 1');
        }

        return RentingBooking::where('customer_id', $cid);
    }
}
