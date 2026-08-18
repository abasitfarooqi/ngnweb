<?php

namespace App\Services\Communications;

use App\Models\CommunicationAudit;
use App\Models\CommunicationDefinition;
use Illuminate\Contracts\Auth\Authenticatable;

class CommunicationAuditRecorder
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(
        string $event,
        ?CommunicationDefinition $definition = null,
        ?string $field = null,
        mixed $oldValue = null,
        mixed $newValue = null,
        ?string $reason = null,
        array $metadata = [],
        ?Authenticatable $actor = null,
    ): ?CommunicationAudit {
        if (! app(CommunicationSchema::class)->ready()) {
            return null;
        }

        $actor ??= function_exists('backpack_user') ? backpack_user() : auth()->user();
        $actorSnapshot = $this->actorSnapshot($actor);

        return CommunicationAudit::query()->create([
            'actor_user_id' => $actorSnapshot['actor_user_id'],
            'communication_definition_id' => $definition?->id,
            'event' => $event,
            'field' => $field,
            'old_value' => $this->stringify($oldValue),
            'new_value' => $this->stringify($newValue),
            'reason' => $reason,
            'metadata' => array_merge($metadata, $actorSnapshot),
        ]);
    }

    /**
     * @return array{actor_user_id: int|string|null, actor_name: string, actor_email: string|null}
     */
    private function actorSnapshot(?Authenticatable $actor): array
    {
        if ($actor === null) {
            return [
                'actor_user_id' => null,
                'actor_name' => 'System',
                'actor_email' => null,
            ];
        }

        $id = $actor->getAuthIdentifier();
        $name = '';

        if (isset($actor->full_name) && trim((string) $actor->full_name) !== '') {
            $name = trim((string) $actor->full_name);
        } elseif (isset($actor->first_name) || isset($actor->last_name)) {
            $name = trim(trim((string) ($actor->first_name ?? '')).' '.trim((string) ($actor->last_name ?? '')));
        }

        if ($name === '' && isset($actor->name) && trim((string) $actor->name) !== '') {
            $name = trim((string) $actor->name);
        }

        if ($name === '' && isset($actor->email) && trim((string) $actor->email) !== '') {
            $name = trim((string) $actor->email);
        }

        if ($name === '' && $id !== null) {
            $name = 'User #'.$id;
        }

        return [
            'actor_user_id' => $id,
            'actor_name' => $name !== '' ? $name : 'Unknown staff',
            'actor_email' => isset($actor->email) ? (string) $actor->email : null,
        ];
    }

    private function stringify(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_THROW_ON_ERROR);
    }
}
