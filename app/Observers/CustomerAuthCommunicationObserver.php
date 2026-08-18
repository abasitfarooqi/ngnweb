<?php

namespace App\Observers;

use App\Models\CustomerAuth;
use App\Services\Communications\CommunicationInboxClaimer;

class CustomerAuthCommunicationObserver
{
    public function created(CustomerAuth $customerAuth): void
    {
        app(CommunicationInboxClaimer::class)->claimFor($customerAuth);
    }
}
