<?php

use App\Livewire\FluxAdmin\Pages\Branches\BranchIndex;
use App\Livewire\FluxAdmin\Pages\Branches\BranchShow;
use App\Livewire\FluxAdmin\Pages\Club\ClubIndex;
use App\Livewire\FluxAdmin\Pages\Club\ClubShow;
use App\Livewire\FluxAdmin\Pages\Customers\AppointmentIndex;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerIndex;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerShow;
use App\Livewire\FluxAdmin\Pages\Customers\DocumentIndex as CustomerDocumentIndex;
use App\Livewire\FluxAdmin\Pages\Customers\DocumentReview as CustomerDocumentReview;
use App\Livewire\FluxAdmin\Pages\Dashboard;
use App\Livewire\FluxAdmin\Pages\Finance\AgreementAccessIndex;
use App\Livewire\FluxAdmin\Pages\Finance\ApplicationItemIndex;
use App\Livewire\FluxAdmin\Pages\Finance\ContractAccessIndex;
use App\Livewire\FluxAdmin\Pages\Finance\ContractExtraItemIndex;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceIndex;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceShow;
use App\Livewire\FluxAdmin\Pages\Motorbikes\CatBIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\ComplianceIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\DeliveryEnquiryIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\EbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\ForSaleIndex as MotorbikeForSaleIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeShow;
use App\Livewire\FluxAdmin\Pages\Motorbikes\NewMotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\RepairIndex as MotorbikeRepairIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\SaleIndex as MotorbikeSaleIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnDashboard;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnShow;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnTolIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnUpdateIndex;
use App\Livewire\FluxAdmin\Pages\Permissions\PermissionIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingInvoiceIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\RentalIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\RentalShow;
use App\Livewire\FluxAdmin\Pages\Roles\RoleForm;
use App\Livewire\FluxAdmin\Pages\Roles\RoleIndex;
use App\Livewire\FluxAdmin\Pages\Security\AccessLogIndex;
use App\Livewire\FluxAdmin\Pages\Security\IpRestrictionIndex;
use App\Livewire\FluxAdmin\Pages\Users\UserForm;
use App\Livewire\FluxAdmin\Pages\Users\UserIndex;
use App\Livewire\FluxAdmin\Pages\Users\UserShow;
use App\Livewire\FluxAdmin\Pages\Vehicles\ClaimIndex as VehicleClaimIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\CompanyVehicleIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotBookingIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\PurchaseUsedIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\RecoveredIndex as RecoveredMotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\ServiceBookingIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleIssuanceIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleNotificationIndex;
use App\Livewire\FluxAdmin\Pages\Blog\BlogPostIndex;
use App\Livewire\FluxAdmin\Pages\Blog\BlogCategoryIndex;
use App\Livewire\FluxAdmin\Pages\Blog\BlogTagIndex;
use App\Livewire\FluxAdmin\Pages\Misc\ContactQueryIndex;
use App\Livewire\FluxAdmin\Pages\Misc\CareerIndex;
use App\Livewire\FluxAdmin\Pages\Misc\EmployeeScheduleIndex;
use App\Livewire\FluxAdmin\Pages\Misc\RentingPricingIndex;
use App\Livewire\FluxAdmin\Pages\Support\SupportConversationIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\EcOrderIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DsOrderIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DigitalInvoiceIndex;
use App\Livewire\FluxAdmin\Pages\Club\PurchaseIndex as ClubPurchaseIndex;
use App\Livewire\FluxAdmin\Pages\Club\SpendingIndex as ClubSpendingIndex;
use App\Livewire\FluxAdmin\Pages\Club\SpendingPaymentIndex as ClubSpendingPaymentIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\BrandIndex as InventoryBrandIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\CategoryIndex as InventoryCategoryIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\ModelIndex as InventoryModelIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\OxfordProductIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\PartnerIndex as InventoryPartnerIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\ProductIndex as InventoryProductIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\PurchaseRequestIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\PurchaseRequestItemIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\StockMovementIndex as InventoryStockMovementIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\AssemblyIndex as SpAssemblyIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\AssemblyPartIndex as SpAssemblyPartIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\FitmentIndex as SpFitmentIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\MakeIndex as SpMakeIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\PartIndex as SpPartIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\SpModelIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\StockMovementIndex as SpStockMovementIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\MitQueueIndex as JudopayMitQueueIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\NgnMitQueueIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\SubscriptionIndex as JudopaySubscriptionIndex;
use App\Livewire\FluxAdmin\Pages\Access\RentalTerminateIndex;
use App\Livewire\FluxAdmin\Pages\Access\ServiceVideoIndex;
use App\Livewire\FluxAdmin\Pages\Access\UploadDocumentIndex;
use App\Livewire\FluxAdmin\Pages\Club\RedeemIndex as ClubRedeemIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DigitalInvoiceItemIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DsOrderItemIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\RepairUpdateIndex as MotorbikeRepairUpdateIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\RecurringIndex as JudopayRecurringIndex;
use App\Livewire\FluxAdmin\Pages\Misc\CalendarIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\ActiveRentalsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\AdjustWeekdayIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\DuePaymentsIndex as RentalDuePaymentsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\OperationsHub as RentalOperationsHub;
use App\Livewire\FluxAdmin\Pages\Settings\AgentSettingsForm;
use App\Livewire\FluxAdmin\Pages\Support\SupportInbox;
use App\Livewire\FluxAdmin\Pages\Support\SupportMessageIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotCheckerIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotStatsIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleDeliveryOrderIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeRecordViewIndex;
use App\Livewire\FluxAdmin\Pages\Club\MemberVehicleIndex as ClubMemberVehicleIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\ActiveBookingsSummary;
use App\Livewire\FluxAdmin\Pages\Judopay\MitDashboard as JudopayMitDashboard;
use App\Livewire\FluxAdmin\Pages\Judopay\WeeklyMitQueueIndex as JudopayWeeklyMitQueueIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\StoreIndex as EcommerceStoreIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\AllBookingsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingInvoiceDatesIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingsManagementIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\ChangeStartDateIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\InactiveBookingsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\NewBookingWizard;
use App\Livewire\FluxAdmin\Pages\Motorbikes\DvlaAddVehicle;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('flux-admin.dashboard');

