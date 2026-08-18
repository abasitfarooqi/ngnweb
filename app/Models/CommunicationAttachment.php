<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function downloadResponse(): StreamedResponse
    {
        abort_unless(Storage::disk($this->disk)->exists($this->path), 404);

        return Storage::disk($this->disk)->download(
            $this->path,
            $this->display_name ?: $this->filename,
            array_filter(['Content-Type' => $this->mime_type])
        );
    }
}
