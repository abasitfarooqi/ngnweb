<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationAudit extends Model
{
    protected $fillable = [
        'actor_user_id',
        'communication_definition_id',
        'event',
        'field',
        'old_value',
        'new_value',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function actorLabel(): string
    {
        $metadata = is_array($this->metadata) ? $this->metadata : [];
        $name = trim((string) ($metadata['actor_name'] ?? ''));
        $id = $metadata['actor_user_id'] ?? $this->actor_user_id;

        if ($name !== '') {
            return $id !== null && $id !== '' ? "{$name} (#{$id})" : $name;
        }

        if ($this->relationLoaded('actor') && $this->actor) {
            $resolved = trim((string) ($this->actor->full_name ?? ''));

            if ($resolved === '' && ($this->actor->first_name ?? null) !== null) {
                $resolved = trim(trim((string) $this->actor->first_name).' '.trim((string) ($this->actor->last_name ?? '')));
            }

            if ($resolved !== '') {
                return $this->actor_user_id ? "{$resolved} (#{$this->actor_user_id})" : $resolved;
            }
        }

        return $this->actor_user_id ? 'User #'.$this->actor_user_id : 'System';
    }
}
