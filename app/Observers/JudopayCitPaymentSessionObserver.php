<?php

namespace App\Observers;

use App\Models\JudopayCitPaymentSession;

class JudopayCitPaymentSessionObserver
{
    /**
     * Handle the JudopayCitPaymentSession "creating" event.
     */
    public function creating(JudopayCitPaymentSession $judopayCitPaymentSession): void
    {
        // Automatically inject authenticated user ID if available
        if (auth()->check() && !$judopayCitPaymentSession->user_id) {
            $judopayCitPaymentSession->user_id = auth()->id();
        }
    }
}