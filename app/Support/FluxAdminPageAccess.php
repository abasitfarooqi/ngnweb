<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Menu, module-home and page gates for Flux Admin, aligned with
 * resources/views/vendor/backpack/ui/inc/menu_items.blade.php.
 */
final class FluxAdminPageAccess
{
    public static function allows(?Authenticatable $user, ?string $routeName, mixed $module = null): bool
    {
        if ($user === null) {
            return false;
        }

        return self::allowsRequirement($user, self::requirementForRoute($routeName, $module));
    }

    /** @param  array<string, mixed>  $requirement */
    public static function allowsRequirement(?Authenticatable $user, array $requirement): bool
    {
        if ($user === null) {
            return false;
        }

        if (FluxAdminAccess::isSuperAdmin($user)) {
            return true;
        }

        if (! empty($requirement['open'])) {
            return true;
        }

        if (! empty($requirement['super_admin'])) {
            return false;
        }

        if (! empty($requirement['renting_referral_investigate'])) {
            return RentingReferralAccess::canInvestigate($user);
        }

        if (! empty($requirement['role'])) {
            return FluxAdminAccess::userHasNamedRole($user, (string) $requirement['role']);
        }

        if (! empty($requirement['any']) && is_array($requirement['any'])) {
            foreach ($requirement['any'] as $permission) {
                if (self::userHasPermission($user, (string) $permission)) {
                    return true;
                }
            }

            return false;
        }

        $permission = $requirement['permission'] ?? null;

        if (is_string($permission) && $permission !== '') {
            return self::userHasPermission($user, $permission);
        }

        return false;
    }

    /**
     * @return array{open?: bool, super_admin?: bool, role?: string, permission?: string, any?: list<string>}
     */
    public static function requirementForRoute(?string $routeName, mixed $module = null): array
    {
        if (! is_string($routeName) || $routeName === '') {
            return ['open' => true];
        }

        $name = str_starts_with($routeName, 'flux-admin.')
            ? substr($routeName, strlen('flux-admin.'))
            : $routeName;

        if ($name === 'modules.show') {
            return self::moduleRequirement(is_string($module) ? $module : (string) request()->route('module'));
        }

        foreach (self::routeMap() as $prefix => $requirement) {
            if ($name === $prefix || str_starts_with($name, $prefix.'.') || str_starts_with($name, $prefix.'-')) {
                return $requirement;
            }
        }

        return ['super_admin' => true];
    }

    /**
     * @return array{open?: bool, super_admin?: bool, role?: string, permission?: string, any?: list<string>}
     */
    public static function moduleRequirement(string $module): array
    {
        return match ($module) {
            'finance' => ['permission' => 'see-menu-finance'],
            'rentals' => ['permission' => 'see-menu-rentals'],
            'pcn' => ['permission' => 'see-menu-pcns'],
            'vehicles' => ['permission' => 'see-menu-vehicles'],
            'vehicle-records' => ['permission' => 'see-menu-commons'],
            'customers' => ['permission' => 'see-menu-commons'],
            'services' => ['permission' => 'see-menu-services-and-repairs-and-report'],
            'club' => ['role' => 'Club Member Access'],
            'delivery' => ['any' => ['see-menu-vehicles', 'see-menu-commons']],
            'claims' => ['permission' => 'see-menu-claims'],
            'ecommerce' => ['permission' => 'see-menu-ecommerce'],
            'spare-parts', 'inventory' => ['permission' => 'see-menu-inventory'],
            'orders', 'misc' => ['role' => 'Admin'],
            'blog', 'chat', 'surveys' => ['permission' => 'see-menu-commons'],
            'b2b' => ['permission' => 'see-menu-b2b'],
            'security' => ['permission' => 'see-menu-security'],
            'permissions' => ['super_admin' => true],
            'judopay' => ['any' => ['see-judopay-home', 'see-judopay']],
            default => ['open' => true],
        };
    }

