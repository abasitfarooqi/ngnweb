<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;

    /** @var int Default {@see SystemCountry} id used across shop/checkout when none supplied (UK). */
    public const DEFAULT_COUNTRY_ID = 3;

    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_id',
        'last_name',
        'first_name',
        'company_name',
        'street_address',
        'street_address_plus',
        'postcode',
        'city',
        'phone_number',
        'is_default',
        'type',
        'country_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $address): void {
            if ($address->country_id === null) {
                $address->country_id = self::DEFAULT_COUNTRY_ID;
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
