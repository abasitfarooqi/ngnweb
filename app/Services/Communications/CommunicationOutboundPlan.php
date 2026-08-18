<?php

namespace App\Services\Communications;

use App\Models\Communication;
use App\Models\CustomerAuth;

final class CommunicationOutboundPlan
{
    public function __construct(
        public readonly CommunicationSendDecision $decision,
        public readonly bool $sendEmail,
        public readonly bool $legacyEmailFallback,
        public readonly ?string $customerEmail,
        public readonly ?CustomerAuth $customerAuth,
        public ?Communication $communication = null,
    ) {}
}
