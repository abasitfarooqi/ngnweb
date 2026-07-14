<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Sidebar menu items / shortcuts for Flux Admin global search.
 */
final class FluxAdminMenuShortcuts
{
    /**
     * @return list<array{label: string, group: string, url: string, keywords: string}>
     */
    public static function items(): array
    {
        $items = [
            ['Dashboard', 'General', 'flux-admin.dashboard', 'home overview'],
            ['Global search', 'General', 'flux-admin.search', 'find menu'],
            ['Online store', 'General', 'flux-admin.ec-orders.index', 'ecommerce orders shop'],

            ['Payment Plan module home', 'Payment Plan', 'flux-admin.modules.show', 'finance', ['module' => 'finance']],
            ['Payment Plan create / edit', 'Payment Plan', 'flux-admin.finance.index', 'applications contracts'],
            ['Contract signature expire', 'Payment Plan', 'flux-admin.contract-access.index', 'passcode link'],
            ['Application items', 'Payment Plan', 'flux-admin.application-items.index', 'payment plan items'],
            ['Contract extras', 'Payment Plan', 'flux-admin.contract-extra-items.index', 'extras'],

            ['Rentals module home', 'Rentals', 'flux-admin.modules.show', 'rentals', ['module' => 'rentals']],
            ['Rental operations hub', 'Rentals', 'flux-admin.rental-operations.index', 'hub'],
            ['Rentals list', 'Rentals', 'flux-admin.rentals.index', 'bookings'],
            ['New booking', 'Rentals', 'flux-admin.new-booking.index', 'create booking'],
            ['Bookings management', 'Rentals', 'flux-admin.bookings-management.index', 'manage'],
            ['Inactive bookings', 'Rentals', 'flux-admin.inactive-bookings.index', ''],
            ['All bookings', 'Rentals', 'flux-admin.all-bookings.index', ''],
            ['Booking invoices', 'Rentals', 'flux-admin.booking-invoices.index', 'invoice'],
            ['Booking invoice dates', 'Rentals', 'flux-admin.booking-invoice-dates.index', ''],
            ['Change booking start date', 'Rentals', 'flux-admin.change-start-date.index', ''],
            ['Add new vehicle pricing', 'Rentals', 'flux-admin.renting-pricing.index', 'pricing'],
            ['Document expire date', 'Rentals', 'flux-admin.upload-document-links.index', 'upload'],
            ['Signature expire date', 'Rentals', 'flux-admin.agreement-access.index', 'agreement'],
            ['Terminate / generate link', 'Rentals', 'flux-admin.rental-terminate-links.index', 'termination'],
            ['Active rentals overview', 'Rentals', 'flux-admin.active-rentals.index', ''],
            ['Due payments', 'Rentals', 'flux-admin.rental-due-payments.index', ''],
            ['Renting service videos', 'Rentals', 'flux-admin.service-videos.index', ''],
            ['Adjust booking weekday', 'Rentals', 'flux-admin.adjust-weekday.index', ''],
            ['Active bookings summary', 'Rentals', 'flux-admin.active-bookings-summary.index', ''],

            ['PCN module home', 'PCNs', 'flux-admin.modules.show', 'pcn', ['module' => 'pcn']],
            ['PCN add / edit', 'PCNs', 'flux-admin.pcn.index', 'cases'],
            ['PCN updates', 'PCNs', 'flux-admin.pcn-updates.index', ''],
            ['TOL requests', 'PCNs', 'flux-admin.pcn-tol-requests.index', ''],
            ['PCN overview', 'PCNs', 'flux-admin.pcn-dashboard.index', 'dashboard'],

            ['Services / repairs booking', 'Bookings', 'flux-admin.customer-appointments.index', 'appointments'],
            ['Repairs report', 'Bookings', 'flux-admin.motorbike-repairs.index', ''],
            ['Repair updates', 'Bookings', 'flux-admin.motorbike-repair-updates.index', ''],

            ['MOT add / edit', 'MOT', 'flux-admin.mot-bookings.index', 'bookings'],
            ['MOT checker', 'MOT', 'flux-admin.mot-checker.index', ''],
            ['MOT stats', 'MOT', 'flux-admin.mot-stats.index', ''],

            ['Customers module home', 'Customers', 'flux-admin.modules.show', 'customers', ['module' => 'customers']],
            ['Customer list', 'Customers', 'flux-admin.customers.index', ''],
            ['Verify documents', 'Customers', 'flux-admin.customer-documents.index', ''],

            ['B2B partners', 'B2B', 'flux-admin.inventory-partners.index', 'partners'],

            ['Inventory products', 'Inventory', 'flux-admin.inventory-products.index', 'shop products'],
            ['Stock management', 'Inventory', 'flux-admin.inventory-stock-movements.index', 'stock'],
            ['Inventory brands', 'Inventory', 'flux-admin.inventory-brands.index', ''],
            ['Inventory categories', 'Inventory', 'flux-admin.inventory-categories.index', ''],
            ['Product models', 'Inventory', 'flux-admin.inventory-models.index', ''],
            ['Oxford products', 'Inventory', 'flux-admin.oxford-products.index', ''],
            ['Purchase requests', 'Inventory', 'flux-admin.purchase-requests.index', ''],

            ['Used Motorcycle Sale', 'Website', 'flux-admin.motorbike-sales.index', 'used bike sale images'],
            ['Brand New vehicles', 'Website', 'flux-admin.motorbike-for-sale.index', 'new bike sale images'],
            ['Fleet motorbikes', 'Vehicles', 'flux-admin.motorbikes.index', 'fleet'],

            ['Spare parts', 'Spare parts', 'flux-admin.sp-parts.index', 'parts'],
            ['SP makes', 'Spare parts', 'flux-admin.sp-makes.index', ''],
            ['SP models', 'Spare parts', 'flux-admin.sp-models.index', ''],

            ['Club members', 'Club', 'flux-admin.club.index', 'ngn club'],
            ['Support inbox', 'Support', 'flux-admin.support-inbox.index', 'inbox'],
            ['Users', 'Permissions', 'flux-admin.users.index', 'staff'],
            ['Roles', 'Permissions', 'flux-admin.roles.index', ''],
            ['Permissions', 'Permissions', 'flux-admin.permissions.index', ''],

            ['Judo Pay', 'Judo Pay', 'flux-admin.judopay-recurring.index', ''],
            ['MIT dashboard', 'Judo Pay', 'flux-admin.judopay-mit-dashboard.index', ''],
            ['Weekly schedule', 'Judo Pay', 'flux-admin.judopay-weekly-queue.index', ''],
            ['Subscriptions', 'Judo Pay', 'flux-admin.judopay-subscriptions.index', ''],
            ['NGN MIT queue', 'Judo Pay', 'flux-admin.ngn-mit-queue.index', ''],
            ['Judopay MIT queue', 'Judo Pay', 'flux-admin.judopay-mit-queue.index', ''],
        ];

        $out = [];
        foreach ($items as $row) {
            [$label, $group, $route, $keywords] = [$row[0], $row[1], $row[2], $row[3] ?? ''];
            $params = $row[4] ?? [];
            if (! Route::has($route)) {
                continue;
            }
            try {
                $url = route($route, $params);
            } catch (\Throwable) {
                continue;
            }
            $out[] = [
                'label' => $label,
                'group' => $group,
                'url' => $url,
                'keywords' => Str::lower($label.' '.$group.' '.$keywords),
            ];
        }

        return $out;
    }

    /**
     * @return list<array{label: string, title: string, snippet: string, id: string, index_url: string, show_url: ?string, edit_url: ?string, is_menu: bool}>
     */
    public static function search(string $query, int $limit = 20): array
    {
        $term = Str::lower(trim($query));
        if (mb_strlen($term) < 2) {
            return [];
        }

        $hits = [];
        foreach (self::items() as $item) {
            if (! Str::contains($item['keywords'], $term) && ! Str::contains(Str::lower($item['label']), $term)) {
                continue;
            }
            $hits[] = [
                'label' => 'Menu · '.$item['group'],
                'title' => $item['label'],
                'snippet' => 'Open sidebar page',
                'id' => 'menu-'.md5($item['url']),
                'index_url' => $item['url'],
                'show_url' => $item['url'],
                'edit_url' => null,
                'is_menu' => true,
            ];
            if (count($hits) >= $limit) {
                break;
            }
        }

        return $hits;
    }
}
