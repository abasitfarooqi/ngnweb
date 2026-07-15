<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDeviceToken extends Model
{
    protected $table = 'customer_device_tokens';

    protected $fillable = [
        'customer_auth_id',
        'token',
        'provider',
        'platform',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function customerAuth(): BelongsTo
    {
        return $this->belongsTo(CustomerAuth::class, 'customer_auth_id');
    }
}
