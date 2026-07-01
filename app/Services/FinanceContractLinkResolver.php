<?php

namespace App\Services;

use App\Models\FinanceApplication;

class FinanceContractLinkResolver
{
    public static function resolve(FinanceApplication $application, string $passcode, ?bool $isInsuranceOrPcn = null): ?array
    {
        if (! $application->hasLatestContractType()) {
            return null;
        }

        $isInsuranceOrPcn = $isInsuranceOrPcn ?? true;
        $params = [
            'customer_id' => $application->customer_id,
            'passcode' => $passcode,
        ];

        if ($application->is_new_latest) {
            if ($application->is_subscription) {
                $standard = route('finance.show.merged.new', $params);
                $ins = route('finance.ins.show.merged.new', $params);
            } else {
                $standard = route('finance.show.latest', $params);
                $ins = route('finance.ins.show.latest', $params);
            }
        } elseif ($application->is_used_latest) {
            if ($application->is_subscription) {
                $standard = route('finance.show.merged.used', $params);
                $ins = route('finance.ins.show.merged.used', $params);
            } else {
                $standard = route('finance.show.used.latest', $params);
                $ins = route('finance.ins.show.used.latest', $params);
            }
        } else {
            return null;
        }

        return [
            'primary' => $isInsuranceOrPcn ? $ins : $standard,
            'standard' => $standard,
            'ins' => $ins,
        ];
    }

    public static function primaryUrl(FinanceApplication $application, string $passcode, ?bool $isInsuranceOrPcn = null): ?string
    {
        $resolved = self::resolve($application, $passcode, $isInsuranceOrPcn);

        return $resolved['primary'] ?? null;
    }
}
