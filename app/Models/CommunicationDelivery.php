<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationDelivery extends Model
{
    protected $fillable = [
        'communication_id',
        'channel',
        'status',
        'provider',
        'provider_message_id',
        'queued_at',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
        'retry_count',
        'metadata',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'retry_count' => 'integer',
        'metadata' => 'array',
    ];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
