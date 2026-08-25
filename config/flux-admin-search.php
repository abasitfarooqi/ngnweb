<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Flux Admin global search visibility
    |--------------------------------------------------------------------------
    |
    | Gate menu shortcuts and registry resources by Spatie permission (or role
    | via the same can() stack). Super-admins always see everything.
    |
    | - permission: required ability name; omit / null = visible to all admins
    | - hidden: true = never appear in global search (sidebar Menu may still list it)
    |
    */

    'menu_routes' => [
        'flux-admin.users.index' => ['super_admin' => true],
        'flux-admin.roles.index' => ['super_admin' => true],
        'flux-admin.permissions.index' => ['super_admin' => true],
        'flux-admin.access-logs.index' => ['permission' => 'see-menu-security'],
        'flux-admin.ip-restrictions.index' => ['permission' => 'see-menu-security'],
        'flux-admin.club.index' => ['super_admin' => true],
        'flux-admin.club-members.index' => ['role' => 'Club Member Access'],
        'flux-admin.club-purchases.index' => ['super_admin' => true],
        'flux-admin.club-spending.index' => ['super_admin' => true],
        'flux-admin.club-spending-payments.index' => ['super_admin' => true],
        'flux-admin.club-redemptions.index' => ['super_admin' => true],
        'flux-admin.dev-club-otp.index' => ['super_admin' => true],
        'flux-admin.application-items.index' => ['hidden' => true],
        'flux-admin.communications.index' => ['hidden' => true],
        'flux-admin.communications.show' => ['hidden' => true],

        // Folded into rental show — keep URLs for bookmark/admin use but hide from search/menu.
        'flux-admin.booking-invoices.index' => ['hidden' => true],
        'flux-admin.booking-invoice-dates.index' => ['hidden' => true],
        'flux-admin.change-start-date.index' => ['hidden' => true],
        'flux-admin.adjust-weekday.index' => ['hidden' => true],
    ],

    'resources' => [
        \App\Models\User::class => ['super_admin' => true],
        \App\Models\Role::class => ['super_admin' => true],
        \App\Models\Permission::class => ['super_admin' => true],
        \App\Models\AccessLog::class => ['permission' => 'see-menu-security'],
        \App\Models\IpRestriction::class => ['permission' => 'see-menu-security'],
        \App\Models\ClubMember::class => ['super_admin' => true],
        \App\Models\ClubMemberPurchase::class => ['super_admin' => true],
        \App\Models\ClubMemberSpending::class => ['super_admin' => true],
        \App\Models\ClubMemberSpendingPayment::class => ['super_admin' => true],
        \App\Models\ClubMemberRedeem::class => ['super_admin' => true],
        \App\Models\RentingBookingInvoice::class => ['hidden' => true],
        \App\Models\ApplicationItem::class => ['hidden' => true],
    ],

];
