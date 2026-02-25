<?php

namespace App\Observers;

use App\Models\JudopayMitPaymentSession;

class JudopayMitPaymentSessionObserver
{
    /**
     * Handle the JudopayMitPaymentSession "creating" event.
     */
    public function creating(JudopayMitPaymentSession $judopayMitPaymentSession): void
    {
        // Automatically inject authenticated user ID if available
        if (auth()->check() && !$judopayMitPaymentSession->user_id) {
            $judopayMitPaymentSession->user_id = auth()->id();
        }
    }
}