<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';

    protected $casts = [
        'is_enabled' => 'bool',
    ];

    protected $fillable = [
        'title',
        'slug',
        'logo',
        'link_url',
        'instructions',
        'is_enabled',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}