Route::get('/motorbikes', MotorbikeIndex::class)->name('flux-admin.motorbikes.index');
Route::get('/motorbikes/{motorbike}', MotorbikeShow::class)->name('flux-admin.motorbikes.show');

Route::get('/customers', CustomerIndex::class)->name('flux-admin.customers.index');
Route::get('/customers/{customer}', CustomerShow::class)->name('flux-admin.customers.show');

Route::get('/rentals', RentalIndex::class)->name('flux-admin.rentals.index');
Route::get('/rentals/{booking}', RentalShow::class)->name('flux-admin.rentals.show');

Route::get('/finance', FinanceIndex::class)->name('flux-admin.finance.index');
Route::get('/finance/{application}', FinanceShow::class)->name('flux-admin.finance.show');

Route::get('/pcn', PcnIndex::class)->name('flux-admin.pcn.index');
Route::get('/pcn/{pcnCase}', PcnShow::class)->name('flux-admin.pcn.show');

Route::get('/club', ClubIndex::class)->name('flux-admin.club.index');
Route::get('/club/{clubMember}', ClubShow::class)->name('flux-admin.club.show');

Route::get('/branches', BranchIndex::class)->name('flux-admin.branches.index');
Route::get('/branches/{branch}', BranchShow::class)->name('flux-admin.branches.show');

// Phase 1 — Users & Security
Route::get('/users', UserIndex::class)->name('flux-admin.users.index');
Route::get('/users/create', UserForm::class)->name('flux-admin.users.create');
Route::get('/users/{user}', UserShow::class)->name('flux-admin.users.show');
Route::get('/users/{user}/edit', UserForm::class)->name('flux-admin.users.edit');

