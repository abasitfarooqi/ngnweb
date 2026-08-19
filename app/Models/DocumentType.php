<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'is_mandatory',
        'required_for',
        'validation_rules',
        'sort_order',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'required_for' => 'array',
        'validation_rules' => 'array',
    ];

    /** Issued by NGN (email/PDF). Never a customer upload slot. */
    public const STAFF_ISSUED_CODES = [
        'loyalty_scheme_policy',
        'rental_agreement',
        'ebike_battery_safety_leaflet',
    ];

    public const STAFF_ISSUED_NAMES = [
        'Loyalty Scheme Policy',
        'Rental Agreement',
        'E-Bike Battery Safety Leaflet',
    ];

    public function scopeForCustomerUpload($query)
    {
        return $query
            ->where(function ($q) {
                $q->whereNull('code')->orWhereNotIn('code', self::STAFF_ISSUED_CODES);
            })
            ->where(function ($q) {
                $q->whereNull('slug')->orWhereNotIn('slug', self::STAFF_ISSUED_CODES);
            })
            ->where(function ($q) {
                $q->whereNull('name')->orWhereNotIn('name', self::STAFF_ISSUED_NAMES);
            });
    }

    /**
     * Scope: document types required for rental.
     */
    public function scopeForRental($query)
    {
        return $query->where(function ($q) {
            $q->whereJsonContains('required_for', 'rental')
                ->orWhere('slug', 'like', '%rental%')
                ->orWhere('name', 'like', '%licence%')
                ->orWhere('name', 'like', '%address%')
                ->orWhere('name', 'like', '%CBT%')
                ->orWhere('name', 'like', '%insurance%');
        });
    }

    /**
     * Scope: document types required for finance.
     */
    public function scopeForFinance($query)
    {
        return $query->where(function ($q) {
            $q->whereJsonContains('required_for', 'finance')
                ->orWhere('slug', 'like', '%finance%')
                ->orWhere('name', 'like', '%finance%');
        });
    }
}