    /**
     * @param  list<array<string, mixed>>  $links
     * @return list<array<string, mixed>>
     */
    public static function visibleLinks(array $links): array
    {
        $user = FluxAdminAccess::user();

        return array_values(array_filter($links, function (array $link) use ($user) {
            if (! empty($link['super_admin']) || ! empty($link['role']) || ! empty($link['permission']) || ! empty($link['any'])) {
                return self::allowsRequirement($user, $link);
            }

            $route = $link['route'] ?? null;

            return is_string($route) && $route !== ''
                ? self::allows($user, $route)
                : true;
        }));
    }

    public static function userHasPermission(Authenticatable $user, string $permission): bool
    {
        try {
            return method_exists($user, 'can') && $user->can($permission);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Longest prefix first.
     *
     * @return array<string, array{open?: bool, super_admin?: bool, role?: string, permission?: string, any?: list<string>}>
     */
    private static function routeMap(): array
    {
        $p = fn (string $permission) => ['permission' => $permission];
        $sa = ['super_admin' => true];
        $admin = ['role' => 'Admin'];
        $judo = ['any' => ['see-judopay-home', 'see-judopay']];

        $map = [
            'dashboard' => ['open' => true],
            'dashboard.path' => ['open' => true],
            'search' => ['open' => true],
            'unread-badges' => ['open' => true],
            'logout' => ['open' => true],
            'director-command-centre' => ['renting_referral_investigate' => true],

            'communications' => $sa,
            'users' => $sa,
            'user' => $sa,
            'roles' => $sa,
            'role' => $sa,
            'permissions' => $sa,
            'permission' => $sa,
            'backpack.user' => $sa,
            'backpack.role' => $sa,
            'backpack.permission' => $sa,

            'club-purchases' => $sa,
            'club-redemptions' => $sa,
            'club-spending-payments' => $sa,
            'club-spending' => $sa,
            'club-members' => ['role' => 'Club Member Access'],
            'club-member-vehicles' => $p('see-menu-commons'),
            'club' => $sa,
            'dev-club-otp' => $sa,
            'backpack.club-member-spending-payment' => $sa,
            'backpack.club-member-purchase' => $sa,
            'backpack.club-member-spending' => $sa,
            'backpack.club-member-redeem' => $sa,
            'backpack.clubmembers-details' => $p('see-menu-commons'),
            'backpack.club-member' => $sa,
            'backpack.dev-club-otp' => $sa,

            'finance' => $p('see-menu-finance'),
            'contract-access' => $p('see-menu-finance'),
            'application-items' => $p('see-menu-finance'),
            'contract-extra-item' => $p('see-menu-finance'),
            'backpack.finance-application' => $p('see-menu-finance'),
            'backpack.application-item' => $p('see-menu-finance'),
            'backpack.contract-extra-item' => $p('see-menu-finance'),

            'rental-referral-investigation' => ['renting_referral_investigate' => true],
            'rental-weekly-follow-up-report' => ['renting_referral_investigate' => true],
            'rental-referrals' => $p('see-menu-rentals'),
            'rental-operations' => $p('see-menu-rentals'),
            'rental-due-payments' => $p('see-menu-rentals'),
            'rental-terminate' => $p('see-menu-rentals'),
            'rentals' => $p('see-menu-rentals'),
            'new-booking' => $p('see-menu-rentals'),
            'inactive-bookings' => $p('see-menu-rentals'),
            'ended-with-pendings' => $p('see-menu-rentals'),
            'all-bookings' => $p('see-menu-rentals'),
            'motorbike-pricing' => $p('see-menu-rentals'),
            'renting-pricing' => $p('see-menu-rentals'),
            'upload-document-links' => $p('see-menu-rentals'),
            'agreement-access' => $p('see-menu-rentals'),
            'active-rentals' => $p('see-menu-rentals'),
            'service-videos' => $p('see-menu-rentals'),
            'booking-invoices' => $p('see-menu-rentals'),
            'booking-invoice-dates' => $p('see-menu-rentals'),
            'change-start-date' => $p('see-menu-rentals'),
            'adjust-weekday' => $p('see-menu-rentals'),
            'active-bookings-summary' => $admin,
            'backpack.ngn-renting-booking' => $p('see-menu-rentals'),
            'backpack.booking-invoice' => $p('see-menu-rentals'),
            'backpack.upload-document-access' => $p('see-menu-rentals'),
            'backpack.rental-terminate-access' => $p('see-menu-rentals'),
            'backpack.renting-service-video' => $p('see-menu-rentals'),

            'ebikes' => ['any' => ['see-menu-rentals', 'see-menu-vehicles']],

            'pcn-updates' => $p('see-menu-pcns'),
            'pcn-tol-requests' => $p('see-menu-pcns'),
            'pcn-dashboard' => $p('see-menu-pcns'),
            'pcn' => $p('see-menu-pcns'),
            'backpack.pcn-case-update' => $p('see-menu-pcns'),
            'backpack.pcn-case-exp' => $p('see-menu-pcns'),
            'backpack.pcn-case' => $p('see-menu-pcns'),
            'backpack.pcn-tol-request' => $p('see-menu-pcns'),

            'motorbikes-dvla' => $p('see-menu-vehicles'),
            'motorbikes' => $p('see-menu-vehicles'),
            'motorbike-compliance' => $p('see-menu-vehicles'),
            'motorbike-new' => $p('see-menu-vehicles'),
            'vehicle-notifications' => $p('see-menu-vehicles'),
            'recovered-motorbikes' => $p('see-menu-vehicles'),
            'sale' => $p('see-menu-vehicles'),
            'motorbike-sales' => $p('see-menu-vehicles'),
            'motorbike-for-sale' => $p('see-menu-vehicles'),
            'backpack.motorbike-available' => $p('see-menu-vehicles'),
            'backpack.motorbike-annual-compliance-m' => $p('see-menu-vehicles'),
            'backpack.motorbike-annual-compliance' => $p('see-menu-vehicles'),
            'backpack.vehicle-database' => $p('see-menu-vehicles'),
            'backpack.vehicle-notification' => $p('see-menu-vehicles'),
            'backpack.motorbike-list' => $p('see-menu-vehicles'),
            'backpack.motorbike' => $p('see-menu-vehicles'),
            'backpack.recovered-motorbike' => $p('see-menu-vehicles'),
            'backpack.new-motorbike' => $p('see-menu-vehicles'),
            'backpack.motorbikes-sale' => $p('see-menu-vehicles'),
            'backpack.new-motorbikes-for-sale' => $p('see-menu-vehicles'),

            'motorbike-cat-b' => $p('see-menu-commons'),
            'vehicle-history' => $p('see-menu-commons'),
            'company-vehicles' => $p('see-menu-commons'),
            'total-vehicles' => $p('see-menu-commons'),
            'backpack.company-vehicle' => $p('see-menu-commons'),
            'backpack.motorbike-record-view' => $p('see-menu-commons'),

            'customers' => $p('see-menu-commons'),
            'customer-documents' => $p('see-menu-commons'),
            'backpack.customer-document' => $p('see-menu-commons'),
            'backpack.customer' => $p('see-menu-commons'),

            'customer-appointments' => $p('see-menu-services-and-repairs-and-report'),
            'motorbike-repairs' => $p('see-menu-services-and-repairs-and-report'),
            'motorbike-repair-updates' => $p('see-menu-services-and-repairs-and-report'),
            'backpack.motorbike-repair-update' => $p('see-menu-services-and-repairs-and-report'),
            'backpack.motorbike-repair' => $p('see-menu-services-and-repairs-and-report'),

            'service-bookings' => $p('see-menu-commons'),
            'backpack.service-booking' => $p('see-menu-commons'),

            'delivery-enquiries' => ['any' => ['see-menu-vehicles', 'see-menu-commons']],
            'vehicle-delivery-orders' => $admin,
            'backpack.motorbike-delivery-order-enquiries' => ['any' => ['see-menu-vehicles', 'see-menu-commons']],
            'backpack.vehicle-delivery-order' => $admin,

            'used-purchases' => $p('see-menu-commons'),
            'backpack.used-vehicle-seller' => $p('see-menu-commons'),

            'mot-bookings' => $p('see-menu-mot-bookings'),
            'mot-checker' => $p('see-menu-mot-bookings'),
            'mot-stats' => $p('see-menu-mot-bookings'),
            'mot' => $p('see-menu-mot-bookings'),
            'backpack.mot-booking' => $p('see-menu-mot-bookings'),

            'motorbike-claims' => $p('see-menu-claims'),
            'backpack.claim-motorbike' => $p('see-menu-claims'),

            'shop-orders' => $p('see-menu-ecommerce'),
            'spare-part-orders' => $p('see-menu-ecommerce'),
            'ec-orders' => $p('see-menu-ecommerce'),
            'store-front' => $p('see-menu-ecommerce'),
            'backpack.ec-order' => $p('see-menu-ecommerce'),

            'sp-parts' => $p('see-menu-inventory'),
            'sp-makes' => $p('see-menu-inventory'),
            'sp-models' => $p('see-menu-inventory'),
            'sp-fitments' => $p('see-menu-inventory'),
            'sp-assemblies' => $p('see-menu-inventory'),
            'sp-assembly-parts' => $p('see-menu-inventory'),
            'sp-stock-movements' => $p('see-menu-inventory'),
            'inventory-products' => $p('see-menu-inventory'),
            'inventory-stock-movements' => $p('see-menu-inventory'),
            'inventory-brands' => $p('see-menu-inventory'),
            'inventory-categories' => $p('see-menu-inventory'),
            'inventory-models' => $p('see-menu-inventory'),
            'oxford-products' => $p('see-menu-inventory'),
            'purchase-requests' => $p('see-menu-inventory'),
            'purchase-request-items' => $admin,
            'backpack.sp-make' => $p('see-menu-inventory'),
            'backpack.sp-model' => $p('see-menu-inventory'),
            'backpack.sp-fitment' => $p('see-menu-inventory'),
            'backpack.sp-assembly-part' => $p('see-menu-inventory'),
            'backpack.sp-assembly' => $p('see-menu-inventory'),
            'backpack.sp-part' => $p('see-menu-inventory'),
            'backpack.sp-stock-movement' => $p('see-menu-inventory'),
            'backpack.sp-stock-handler' => $p('see-menu-inventory'),
            'backpack.ngn-product-management' => $p('see-menu-inventory'),
            'backpack.ngn-inventory-management' => $p('see-menu-inventory'),
            'backpack.ngn-stock-movement' => $p('see-menu-inventory'),
            'backpack.ngn-stock-handler' => $p('see-menu-inventory'),
            'backpack.ngn-product' => $p('see-menu-inventory'),
            'backpack.ngn-category' => $p('see-menu-inventory'),
            'backpack.ngn-model' => $p('see-menu-inventory'),
            'backpack.ngn-brand' => $p('see-menu-inventory'),
            'backpack.create-stock-logs' => $p('see-menu-inventory'),
            'backpack.purchase-request-item' => $admin,
            'backpack.purchase-request' => $p('see-menu-inventory'),

            'ds-orders' => $admin,
            'ds-order-items' => $admin,
            'digital-invoices' => $admin,
            'digital-invoice-items' => $admin,
            'backpack.ngn-digital-invoice-item' => $admin,
            'backpack.ngn-digital-invoice' => $admin,

            'blog-posts' => $p('see-menu-commons'),
            'blog-categories' => $p('see-menu-commons'),
            'blog-tags' => $p('see-menu-commons'),
            'backpack.blog-post' => $p('see-menu-commons'),
            'backpack.blog-category' => $p('see-menu-commons'),
            'backpack.blog-tag' => $p('see-menu-commons'),

            'support-inbox' => $p('see-menu-commons'),
            'support-conversations' => $p('see-menu-commons'),
            'support-messages' => $p('see-menu-commons'),
            'contact-queries' => $p('see-menu-commons'),
            'careers' => $p('see-menu-commons'),
            'backpack.support-conversation' => $p('see-menu-commons'),
            'backpack.support-message' => $p('see-menu-commons'),
            'backpack.contact-query' => $p('see-menu-commons'),
            'backpack.ngn-career' => $p('see-menu-commons'),

            'inventory-partners' => $p('see-menu-b2b'),
            'backpack.ngn-partner' => $p('see-menu-b2b'),

            'survey-questions' => $p('see-menu-commons'),
            'survey-options' => $p('see-menu-commons'),
            'survey-responses' => $p('see-menu-commons'),
            'survey-answers' => $p('see-menu-commons'),
            'surveys' => $p('see-menu-commons'),
            'spaces-vault' => $admin,
            'bookings-management' => $p('see-menu-rentals'),
            'rental-terminate-links' => $p('see-menu-rentals'),
            'contract-extra-items' => $p('see-menu-finance'),
            'judopay.cit-refund' => ['permission' => 'judopay-can-refund'],
            'judopay-recurring' => $judo,
            'judopay-mit-dashboard' => ['permission' => 'can-run-mit'],
            'judopay-weekly-queue' => ['permission' => 'see-weekly-queue'],
            'backpack.survey-question' => $p('see-menu-commons'),
            'backpack.survey-option' => $p('see-menu-commons'),
            'backpack.survey-response' => $p('see-menu-commons'),
            'backpack.survey-answer' => $p('see-menu-commons'),
            'backpack.survey' => $p('see-menu-commons'),

            'calendar' => $admin,
            'employee-schedules' => $admin,
            'agent-settings' => $admin,
            'branches' => $admin,
            'vehicle-issuances' => $admin,
            'queue-monitor' => $admin,
            'backpack.calander' => $admin,
            'backpack.employee-schedule' => $admin,
            'backpack.branch' => $admin,
            'backpack.vehicle-issuance' => $admin,

            'ip-restrictions' => $p('see-menu-security'),
            'access-logs' => $p('see-menu-security'),
            'backpack.ip-restriction' => $p('see-menu-security'),
            'backpack.access-log' => $p('see-menu-security'),

            'judopay.mit-dashboard' => ['permission' => 'can-run-mit'],
            'judopay.weekly-mit-queue' => ['permission' => 'see-weekly-queue'],
            'judopay.fire-direct-mit' => ['permission' => 'can-fire-mit'],
            'judopay.add-to-queue' => ['any' => ['add-weekly-queue', 'add-monthly-queue']],
            'judopay.stop-live-queue' => ['permission' => 'can-run-mit'],
            'judopay.create-cit-session' => ['permission' => 'can-run-cit'],
            'judopay.generate-authorization-access' => ['permission' => 'can-run-cit'],
            'judopay.kill-previous-links' => ['permission' => 'can-run-cit'],
            'judopay.send-authorization-email' => ['permission' => 'can-run-cit'],
            'judopay.update-billing-day' => ['permission' => 'can-run-cit'],
            'judopay.update-amount' => ['permission' => 'can-run-cit'],
            'judopay.close-subscription' => ['permission' => 'can-run-cit'],
            'judopay.subscribe' => $judo,
            'judopay' => $judo,
            'judopay-subscriptions' => $judo,
            'judopay-mit-queue' => ['permission' => 'can-run-mit'],
            'ngn-mit-queue' => ['permission' => 'see-monthly-queue'],
            'backpack.dev-judopay-subscription' => $judo,
            'backpack.dev-judopay-mit-queue' => ['permission' => 'can-run-mit'],
            'backpack.dev-ngn-mit-queue' => ['permission' => 'see-monthly-queue'],
        ];

        uksort($map, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        return $map;
    }
}