Route::get('/roles', RoleIndex::class)->name('flux-admin.roles.index');
Route::get('/roles/create', RoleForm::class)->name('flux-admin.roles.create');
Route::get('/roles/{role}/edit', RoleForm::class)->name('flux-admin.roles.edit');

Route::get('/permissions', PermissionIndex::class)->name('flux-admin.permissions.index');

Route::get('/access-logs', AccessLogIndex::class)->name('flux-admin.access-logs.index');
Route::get('/ip-restrictions', IpRestrictionIndex::class)->name('flux-admin.ip-restrictions.index');

// Phase 6 — Customer sub-CRUDs
Route::get('/customer-appointments', AppointmentIndex::class)->name('flux-admin.customer-appointments.index');
Route::get('/customer-documents', CustomerDocumentIndex::class)->name('flux-admin.customer-documents.index');
Route::get('/customer-documents/{document}/review', CustomerDocumentReview::class)->name('flux-admin.customer-documents.review');

// Phase 2 — Motorbike sub-CRUDs
Route::get('/motorbike-sales', MotorbikeSaleIndex::class)->name('flux-admin.motorbike-sales.index');
Route::get('/motorbike-new', NewMotorbikeIndex::class)->name('flux-admin.motorbike-new.index');
Route::get('/motorbike-for-sale', MotorbikeForSaleIndex::class)->name('flux-admin.motorbike-for-sale.index');
Route::get('/motorbike-repairs', MotorbikeRepairIndex::class)->name('flux-admin.motorbike-repairs.index');
Route::get('/motorbike-compliance', ComplianceIndex::class)->name('flux-admin.motorbike-compliance.index');
Route::get('/motorbike-cat-b', CatBIndex::class)->name('flux-admin.motorbike-cat-b.index');
Route::get('/delivery-enquiries', DeliveryEnquiryIndex::class)->name('flux-admin.delivery-enquiries.index');
Route::get('/ebikes', EbikeIndex::class)->name('flux-admin.ebikes.index');

// Phase 3 — Finance sub-CRUDs
Route::get('/application-items', ApplicationItemIndex::class)->name('flux-admin.application-items.index');
Route::get('/contract-extra-items', ContractExtraItemIndex::class)->name('flux-admin.contract-extra-items.index');
Route::get('/contract-access', ContractAccessIndex::class)->name('flux-admin.contract-access.index');
Route::get('/agreement-access', AgreementAccessIndex::class)->name('flux-admin.agreement-access.index');
Route::get('/booking-invoices', BookingInvoiceIndex::class)->name('flux-admin.booking-invoices.index');

// Phase 5 — PCN sub-CRUDs
Route::get('/pcn-updates', PcnUpdateIndex::class)->name('flux-admin.pcn-updates.index');
Route::get('/pcn-tol-requests', PcnTolIndex::class)->name('flux-admin.pcn-tol-requests.index');
Route::get('/pcn-dashboard', PcnDashboard::class)->name('flux-admin.pcn-dashboard.index');

// Phase 7 — Club sub-CRUDs
Route::get('/club-purchases', ClubPurchaseIndex::class)->name('flux-admin.club-purchases.index');
Route::get('/club-spending', ClubSpendingIndex::class)->name('flux-admin.club-spending.index');
Route::get('/club-spending-payments', ClubSpendingPaymentIndex::class)->name('flux-admin.club-spending-payments.index');

// Phase 10 — Vehicles / Fleet
Route::get('/company-vehicles', CompanyVehicleIndex::class)->name('flux-admin.company-vehicles.index');
Route::get('/mot-bookings', MotBookingIndex::class)->name('flux-admin.mot-bookings.index');
Route::get('/service-bookings', ServiceBookingIndex::class)->name('flux-admin.service-bookings.index');
Route::get('/vehicle-notifications', VehicleNotificationIndex::class)->name('flux-admin.vehicle-notifications.index');
Route::get('/vehicle-issuances', VehicleIssuanceIndex::class)->name('flux-admin.vehicle-issuances.index');
Route::get('/motorbike-claims', VehicleClaimIndex::class)->name('flux-admin.motorbike-claims.index');
Route::get('/recovered-motorbikes', RecoveredMotorbikeIndex::class)->name('flux-admin.recovered-motorbikes.index');
Route::get('/used-purchases', PurchaseUsedIndex::class)->name('flux-admin.used-purchases.index');

