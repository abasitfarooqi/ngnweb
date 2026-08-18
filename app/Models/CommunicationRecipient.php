<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationRecipient extends Model
{
    protected $fillable = [
        'communication_id',
        'customer_auth_id',
        'seen_at',
        'read_at',
        'archived_at',
    ];

    protected $casts = [
        'seen_at' => 'datetime',
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
