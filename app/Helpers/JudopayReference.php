<?php

namespace App\Helpers;

use App\Models\FinanceApplication;
use App\Models\RentingBooking;

class JudopayReference
{
    public static function buildConsumerReference(string $subscribableType, $subscribable, ?string $fallback = null): string
    {
        $template = config('judopay.consumer_reference_format.'.$subscribableType);

        if ($template && $subscribable) {
            if ($subscribableType === RentingBooking::class) {
                return strtr($template, [
                    '{rental_id}' => $subscribable->id,
                    '{customer_id}' => $subscribable->customer_id,
                ]);
            }

            if ($subscribableType === FinanceApplication::class) {
                return strtr($template, [
                    '{contract_id}' => $subscribable->id,
                    '{customer_id}' => $subscribable->customer_id,
                ]);
            }
        }

        if ($fallback !== null) {
            return $fallback;
        }

        // Sensible generic fallback
        $id = is_object($subscribable) && isset($subscribable->id) ? $subscribable->id : 'NA';
        $customerId = is_object($subscribable) && isset($subscribable->customer_id) ? $subscribable->customer_id : 'NA';
        return 'REF-'.$id.'-'.$customerId;
    }
}


