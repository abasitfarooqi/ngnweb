<?php

use App\Http\Controllers\Admin\RecurringController as JudopayRecurringController;
use App\Http\Controllers\FluxAdmin\CommunicationAttachmentController;
use App\Http\Controllers\FluxAdmin\CommunicationEmailPreviewController;
use App\Http\Controllers\FluxAdmin\SpacesVaultController;
use App\Http\Controllers\FluxAdmin\UnreadBadgeController;
use App\Livewire\FluxAdmin\Pages\Access\RentalTerminateForm;
use App\Livewire\FluxAdmin\Pages\Access\RentalTerminateIndex;
use App\Livewire\FluxAdmin\Pages\Access\ServiceVideoForm;
use App\Livewire\FluxAdmin\Pages\Access\ServiceVideoIndex;
use App\Livewire\FluxAdmin\Pages\Access\UploadDocumentForm;
use App\Livewire\FluxAdmin\Pages\Access\UploadDocumentIndex;
use App\Livewire\FluxAdmin\Pages\Blog\BlogCategoryForm;
use App\Livewire\FluxAdmin\Pages\Blog\BlogCategoryIndex;
use App\Livewire\FluxAdmin\Pages\Blog\BlogPostForm;
use App\Livewire\FluxAdmin\Pages\Blog\BlogPostIndex;
use App\Livewire\FluxAdmin\Pages\Blog\BlogTagForm;
use App\Livewire\FluxAdmin\Pages\Blog\BlogTagIndex;
use App\Livewire\FluxAdmin\Pages\Branches\BranchForm;
use App\Livewire\FluxAdmin\Pages\Branches\BranchIndex;
use App\Livewire\FluxAdmin\Pages\Branches\BranchShow;
use App\Livewire\FluxAdmin\Pages\Club\ClubForm;
use App\Livewire\FluxAdmin\Pages\Club\ClubIndex;
use App\Livewire\FluxAdmin\Pages\Club\ClubMembersIndex;
use App\Livewire\FluxAdmin\Pages\Club\ClubMembersShow;
use App\Livewire\FluxAdmin\Pages\Club\ClubShow;
use App\Livewire\FluxAdmin\Pages\Club\MemberVehicleForm;
use App\Livewire\FluxAdmin\Pages\Club\MemberVehicleIndex as ClubMemberVehicleIndex;
use App\Livewire\FluxAdmin\Pages\Club\PurchaseForm as ClubPurchaseForm;
use App\Livewire\FluxAdmin\Pages\Club\PurchaseIndex as ClubPurchaseIndex;
use App\Livewire\FluxAdmin\Pages\Club\RedeemForm as ClubRedeemForm;
use App\Livewire\FluxAdmin\Pages\Club\RedeemIndex as ClubRedeemIndex;
use App\Livewire\FluxAdmin\Pages\Club\SpendingForm as ClubSpendingForm;
use App\Livewire\FluxAdmin\Pages\Club\SpendingIndex as ClubSpendingIndex;
use App\Livewire\FluxAdmin\Pages\Club\SpendingPaymentForm as ClubSpendingPaymentForm;
use App\Livewire\FluxAdmin\Pages\Club\SpendingPaymentIndex as ClubSpendingPaymentIndex;
use App\Livewire\FluxAdmin\Pages\Communications\CommunicationIndex;
use App\Livewire\FluxAdmin\Pages\Communications\CommunicationSentIndex;
use App\Livewire\FluxAdmin\Pages\Communications\CommunicationSentShow;
use App\Livewire\FluxAdmin\Pages\Communications\CommunicationShow;
use App\Livewire\FluxAdmin\Pages\Customers\AppointmentForm;
use App\Livewire\FluxAdmin\Pages\Customers\AppointmentIndex;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerForm;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerIndex;
use App\Livewire\FluxAdmin\Pages\Customers\CustomerShow;
use App\Livewire\FluxAdmin\Pages\Customers\DocumentIndex as CustomerDocumentIndex;
use App\Livewire\FluxAdmin\Pages\Customers\DocumentReview as CustomerDocumentReview;
use App\Livewire\FluxAdmin\Pages\Dashboard;
use App\Livewire\FluxAdmin\Pages\Dev\DevClubOtpIndex;
use App\Livewire\FluxAdmin\Pages\Dev\QueueMonitorIndex;
use App\Livewire\FluxAdmin\Pages\Dev\SpacesVaultExplorer;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DigitalInvoiceForm;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DigitalInvoiceIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DigitalInvoiceItemForm;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DigitalInvoiceItemIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DsOrderForm;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DsOrderIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DsOrderItemForm;
use App\Livewire\FluxAdmin\Pages\Ecommerce\DsOrderItemIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\EcOrderForm;
use App\Livewire\FluxAdmin\Pages\Ecommerce\EcOrderIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\ShopOrderIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\SparePartOrderIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\StoreIndex as EcommerceStoreIndex;
use App\Livewire\FluxAdmin\Pages\Ecommerce\StoreProductForm;
use App\Livewire\FluxAdmin\Pages\Finance\AgreementAccessForm;
use App\Livewire\FluxAdmin\Pages\Finance\AgreementAccessIndex;
use App\Livewire\FluxAdmin\Pages\Finance\ApplicationItemForm;
use App\Livewire\FluxAdmin\Pages\Finance\ApplicationItemIndex;
use App\Livewire\FluxAdmin\Pages\Finance\ContractAccessForm;
use App\Livewire\FluxAdmin\Pages\Finance\ContractAccessIndex;
use App\Livewire\FluxAdmin\Pages\Finance\ContractExtraItemForm;
use App\Livewire\FluxAdmin\Pages\Finance\ContractExtraItemIndex;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceForm;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceIndex;
use App\Livewire\FluxAdmin\Pages\Finance\FinanceShow;
use App\Livewire\FluxAdmin\Pages\GlobalSearchIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\BrandForm as InventoryBrandForm;
use App\Livewire\FluxAdmin\Pages\Inventory\BrandIndex as InventoryBrandIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\CategoryForm as InventoryCategoryForm;
use App\Livewire\FluxAdmin\Pages\Inventory\CategoryIndex as InventoryCategoryIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\InventoryModelForm;
use App\Livewire\FluxAdmin\Pages\Inventory\InventoryStockMovementForm;
use App\Livewire\FluxAdmin\Pages\Inventory\ModelIndex as InventoryModelIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\OxfordProductForm;
use App\Livewire\FluxAdmin\Pages\Inventory\OxfordProductIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\PartnerForm as InventoryPartnerForm;
use App\Livewire\FluxAdmin\Pages\Inventory\PartnerIndex as InventoryPartnerIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\ProductForm as InventoryProductForm;
use App\Livewire\FluxAdmin\Pages\Inventory\ProductIndex as InventoryProductIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\PurchaseRequestForm;
use App\Livewire\FluxAdmin\Pages\Inventory\PurchaseRequestIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\PurchaseRequestItemForm;
use App\Livewire\FluxAdmin\Pages\Inventory\PurchaseRequestItemIndex;
use App\Livewire\FluxAdmin\Pages\Inventory\StockMovementIndex as InventoryStockMovementIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\MitQueueForm as JudopayMitQueueForm;
use App\Livewire\FluxAdmin\Pages\Judopay\MitQueueIndex as JudopayMitQueueIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\NgnMitQueueForm;
use App\Livewire\FluxAdmin\Pages\Judopay\NgnMitQueueIndex;
use App\Livewire\FluxAdmin\Pages\Judopay\SubscriptionForm as JudopaySubscriptionForm;
use App\Livewire\FluxAdmin\Pages\Judopay\SubscriptionIndex as JudopaySubscriptionIndex;
use App\Livewire\FluxAdmin\Pages\Misc\CalendarForm;
use App\Livewire\FluxAdmin\Pages\Misc\CalendarIndex;
use App\Livewire\FluxAdmin\Pages\Misc\CareerForm;
use App\Livewire\FluxAdmin\Pages\Misc\CareerIndex;
use App\Livewire\FluxAdmin\Pages\Misc\ContactQueryForm;
use App\Livewire\FluxAdmin\Pages\Misc\ContactQueryIndex;
use App\Livewire\FluxAdmin\Pages\Misc\EmployeeScheduleForm;
use App\Livewire\FluxAdmin\Pages\Misc\EmployeeScheduleIndex;
use App\Livewire\FluxAdmin\Pages\Misc\RentingPricingForm;
use App\Livewire\FluxAdmin\Pages\Modules\ModuleHub;
use App\Livewire\FluxAdmin\Pages\Motorbikes\CatBForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\CatBIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\ComplianceForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\ComplianceIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\CompliancePreviewIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\DeliveryEnquiryForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\DeliveryEnquiryIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\DvlaAddVehicle;
use App\Livewire\FluxAdmin\Pages\Motorbikes\EbikeForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\EbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\ForSaleForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\ForSaleIndex as MotorbikeForSaleIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeAvailableForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeAvailableIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeRecordViewIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\MotorbikeShow;
use App\Livewire\FluxAdmin\Pages\Motorbikes\NewMotorbikeForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\NewMotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\RepairForm as MotorbikeRepairForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\RepairIndex as MotorbikeRepairIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\RepairUpdateForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\RepairUpdateIndex as MotorbikeRepairUpdateIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\SaleForm as MotorbikeSaleForm;
use App\Livewire\FluxAdmin\Pages\Motorbikes\SaleIndex as MotorbikeSaleIndex;
use App\Livewire\FluxAdmin\Pages\Motorbikes\SaleOperationsHub;
use App\Livewire\FluxAdmin\Pages\Motorbikes\TotalVehiclesIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnCreate;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnDashboard;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnEdit;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnShow;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnTolForm;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnTolIndex;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnUpdateForm;
use App\Livewire\FluxAdmin\Pages\Pcn\PcnUpdateIndex;
use App\Livewire\FluxAdmin\Pages\Permissions\PermissionForm;
use App\Livewire\FluxAdmin\Pages\Permissions\PermissionIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\ActiveBookingsSummary;
use App\Livewire\FluxAdmin\Pages\Rentals\ActiveRentalsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\AdjustWeekdayIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\AllBookingsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingInvoiceDatesIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingInvoiceForm;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingInvoiceIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\BookingsManagementIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\ChangeStartDateIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\DuePaymentsIndex as RentalDuePaymentsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\EndedWithPendingsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\InactiveBookingsIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\MotorbikePricingHub;
use App\Livewire\FluxAdmin\Pages\Rentals\NewBookingWizard;
use App\Livewire\FluxAdmin\Pages\Rentals\OperationsHub as RentalOperationsHub;
use App\Livewire\FluxAdmin\Pages\Rentals\RentalIndex;
use App\Livewire\FluxAdmin\Pages\Rentals\RentalShow;
use App\Livewire\FluxAdmin\Pages\Roles\RoleForm;
use App\Livewire\FluxAdmin\Pages\Roles\RoleIndex;
use App\Livewire\FluxAdmin\Pages\Security\AccessLogIndex;
use App\Livewire\FluxAdmin\Pages\Security\IpRestrictionForm;
use App\Livewire\FluxAdmin\Pages\Security\IpRestrictionIndex;
use App\Livewire\FluxAdmin\Pages\Settings\AgentSettingsForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\AssemblyForm as SpAssemblyForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\AssemblyIndex as SpAssemblyIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\AssemblyPartForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\AssemblyPartIndex as SpAssemblyPartIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\FitmentForm as SpFitmentForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\FitmentIndex as SpFitmentIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\MakeForm as SpMakeForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\MakeIndex as SpMakeIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\PartForm as SpPartForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\PartIndex as SpPartIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\SpModelForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\SpModelIndex;
use App\Livewire\FluxAdmin\Pages\SpareParts\SpStockMovementForm;
use App\Livewire\FluxAdmin\Pages\SpareParts\StockMovementIndex as SpStockMovementIndex;
use App\Livewire\FluxAdmin\Pages\Support\SupportConversationForm;
use App\Livewire\FluxAdmin\Pages\Support\SupportConversationIndex;
use App\Livewire\FluxAdmin\Pages\Support\SupportInbox;
use App\Livewire\FluxAdmin\Pages\Support\SupportMessageForm;
use App\Livewire\FluxAdmin\Pages\Support\SupportMessageIndex;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyAnswerIndex;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyCampaignIndex;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyForm;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyIndex;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyOptionForm;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyOptionIndex;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyQuestionForm;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyQuestionIndex;
use App\Livewire\FluxAdmin\Pages\Surveys\SurveyResponseIndex;
use App\Livewire\FluxAdmin\Pages\Users\UserForm;
use App\Livewire\FluxAdmin\Pages\Users\UserIndex;
use App\Livewire\FluxAdmin\Pages\Users\UserShow;
use App\Livewire\FluxAdmin\Pages\Vehicles\ClaimForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\ClaimIndex as VehicleClaimIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\CompanyVehicleForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\CompanyVehicleIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotBookingCalendar;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotBookingForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotBookingIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotCheckerForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotCheckerIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotOperationsHub;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotStatsForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\MotStatsIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\PurchaseUsedForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\PurchaseUsedIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\RecoveredIndex as RecoveredMotorbikeIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\RecoveredMotorbikeForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\ServiceBookingForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\ServiceBookingIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleDeliveryOrderForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleDeliveryOrderIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleIssuanceForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleIssuanceIndex;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleNotificationForm;
use App\Livewire\FluxAdmin\Pages\Vehicles\VehicleNotificationIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('flux-admin.dashboard');
Route::get('/dashboard', Dashboard::class)->name('flux-admin.dashboard.path');
Route::get('/search', GlobalSearchIndex::class)->name('flux-admin.search');

