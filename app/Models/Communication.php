<?php

namespace App\Models;

use App\Support\Communications\CommunicationPreviewText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Communication extends Model
{
    protected $fillable = [
        'uuid',
        'communication_definition_id',
        'communication_key',
        'customer_id',
        'customer_auth_id',
        'recipient_email',
        'subject',
        'title',
        'preview',
        'content_html',
        'content_text',
        'structured_content',
        'payload_snapshot',
        'policy_snapshot',
        'source_type',
        'source_id',
        'correlation_id',
        'template_version',
        'priority',
        'category',
    ];

    protected $casts = [
        'structured_content' => 'array',
        'payload_snapshot' => 'array',
        'policy_snapshot' => 'array',
    ];

    public function getPreviewAttribute(?string $value): string
    {
        $fallback = trim((string) ($this->attributes['subject'] ?? $this->attributes['title'] ?? ''));

        return CommunicationPreviewText::readable($value, $fallback);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(CommunicationDefinition::class, 'communication_definition_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CommunicationRecipient::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(CommunicationDelivery::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(CommunicationAttachment::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(CommunicationReply::class);
    }
}
