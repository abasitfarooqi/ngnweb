<?php

/**
 * Flux Admin sidebar + quick links for global menu search.
 * Keep labels aligned with resources/views/flux-admin/layouts/app.blade.php
 */
return [

    'entries' => [
        // Quick links (sidebar top)
        ['group' => 'Quick links', 'label' => 'Delivery enquiries', 'route' => 'flux-admin.delivery-enquiries.index', 'permission' => 'see-menu-vehicles'],
        ['group' => 'Quick links', 'label' => 'MOT bookings', 'route' => 'flux-admin.mot-bookings.index', 'permission' => 'see-menu-mot-bookings', 'keywords' => 'mot booking'],
        ['group' => 'Quick links', 'label' => 'Service enquiries', 'route' => 'flux-admin.service-bookings.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Quick links', 'label' => 'Support inbox', 'route' => 'flux-admin.support-inbox.index', 'permission' => 'see-menu-commons', 'keywords' => 'chat conversations'],

        ['group' => 'General', 'label' => 'Dashboard', 'route' => 'flux-admin.dashboard', 'keywords' => 'home grid overview'],
        ['group' => 'General', 'label' => 'Total NGN Vehicles', 'route' => 'flux-admin.total-vehicles.index', 'permission' => 'see-menu-commons', 'keywords' => 'fleet internal ngn vehicles'],
        ['group' => 'General', 'label' => 'Global search', 'route' => 'flux-admin.search', 'keywords' => 'find menu records'],

        ['group' => 'Payment Plan', 'label' => 'Module home', 'route' => 'flux-admin.modules.show', 'params' => ['module' => 'finance'], 'permission' => 'see-menu-finance'],
        ['group' => 'Payment Plan', 'label' => 'Create / Edit', 'route' => 'flux-admin.finance.index', 'permission' => 'see-menu-finance', 'keywords' => 'finance applications contracts payment plan'],
        ['group' => 'Payment Plan', 'label' => 'Contract signature expire', 'route' => 'flux-admin.contract-access.index', 'permission' => 'see-menu-finance', 'keywords' => 'passcode signing link'],

        ['group' => 'Rentals', 'label' => 'Rentals home', 'route' => 'flux-admin.rental-operations.index', 'permission' => 'see-menu-rentals', 'keywords' => 'module hub'],
        ['group' => 'Rentals', 'label' => 'New booking', 'route' => 'flux-admin.new-booking.index', 'permission' => 'see-menu-rentals', 'keywords' => 'create booking wizard'],
        ['group' => 'Rentals', 'label' => 'Active bookings rental', 'route' => 'flux-admin.rentals.index', 'permission' => 'see-menu-rentals', 'keywords' => 'manage bookings'],
        ['group' => 'Rentals', 'label' => 'Inactive bookings', 'route' => 'flux-admin.inactive-bookings.index', 'permission' => 'see-menu-rentals', 'keywords' => 'ended closed'],
        ['group' => 'Rentals', 'label' => 'Inactive pendings payments', 'route' => 'flux-admin.ended-with-pendings.index', 'permission' => 'see-menu-rentals', 'keywords' => 'proceed anyway unpaid'],
        ['group' => 'Rentals', 'label' => 'All bookings', 'route' => 'flux-admin.all-bookings.index', 'permission' => 'see-menu-rentals'],
        ['group' => 'Rentals', 'label' => 'E-bike manager', 'route' => 'flux-admin.ebikes.index', 'permission' => 'see-menu-rentals', 'keywords' => 'electric ebike'],
        ['group' => 'Rentals', 'label' => 'Motorbike pricing', 'route' => 'flux-admin.motorbike-pricing.index', 'permission' => 'see-menu-rentals', 'keywords' => 'weekly rates'],
        ['group' => 'Rentals', 'label' => 'Add new vehicle (pricing)', 'route' => 'flux-admin.renting-pricing.index', 'permission' => 'see-menu-rentals', 'keywords' => 'rental pricing reg'],
        ['group' => 'Rentals', 'label' => 'Document expire date', 'route' => 'flux-admin.upload-document-links.index', 'permission' => 'see-menu-rentals', 'keywords' => 'upload documents link'],
        ['group' => 'Rentals', 'label' => 'Signature expire date', 'route' => 'flux-admin.agreement-access.index', 'permission' => 'see-menu-rentals', 'keywords' => 'agreement signing passcode'],
        ['group' => 'Rentals', 'label' => 'Terminate / generate link', 'route' => 'flux-admin.rental-terminate-links.index', 'permission' => 'see-menu-rentals', 'keywords' => 'termination end rental'],
        ['group' => 'Rentals', 'label' => 'Overview', 'route' => 'flux-admin.active-rentals.index', 'permission' => 'see-menu-rentals', 'keywords' => 'active rentals overview'],
        ['group' => 'Rentals', 'label' => 'Due payments', 'route' => 'flux-admin.rental-due-payments.index', 'permission' => 'see-menu-rentals', 'keywords' => 'unpaid invoices'],
        ['group' => 'Rentals', 'label' => 'Renting service videos', 'route' => 'flux-admin.service-videos.index', 'permission' => 'see-menu-rentals'],

        ['group' => 'PCNs', 'label' => 'Module home', 'route' => 'flux-admin.modules.show', 'params' => ['module' => 'pcn'], 'permission' => 'see-menu-pcns'],
        ['group' => 'PCNs', 'label' => 'Add / Edit', 'route' => 'flux-admin.pcn.index', 'permission' => 'see-menu-pcns', 'keywords' => 'pcn cases penalty'],

        ['group' => 'Vehicles', 'label' => 'DVLA add / edit', 'route' => 'flux-admin.motorbikes-dvla.create', 'permission' => 'see-menu-vehicles', 'keywords' => 'dvla lookup'],
        ['group' => 'Vehicles', 'label' => 'Repair rental availability', 'route' => 'flux-admin.backpack.motorbike-available.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'make available pricing'],
        ['group' => 'Vehicles', 'label' => 'Manual add / edit', 'route' => 'flux-admin.motorbikes.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'fleet motorbikes reg'],
        ['group' => 'Vehicles', 'label' => 'MOT / TAX compliance', 'route' => 'flux-admin.motorbike-compliance.preview', 'permission' => 'see-menu-vehicles', 'keywords' => 'annual compliance table preview read only dvla road tax mot'],
        ['group' => 'Vehicles', 'label' => 'Vehicle database', 'route' => 'flux-admin.motorbike-compliance.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'mot tax compliance dvla association fleet all rows history'],
        ['group' => 'Vehicles', 'label' => 'New arrivals', 'route' => 'flux-admin.motorbike-new.index', 'permission' => 'see-menu-vehicles'],
        ['group' => 'Vehicles', 'label' => 'E-bike manager', 'route' => 'flux-admin.ebikes.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'electric'],
        ['group' => 'Vehicles', 'label' => 'Vehicle notifications', 'route' => 'flux-admin.vehicle-notifications.index', 'permission' => 'see-menu-vehicles'],
        ['group' => 'Vehicles', 'label' => 'Recovered', 'route' => 'flux-admin.recovered-motorbikes.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'recovery'],

        ['group' => 'Vehicle records', 'label' => 'Category B', 'route' => 'flux-admin.motorbike-cat-b.index', 'permission' => 'see-menu-commons', 'keywords' => 'cat b write off'],
        ['group' => 'Vehicle records', 'label' => 'Club member vehicles details', 'route' => 'flux-admin.club-member-vehicles.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Vehicle records', 'label' => 'Vehicle history', 'route' => 'flux-admin.vehicle-history.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Vehicle records', 'label' => 'Company vehicles', 'route' => 'flux-admin.company-vehicles.index', 'permission' => 'see-menu-commons'],

        ['group' => 'Customers', 'label' => 'Module home', 'route' => 'flux-admin.modules.show', 'params' => ['module' => 'customers'], 'permission' => 'see-menu-commons'],
        ['group' => 'Customers', 'label' => 'Customer list', 'route' => 'flux-admin.customers.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Customers', 'label' => 'Verify documents', 'route' => 'flux-admin.customer-documents.index', 'permission' => 'see-menu-commons', 'keywords' => 'kyc review queue'],

        ['group' => 'Service enquiries', 'label' => 'Service enquiries', 'route' => 'flux-admin.service-bookings.index', 'permission' => 'see-menu-commons'],

        ['group' => 'Book services / repairs / report', 'label' => 'Services / repairs booking', 'route' => 'flux-admin.customer-appointments.index', 'permission' => 'see-menu-services-and-repairs-and-report', 'keywords' => 'appointments'],
        ['group' => 'Book services / repairs / report', 'label' => 'Repairs report', 'route' => 'flux-admin.motorbike-repairs.index', 'permission' => 'see-menu-services-and-repairs-and-report'],
        ['group' => 'Book services / repairs / report', 'label' => 'Repair updates', 'route' => 'flux-admin.motorbike-repair-updates.index', 'permission' => 'see-menu-services-and-repairs-and-report'],

        ['group' => 'Club', 'label' => 'Club members', 'route' => 'flux-admin.club.index', 'when' => 'full_club_admin', 'keywords' => 'ngn club loyalty'],
        ['group' => 'Club', 'label' => 'Club member access', 'route' => 'flux-admin.club-members.index', 'when' => 'full_club_admin'],
        ['group' => 'Club', 'label' => 'Club member purchases', 'route' => 'flux-admin.club-purchases.index', 'when' => 'full_club_admin'],
        ['group' => 'Club', 'label' => 'Club member redeems', 'route' => 'flux-admin.club-redemptions.index', 'when' => 'full_club_admin'],
        ['group' => 'Club', 'label' => '0% spendings', 'route' => 'flux-admin.club-spending.index', 'when' => 'full_club_admin'],
        ['group' => 'Club', 'label' => 'Spending payments', 'route' => 'flux-admin.club-spending-payments.index', 'when' => 'full_club_admin'],
        ['group' => 'Club', 'label' => 'Dev Club OTP', 'route' => 'flux-admin.dev-club-otp.index', 'when' => 'full_club_admin'],
        ['group' => 'Club', 'label' => 'Club member access', 'route' => 'flux-admin.club-members.index', 'when' => 'club_commons_role', 'keywords' => 'staff club portal'],
        ['group' => 'Club', 'label' => 'Club members', 'route' => 'flux-admin.club-members.index', 'when' => 'limited_club', 'keywords' => 'ngn club'],

        ['group' => 'Delivery', 'label' => 'Delivery enquiries', 'route' => 'flux-admin.delivery-enquiries.index', 'permission' => 'see-menu-vehicles'],
        ['group' => 'Delivery', 'label' => 'Motorbike delivery orders', 'route' => 'flux-admin.vehicle-delivery-orders.index', 'permission' => 'see-menu-commons'],

        ['group' => 'Sale', 'label' => 'Used Motorcycle Sale', 'route' => 'flux-admin.motorbike-sales.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'used bike sale'],
        ['group' => 'Sale', 'label' => 'Brand New vehicles', 'route' => 'flux-admin.motorbike-for-sale.index', 'permission' => 'see-menu-vehicles', 'keywords' => 'new stock catalogue'],

        ['group' => 'Purchase', 'label' => 'Add / Edit', 'route' => 'flux-admin.used-purchases.index', 'permission' => 'see-menu-commons', 'keywords' => 'buy used purchase'],

        ['group' => 'MOT', 'label' => 'Add / Edit', 'route' => 'flux-admin.mot-bookings.index', 'permission' => 'see-menu-mot-bookings', 'keywords' => 'mot bookings'],
        ['group' => 'MOT', 'label' => 'MOT checker', 'route' => 'flux-admin.mot-checker.index', 'permission' => 'see-menu-mot-bookings'],
        ['group' => 'MOT', 'label' => 'MOT stats', 'route' => 'flux-admin.mot-stats.index', 'permission' => 'see-menu-mot-bookings'],

        ['group' => 'Claims', 'label' => 'Add / Edit', 'route' => 'flux-admin.motorbike-claims.index', 'permission' => 'see-menu-claims', 'keywords' => 'insurance claim'],

        ['group' => 'Ecommerce', 'label' => 'Shop orders', 'route' => 'flux-admin.shop-orders.index', 'permission' => 'see-menu-ecommerce', 'keywords' => 'shop checkout catalogue'],
        ['group' => 'Ecommerce', 'label' => 'Spare parts orders', 'route' => 'flux-admin.spare-part-orders.index', 'permission' => 'see-menu-ecommerce', 'keywords' => 'spare parts checkout'],
        ['group' => 'Ecommerce', 'label' => 'All orders', 'route' => 'flux-admin.ec-orders.index', 'permission' => 'see-menu-ecommerce', 'keywords' => 'ecommerce orders'],
        ['group' => 'Ecommerce', 'label' => 'Store front', 'route' => 'flux-admin.store-front.index', 'permission' => 'see-menu-ecommerce'],

        ['group' => 'Spare parts', 'label' => 'Parts', 'route' => 'flux-admin.sp-parts.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Makes', 'route' => 'flux-admin.sp-makes.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Models', 'route' => 'flux-admin.sp-models.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Fitments', 'route' => 'flux-admin.sp-fitments.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Assemblies', 'route' => 'flux-admin.sp-assemblies.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Assembly parts', 'route' => 'flux-admin.sp-assembly-parts.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Stock movements', 'route' => 'flux-admin.sp-stock-movements.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Spare parts', 'label' => 'Stock handler', 'route' => 'flux-admin.sp-parts.index', 'permission' => 'see-menu-inventory', 'keywords' => 'parts stock'],

        ['group' => 'Inventory', 'label' => 'Products (add / edit)', 'route' => 'flux-admin.inventory-products.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Stock management', 'route' => 'flux-admin.inventory-stock-movements.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Brands', 'route' => 'flux-admin.inventory-brands.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Categories', 'route' => 'flux-admin.inventory-categories.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Product models', 'route' => 'flux-admin.inventory-models.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Oxford products', 'route' => 'flux-admin.oxford-products.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Purchase requests', 'route' => 'flux-admin.purchase-requests.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Purchase request items', 'route' => 'flux-admin.purchase-request-items.index', 'permission' => 'see-menu-inventory'],
        ['group' => 'Inventory', 'label' => 'Store front', 'route' => 'flux-admin.store-front.index', 'permission' => 'see-menu-inventory'],

        ['group' => 'Orders', 'label' => 'DS orders', 'route' => 'flux-admin.ds-orders.index', 'role' => 'Admin'],
        ['group' => 'Orders', 'label' => 'DS order legs', 'route' => 'flux-admin.ds-order-items.index', 'role' => 'Admin'],
        ['group' => 'Orders', 'label' => 'Digital invoices', 'route' => 'flux-admin.digital-invoices.index', 'role' => 'Admin'],
        ['group' => 'Orders', 'label' => 'Invoice items', 'route' => 'flux-admin.digital-invoice-items.index', 'role' => 'Admin'],

        ['group' => 'Blog management', 'label' => 'Blog posts', 'route' => 'flux-admin.blog-posts.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Blog management', 'label' => 'Blog categories', 'route' => 'flux-admin.blog-categories.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Blog management', 'label' => 'Blog tags', 'route' => 'flux-admin.blog-tags.index', 'permission' => 'see-menu-commons'],

        ['group' => 'Chat', 'label' => 'Conversations inbox', 'route' => 'flux-admin.support-inbox.index', 'permission' => 'see-menu-commons', 'keywords' => 'support messages'],
        ['group' => 'Chat', 'label' => 'Support conversations', 'route' => 'flux-admin.support-conversations.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Chat', 'label' => 'Support messages', 'route' => 'flux-admin.support-messages.index', 'permission' => 'see-menu-commons'],
        ['group' => 'Chat', 'label' => 'Contact queries', 'route' => 'flux-admin.contact-queries.index', 'permission' => 'see-menu-commons'],

        ['group' => 'General', 'label' => 'Careers', 'route' => 'flux-admin.careers.index', 'permission' => 'see-menu-commons', 'keywords' => 'jobs vacancies'],

        ['group' => 'B2B', 'label' => 'Partners', 'route' => 'flux-admin.inventory-partners.index', 'permission' => 'see-menu-b2b'],

        ['group' => 'Surveys', 'label' => 'Surveys', 'route' => 'flux-admin.surveys.index', 'permission' => 'see-menu-surveys'],
        ['group' => 'Surveys', 'label' => 'Questions', 'route' => 'flux-admin.survey-questions.index', 'permission' => 'see-menu-surveys'],
        ['group' => 'Surveys', 'label' => 'Options', 'route' => 'flux-admin.survey-options.index', 'permission' => 'see-menu-surveys'],
        ['group' => 'Surveys', 'label' => 'Responses', 'route' => 'flux-admin.survey-responses.index', 'permission' => 'see-menu-surveys'],
        ['group' => 'Surveys', 'label' => 'Answers', 'route' => 'flux-admin.survey-answers.index', 'permission' => 'see-menu-surveys'],

        ['group' => 'Misc / Experiments', 'label' => 'Calendar', 'route' => 'flux-admin.calendar.index', 'role' => 'Admin'],
        ['group' => 'Misc / Experiments', 'label' => 'Staff schedules', 'route' => 'flux-admin.employee-schedules.index', 'role' => 'Admin'],
        ['group' => 'Misc / Experiments', 'label' => 'AI agent settings', 'route' => 'flux-admin.agent-settings.index', 'role' => 'Admin'],
        ['group' => 'Misc / Experiments', 'label' => 'Branches', 'route' => 'flux-admin.branches.index', 'role' => 'Admin'],
        ['group' => 'Misc / Experiments', 'label' => 'Vehicle issuances', 'route' => 'flux-admin.vehicle-issuances.index', 'role' => 'Admin'],
        ['group' => 'Misc / Experiments', 'label' => 'Old admin panel', 'url' => '/admin', 'role' => 'Admin', 'keywords' => 'backpack legacy'],
        ['group' => 'Misc / Experiments', 'label' => 'Queue monitor', 'route' => 'flux-admin.queue-monitor.index', 'role' => 'Admin'],

        ['group' => 'Security', 'label' => 'IP restrictions', 'route' => 'flux-admin.ip-restrictions.index', 'permission' => 'see-menu-security'],
        ['group' => 'Security', 'label' => 'Access logs', 'route' => 'flux-admin.access-logs.index', 'permission' => 'manage_access_logs'],

        ['group' => 'Permissions', 'label' => 'Users', 'route' => 'flux-admin.users.index', 'permission' => 'see-menu-permissions'],
        ['group' => 'Permissions', 'label' => 'Roles', 'route' => 'flux-admin.roles.index', 'permission' => 'see-menu-permissions'],
        ['group' => 'Permissions', 'label' => 'Permissions', 'route' => 'flux-admin.permissions.index', 'permission' => 'see-menu-permissions'],

        ['group' => 'Judo Pay', 'label' => 'Judo Pay', 'route' => 'flux-admin.judopay.index', 'canany' => ['see-judopay-home', 'see-judopay']],
        ['group' => 'Judo Pay', 'label' => 'MIT dashboard', 'route' => 'flux-admin.judopay.mit-dashboard', 'canany' => ['see-judopay-home', 'see-judopay']],
        ['group' => 'Judo Pay', 'label' => 'Weekly schedule', 'route' => 'flux-admin.judopay.weekly-mit-queue', 'canany' => ['see-judopay-home', 'see-judopay']],
        ['group' => 'Judo Pay', 'label' => 'Subscriptions', 'route' => 'flux-admin.judopay-subscriptions.index', 'canany' => ['see-judopay-home', 'see-judopay']],
        ['group' => 'Judo Pay', 'label' => 'NGN MIT queue', 'route' => 'flux-admin.ngn-mit-queue.index', 'canany' => ['see-judopay-home', 'see-judopay']],
        ['group' => 'Judo Pay', 'label' => 'Judopay MIT queue', 'route' => 'flux-admin.judopay-mit-queue.index', 'canany' => ['see-judopay-home', 'see-judopay']],
        ['group' => 'Judo Pay', 'label' => 'Open in Backpack UI', 'url' => '/ngn-admin/judopay', 'canany' => ['see-judopay-home', 'see-judopay'], 'keywords' => 'legacy judopay'],
    ],

];