Route::get('/modules/{module}', ModuleHub::class)->name('flux-admin.modules.show');

// Backpack-compatible Flux slugs. These keep the same URL shape as Backpack,
// with only the prefix changed from `/ngn-admin` to `/flux-admin`.
$backpackCrudAlias = function (
    string $slug,
    string $indexComponent,
    ?string $formComponent = null,
    string $parameter = 'id',
    ?string $showComponent = null,
): void {
    Route::get('/'.$slug, $indexComponent)->name('flux-admin.backpack.'.$slug.'.index');

    if ($formComponent) {
        Route::get('/'.$slug.'/create', $formComponent)->name('flux-admin.backpack.'.$slug.'.create');
        Route::get('/'.$slug.'/{'.$parameter.'}/edit', $formComponent)->name('flux-admin.backpack.'.$slug.'.edit');
    }

    if ($showComponent) {
        Route::get('/'.$slug.'/{'.$parameter.'}', $showComponent)->name('flux-admin.backpack.'.$slug.'.show');
    }
};

$backpackCrudAlias('user', UserIndex::class, UserForm::class, 'user', UserShow::class);
$backpackCrudAlias('role', RoleIndex::class, RoleForm::class, 'role');
$backpackCrudAlias('permission', PermissionIndex::class, PermissionForm::class, 'id');
$backpackCrudAlias('finance-application', FinanceIndex::class, FinanceForm::class, 'application', FinanceShow::class);
$backpackCrudAlias('application-item', ApplicationItemIndex::class, ApplicationItemForm::class, 'applicationItem');
$backpackCrudAlias('customer', CustomerIndex::class, CustomerForm::class, 'customer', CustomerShow::class);
Route::get('/pcn-case', PcnIndex::class)->name('flux-admin.backpack.pcn-case.index');
Route::get('/pcn-case/create', PcnCreate::class)->name('flux-admin.backpack.pcn-case.create');
Route::get('/pcn-case/{pcnCase}/edit', PcnEdit::class)->name('flux-admin.backpack.pcn-case.edit');
Route::get('/pcn-case/{pcnCase}', PcnShow::class)->name('flux-admin.backpack.pcn-case.show');
Route::get('/pcn-case-exp', PcnIndex::class)->name('flux-admin.backpack.pcn-case-exp.index');
Route::get('/pcn-case-exp/create', PcnCreate::class)->name('flux-admin.backpack.pcn-case-exp.create');
Route::get('/pcn-case-exp/{pcnCase}/edit', PcnEdit::class)->name('flux-admin.backpack.pcn-case-exp.edit');
Route::get('/pcn-case-exp/{pcnCase}', PcnShow::class)->name('flux-admin.backpack.pcn-case-exp.show');
$backpackCrudAlias('pcn-case-update', PcnUpdateIndex::class, PcnUpdateForm::class, 'id');
$backpackCrudAlias('booking-invoice', BookingInvoiceIndex::class, BookingInvoiceForm::class, 'bookingInvoice');
$backpackCrudAlias('contract-extra-item', ContractExtraItemIndex::class, ContractExtraItemForm::class, 'contractExtraItem');
$backpackCrudAlias('employee-schedule', EmployeeScheduleIndex::class, EmployeeScheduleForm::class, 'employeeSchedule');
$backpackCrudAlias('motorbike-annual-compliance', ComplianceIndex::class, ComplianceForm::class, 'compliance');
$backpackCrudAlias('motorbike-annual-compliance-m', ComplianceIndex::class, ComplianceForm::class, 'compliance');
$backpackCrudAlias('branch', BranchIndex::class, BranchForm::class, 'branch', BranchShow::class);
$backpackCrudAlias('mot-booking', MotBookingIndex::class, MotBookingForm::class, 'motBooking');
Route::get('/mot-booking/calendar', MotBookingCalendar::class)->name('flux-admin.backpack.mot-booking.calendar');
$backpackCrudAlias('calander', CalendarIndex::class, CalendarForm::class, 'calendarEvent');
$backpackCrudAlias('company-vehicle', CompanyVehicleIndex::class, CompanyVehicleForm::class, 'companyVehicle');
$backpackCrudAlias('vehicle-database', ComplianceIndex::class, ComplianceForm::class, 'compliance');
$backpackCrudAlias('vehicle-notification', VehicleNotificationIndex::class, VehicleNotificationForm::class, 'vehicleNotification');
$backpackCrudAlias('motorbike', MotorbikeIndex::class, MotorbikeForm::class, 'motorbike', MotorbikeShow::class);
$backpackCrudAlias('motorbike-list', MotorbikeIndex::class, MotorbikeForm::class, 'motorbike', MotorbikeShow::class);
$backpackCrudAlias('motorbike-repair', MotorbikeRepairIndex::class, MotorbikeRepairForm::class, 'motorbikeRepair');
$backpackCrudAlias('motorbike-repair-update', MotorbikeRepairUpdateIndex::class, RepairUpdateForm::class, 'motorbikeRepairUpdate');
$backpackCrudAlias('claim-motorbike', VehicleClaimIndex::class, ClaimForm::class, 'claimMotorbike');
$backpackCrudAlias('purchase-request', PurchaseRequestIndex::class, PurchaseRequestForm::class, 'purchaseRequest');
$backpackCrudAlias('purchase-request-item', PurchaseRequestItemIndex::class, PurchaseRequestItemForm::class, 'purchaseRequestItem');
$backpackCrudAlias('upload-document-access', UploadDocumentIndex::class, UploadDocumentForm::class, 'id');
$backpackCrudAlias('create-stock-logs', InventoryStockMovementIndex::class);
$backpackCrudAlias('recovered-motorbike', RecoveredMotorbikeIndex::class, RecoveredMotorbikeForm::class, 'recoveredMotorbike');
$backpackCrudAlias('vehicle-issuance', VehicleIssuanceIndex::class, VehicleIssuanceForm::class, 'vehicleIssuance');
$backpackCrudAlias('customer-document', CustomerDocumentIndex::class);
$backpackCrudAlias('used-vehicle-seller', PurchaseUsedIndex::class, PurchaseUsedForm::class, 'purchaseUsed');
$backpackCrudAlias('ngn-product', InventoryProductIndex::class, InventoryProductForm::class, 'product');
$backpackCrudAlias('ngn-category', InventoryCategoryIndex::class, InventoryCategoryForm::class, 'category');
$backpackCrudAlias('ngn-model', InventoryModelIndex::class, InventoryModelForm::class, 'inventoryModel');
$backpackCrudAlias('ngn-brand', InventoryBrandIndex::class, InventoryBrandForm::class, 'brand');
$backpackCrudAlias('ngn-career', CareerIndex::class, CareerForm::class, 'career');
$backpackCrudAlias('ngn-stock-movement', InventoryStockMovementIndex::class);
$backpackCrudAlias('ngn-inventory-management', InventoryStockMovementIndex::class);
$backpackCrudAlias('ngn-product-management', InventoryProductIndex::class, InventoryProductForm::class, 'product');
$backpackCrudAlias('ngn-stock-handler', InventoryProductIndex::class, InventoryProductForm::class, 'product');
$backpackCrudAlias('sp-make', SpMakeIndex::class, SpMakeForm::class, 'spMake');
$backpackCrudAlias('sp-model', SpModelIndex::class, SpModelForm::class, 'spModel');
$backpackCrudAlias('sp-fitment', SpFitmentIndex::class, SpFitmentForm::class, 'spFitment');
$backpackCrudAlias('sp-assembly', SpAssemblyIndex::class, SpAssemblyForm::class, 'spAssembly');
$backpackCrudAlias('sp-part', SpPartIndex::class, SpPartForm::class, 'spPart');
$backpackCrudAlias('sp-assembly-part', SpAssemblyPartIndex::class);
$backpackCrudAlias('sp-stock-movement', SpStockMovementIndex::class);
$backpackCrudAlias('sp-stock-handler', SpPartIndex::class, SpPartForm::class, 'spPart');
$backpackCrudAlias('new-motorbike', NewMotorbikeIndex::class, NewMotorbikeForm::class, 'newMotorbike');
$backpackCrudAlias('club-member', ClubIndex::class, ClubForm::class, 'clubMember', ClubShow::class);
$backpackCrudAlias('club-member-purchase', ClubPurchaseIndex::class, ClubPurchaseForm::class, 'purchase');
$backpackCrudAlias('club-member-spending', ClubSpendingIndex::class, ClubSpendingForm::class, 'spending');
$backpackCrudAlias('club-member-redeem', ClubRedeemIndex::class, ClubRedeemForm::class, 'redeem');
$backpackCrudAlias('club-member-spending-payment', ClubSpendingPaymentIndex::class, ClubSpendingPaymentForm::class, 'spendingPayment');
$backpackCrudAlias('clubmembers-details', ClubMemberVehicleIndex::class);
$backpackCrudAlias('motorbike-record-view', MotorbikeRecordViewIndex::class);
$backpackCrudAlias('rental-terminate-access', RentalTerminateIndex::class, RentalTerminateForm::class, 'id');
$backpackCrudAlias('ngn-partner', InventoryPartnerIndex::class, InventoryPartnerForm::class, 'partner');
$backpackCrudAlias('blog-post', BlogPostIndex::class, BlogPostForm::class, 'blogPost');
$backpackCrudAlias('blog-category', BlogCategoryIndex::class, BlogCategoryForm::class, 'blogCategory');
$backpackCrudAlias('blog-tag', BlogTagIndex::class, BlogTagForm::class, 'blogTag');
$backpackCrudAlias('motorbikes-sale', MotorbikeSaleIndex::class, MotorbikeSaleForm::class, 'motorbikesSale');
$backpackCrudAlias('new-motorbikes-for-sale', MotorbikeForSaleIndex::class, ForSaleForm::class, 'motorcycle');
$backpackCrudAlias('vehicle-delivery-order', VehicleDeliveryOrderIndex::class, VehicleDeliveryOrderForm::class, 'vehicleDeliveryOrder');
$backpackCrudAlias('ec-order', EcOrderIndex::class, EcOrderForm::class, 'ecOrder');
$backpackCrudAlias('survey', SurveyIndex::class, SurveyForm::class, 'survey');
$backpackCrudAlias('survey-question', SurveyQuestionIndex::class, SurveyQuestionForm::class, 'surveyQuestion');
$backpackCrudAlias('survey-option', SurveyOptionIndex::class, SurveyOptionForm::class, 'surveyOption');
$backpackCrudAlias('survey-response', SurveyResponseIndex::class);
$backpackCrudAlias('survey-answer', SurveyAnswerIndex::class);
$backpackCrudAlias('contact-query', ContactQueryIndex::class, ContactQueryForm::class, 'contactQuery');
$backpackCrudAlias('service-booking', ServiceBookingIndex::class, ServiceBookingForm::class, 'serviceBooking');
$backpackCrudAlias('support-conversation', SupportConversationIndex::class, SupportConversationForm::class, 'supportConversation');
$backpackCrudAlias('support-message', SupportMessageIndex::class, SupportMessageForm::class, 'supportMessage');
$backpackCrudAlias('motorbike-delivery-order-enquiries', DeliveryEnquiryIndex::class, DeliveryEnquiryForm::class, 'deliveryEnquiry');
$backpackCrudAlias('ip-restriction', IpRestrictionIndex::class, IpRestrictionForm::class, 'ipRestriction');
$backpackCrudAlias('access-log', AccessLogIndex::class);
$backpackCrudAlias('renting-service-video', ServiceVideoIndex::class, ServiceVideoForm::class, 'serviceVideo');
$backpackCrudAlias('motorbike-available', MotorbikeAvailableIndex::class, MotorbikeAvailableForm::class, 'motorbike');
$backpackCrudAlias('ngn-digital-invoice', DigitalInvoiceIndex::class, DigitalInvoiceForm::class, 'digitalInvoice');
$backpackCrudAlias('ngn-digital-invoice-item', DigitalInvoiceItemIndex::class, DigitalInvoiceItemForm::class, 'invoiceItem');
$backpackCrudAlias('pcn-tol-request', PcnTolIndex::class, PcnTolForm::class, 'id');
$backpackCrudAlias('dev-ngn-mit-queue', NgnMitQueueIndex::class, NgnMitQueueForm::class, 'id');
$backpackCrudAlias('dev-judopay-subscription', JudopaySubscriptionIndex::class, JudopaySubscriptionForm::class, 'id');
$backpackCrudAlias('dev-judopay-mit-queue', JudopayMitQueueIndex::class, JudopayMitQueueForm::class, 'id');
$backpackCrudAlias('dev-club-otp', DevClubOtpIndex::class);
Route::get('/ngn-renting-booking', AllBookingsIndex::class)->name('flux-admin.backpack.ngn-renting-booking.index');
Route::get('/ngn-renting-booking/create', NewBookingWizard::class)->name('flux-admin.backpack.ngn-renting-booking.create');
Route::get('/ngn-renting-booking/{booking}/edit', RentalShow::class)->name('flux-admin.backpack.ngn-renting-booking.edit');
Route::get('/ngn-renting-booking/{booking}', RentalShow::class)->name('flux-admin.backpack.ngn-renting-booking.show');

