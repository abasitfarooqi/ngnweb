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
    | - hidden: true = never appear in global search (direct URL may still work)
    |
    */

    'menu_routes' => [
        'flux-admin.users.index' => ['permission' => 'see-menu-permissions'],
        'flux-admin.roles.index' => ['permission' => 'see-menu-permissions'],
        'flux-admin.permissions.index' => ['permission' => 'see-menu-permissions'],
        'flux-admin.access-logs.index' => ['permission' => 'manage_access_logs'],
        'flux-admin.ip-restrictions.index' => ['permission' => 'see-menu-security'],
        'flux-admin.club.index' => ['full_club_admin' => true],
        'flux-admin.club-members.index' => [],
        'flux-admin.club-purchases.index' => ['full_club_admin' => true],
        'flux-admin.club-spending.index' => ['full_club_admin' => true],
        'flux-admin.club-spending-payments.index' => ['full_club_admin' => true],
        'flux-admin.club-redemptions.index' => ['full_club_admin' => true],
        'flux-admin.dev-club-otp.index' => ['full_club_admin' => true],
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
        \App\Models\User::class => ['permission' => 'see-menu-permissions'],
        \App\Models\Role::class => ['permission' => 'see-menu-permissions'],
        // Permission rows (e.g. can-view-mit-history) only when staff can open Permissions menu.
        \App\Models\Permission::class => ['permission' => 'see-menu-permissions'],
        \App\Models\AccessLog::class => ['permission' => 'manage_access_logs'],
        \App\Models\IpRestriction::class => ['permission' => 'see-menu-security'],
        \App\Models\ClubMember::class => ['full_club_admin' => true],
        \App\Models\ClubMemberPurchase::class => ['full_club_admin' => true],
        \App\Models\ClubMemberSpending::class => ['full_club_admin' => true],
        \App\Models\ClubMemberSpendingPayment::class => ['full_club_admin' => true],
        \App\Models\ClubMemberRedeem::class => ['full_club_admin' => true],
        \App\Models\RentingBookingInvoice::class => ['hidden' => true],
        \App\Models\ApplicationItem::class => ['hidden' => true],
    ],

];
