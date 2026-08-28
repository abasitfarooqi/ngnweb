<?php

namespace App\Services\Communications;

use App\Models\CommunicationDefinition;
use App\Models\CommunicationPolicy;
use App\Support\Communications\CommunicationDefinitionData;
use Illuminate\Support\Facades\DB;

class CommunicationDefinitionSynchronizer
{
    /**
     * @return array{created:int, updated:int, skipped:int}
     */
    public function sync(): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $definitions = app(CommunicationDefinitionRegistry::class)->all();

        if (! app(CommunicationSchema::class)->ready()) {
            return ['created' => 0, 'updated' => 0, 'skipped' => count($definitions)];
        }

        $knownKeys = [];

        foreach ($definitions as $definition) {
            if ($definition->classification !== 'transactional') {
                $skipped++;

                continue;
            }

            $knownKeys[] = $definition->key;

            DB::transaction(function () use ($definition, &$created, &$updated): void {
                $model = CommunicationDefinition::query()->where('key', $definition->key)->first();
                $payload = $this->metadataPayload($definition);

                if ($model) {
                    unset($payload['active']);
                    $model->fill($payload)->save();
                    $updated++;
                } else {
                    $model = CommunicationDefinition::query()->create($payload);
                    $created++;
                }

                CommunicationPolicy::query()->firstOrCreate(
                    ['communication_definition_id' => $model->id],
                    $this->policyDefaults($definition)
                );
            });
        }

        if ($knownKeys !== []) {
            CommunicationDefinition::query()
                ->where('classification', 'transactional')
                ->whereNotIn('key', $knownKeys)
                ->where('active', true)
                ->update(['active' => false]);
        }

        return compact('created', 'updated', 'skipped');
    }

    /**
     * @return array<string, mixed>
     */
    private function metadataPayload(CommunicationDefinitionData $definition): array
    {
        return [
            'key' => $definition->key,
            'name' => $definition->name,
            'description' => $definition->description,
            'classification' => $definition->classification,
            'category' => $definition->category,
            'priority' => $definition->priority,
            'source_class' => $definition->sourceClass,
            'source_trigger' => $definition->sourceTrigger,
            'email_class' => $definition->emailClass,
            'template_view' => $definition->templateView,
            'recipient_summary' => $definition->recipientSummary,
            'supported_channels' => $definition->supportedChannels,
            'variables' => $definition->variables,
            'metadata' => $definition->metadata,
            'existing_email_default' => $definition->existingEmailDefault,
            'active' => $definition->activeDefault,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function policyDefaults(CommunicationDefinitionData $definition): array
    {
        return [
            'email_enabled' => $definition->emailDefault,
            'internal_inbox_enabled' => $definition->internalInboxDefault,
            'staff_copy_enabled' => false,
            'web_push_enabled' => $definition->webPushDefault,
            'mobile_push_enabled' => $definition->mobilePushDefault,
            'reply_allowed' => $definition->replyAllowedDefault,
            'enquiry_allowed' => $definition->enquiryAllowedDefault,
            'mandatory' => $definition->mandatoryDefault,
            'priority' => $definition->priority,
        ];
    }
}