Route::get('/total-vehicles', TotalVehiclesIndex::class)->name('flux-admin.total-vehicles.index');

Route::get('/motorbikes', MotorbikeIndex::class)->name('flux-admin.motorbikes.index');
Route::get('/motorbikes/create', MotorbikeForm::class)->name('flux-admin.motorbikes.create');
Route::get('/motorbikes/{motorbike}/edit', MotorbikeForm::class)->name('flux-admin.motorbikes.edit');
Route::get('/motorbikes/{motorbike}', MotorbikeShow::class)->name('flux-admin.motorbikes.show');

Route::get('/customers/create', CustomerForm::class)->name('flux-admin.customers.create');
Route::get('/customers/{customer}/edit', CustomerForm::class)->name('flux-admin.customers.edit');
Route::get('/customers', CustomerIndex::class)->name('flux-admin.customers.index');
Route::get('/customers/{customer}', CustomerShow::class)->name('flux-admin.customers.show');

Route::get('/rentals', RentalIndex::class)->name('flux-admin.rentals.index');
Route::get('/rentals/{booking}', RentalShow::class)->name('flux-admin.rentals.show');
Route::post('/rentals/{booking}/service-videos', [RentalServiceVideoUploadController::class, 'store'])->name('flux-admin.rentals.service-videos.store');

