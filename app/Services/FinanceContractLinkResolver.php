<?php

namespace App\Services;

use App\Models\ContractAccess;
use App\Models\FinanceApplication;

class FinanceContractLinkResolver
{
    /**
     * Catalogue of latest contract URLs staff may issue (new, new+subs, used+subs).
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
     * Which catalogue key matches the finance application contract flags.
     */
    public static function matchingKey(FinanceApplication $application): ?string
    {
        if (! $application->hasLatestContractType()) {
            return null;
        }

        if ($application->is_new_latest && $application->is_subscription) {
            return 'sale_subscription_new';
        }

        if ($application->is_new_latest) {
            return 'sale_latest';
        }

        // Used latest (with or without subscription): merged used template.
        if ($application->is_used_latest) {
            return 'sale_subscription_used';
        }

        return null;
    }

    /**
     * Only the contract link that was chosen when the booking/application was made.
     *
     * @return list<array{key: string, label: string, url: string, is_customer: bool}>
     */
    public static function matchingAccessLinks(FinanceApplication $application, string $passcode): array
    {
        $customerId = (int) ($application->customer_id ?? 0);
        if ($customerId < 1 || $passcode === '') {
            return [];
        }

        $key = self::matchingKey($application);
        if ($key === null) {
            return [];
        }

        foreach (self::accessLinks($customerId, $passcode) as $link) {
            if ($link['key'] === $key) {
                $link['is_customer'] = true;

                return [$link];
            }
        }

        return [];
    }

    /**
     * Resolve links for a Contract Access row: use application type when present.
     *
     * @return list<array{key: string, label: string, url: string, is_customer?: bool}>
     */
    public static function linksForContractAccess(ContractAccess $access): array
    {
        $passcode = (string) ($access->passcode ?? '');
        $customerId = (int) ($access->customer_id ?? 0);

        if ($passcode === '' || $customerId < 1) {
            return [];
        }

        $application = $access->relationLoaded('application')
            ? $access->application
            : $access->application()->first();

        if ($application instanceof FinanceApplication) {
            return self::matchingAccessLinks($application, $passcode);
        }

        return [];
    }

    /**
     * @return array{
     *     primary: string,
     *     primary_key: string,
     *     standard: string,
     *     ins: string,
     *     sale_latest: string,
     *     sale_subscription_new: string,
     *     sale_subscription_used: string,
     *     links: list<array{key: string, label: string, url: string, is_customer: bool}>
     * }|null
     */
    public static function resolve(FinanceApplication $application, string $passcode, ?bool $isInsuranceOrPcn = null): ?array
    {
        $customerId = (int) ($application->customer_id ?? 0);
        if ($customerId < 1 || $passcode === '') {
            return null;
        }

        $links = self::matchingAccessLinks($application, $passcode);
        if ($links === []) {
            return null;
        }

        $primary = $links[0];
        $catalogue = collect(self::accessLinks($customerId, $passcode))->keyBy('key');

        return [
            'primary' => $primary['url'],
            'primary_key' => $primary['key'],
            // Legacy aliases kept for older callers (no insurance variants any more).
            'standard' => $primary['url'],
            'ins' => $primary['url'],
            'sale_latest' => $catalogue['sale_latest']['url'] ?? $primary['url'],
            'sale_subscription_new' => $catalogue['sale_subscription_new']['url'] ?? $primary['url'],
            'sale_subscription_used' => $catalogue['sale_subscription_used']['url'] ?? $primary['url'],
            'links' => $links,
        ];
    }

    public static function primaryUrl(FinanceApplication $application, string $passcode, ?bool $isInsuranceOrPcn = null): ?string
    {
        $resolved = self::resolve($application, $passcode, $isInsuranceOrPcn);

        return $resolved['primary'] ?? null;
    }

    /**
     * Links for staff copy-paste on an application: only the booked contract type.
     *
     * @return list<array{key: string, label: string, url: string, is_customer: bool}>
     */
    public static function linksForApplication(FinanceApplication $application, string $passcode): array
    {
        return self::matchingAccessLinks($application, $passcode);
    }
}
