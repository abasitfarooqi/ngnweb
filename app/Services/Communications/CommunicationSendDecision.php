<?php

namespace App\Services\Communications;

use App\Models\CommunicationDefinition;

final class CommunicationSendDecision
{
    public function __construct(
        public readonly bool $legacy,
        public readonly bool $sendEmail,
        public readonly bool $recordSnapshot,
        public readonly bool $createInbox,
        public readonly ?CommunicationDefinition $definition,
    ) {}
}