Route::get('/finance/create', FinanceForm::class)->name('flux-admin.finance.create');
Route::get('/finance/{application}/edit', FinanceForm::class)->name('flux-admin.finance.edit');
Route::get('/finance', FinanceIndex::class)->name('flux-admin.finance.index');
Route::get('/finance/{application}', FinanceShow::class)->name('flux-admin.finance.show');

Route::get('/pcn', PcnIndex::class)->name('flux-admin.pcn.index');
Route::get('/pcn/create', PcnCreate::class)->name('flux-admin.pcn.create');
Route::get('/pcn/{pcnCase}/edit', PcnEdit::class)->name('flux-admin.pcn.edit');
Route::get('/pcn/{pcnCase}', PcnShow::class)->name('flux-admin.pcn.show');

Route::get('/club', ClubIndex::class)->name('flux-admin.club.index');
Route::get('/club/create', ClubForm::class)->name('flux-admin.club.create');
Route::get('/club/{clubMember}/edit', ClubForm::class)->name('flux-admin.club.edit');
Route::get('/club/{clubMember}', ClubShow::class)->name('flux-admin.club.show');

Route::get('/club-members', ClubMembersIndex::class)->name('flux-admin.club-members.index');
Route::get('/club-members/{clubMember}', ClubMembersShow::class)->name('flux-admin.club-members.show');

Route::get('/branches', BranchIndex::class)->name('flux-admin.branches.index');
Route::get('/branches/create', BranchForm::class)->name('flux-admin.branches.create');
Route::get('/branches/{branch}', BranchShow::class)->name('flux-admin.branches.show');

Route::get('/unread-badges', UnreadBadgeController::class)->name('flux-admin.unread-badges');

Route::get('/communications', CommunicationIndex::class)->name('flux-admin.communications.index');
Route::get('/communications/sent', CommunicationSentIndex::class)->name('flux-admin.communications.sent.index');
Route::get('/communications/sent/{communication:uuid}/attachments/{attachment}', CommunicationAttachmentController::class)->name('flux-admin.communications.sent.attachments.show');
Route::get('/communications/sent/{communication:uuid}', CommunicationSentShow::class)->name('flux-admin.communications.sent.show');
Route::get('/communications/{communicationDefinition}/email-preview', CommunicationEmailPreviewController::class)->name('flux-admin.communications.email-preview');
Route::get('/communications/{communicationDefinition}', CommunicationShow::class)->name('flux-admin.communications.show');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('flux-admin.login');
})->name('flux-admin.logout');

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
Route::get('/customer-appointments/create', AppointmentForm::class)->name('flux-admin.customer-appointments.create');
Route::get('/customer-appointments/{customerAppointment}/edit', AppointmentForm::class)->name('flux-admin.customer-appointments.edit');
Route::get('/customer-documents', CustomerDocumentIndex::class)->name('flux-admin.customer-documents.index');
Route::get('/customer-documents/{document}/review', CustomerDocumentReview::class)->name('flux-admin.customer-documents.review');

