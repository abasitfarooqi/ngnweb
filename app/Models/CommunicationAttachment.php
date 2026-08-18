<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationAttachment extends Model
{
    protected $fillable = [
        'communication_id',
        'uuid',
        'disk',
        'path',
        'filename',
        'display_name',
        'mime_type',
        'file_size',
        'checksum',
        'metadata',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array',
    ];

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
