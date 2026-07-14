<?php

namespace App\Services;

use App\Models\FinanceApplication;

class FinanceContractLinkResolver
{
    /**
     * The only three contract-access URLs we issue to customers.
     *
     * @return list<array{key: string, label: string, url: string}>
     */
    public static function accessLinks(int $customerId, string $passcode): array
    {
        $params = [
            'customer_id' => $customerId,
            'passcode' => $passcode,
        ];

        return [
            [
                'key' => 'sale_latest',
                'label' => 'New Motorcycle – 12 Month (Latest) Sale Contract',
                'url' => route('finance.show.latest', $params),
            ],
            [
                'key' => 'sale_subscription_new',
                'label' => 'New Motorcycle – Sale + Subscription',
                'url' => route('finance.show.merged.new', $params),
            ],
            [
                'key' => 'sale_subscription_used',
                'label' => 'Used Motorcycle – Sale + Subscription',
                'url' => route('finance.show.merged.used', $params),
            ],
        ];
    }

    /**
     * @return array{
     *     primary: string,
     *     standard: string,
     *     ins: string,
     *     sale_latest: string,
     *     sale_subscription_new: string,
     *     sale_subscription_used: string,
     *     links: list<array{key: string, label: string, url: string}>
     * }|null
     */
    public static function resolve(FinanceApplication $application, string $passcode, ?bool $isInsuranceOrPcn = null): ?array
    {
        if (! $application->hasLatestContractType()) {
            return null;
        }

        $customerId = (int) ($application->customer_id ?? 0);
        if ($customerId < 1 || $passcode === '') {
            return null;
        }

        $links = self::accessLinks($customerId, $passcode);
        $byKey = collect($links)->keyBy('key');

        $saleLatest = $byKey['sale_latest']['url'];
        $mergedNew = $byKey['sale_subscription_new']['url'];
        $mergedUsed = $byKey['sale_subscription_used']['url'];

        // Latest contract only — never old /finance or INS variants for customers.
        if ($application->is_new_latest && $application->is_subscription) {
            $primaryKey = 'sale_subscription_new';
            $primary = $mergedNew;
        } elseif ($application->is_new_latest) {
            $primaryKey = 'sale_latest';
            $primary = $saleLatest;
        } else {
            // is_used_latest (with or without subscription flag): merged used is the
            // customer-facing used template in the three-link set.
            $primaryKey = 'sale_subscription_used';
            $primary = $mergedUsed;
        }

        $linksForCustomer = array_map(function (array $link) use ($primaryKey) {
            $link['is_customer'] = $link['key'] === $primaryKey;

            return $link;
        }, $links);

        return [
            'primary' => $primary,
            'primary_key' => $primaryKey,
            // Legacy aliases kept for older callers (no insurance variants any more).
            'standard' => $primary,
            'ins' => $primary,
            'sale_latest' => $saleLatest,
            'sale_subscription_new' => $mergedNew,
            'sale_subscription_used' => $mergedUsed,
            'links' => $linksForCustomer,
        ];
    }

    public static function primaryUrl(FinanceApplication $application, string $passcode, ?bool $isInsuranceOrPcn = null): ?string
    {
        $resolved = self::resolve($application, $passcode, $isInsuranceOrPcn);

        return $resolved['primary'] ?? null;
    }

    /**
     * Links for staff copy-paste on an application: customer email link first.
     *
     * @return list<array{key: string, label: string, url: string, is_customer: bool}>
     */
    public static function linksForApplication(FinanceApplication $application, string $passcode): array
    {
        $resolved = self::resolve($application, $passcode);

        if (! $resolved) {
            return [];
        }

        $links = $resolved['links'];
        usort($links, fn (array $a, array $b) => ((int) $b['is_customer']) <=> ((int) $a['is_customer']));

        return $links;
    }
}