// Phase 2 — Motorbike sub-CRUDs
Route::get('/sale', SaleOperationsHub::class)->name('flux-admin.sale.index');
Route::get('/motorbike-sales', MotorbikeSaleIndex::class)->name('flux-admin.motorbike-sales.index');
Route::get('/motorbike-sales/create', MotorbikeSaleForm::class)->name('flux-admin.motorbike-sales.create');
Route::get('/motorbike-sales/{motorbikesSale}/edit', MotorbikeSaleForm::class)->name('flux-admin.motorbike-sales.edit');
Route::get('/motorbike-new', NewMotorbikeIndex::class)->name('flux-admin.motorbike-new.index');
Route::get('/motorbike-new/create', NewMotorbikeForm::class)->name('flux-admin.motorbike-new.create');
Route::get('/motorbike-new/{newMotorbike}/edit', NewMotorbikeForm::class)->name('flux-admin.motorbike-new.edit');
Route::get('/motorbike-for-sale', MotorbikeForSaleIndex::class)->name('flux-admin.motorbike-for-sale.index');
Route::get('/motorbike-for-sale/create', ForSaleForm::class)->name('flux-admin.motorbike-for-sale.create');
Route::get('/motorbike-for-sale/{motorcycle}/edit', ForSaleForm::class)->name('flux-admin.motorbike-for-sale.edit');
Route::get('/motorbike-repairs', MotorbikeRepairIndex::class)->name('flux-admin.motorbike-repairs.index');
Route::get('/motorbike-repairs/create', MotorbikeRepairForm::class)->name('flux-admin.motorbike-repairs.create');
Route::get('/motorbike-repairs/{motorbikeRepair}/edit', MotorbikeRepairForm::class)->name('flux-admin.motorbike-repairs.edit');
Route::get('/motorbike-compliance/preview', CompliancePreviewIndex::class)->name('flux-admin.motorbike-compliance.preview');
Route::get('/motorbike-compliance', ComplianceIndex::class)->name('flux-admin.motorbike-compliance.index');
Route::get('/motorbike-compliance/create', ComplianceForm::class)->name('flux-admin.motorbike-compliance.create');
Route::get('/motorbike-compliance/{compliance}/edit', ComplianceForm::class)->name('flux-admin.motorbike-compliance.edit');
Route::get('/motorbike-cat-b', CatBIndex::class)->name('flux-admin.motorbike-cat-b.index');
Route::get('/motorbike-cat-b/create', CatBForm::class)->name('flux-admin.motorbike-cat-b.create');
Route::get('/motorbike-cat-b/{motorbikeCatB}/edit', CatBForm::class)->name('flux-admin.motorbike-cat-b.edit');
Route::get('/delivery-enquiries', DeliveryEnquiryIndex::class)->name('flux-admin.delivery-enquiries.index');
Route::get('/delivery-enquiries/create', DeliveryEnquiryForm::class)->name('flux-admin.delivery-enquiries.create');
Route::get('/delivery-enquiries/{deliveryEnquiry}/edit', DeliveryEnquiryForm::class)->name('flux-admin.delivery-enquiries.edit');
Route::get('/ebikes', EbikeIndex::class)->name('flux-admin.ebikes.index');
Route::get('/ebikes/create', EbikeForm::class)->name('flux-admin.ebikes.create');
Route::get('/ebikes/{motorbike}/edit', EbikeForm::class)->name('flux-admin.ebikes.edit');

// Phase 3 — Finance sub-CRUDs
Route::get('/application-items', ApplicationItemIndex::class)->name('flux-admin.application-items.index');
Route::get('/application-items/create', ApplicationItemForm::class)->name('flux-admin.application-items.create');
Route::get('/application-items/{applicationItem}/edit', ApplicationItemForm::class)->name('flux-admin.application-items.edit');
Route::get('/contract-extra-items', ContractExtraItemIndex::class)->name('flux-admin.contract-extra-items.index');
Route::get('/contract-extra-items/create', ContractExtraItemForm::class)->name('flux-admin.contract-extra-items.create');
Route::get('/contract-extra-items/{contractExtraItem}/edit', ContractExtraItemForm::class)->name('flux-admin.contract-extra-items.edit');
Route::get('/contract-access', ContractAccessIndex::class)->name('flux-admin.contract-access.index');
Route::get('/contract-access/create', ContractAccessForm::class)->name('flux-admin.contract-access.create');
Route::get('/contract-access/{id}/edit', ContractAccessForm::class)->name('flux-admin.contract-access.edit');
Route::get('/agreement-access', AgreementAccessIndex::class)->name('flux-admin.agreement-access.index');
Route::get('/agreement-access/create', AgreementAccessForm::class)->name('flux-admin.agreement-access.create');
Route::get('/agreement-access/{id}/edit', AgreementAccessForm::class)->name('flux-admin.agreement-access.edit');
Route::get('/booking-invoices', BookingInvoiceIndex::class)->name('flux-admin.booking-invoices.index');
Route::get('/booking-invoices/create', BookingInvoiceForm::class)->name('flux-admin.booking-invoices.create');
Route::get('/booking-invoices/{bookingInvoice}/edit', BookingInvoiceForm::class)->name('flux-admin.booking-invoices.edit');

// Phase 5 — PCN sub-CRUDs
Route::get('/pcn-updates', PcnUpdateIndex::class)->name('flux-admin.pcn-updates.index');
Route::get('/pcn-updates/create', PcnUpdateForm::class)->name('flux-admin.pcn-updates.create');
Route::get('/pcn-updates/{id}/edit', PcnUpdateForm::class)->name('flux-admin.pcn-updates.edit');
Route::get('/pcn-tol-requests', PcnTolIndex::class)->name('flux-admin.pcn-tol-requests.index');
Route::get('/pcn-tol-requests/create', PcnTolForm::class)->name('flux-admin.pcn-tol-requests.create');
Route::get('/pcn-tol-requests/{id}/edit', PcnTolForm::class)->name('flux-admin.pcn-tol-requests.edit');
Route::get('/pcn-dashboard', PcnDashboard::class)->name('flux-admin.pcn-dashboard.index');

// Phase 7 — Club sub-CRUDs
Route::get('/club-purchases', ClubPurchaseIndex::class)->name('flux-admin.club-purchases.index');
Route::get('/club-spending', ClubSpendingIndex::class)->name('flux-admin.club-spending.index');
Route::get('/club-spending-payments', ClubSpendingPaymentIndex::class)->name('flux-admin.club-spending-payments.index');

// Phase 10 — Vehicles / Fleet
Route::get('/company-vehicles', CompanyVehicleIndex::class)->name('flux-admin.company-vehicles.index');
Route::get('/company-vehicles/create', CompanyVehicleForm::class)->name('flux-admin.company-vehicles.create');
Route::get('/company-vehicles/{companyVehicle}/edit', CompanyVehicleForm::class)->name('flux-admin.company-vehicles.edit');
Route::get('/mot', MotOperationsHub::class)->name('flux-admin.mot.index');
Route::get('/mot-bookings', MotBookingIndex::class)->name('flux-admin.mot-bookings.index');
Route::get('/mot-bookings/calendar', MotBookingCalendar::class)->name('flux-admin.mot-bookings.calendar');
Route::get('/mot-bookings/create', MotBookingForm::class)->name('flux-admin.mot-bookings.create');
Route::get('/mot-bookings/{motBooking}/edit', MotBookingForm::class)->name('flux-admin.mot-bookings.edit');
Route::get('/service-bookings', ServiceBookingIndex::class)->name('flux-admin.service-bookings.index');
Route::get('/service-bookings/create', ServiceBookingForm::class)->name('flux-admin.service-bookings.create');
Route::get('/service-bookings/{serviceBooking}/edit', ServiceBookingForm::class)->name('flux-admin.service-bookings.edit');
Route::get('/vehicle-notifications', VehicleNotificationIndex::class)->name('flux-admin.vehicle-notifications.index');
Route::get('/vehicle-notifications/create', VehicleNotificationForm::class)->name('flux-admin.vehicle-notifications.create');
Route::get('/vehicle-notifications/{vehicleNotification}/edit', VehicleNotificationForm::class)->name('flux-admin.vehicle-notifications.edit');
Route::get('/vehicle-issuances', VehicleIssuanceIndex::class)->name('flux-admin.vehicle-issuances.index');
Route::get('/vehicle-issuances/create', VehicleIssuanceForm::class)->name('flux-admin.vehicle-issuances.create');
Route::get('/vehicle-issuances/{vehicleIssuance}/edit', VehicleIssuanceForm::class)->name('flux-admin.vehicle-issuances.edit');
Route::get('/motorbike-claims', VehicleClaimIndex::class)->name('flux-admin.motorbike-claims.index');
Route::get('/motorbike-claims/create', ClaimForm::class)->name('flux-admin.motorbike-claims.create');
Route::get('/motorbike-claims/{claimMotorbike}/edit', ClaimForm::class)->name('flux-admin.motorbike-claims.edit');
Route::get('/recovered-motorbikes', RecoveredMotorbikeIndex::class)->name('flux-admin.recovered-motorbikes.index');
Route::get('/recovered-motorbikes/create', RecoveredMotorbikeForm::class)->name('flux-admin.recovered-motorbikes.create');
Route::get('/recovered-motorbikes/{recoveredMotorbike}/edit', RecoveredMotorbikeForm::class)->name('flux-admin.recovered-motorbikes.edit');
Route::get('/used-purchases', PurchaseUsedIndex::class)->name('flux-admin.used-purchases.index');
Route::get('/used-purchases/create', PurchaseUsedForm::class)->name('flux-admin.used-purchases.create');
Route::get('/used-purchases/{purchaseUsed}/edit', PurchaseUsedForm::class)->name('flux-admin.used-purchases.edit');

