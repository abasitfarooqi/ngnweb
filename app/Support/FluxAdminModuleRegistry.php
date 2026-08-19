<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\FinanceApplication;
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
            'vehicles'  => self::menu('Vehicles', 'Fleet records, compliance and vehicle operations.', [
                ['DVLA add / edit', 'flux-admin.motorbikes-dvla.create', 'pencil-square'],
                ['Repair rental availability', 'flux-admin.backpack.motorbike-available.index', 'wrench-screwdriver'],
                ['Manual add / edit', 'flux-admin.motorbikes.index', 'pencil-square'],
                ['MOT / TAX compliance', 'flux-admin.motorbike-compliance.preview', 'shield-check'],
                ['Vehicle database', 'flux-admin.motorbike-compliance.index', 'table-cells'],
                ['New arrivals', 'flux-admin.motorbike-new.index', 'sparkles'],
                ['E-bike manager', 'flux-admin.ebikes.index', 'bolt'],
                ['Vehicle notifications', 'flux-admin.vehicle-notifications.index', 'bell'],
                ['Recovered', 'flux-admin.recovered-motorbikes.index', 'arrow-uturn-left'],
            ]),
            'vehicle-records' => self::menu('Vehicle records', 'Additional vehicle records and internal fleet history.', [
                ['Category B', 'flux-admin.motorbike-cat-b.index', 'archive-box'],
                ['Club member vehicles details', 'flux-admin.club-member-vehicles.index', 'users'],
                ['Vehicle history', 'flux-admin.vehicle-history.index', 'clock'],
                ['Company vehicles', 'flux-admin.company-vehicles.index', 'building-office'],
            ]),
            'services' => self::menu('Services and repairs', 'Appointments, repairs and repair updates.', [
                ['Services / repairs booking', 'flux-admin.customer-appointments.index', 'calendar-days'],
                ['Repairs report', 'flux-admin.motorbike-repairs.index', 'wrench-screwdriver'],
                ['Repair updates', 'flux-admin.motorbike-repair-updates.index', 'arrow-path'],
            ]),
            'club' => self::menu('Club', 'Club members, access, purchases and spending.', [
                ['Club members', 'flux-admin.club.index', 'users'],
                ['Club member access', 'flux-admin.club-members.index', 'key'],
                ['Club member purchases', 'flux-admin.club-purchases.index', 'shopping-bag'],
                ['Club member redeems', 'flux-admin.club-redemptions.index', 'gift'],
                ['0% spendings', 'flux-admin.club-spending.index', 'currency-pound'],
                ['Spending payments', 'flux-admin.club-spending-payments.index', 'banknotes'],
                ['Dev Club OTP', 'flux-admin.dev-club-otp.index', 'command-line'],
            ]),
            'delivery' => self::menu('Delivery', 'Delivery enquiries and motorbike delivery orders.', [
                ['Delivery enquiries', 'flux-admin.delivery-enquiries.index', 'truck'],
                ['Motorbike delivery orders', 'flux-admin.vehicle-delivery-orders.index', 'truck'],
            ]),
            'claims' => self::menu('Claims', 'Motorbike claims and claim records.', [
                ['Add / Edit', 'flux-admin.motorbike-claims.index', 'exclamation-triangle'],
            ]),
            'ecommerce' => self::menu('Ecommerce', 'Shop orders, spare parts orders and store front.', [
                ['Shop orders', 'flux-admin.shop-orders.index', 'shopping-bag'],
                ['Spare parts orders', 'flux-admin.spare-part-orders.index', 'wrench-screwdriver'],
                ['All orders', 'flux-admin.ec-orders.index', 'list-bullet'],
                ['Store front', 'flux-admin.store-front.index', 'shopping-cart'],
            ]),
            'spare-parts' => self::menu('Spare parts', 'Parts, assemblies, fitments and stock movements.', [
                ['Parts', 'flux-admin.sp-parts.index', 'wrench-screwdriver'],
                ['Makes', 'flux-admin.sp-makes.index', 'tag'],
                ['Models', 'flux-admin.sp-models.index', 'cube'],
                ['Fitments', 'flux-admin.sp-fitments.index', 'link'],
                ['Assemblies', 'flux-admin.sp-assemblies.index', 'squares-2x2'],
                ['Assembly parts', 'flux-admin.sp-assembly-parts.index', 'queue-list'],
                ['Stock movements', 'flux-admin.sp-stock-movements.index', 'arrows-right-left'],
                ['Stock handler', 'flux-admin.sp-parts.index', 'clipboard-document-check'],
            ]),
            'inventory' => self::menu('Inventory', 'Products, stock, brands, categories and purchase requests.', [
                ['Products (add / edit)', 'flux-admin.inventory-products.index', 'cube'],
                ['Stock management', 'flux-admin.inventory-stock-movements.index', 'arrows-right-left'],
                ['Brands', 'flux-admin.inventory-brands.index', 'tag'],
                ['Categories', 'flux-admin.inventory-categories.index', 'squares-2x2'],
                ['Product models', 'flux-admin.inventory-models.index', 'cube'],
                ['Oxford products', 'flux-admin.oxford-products.index', 'document-text'],
                ['Purchase requests', 'flux-admin.purchase-requests.index', 'clipboard-document-list'],
                ['Purchase request items', 'flux-admin.purchase-request-items.index', 'list-bullet'],
                ['Store front', 'flux-admin.store-front.index', 'shopping-cart'],
            ]),
            'orders' => self::menu('Orders', 'Delivery service orders and digital invoices.', [
                ['DS orders', 'flux-admin.ds-orders.index', 'truck'],
                ['DS order legs', 'flux-admin.ds-order-items.index', 'list-bullet'],
                ['Digital invoices', 'flux-admin.digital-invoices.index', 'document-text'],
                ['Invoice items', 'flux-admin.digital-invoice-items.index', 'list-bullet'],
            ]),
            'blog' => self::menu('Blog management', 'Blog posts, categories and tags.', [
                ['Blog posts', 'flux-admin.blog-posts.index', 'document-text'],
                ['Blog categories', 'flux-admin.blog-categories.index', 'folder'],
                ['Blog tags', 'flux-admin.blog-tags.index', 'tag'],
            ]),
            'chat' => self::menu('Chat', 'Support conversations, messages and contact queries.', [
                ['Conversations inbox', 'flux-admin.support-inbox.index', 'inbox'],
                ['Support conversations', 'flux-admin.support-conversations.index', 'chat-bubble-left-right'],
                ['Support messages', 'flux-admin.support-messages.index', 'envelope'],
                ['Contact queries', 'flux-admin.contact-queries.index', 'question-mark-circle'],
            ]),
            'b2b' => self::menu('B2B', 'Business partners and partner records.', [
                ['Partners', 'flux-admin.inventory-partners.index', 'building-office'],
            ]),
            'surveys' => self::menu('Surveys', 'Surveys, questions, options and responses.', [
                ['Surveys', 'flux-admin.surveys.index', 'clipboard-document-list'],
                ['Questions', 'flux-admin.survey-questions.index', 'question-mark-circle'],
                ['Options', 'flux-admin.survey-options.index', 'list-bullet'],
                ['Responses', 'flux-admin.survey-responses.index', 'chat-bubble-left-right'],
                ['Answers', 'flux-admin.survey-answers.index', 'document-text'],
            ]),
            'misc' => self::menu('Misc / Experiments', 'Internal tools and operational utilities.', [
                ['Calendar', 'flux-admin.calendar.index', 'calendar-days'],
                ['Staff schedules', 'flux-admin.employee-schedules.index', 'calendar'],
                ['AI agent settings', 'flux-admin.agent-settings.index', 'cog-6-tooth'],
                ['Branches', 'flux-admin.branches.index', 'building-office'],
                ['Vehicle issuances', 'flux-admin.vehicle-issuances.index', 'truck'],
                ['Old admin panel', 'flux-admin.dashboard', 'arrow-top-right-on-square'],
                ['Queue monitor', 'flux-admin.queue-monitor.index', 'queue-list'],
            ]),
            'security' => self::menu('Security', 'Access restrictions and security logs.', [
                ['IP restrictions', 'flux-admin.ip-restrictions.index', 'lock-closed'],
                ['Access logs', 'flux-admin.access-logs.index', 'document-magnifying-glass'],
            ]),
            'permissions' => self::menu('Permissions', 'Users, roles and permissions.', [
                ['Users', 'flux-admin.users.index', 'users'],
                ['Roles', 'flux-admin.roles.index', 'shield-check'],
                ['Permissions', 'flux-admin.permissions.index', 'key'],
            ]),
            'judopay' => self::menu('Judo Pay', 'Judo Pay subscriptions, dashboards and payment queues.', [
                ['Judo Pay', 'flux-admin.judopay.index', 'banknotes'],
                ['MIT dashboard', 'flux-admin.judopay.mit-dashboard', 'chart-bar'],
                ['Weekly schedule', 'flux-admin.judopay.weekly-mit-queue', 'calendar-days'],
                ['Subscriptions', 'flux-admin.judopay-subscriptions.index', 'credit-card'],
                ['NGN MIT queue', 'flux-admin.ngn-mit-queue.index', 'queue-list'],
                ['Judopay MIT queue', 'flux-admin.judopay-mit-queue.index', 'arrows-right-left'],
                ['Open in Backpack UI', 'flux-admin.dashboard', 'arrow-path'],
            ]),
        ];
    }

    public static function get(string $slug): ?array
    {
        return self::all()[$slug] ?? null;
    }

    private static function menu(string $title, string $description, array $links): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'stats' => [],
            'links' => array_map(fn (array $link) => [
                'label' => $link[0],
                'route' => $link[1],
                'icon' => $link[2],
            ], $links),
        ];
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
                ['label' => 'Rentals home', 'route' => 'flux-admin.rental-operations.index', 'icon' => 'squares-2x2'],
                ['label' => 'New booking', 'route' => 'flux-admin.new-booking.index', 'icon' => 'plus-circle'],
                ['label' => 'Active bookings rental', 'route' => 'flux-admin.rentals.index', 'icon' => 'list-bullet'],
                ['label' => 'Inactive bookings', 'route' => 'flux-admin.inactive-bookings.index', 'icon' => 'archive-box'],
                ['label' => 'Inactive pendings payments', 'route' => 'flux-admin.ended-with-pendings.index', 'icon' => 'exclamation-triangle'],
                ['label' => 'All bookings', 'route' => 'flux-admin.all-bookings.index', 'icon' => 'clock'],
                ['label' => 'E-bike manager', 'route' => 'flux-admin.ebikes.index', 'icon' => 'bolt'],
                ['label' => 'Due payments', 'route' => 'flux-admin.rental-due-payments.index', 'icon' => 'banknotes'],
            ],
        ];
    }

    public static function finance(): array
    {
        return [
            'title'       => 'Finance module',
            'description' => 'Finance applications, contracts and related customer records.',
            'stats'       => [
                ['label' => 'Payment Plans', 'value' => FinanceApplication::count()],
                ['label' => 'Active Payment Plans', 'value' => FinanceApplication::activePaymentPlanListedCount()],
            ],
            'links' => [
                ['label' => 'Finance applications', 'route' => 'flux-admin.finance.index', 'icon' => 'banknotes'],
                ['label' => 'Contract access', 'route' => 'flux-admin.contract-access.index', 'icon' => 'key'],
            ],
        ];
    }

    public static function pcn(): array
    {
        return [
            'title'       => 'PCN module',
            'description' => 'Penalty charge notices, updates, TOL requests and operational overview.',
            'stats'       => [],
            'links' => [
                ['label' => 'PCN cases Add New / Edit', 'route' => 'flux-admin.pcn.index', 'icon' => 'shield-exclamation'],
                ['label' => 'PCN updates', 'route' => 'flux-admin.pcn-updates.index', 'icon' => 'chat-bubble-left-right'],
                ['label' => 'TOL requests', 'route' => 'flux-admin.pcn-tol-requests.index', 'icon' => 'document'],
            ],
        ];
    }
}
