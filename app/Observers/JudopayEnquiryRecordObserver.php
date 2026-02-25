<?php

namespace App\Observers;

use App\Models\JudopayEnquiryRecord;

class JudopayEnquiryRecordObserver
{
    /**
     * Handle the JudopayEnquiryRecord "creating" event.
     */
    public function creating(JudopayEnquiryRecord $judopayEnquiryRecord): void
    {
        // Automatically inject authenticated user ID if available
        if (auth()->check() && !$judopayEnquiryRecord->user_id) {
            $judopayEnquiryRecord->user_id = auth()->id();
        }
    }
}