// Phase 12 — Blog
Route::get('/blog-posts', BlogPostIndex::class)->name('flux-admin.blog-posts.index');
Route::get('/blog-categories', BlogCategoryIndex::class)->name('flux-admin.blog-categories.index');
Route::get('/blog-tags', BlogTagIndex::class)->name('flux-admin.blog-tags.index');

// Phase 14 — Support
Route::get('/support-conversations', SupportConversationIndex::class)->name('flux-admin.support-conversations.index');
Route::get('/support-conversations/create', SupportConversationForm::class)->name('flux-admin.support-conversations.create');
Route::get('/support-conversations/{supportConversation}/edit', SupportConversationForm::class)->name('flux-admin.support-conversations.edit');

// Phase 15 — E-commerce
Route::get('/ec-orders', EcOrderIndex::class)->name('flux-admin.ec-orders.index');
Route::get('/shop-orders', ShopOrderIndex::class)->name('flux-admin.shop-orders.index');
Route::get('/spare-part-orders', SparePartOrderIndex::class)->name('flux-admin.spare-part-orders.index');
Route::get('/ec-orders/create', EcOrderForm::class)->name('flux-admin.ec-orders.create');
Route::get('/ec-orders/{ecOrder}/edit', EcOrderForm::class)->name('flux-admin.ec-orders.edit');
Route::get('/ds-orders', DsOrderIndex::class)->name('flux-admin.ds-orders.index');
Route::get('/digital-invoices', DigitalInvoiceIndex::class)->name('flux-admin.digital-invoices.index');

// Phase 17 — Misc
Route::get('/contact-queries', ContactQueryIndex::class)->name('flux-admin.contact-queries.index');
Route::get('/contact-queries/create', ContactQueryForm::class)->name('flux-admin.contact-queries.create');
Route::get('/contact-queries/{contactQuery}/edit', ContactQueryForm::class)->name('flux-admin.contact-queries.edit');
Route::get('/careers', CareerIndex::class)->name('flux-admin.careers.index');
Route::get('/employee-schedules', EmployeeScheduleIndex::class)->name('flux-admin.employee-schedules.index');
Route::get('/renting-pricing', MotorbikePricingHub::class)->name('flux-admin.renting-pricing.index');
Route::get('/renting-pricing/create', RentingPricingForm::class)->name('flux-admin.renting-pricing.create');
Route::get('/renting-pricing/{id}/edit', RentingPricingForm::class)->name('flux-admin.renting-pricing.edit');

// Phase 8 — Inventory (FIFO)
Route::get('/inventory-brands', InventoryBrandIndex::class)->name('flux-admin.inventory-brands.index');
Route::get('/inventory-categories', InventoryCategoryIndex::class)->name('flux-admin.inventory-categories.index');
Route::get('/inventory-models', InventoryModelIndex::class)->name('flux-admin.inventory-models.index');
Route::get('/inventory-products', InventoryProductIndex::class)->name('flux-admin.inventory-products.index');
Route::get('/inventory-partners', InventoryPartnerIndex::class)->name('flux-admin.inventory-partners.index');
Route::get('/inventory-stock-movements', InventoryStockMovementIndex::class)->name('flux-admin.inventory-stock-movements.index');
Route::get('/inventory-stock-movements/create', InventoryStockMovementForm::class)->name('flux-admin.inventory-stock-movements.create');
Route::get('/inventory-stock-movements/{ngnStockMovement}/edit', InventoryStockMovementForm::class)->name('flux-admin.inventory-stock-movements.edit');
Route::get('/oxford-products', OxfordProductIndex::class)->name('flux-admin.oxford-products.index');
Route::get('/oxford-products/create', OxfordProductForm::class)->name('flux-admin.oxford-products.create');
Route::get('/oxford-products/{oxfordProduct}/edit', OxfordProductForm::class)->name('flux-admin.oxford-products.edit');
Route::get('/purchase-requests', PurchaseRequestIndex::class)->name('flux-admin.purchase-requests.index');
Route::get('/purchase-requests/create', PurchaseRequestForm::class)->name('flux-admin.purchase-requests.create');
Route::get('/purchase-requests/{purchaseRequest}/edit', PurchaseRequestForm::class)->name('flux-admin.purchase-requests.edit');
Route::get('/purchase-request-items', PurchaseRequestItemIndex::class)->name('flux-admin.purchase-request-items.index');

// Phase 9 — Spare parts
Route::get('/sp-makes', SpMakeIndex::class)->name('flux-admin.sp-makes.index');
Route::get('/sp-models', SpModelIndex::class)->name('flux-admin.sp-models.index');
Route::get('/sp-fitments', SpFitmentIndex::class)->name('flux-admin.sp-fitments.index');
Route::get('/sp-assemblies', SpAssemblyIndex::class)->name('flux-admin.sp-assemblies.index');
Route::get('/sp-assembly-parts', SpAssemblyPartIndex::class)->name('flux-admin.sp-assembly-parts.index');
Route::get('/sp-assembly-parts/create', AssemblyPartForm::class)->name('flux-admin.sp-assembly-parts.create');
Route::get('/sp-assembly-parts/{spAssemblyPart}/edit', AssemblyPartForm::class)->name('flux-admin.sp-assembly-parts.edit');
Route::get('/sp-parts', SpPartIndex::class)->name('flux-admin.sp-parts.index');
Route::get('/sp-stock-movements', SpStockMovementIndex::class)->name('flux-admin.sp-stock-movements.index');
Route::get('/sp-stock-movements/create', SpStockMovementForm::class)->name('flux-admin.sp-stock-movements.create');
Route::get('/sp-stock-movements/{spStockMovement}/edit', SpStockMovementForm::class)->name('flux-admin.sp-stock-movements.edit');

// Phase 16 — Judopay & recurring billing
Route::get('/judopay-subscriptions', JudopaySubscriptionIndex::class)->name('flux-admin.judopay-subscriptions.index');
Route::get('/judopay-subscriptions/create', JudopaySubscriptionForm::class)->name('flux-admin.judopay-subscriptions.create');
Route::get('/judopay-subscriptions/{id}/edit', JudopaySubscriptionForm::class)->name('flux-admin.judopay-subscriptions.edit');
Route::get('/judopay-mit-queue', JudopayMitQueueIndex::class)->name('flux-admin.judopay-mit-queue.index');
Route::get('/judopay-mit-queue/create', JudopayMitQueueForm::class)->name('flux-admin.judopay-mit-queue.create');
Route::get('/judopay-mit-queue/{id}/edit', JudopayMitQueueForm::class)->name('flux-admin.judopay-mit-queue.edit');
Route::get('/ngn-mit-queue', NgnMitQueueIndex::class)->name('flux-admin.ngn-mit-queue.index');
Route::get('/ngn-mit-queue/create', NgnMitQueueForm::class)->name('flux-admin.ngn-mit-queue.create');
Route::get('/ngn-mit-queue/{id}/edit', NgnMitQueueForm::class)->name('flux-admin.ngn-mit-queue.edit');

// Phase 18 — Customer access links & service videos
Route::get('/rental-terminate-links', RentalTerminateIndex::class)->name('flux-admin.rental-terminate-links.index');
Route::get('/rental-terminate-links/create', RentalTerminateForm::class)->name('flux-admin.rental-terminate-links.create');
Route::get('/rental-terminate-links/{id}/edit', RentalTerminateForm::class)->name('flux-admin.rental-terminate-links.edit');
Route::get('/upload-document-links', UploadDocumentIndex::class)->name('flux-admin.upload-document-links.index');
Route::get('/upload-document-links/create', UploadDocumentForm::class)->name('flux-admin.upload-document-links.create');
Route::get('/upload-document-links/{id}/edit', UploadDocumentForm::class)->name('flux-admin.upload-document-links.edit');
Route::get('/service-videos', ServiceVideoIndex::class)->name('flux-admin.service-videos.index');
Route::get('/service-videos/create', ServiceVideoForm::class)->name('flux-admin.service-videos.create');
Route::get('/service-videos/{serviceVideo}/edit', ServiceVideoForm::class)->name('flux-admin.service-videos.edit');

