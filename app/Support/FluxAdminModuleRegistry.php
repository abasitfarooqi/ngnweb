<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\PcnCase;
use App\Models\RentingBooking;

class FluxAdminModuleRegistry
{
    /** @return array<string, array{title: string, description: string, stats: array<int, array{label: string, value: string|int}>, links: array<int, array{label: string, route: string, icon: string}>}> */
    public static function all(): array
    {
        return [
            'customers' => self::customers(),
            'rentals'   => self::rentals(),
            'finance'   => self::finance(),
            'pcn'       => self::pcn(),
        ];
    }

    public static function get(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    public static function customers(): array
    {
        return [
            'title'       => 'Customers module',
            'description' => 'Customer 360 — profile, documents, rentals, finance and PCN links.',
            'stats'       => [
                ['label' => 'Customers', 'value' => Customer::count()],
            ],
            'links' => [
                ['label' => 'Customer list', 'route' => 'flux-admin.customers.index', 'icon' => 'users'],
                ['label' => 'Verify documents', 'route' => 'flux-admin.customer-documents.index', 'icon' => 'document-check'],
                ['label' => 'Appointments', 'route' => 'flux-admin.customer-appointments.index', 'icon' => 'calendar'],
            ],
        ];
    }

    public static function rentals(): array
    {
        $active = RentingBooking::active()->count();

        return [
            'title'       => 'Rentals module',
            'description' => 'Same-day rental lifecycle — intake, documents, payments, issuance, closing.',
            'stats'       => [
                ['label' => 'Active rentals', 'value' => $active],
            ],
            'links' => [
                ['label' => 'Operations hub', 'route' => 'flux-admin.rental-operations.index', 'icon' => 'squares-2x2'],
                ['label' => 'Bookings management', 'route' => 'flux-admin.bookings-management.index', 'icon' => 'list-bullet'],
                ['label' => 'New booking', 'route' => 'flux-admin.new-booking.index', 'icon' => 'plus-circle'],
                ['label' => 'Booking invoices', 'route' => 'flux-admin.booking-invoices.index', 'icon' => 'document-text'],
                ['label' => 'Due payments', 'route' => 'flux-admin.rental-due-payments.index', 'icon' => 'exclamation-triangle'],
                ['label' => 'Inactive bookings', 'route' => 'flux-admin.inactive-bookings.index', 'icon' => 'archive-box'],
            ],
        ];
    }

    public static function finance(): array
    {
        return [
            'title'       => 'Finance module',
            'description' => 'Finance applications, contracts and related customer records.',
            'stats'       => [
                ['label' => 'Applications', 'value' => FinanceApplication::count()],
                ['label' => 'Active', 'value' => FinanceApplication::where('is_cancelled', false)->count()],
            ],
            'links' => [
                ['label' => 'Finance applications', 'route' => 'flux-admin.finance.index', 'icon' => 'banknotes'],
                ['label' => 'Contract access', 'route' => 'flux-admin.contract-access.index', 'icon' => 'key'],
                ['label' => 'Application items', 'route' => 'flux-admin.application-items.index', 'icon' => 'queue-list'],
            ],
        ];
    }

    public static function pcn(): array
    {
        return [
            'title'       => 'PCN module',
            'description' => 'Penalty charge notices, updates, TOL requests and dashboard.',
            'stats'       => [
                ['label' => 'Total cases', 'value' => PcnCase::count()],
                ['label' => 'Open', 'value' => PcnCase::where('isClosed', false)->count()],
            ],
            'links' => [
                ['label' => 'PCN cases', 'route' => 'flux-admin.pcn.index', 'icon' => 'shield-exclamation'],
                ['label' => 'PCN updates', 'route' => 'flux-admin.pcn-updates.index', 'icon' => 'chat-bubble-left-right'],
                ['label' => 'TOL requests', 'route' => 'flux-admin.pcn-tol-requests.index', 'icon' => 'document'],
                ['label' => 'Dashboard', 'route' => 'flux-admin.pcn-dashboard.index', 'icon' => 'chart-bar'],
            ],
        ];
    }
}