// Phase 12 — Blog
Route::get('/blog-posts', BlogPostIndex::class)->name('flux-admin.blog-posts.index');
Route::get('/blog-categories', BlogCategoryIndex::class)->name('flux-admin.blog-categories.index');
Route::get('/blog-tags', BlogTagIndex::class)->name('flux-admin.blog-tags.index');

// Phase 14 — Support
Route::get('/support-conversations', SupportConversationIndex::class)->name('flux-admin.support-conversations.index');

// Phase 15 — E-commerce
Route::get('/ec-orders', EcOrderIndex::class)->name('flux-admin.ec-orders.index');
Route::get('/ds-orders', DsOrderIndex::class)->name('flux-admin.ds-orders.index');
Route::get('/digital-invoices', DigitalInvoiceIndex::class)->name('flux-admin.digital-invoices.index');

// Phase 17 — Misc
Route::get('/contact-queries', ContactQueryIndex::class)->name('flux-admin.contact-queries.index');
Route::get('/careers', CareerIndex::class)->name('flux-admin.careers.index');
Route::get('/employee-schedules', EmployeeScheduleIndex::class)->name('flux-admin.employee-schedules.index');
Route::get('/renting-pricing', RentingPricingIndex::class)->name('flux-admin.renting-pricing.index');

// Phase 8 — Inventory (FIFO)
Route::get('/inventory-brands', InventoryBrandIndex::class)->name('flux-admin.inventory-brands.index');
Route::get('/inventory-categories', InventoryCategoryIndex::class)->name('flux-admin.inventory-categories.index');
Route::get('/inventory-models', InventoryModelIndex::class)->name('flux-admin.inventory-models.index');
Route::get('/inventory-products', InventoryProductIndex::class)->name('flux-admin.inventory-products.index');
Route::get('/inventory-partners', InventoryPartnerIndex::class)->name('flux-admin.inventory-partners.index');
Route::get('/inventory-stock-movements', InventoryStockMovementIndex::class)->name('flux-admin.inventory-stock-movements.index');
Route::get('/oxford-products', OxfordProductIndex::class)->name('flux-admin.oxford-products.index');
Route::get('/purchase-requests', PurchaseRequestIndex::class)->name('flux-admin.purchase-requests.index');
Route::get('/purchase-request-items', PurchaseRequestItemIndex::class)->name('flux-admin.purchase-request-items.index');

// Phase 9 — Spare parts
Route::get('/sp-makes', SpMakeIndex::class)->name('flux-admin.sp-makes.index');
Route::get('/sp-models', SpModelIndex::class)->name('flux-admin.sp-models.index');
Route::get('/sp-fitments', SpFitmentIndex::class)->name('flux-admin.sp-fitments.index');
Route::get('/sp-assemblies', SpAssemblyIndex::class)->name('flux-admin.sp-assemblies.index');
Route::get('/sp-assembly-parts', SpAssemblyPartIndex::class)->name('flux-admin.sp-assembly-parts.index');
Route::get('/sp-parts', SpPartIndex::class)->name('flux-admin.sp-parts.index');
Route::get('/sp-stock-movements', SpStockMovementIndex::class)->name('flux-admin.sp-stock-movements.index');

// Phase 16 — Judopay & recurring billing
Route::get('/judopay-subscriptions', JudopaySubscriptionIndex::class)->name('flux-admin.judopay-subscriptions.index');
Route::get('/judopay-mit-queue', JudopayMitQueueIndex::class)->name('flux-admin.judopay-mit-queue.index');
Route::get('/ngn-mit-queue', NgnMitQueueIndex::class)->name('flux-admin.ngn-mit-queue.index');