// Phase 19 — Remaining sub-CRUDs
Route::get('/motorbike-repair-updates', MotorbikeRepairUpdateIndex::class)->name('flux-admin.motorbike-repair-updates.index');
Route::get('/motorbike-repair-updates/create', RepairUpdateForm::class)->name('flux-admin.motorbike-repair-updates.create');
Route::get('/motorbike-repair-updates/{motorbikeRepairUpdate}/edit', RepairUpdateForm::class)->name('flux-admin.motorbike-repair-updates.edit');
Route::get('/ds-order-items', DsOrderItemIndex::class)->name('flux-admin.ds-order-items.index');
Route::get('/ds-order-items/create', DsOrderItemForm::class)->name('flux-admin.ds-order-items.create');
Route::get('/ds-order-items/{dsOrderItem}/edit', DsOrderItemForm::class)->name('flux-admin.ds-order-items.edit');
Route::get('/digital-invoice-items', DigitalInvoiceItemIndex::class)->name('flux-admin.digital-invoice-items.index');
Route::get('/digital-invoice-items/create', DigitalInvoiceItemForm::class)->name('flux-admin.digital-invoice-items.create');
Route::get('/digital-invoice-items/{invoiceItem}/edit', DigitalInvoiceItemForm::class)->name('flux-admin.digital-invoice-items.edit');
Route::get('/club-redemptions', ClubRedeemIndex::class)->name('flux-admin.club-redemptions.index');
Route::get('/vehicle-delivery-orders', VehicleDeliveryOrderIndex::class)->name('flux-admin.vehicle-delivery-orders.index');
Route::get('/vehicle-delivery-orders/create', VehicleDeliveryOrderForm::class)->name('flux-admin.vehicle-delivery-orders.create');
Route::get('/vehicle-delivery-orders/{vehicleDeliveryOrder}/edit', VehicleDeliveryOrderForm::class)->name('flux-admin.vehicle-delivery-orders.edit');
Route::get('/mot-checker', MotCheckerIndex::class)->name('flux-admin.mot-checker.index');
Route::get('/mot-checker/create', MotCheckerForm::class)->name('flux-admin.mot-checker.create');
Route::get('/mot-checker/{motChecker}/edit', MotCheckerForm::class)->name('flux-admin.mot-checker.edit');
Route::get('/support-messages', SupportMessageIndex::class)->name('flux-admin.support-messages.index');
Route::get('/support-messages/create', SupportMessageForm::class)->name('flux-admin.support-messages.create');
Route::get('/support-messages/{supportMessage}/edit', SupportMessageForm::class)->name('flux-admin.support-messages.edit');

// Phase 20 — Operational dashboards & tools
Route::get('/rental-operations', RentalOperationsHub::class)->name('flux-admin.rental-operations.index');
Route::get('/active-rentals', ActiveRentalsIndex::class)->name('flux-admin.active-rentals.index');
Route::get('/rental-due-payments', RentalDuePaymentsIndex::class)->name('flux-admin.rental-due-payments.index');
Route::get('/adjust-weekday', AdjustWeekdayIndex::class)->name('flux-admin.adjust-weekday.index');
Route::get('/mot-stats', MotStatsIndex::class)->name('flux-admin.mot-stats.index');
Route::get('/mot-stats/create', MotStatsForm::class)->name('flux-admin.mot-stats.create');
Route::get('/mot-stats/{notifier}/edit', MotStatsForm::class)->name('flux-admin.mot-stats.edit');
// Judopay ops — same RecurringController as Backpack (GET + POST under Flux)
Route::prefix('judopay')->name('flux-admin.judopay.')->group(function () {
    Route::get('/', [JudopayRecurringController::class, 'index'])->name('index');
    Route::get('subscribe/{id}', [JudopayRecurringController::class, 'subscribe'])->name('subscribe');
    Route::get('mit-dashboard', [JudopayRecurringController::class, 'mitDashboard'])->name('mit-dashboard');
    Route::get('weekly-mit-queue', [JudopayRecurringController::class, 'weeklyMitQueue'])->name('weekly-mit-queue');

    Route::post('create-cit-session', [JudopayRecurringController::class, 'createCitSession'])->name('create-cit-session');
    Route::post('generate-authorization-access', [JudopayRecurringController::class, 'generateAuthorizationAccess'])->name('generate-authorization-access');
    Route::post('kill-previous-links', [JudopayRecurringController::class, 'killPreviousLinks'])->name('kill-previous-links');
    Route::post('send-authorization-email', [JudopayRecurringController::class, 'sendAuthorizationEmail'])->name('send-authorization-email');
    Route::post('fire-direct-mit', [JudopayRecurringController::class, 'fireDirectMit'])->name('fire-direct-mit');
    Route::post('add-to-queue', [JudopayRecurringController::class, 'addToQueue'])->name('add-to-queue');
    Route::delete('stop-live-queue/{id}', [JudopayRecurringController::class, 'stopLiveQueue'])->name('stop-live-queue');
    Route::post('update-billing-day', [JudopayRecurringController::class, 'updateBillingDay'])->name('update-billing-day');
    Route::post('update-amount', [JudopayRecurringController::class, 'updateAmount'])->name('update-amount');
    Route::post('close-subscription', [JudopayRecurringController::class, 'closeSubscription'])->name('close-subscription');
    Route::post('cit/{session}/refund', [\App\Http\Controllers\Judopay\JudopayController::class, 'manualRefund'])->name('cit-refund');
});

Route::redirect('/judopay-recurring', '/flux-admin/judopay')->name('flux-admin.judopay-recurring.index');
Route::get('/calendar', CalendarIndex::class)->name('flux-admin.calendar.index');
Route::get('/agent-settings', AgentSettingsForm::class)->name('flux-admin.agent-settings.index');
Route::get('/support-inbox', SupportInbox::class)->name('flux-admin.support-inbox.index');

// Phase 21 — Final parity pages
Route::get('/vehicle-history', MotorbikeRecordViewIndex::class)->name('flux-admin.vehicle-history.index');
Route::get('/club-member-vehicles', ClubMemberVehicleIndex::class)->name('flux-admin.club-member-vehicles.index');
Route::get('/club-member-vehicles/{clubMember}/edit', MemberVehicleForm::class)->name('flux-admin.club-member-vehicles.edit');
Route::get('/active-bookings-summary', ActiveBookingsSummary::class)->name('flux-admin.active-bookings-summary.index');
Route::redirect('/judopay-mit-dashboard', '/flux-admin/judopay/mit-dashboard')->name('flux-admin.judopay-mit-dashboard.index');
Route::redirect('/judopay-weekly-queue', '/flux-admin/judopay/weekly-mit-queue')->name('flux-admin.judopay-weekly-queue.index');
Route::get('/store-front', EcommerceStoreIndex::class)->name('flux-admin.store-front.index');
Route::get('/store-front/create', StoreProductForm::class)->name('flux-admin.store-front.create');
Route::get('/store-front/{product}/edit', StoreProductForm::class)->name('flux-admin.store-front.edit');

// Phase 22 — Rentals operations (legacy Backpack replacements)
Route::get('/new-booking', NewBookingWizard::class)->name('flux-admin.new-booking.index');
Route::get('/bookings-management', BookingsManagementIndex::class)->name('flux-admin.bookings-management.index');
Route::get('/inactive-bookings', InactiveBookingsIndex::class)->name('flux-admin.inactive-bookings.index');
Route::get('/ended-with-pendings', EndedWithPendingsIndex::class)->name('flux-admin.ended-with-pendings.index');
Route::get('/motorbike-pricing', MotorbikePricingHub::class)->name('flux-admin.motorbike-pricing.index');
Route::get('/all-bookings', AllBookingsIndex::class)->name('flux-admin.all-bookings.index');
Route::get('/booking-invoice-dates', BookingInvoiceDatesIndex::class)->name('flux-admin.booking-invoice-dates.index');
Route::get('/change-start-date', ChangeStartDateIndex::class)->name('flux-admin.change-start-date.index');
Route::get('/motorbikes-dvla/create', DvlaAddVehicle::class)->name('flux-admin.motorbikes-dvla.create');

