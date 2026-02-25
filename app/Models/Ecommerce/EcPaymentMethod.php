<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EcPaymentMethod extends Model
{
    protected $table = 'ec_payment_methods';

    protected $fillable = [
        'title',
        'slug',
        'logo',
        'link_url',
        'instructions',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(EcOrder::class, 'payment_method_id');
    }

    // Scope for active payment methods
    public function scopeActive($query)
    {
        return $query->where('is_enabled', true);
    }

    public function getNameAttribute()
    {
        return $this->title;
    }
}
