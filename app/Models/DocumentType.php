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