// Phase 18 — Customer access links & service videos
Route::get('/rental-terminate-links', RentalTerminateIndex::class)->name('flux-admin.rental-terminate-links.index');
Route::get('/upload-document-links', UploadDocumentIndex::class)->name('flux-admin.upload-document-links.index');
Route::get('/service-videos', ServiceVideoIndex::class)->name('flux-admin.service-videos.index');

// Phase 19 — Remaining sub-CRUDs
Route::get('/motorbike-repair-updates', MotorbikeRepairUpdateIndex::class)->name('flux-admin.motorbike-repair-updates.index');
Route::get('/ds-order-items', DsOrderItemIndex::class)->name('flux-admin.ds-order-items.index');
Route::get('/digital-invoice-items', DigitalInvoiceItemIndex::class)->name('flux-admin.digital-invoice-items.index');
Route::get('/club-redemptions', ClubRedeemIndex::class)->name('flux-admin.club-redemptions.index');
Route::get('/vehicle-delivery-orders', VehicleDeliveryOrderIndex::class)->name('flux-admin.vehicle-delivery-orders.index');
Route::get('/mot-checker', MotCheckerIndex::class)->name('flux-admin.mot-checker.index');
Route::get('/support-messages', SupportMessageIndex::class)->name('flux-admin.support-messages.index');

// Phase 20 — Operational dashboards & tools
Route::get('/rental-operations', RentalOperationsHub::class)->name('flux-admin.rental-operations.index');
Route::get('/active-rentals', ActiveRentalsIndex::class)->name('flux-admin.active-rentals.index');
Route::get('/rental-due-payments', RentalDuePaymentsIndex::class)->name('flux-admin.rental-due-payments.index');
Route::get('/adjust-weekday', AdjustWeekdayIndex::class)->name('flux-admin.adjust-weekday.index');
Route::get('/mot-stats', MotStatsIndex::class)->name('flux-admin.mot-stats.index');
Route::get('/judopay-recurring', JudopayRecurringIndex::class)->name('flux-admin.judopay-recurring.index');
Route::get('/calendar', CalendarIndex::class)->name('flux-admin.calendar.index');
Route::get('/agent-settings', AgentSettingsForm::class)->name('flux-admin.agent-settings.index');
Route::get('/support-inbox', SupportInbox::class)->name('flux-admin.support-inbox.index');

// Phase 21 — Final parity pages
Route::get('/vehicle-history', MotorbikeRecordViewIndex::class)->name('flux-admin.vehicle-history.index');
Route::get('/club-member-vehicles', ClubMemberVehicleIndex::class)->name('flux-admin.club-member-vehicles.index');
Route::get('/active-bookings-summary', ActiveBookingsSummary::class)->name('flux-admin.active-bookings-summary.index');
Route::get('/judopay-mit-dashboard', JudopayMitDashboard::class)->name('flux-admin.judopay-mit-dashboard.index');
Route::get('/judopay-weekly-queue', JudopayWeeklyMitQueueIndex::class)->name('flux-admin.judopay-weekly-queue.index');
Route::get('/store-front', EcommerceStoreIndex::class)->name('flux-admin.store-front.index');

// Phase 22 — Rentals operations (legacy Backpack replacements)
Route::get('/new-booking', NewBookingWizard::class)->name('flux-admin.new-booking.index');
Route::get('/bookings-management', BookingsManagementIndex::class)->name('flux-admin.bookings-management.index');
Route::get('/inactive-bookings', InactiveBookingsIndex::class)->name('flux-admin.inactive-bookings.index');
Route::get('/all-bookings', AllBookingsIndex::class)->name('flux-admin.all-bookings.index');
Route::get('/booking-invoice-dates', BookingInvoiceDatesIndex::class)->name('flux-admin.booking-invoice-dates.index');
Route::get('/change-start-date', ChangeStartDateIndex::class)->name('flux-admin.change-start-date.index');
Route::get('/motorbikes-dvla/create', DvlaAddVehicle::class)->name('flux-admin.motorbikes-dvla.create');