// Phase 3 — Surveys
Route::get('/surveys', SurveyIndex::class)->name('flux-admin.surveys.index');
Route::get('/surveys/{survey}/campaign', SurveyCampaignIndex::class)->name('flux-admin.surveys.campaign');
Route::get('/survey-questions', SurveyQuestionIndex::class)->name('flux-admin.survey-questions.index');
Route::get('/survey-options', SurveyOptionIndex::class)->name('flux-admin.survey-options.index');
Route::get('/survey-responses', SurveyResponseIndex::class)->name('flux-admin.survey-responses.index');
Route::get('/survey-answers', SurveyAnswerIndex::class)->name('flux-admin.survey-answers.index');

// Phase 3 — Dev tools
Route::get('/dev-club-otp', DevClubOtpIndex::class)->name('flux-admin.dev-club-otp.index');
Route::get('/queue-monitor', QueueMonitorIndex::class)->name('flux-admin.queue-monitor.index');

$spacesVaultPath = trim((string) config('spaces-vault.path', '_vault/do-spaces'), '/');
Route::get('/'.$spacesVaultPath, SpacesVaultExplorer::class)->name('flux-admin.spaces-vault.index');
Route::get('/'.$spacesVaultPath.'/file', [SpacesVaultController::class, 'stream'])->name('flux-admin.spaces-vault.stream');

// Form pages — Inventory
Route::get('/inventory-brands/create', InventoryBrandForm::class)->name('flux-admin.inventory-brands.create');
Route::get('/inventory-brands/{brand}/edit', InventoryBrandForm::class)->name('flux-admin.inventory-brands.edit');
Route::get('/inventory-categories/create', InventoryCategoryForm::class)->name('flux-admin.inventory-categories.create');
Route::get('/inventory-categories/{category}/edit', InventoryCategoryForm::class)->name('flux-admin.inventory-categories.edit');
Route::get('/inventory-models/create', InventoryModelForm::class)->name('flux-admin.inventory-models.create');
Route::get('/inventory-models/{inventoryModel}/edit', InventoryModelForm::class)->name('flux-admin.inventory-models.edit');
Route::get('/inventory-products/create', InventoryProductForm::class)->name('flux-admin.inventory-products.create');
Route::get('/inventory-products/{product}/edit', InventoryProductForm::class)->name('flux-admin.inventory-products.edit');
Route::get('/inventory-partners/create', InventoryPartnerForm::class)->name('flux-admin.inventory-partners.create');
Route::get('/inventory-partners/{partner}/edit', InventoryPartnerForm::class)->name('flux-admin.inventory-partners.edit');
Route::get('/purchase-request-items/create', PurchaseRequestItemForm::class)->name('flux-admin.purchase-request-items.create');
Route::get('/purchase-request-items/{purchaseRequestItem}/edit', PurchaseRequestItemForm::class)->name('flux-admin.purchase-request-items.edit');

// Form pages — Blog
Route::get('/blog-posts/create', BlogPostForm::class)->name('flux-admin.blog-posts.create');
Route::get('/blog-posts/{blogPost}/edit', BlogPostForm::class)->name('flux-admin.blog-posts.edit');
Route::get('/blog-categories/create', BlogCategoryForm::class)->name('flux-admin.blog-categories.create');
Route::get('/blog-categories/{blogCategory}/edit', BlogCategoryForm::class)->name('flux-admin.blog-categories.edit');
Route::get('/blog-tags/create', BlogTagForm::class)->name('flux-admin.blog-tags.create');
Route::get('/blog-tags/{blogTag}/edit', BlogTagForm::class)->name('flux-admin.blog-tags.edit');

// Form pages — Surveys
Route::get('/surveys/create', SurveyForm::class)->name('flux-admin.surveys.create');
Route::get('/surveys/{survey}/edit', SurveyForm::class)->name('flux-admin.surveys.edit');
Route::get('/survey-questions/create', SurveyQuestionForm::class)->name('flux-admin.survey-questions.create');
Route::get('/survey-questions/{surveyQuestion}/edit', SurveyQuestionForm::class)->name('flux-admin.survey-questions.edit');
Route::get('/survey-options/create', SurveyOptionForm::class)->name('flux-admin.survey-options.create');
Route::get('/survey-options/{surveyOption}/edit', SurveyOptionForm::class)->name('flux-admin.survey-options.edit');

// Form pages — Club sub-CRUDs
Route::get('/club-purchases/create', ClubPurchaseForm::class)->name('flux-admin.club-purchases.create');
Route::get('/club-purchases/{purchase}/edit', ClubPurchaseForm::class)->name('flux-admin.club-purchases.edit');
Route::get('/club-redemptions/create', ClubRedeemForm::class)->name('flux-admin.club-redemptions.create');
Route::get('/club-redemptions/{redeem}/edit', ClubRedeemForm::class)->name('flux-admin.club-redemptions.edit');
Route::get('/club-spending/create', ClubSpendingForm::class)->name('flux-admin.club-spending.create');
Route::get('/club-spending/{spending}/edit', ClubSpendingForm::class)->name('flux-admin.club-spending.edit');
Route::get('/club-spending-payments/create', ClubSpendingPaymentForm::class)->name('flux-admin.club-spending-payments.create');
Route::get('/club-spending-payments/{spendingPayment}/edit', ClubSpendingPaymentForm::class)->name('flux-admin.club-spending-payments.edit');

// Form pages — Security
Route::get('/ip-restrictions/create', IpRestrictionForm::class)->name('flux-admin.ip-restrictions.create');
Route::get('/ip-restrictions/{ipRestriction}/edit', IpRestrictionForm::class)->name('flux-admin.ip-restrictions.edit');

// Form pages — Permissions
Route::get('/permissions/create', PermissionForm::class)->name('flux-admin.permissions.create');
Route::get('/permissions/{id}/edit', PermissionForm::class)->name('flux-admin.permissions.edit');

// Form pages — Branches
Route::get('/branches/{branch}/edit', BranchForm::class)->name('flux-admin.branches.edit');

// Form pages — Misc
Route::get('/calendar/create', CalendarForm::class)->name('flux-admin.calendar.create');
Route::get('/calendar/{calendarEvent}/edit', CalendarForm::class)->name('flux-admin.calendar.edit');
Route::get('/employee-schedules/create', EmployeeScheduleForm::class)->name('flux-admin.employee-schedules.create');
Route::get('/employee-schedules/{employeeSchedule}/edit', EmployeeScheduleForm::class)->name('flux-admin.employee-schedules.edit');
Route::get('/careers/create', CareerForm::class)->name('flux-admin.careers.create');
Route::get('/careers/{career}/edit', CareerForm::class)->name('flux-admin.careers.edit');

// Form pages — Ecommerce
Route::get('/ds-orders/create', DsOrderForm::class)->name('flux-admin.ds-orders.create');
Route::get('/ds-orders/{dsOrder}/edit', DsOrderForm::class)->name('flux-admin.ds-orders.edit');
Route::get('/digital-invoices/create', DigitalInvoiceForm::class)->name('flux-admin.digital-invoices.create');
Route::get('/digital-invoices/{digitalInvoice}/edit', DigitalInvoiceForm::class)->name('flux-admin.digital-invoices.edit');

// Form pages — Spare Parts
Route::get('/sp-makes/create', SpMakeForm::class)->name('flux-admin.sp-makes.create');
Route::get('/sp-makes/{spMake}/edit', SpMakeForm::class)->name('flux-admin.sp-makes.edit');
Route::get('/sp-models/create', SpModelForm::class)->name('flux-admin.sp-models.create');
Route::get('/sp-models/{spModel}/edit', SpModelForm::class)->name('flux-admin.sp-models.edit');
Route::get('/sp-fitments/create', SpFitmentForm::class)->name('flux-admin.sp-fitments.create');
Route::get('/sp-fitments/{spFitment}/edit', SpFitmentForm::class)->name('flux-admin.sp-fitments.edit');
Route::get('/sp-assemblies/create', SpAssemblyForm::class)->name('flux-admin.sp-assemblies.create');
Route::get('/sp-assemblies/{spAssembly}/edit', SpAssemblyForm::class)->name('flux-admin.sp-assemblies.edit');
Route::get('/sp-parts/create', SpPartForm::class)->name('flux-admin.sp-parts.create');
Route::get('/sp-parts/{spPart}/edit', SpPartForm::class)->name('flux-admin.sp-parts.edit');
