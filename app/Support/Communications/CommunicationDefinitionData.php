<?php

namespace App\Support\Communications;

final class CommunicationDefinitionData
{
    /**
     * @param  list<string>  $supportedChannels
     * @param  list<string>  $variables
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $description = '',
        public readonly string $classification = 'transactional',
        public readonly string $category = 'general',
        public readonly string $priority = 'normal',
        public readonly ?string $sourceClass = null,
        public readonly ?string $sourceTrigger = null,
        public readonly ?string $emailClass = null,
        public readonly ?string $templateView = null,
        public readonly ?string $recipientSummary = null,
        public readonly array $supportedChannels = ['email', 'internal_inbox'],
        public readonly array $variables = [],
        public readonly array $metadata = [],
        public readonly bool $existingEmailDefault = true,
        public readonly bool $emailDefault = true,
        public readonly bool $internalInboxDefault = false,
        public readonly bool $webPushDefault = false,
        public readonly bool $mobilePushDefault = false,
        public readonly bool $replyAllowedDefault = false,
        public readonly bool $enquiryAllowedDefault = false,
        public readonly bool $mandatoryDefault = false,
        public readonly bool $activeDefault = true,
    ) {}
}
