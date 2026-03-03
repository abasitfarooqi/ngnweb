<?php

// routes/backpack/custom.php
use App\Http\Controllers\Admin\AgentSettingsController;
use App\Http\Controllers\Admin\ClubMemberPurchaseCrudController;
use App\Http\Controllers\Admin\ClubMemberSpendingCrudController;
use App\Http\Controllers\Admin\FinanceApplicationCrudController;
use App\Http\Controllers\Admin\MotorbikeRepairCrudController;
use App\Http\Controllers\Admin\NgnBrandCrudController;
use App\Http\Controllers\Admin\NgnProductCrudController;
use App\Http\Controllers\Admin\NgnStorePageController;
use App\Http\Controllers\Admin\PermissionCrudController;
use App\Http\Controllers\Admin\RecurringController;
use App\Http\Controllers\Admin\RoleCrudController;
use App\Http\Controllers\Admin\PcnCaseCrudController;
use App\Http\Controllers\Admin\MotorbikesSaleCrudController;
use App\Http\Controllers\Admin\NgnStockHandlerCrudController;
use App\Http\Controllers\Admin\NgnProductManagementCrudController;
use App\Http\Controllers\Admin\NgnInventoryManagementCrudController;
use App\Http\Controllers\Admin\PcnTolRequestCrudController;
// use App\Http\Controllers\Admin\ClubMemberStaffCrudController;
// use App\Http\Controllers\Admin\NgnDigitalInvoiceCrudController;
// use App\Http\Controllers\Admin\NgnDigitalInvoiceItemCrudController;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\Admin\VehicleDeliveryOrderCrudController;
use App\Http\Controllers\Admin\QueueMonitorController;
use Backpack\CRUD\app\Http\Controllers\CrudController as CRUD;
use Illuminate\Support\Facades\Route;

// Route::group([
//     'prefix' => config('backpack.base.route_prefix', 'admin'),
//     'middleware' => ['web', 'auth'],
//     // 'middleware' => array_merge(
//     //     (array) config('backpack.base.web_middleware', 'web'),
//     //     (array) config('backpack.base.middleware_key', 'admin')
//     // ),
//     'namespace' => 'App\Http\Controllers\Admin',
// ], function () {
Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', 'auth', 'admin', 'check.admin.access'],
    'namespace' => 'App\Http\Controllers\Admin',
], function () {

    // NGN dashboard (replaces default; data from BackpackDashboardController)
    Route::get('dashboard', [\App\Http\Controllers\Admin\BackpackDashboardController::class, 'dashboard'])->name('backpack.dashboard');
    Route::get('/', function () {
        return redirect(backpack_url('dashboard'));
    })->name('backpack');

    Route::crud('user', UserCrudController::class);
    Route::crud('role', RoleCrudController::class);
    Route::crud('permission', PermissionCrudController::class);

    Route::crud('finance-application', 'FinanceApplicationCrudController');
    Route::crud('application-item', 'ApplicationItemCrudController');
    Route::crud('customer', 'CustomerCrudController');
    // Route::delete('customer/contract/{id}', [\App\Http\Controllers\Admin\CustomerCrudController::class, 'deleteContract'])->name('customer.contract.delete');
    Route::delete('/customer/contract/{id}', [\App\Http\Controllers\Admin\CustomerCrudController::class, 'destroyContract'])
    ->name('customer.contract.destroy');
    Route::delete('customer/agreement/{id}', [\App\Http\Controllers\Admin\CustomerCrudController::class, 'deleteAgreement'])->name('customer.agreement.delete');
    Route::delete('customer/document/{id}', [\App\Http\Controllers\Admin\CustomerCrudController::class, 'deleteDocument'])->name('customer.document.delete');

    Route::crud('motorbikes', 'MotorbikesCrudController');

    Route::get('finance-application/fetch/customer', [FinanceApplicationCrudController::class, 'fetchCustomer'])->name('finance-application.fetch.customer');

    Route::crud('pcn-case', PcnCaseCrudController::class);
    Route::crud('pcn-case-exp', 'PcnCaseExpCrudController');
    Route::crud('vehicle-database', 'VehiclesCrudController');
    Route::crud('pcn-case-update', 'PcnCaseUpdateCrudController');
    Route::crud('booking-invoice', 'BookingInvoiceCrudController');
    Route::crud('motorbikes-sale', MotorbikesSaleCrudController::class);
    // Add the export route here
    // Route::get('motorbikes-sale/export', [MotorbikesSaleCrudController::class, 'export'])->name('motorbikes-sale.export');
    // Route::get('motorbikes-sale/export', 'MotorbikesSaleController@export')->name('motorbikes-sale.export');
    // Route::get('pcn-case/export', [PcnCaseCrudController::class, 'export'])->name('pcn-case.export');

    Route::get('motorbikes-sale/export', [MotorbikesSaleCrudController::class, 'export'])->name('motorbikes-sale.export');
    Route::get('pcn-case/export', [PcnCaseCrudController::class, 'export'])->name('pcn-case.export');
    Route::get('pcn-case/export-summary', [PcnCaseCrudController::class, 'exportSummary'])->name('pcn-case.export-summary');
    Route::get('pcn-case/export-with-updates', [PcnCaseCrudController::class, 'exportWithUpdates'])->name('pcn-case.export-with-updates');

    Route::crud('contract-extra-item', 'ContractExtraItemCrudController');
    Route::crud('customer-appointments', 'CustomerAppointmentsCrudController');
    Route::crud('employee-schedule', 'EmployeeScheduleCrudController');
    Route::crud('motorbike-annual-compliance', 'MotorbikeAnnualComplianceCrudController');
    Route::crud('branch', 'BranchCrudController');
    Route::crud('mot-booking', 'MOTBookingCrudController');
    Route::crud('calander', 'CalanderCrudController');

    Route::crud('mot-checker', 'MotCheckerCrudController');

    // is there any error admin\
    // Route::get('pcn-case/{id}/updates', 'Admin\PcnCaseCrudController@showUpdates')->name('pcn-case.updates');
    // is there any error admin\

    Route::crud('company-vehicle', 'CompanyVehicleCrudController');
    Route::crud('motorbike-annual-compliance-m', 'MotorbikeAnnualComplianceMCrudController');
    Route::crud('agreement-access', 'AgreementAccessCrudController');
    Route::crud('vehicle-notification', 'VehicleNotificationCrudController');
    Route::crud('motorbike-cat-b', 'MotorbikeCatBCrudController');
    Route::crud('motorbike-repair', 'MotorbikeRepairCrudController');
    // Add the route for generating a PDF report for a specific repair
    Route::get('motorbike-repair/{id}/generate-pdf', [MotorbikeRepairCrudController::class, 'generatePdf'])->name('motorbike-repair.generate-pdf');

    Route::crud('motorbike-repair-update', 'MotorbikeRepairUpdateCrudController');
    Route::crud('claim-motorbike', 'ClaimMotorbikeCrudController');
    Route::crud('motorbike-list', 'MotorbikeListCrudController');
    Route::crud('renting-pricing', 'RentingPricingCrudController');
    Route::get('charts/weekly-users', 'Charts\WeeklyUsersChartController@response')->name('charts.weekly-users.index');
    Route::get('charts/renting-status', 'Charts\RentingStatusChartController@response')->name('charts.renting-status.index');

    Route::crud('motorbike', 'MotorbikesCrudController');
    Route::crud('purchase-request', 'PurchaseRequestCrudController');
    Route::crud('purchase-request-item', 'PurchaseRequestItemCrudController');
    Route::crud('upload-document-access', 'UploadDocumentAccessCrudController');
    Route::crud('oxford-products', 'OxfordProductsCrudController');
    Route::crud('create-stock-logs', 'CreateStockLogsCrudController');
    Route::crud('recovered-motorbike', 'RecoveredMotorbikeCrudController');
    Route::crud('vehicle-issuance', 'VehicleIssuanceCrudController');
    Route::crud('customer-document', 'CustomerDocumentCrudController');
    Route::crud('contract-access', 'ContractAccessCrudController');
    Route::crud('used-vehicle-seller', 'PurchaseUsedVehicleCrudController');
    Route::crud('mot-checker', 'MotCheckerCrudController');

    Route::crud('ngn-product', 'NgnProductCrudController');

    Route::get('ngn-product/fetch-brands', [NgnProductCrudController::class, 'fetchBrands']);
    Route::get('ngn-product/fetch-categories', [NgnProductCrudController::class, 'fetchCategories']);
    Route::get('ngn-product/fetch-models', [NgnProductCrudController::class, 'fetchModels']);

    Route::crud('ngn-category', 'NgnCategoryCrudController');
    Route::crud('ngn-model', 'NgnModelCrudController');
    Route::crud('ngn-brand', 'NgnBrandCrudController');

    Route::crud('ngn-career', 'NgnCareerCrudController');
    // Route::get('ngn-op-importer/import', 'Admin\NgnOPImporterCrudController@import')->name('admin.ngn-op-importer.import');
    // Route::post('ngn-op-importer/import', 'Admin\NgnOPImporterCrudController@importProcess')->name('admin.ngn-op-importer.import.process');

    Route::crud('ngn-stock-movement', 'NgnStockMovementCrudController');

    Route::crud('ngn-inventory-management', NgnInventoryManagementCrudController::class);
    Route::crud('ngn-product-management', NgnProductManagementCrudController::class);
    Route::crud('ngn-stock-handler', NgnStockHandlerCrudController::class);
    Route::post('ngn-stock-handler/import', action: [NgnStockHandlerCrudController::class, 'import'])->name('ngn-stock-handler.import');

    // Route::get('ngn-admin/ngn-brand/fetch', [NgnBrandCrudController::class, 'fetch'])->name('ngn-brand.fetch');
    // Route::get('ngn-brand/fetch', [NgnBrandCrudController::class, 'fetch'])->name('ngn-brand.fetch');
    Route::get('ngn-brand/fetch', 'NgnBrandCrudController@fetchBrand')->name('ngn-brand.fetch');

    // Route to handle the export button
    Route::get('ngn-product-management/export-pos', [NgnProductManagementCrudController::class, 'exportForPOS']);
    Route::crud('new-motorbike', 'NewMotorbikeCrudController');
    Route::crud('club-member', 'ClubMemberCrudController');
    Route::crud('club-member-purchase', 'ClubMemberPurchaseCrudController');
    Route::crud('club-member-spending', 'ClubMemberSpendingCrudController');
    Route::crud('club-member-redeem', 'ClubMemberRedeemCrudController');
    Route::crud('clubmembers-details', 'ClubMemberVehicleDetailsCrudController');

    Route::get('club-member-purchase/fetch/check-pos-invoice', [ClubMemberPurchaseCrudController::class, 'checkPosInvoice'])->name('club-member-purchase.check-pos-invoice');
    Route::get('club-member-purchase/fetch/pos-invoice', [ClubMemberPurchaseCrudController::class, 'fetchPosInvoice'])->name('club-member-purchase.fetch-pos-invoice');
    Route::get('club-member-spending/fetch/check-pos-invoice', [ClubMemberSpendingCrudController::class, 'checkPosInvoice'])->name('club-member-spending.check-pos-invoice');
    Route::get('club-member-spending/fetch/pos-invoice', [ClubMemberSpendingCrudController::class, 'fetchPosInvoice'])->name('club-member-spending.fetch-pos-invoice');
    Route::get('club-member/fetch-remaining-balance/{id}', function ($id) {
        $member = \App\Models\ClubMember::findOrFail($id);

        return response()->json([
            'remaining_balance' => $member->available_redeemable_balance,
            'has_today_purchases' => $member->purchases()
                ->whereDate('date', now()->toDateString())
                ->exists(),
        ]);
    });
    Route::get('club-member/fetch-spending-totals/{id}', function ($id) {
        $member = \App\Models\ClubMember::findOrFail($id);

        return response()->json([
            'total_spending' => round($member->total_spending, 2),
            'total_unpaid' => round($member->total_unpaid_spending, 2),
        ]);
    });
    Route::crud('motorbike-record-view', 'MotorbikeRecordViewCrudController');
    Route::crud('ngn-renting-booking', 'NgnRentingBookingCrudController');
    Route::crud('rental-terminate-access', 'RentalTerminateAccessCrudController');

    Route::crud('ngn-partner', 'NgnPartnerCrudController');

    Route::crud('blog-post', 'BlogPostCrudController');
    Route::crud('blog-category', 'BlogCategoryCrudController');
    Route::crud('blog-tag', 'BlogTagCrudController');
    Route::crud('new-motorbikes-for-sale', 'NewMotorbikesForSaleCrudController');
    Route::crud('vehicle-delivery-order', 'VehicleDeliveryOrderCrudController');
    Route::get('vehicle-delivery-order/{id}/confirm', [VehicleDeliveryOrderCrudController::class, 'confirmOrder'])->name('vehicle-delivery-order.confirm');
    Route::get('vehicle-delivery-order/{id}/cancel', [VehicleDeliveryOrderCrudController::class, 'cancelOrder'])->name('vehicle-delivery-order.cancel');

    Route::crud('ec-order', 'EcOrderCrudController');

    Route::get('active_renting', 'ActiveRentingController@index')->name('page.active_renting.index');
    Route::get('ngn_club', 'NgnClubController@index')->name('page.ngn_club.index');

    Route::get('pcn_page', 'PcnPageController@index')->name('page.pcn_page.index');
    Route::post('pcn-case/send-reminder/{id}', 'PcnPageController@sendReminder')->name('pcn-case.send-reminder');

    Route::get('ngn_store_page', 'NgnStorePageController@index')->name('page.ngn_store_page.index');

    Route::get('ngn-store-page/export-csv', [NgnStorePageController::class, 'exportCsv'])->name('ngn-store-page.export-csv');

    // Route::crud('survey', 'SurveyCrudController');
    Route::crud('survey-question', 'SurveyQuestionCrudController');
    Route::crud('survey-option', 'SurveyOptionCrudController');
    Route::crud('survey-response', 'SurveyResponseCrudController');
    Route::crud('survey-answer', 'SurveyAnswerCrudController');
    // Route::get('survey_responses', action: [App\Http\Controllers\SurveyController::class, 'responses'])->name('survey.responses');

    Route::get('survey_index', action: [App\Http\Controllers\SurveyController::class, 'index'])->name('survey.index');
    Route::get('survey_responses/{surveyId}', action: [App\Http\Controllers\SurveyController::class, 'getResponses'])->name('survey.getResponses');

    Route::get('ngn_survey_campaign/{surveyId}', 'NgnSurveyCampaignController@index')
        ->name('page.ngn_survey_campaign.index');

    Route::post('survey/send-reminder/{id}', 'NgnSurveyCampaignController@sendReminder')->name('survey.send-reminder');
    Route::post('survey/send-sms-reminder/{id}', 'NgnSurveyCampaignController@sendSmsReminder')->name('survey.send-sms-reminder');

    Route::crud('contact-query', 'ContactQueryCrudController');

    Route::get('mot_stats_page', 'MOTStatsPageController@index')->name('page.mot_stats_page.index');
    Route::post('mot-stats-page/send-reminder/{id}', 'MOTStatsPageController@sendReminder')->name('mot-stats-page.send-reminder');
    Route::post('mot-stats-page/update/{id}', 'MOTStatsPageController@ajaxUpdate')->name('mot-stats-page.update');

    Route::crud('motorbike-delivery-order-enquiries', 'MotorbikeDeliveryOrderEnquiriesCrudController');
    Route::crud('ip-restriction', 'IpRestrictionCrudController');
    Route::crud('access-log', 'AccessLogCrudController');
    Route::get('rental_due_payments', 'RentalDuePaymentsController@index')->name('page.rental_due_payments.index');

    Route::post('rental-due-payments/send-whatsapp/{invoiceId}', 'RentalDuePaymentsController@sendWhatsappReminder')->name('rental-due-payments.send-whatsapp');
    Route::put('rental-due-payments/update-invoice-date/{invoiceId}', 'RentalDuePaymentsController@updateInvoiceDate')->name('rental-due-payments.update-invoice-date');
    Route::get('adjust_booking_weekday', 'AdjustBookingWeekdayController@index')->name('page.adjust_booking_weekday.index');
    Route::put('adjust-booking-weekday/{id}', 'AdjustBookingWeekdayController@update')->name('adjust-booking-weekday.update');
    Route::crud('renting-service-video', 'RentingServiceVideoCrudController');
    Route::get('active_renting_summary', 'AdjustBookingWeekdayController@ActiveBookingsSummary')->name('page.active_renting_summary.index');
    Route::crud('motorbike-available', 'MotorbikeAvailableCrudController');

    // Route::crud('ngn-digital-invoice', 'NgnDigitalInvoiceCrudController');
    // Route::crud('ngn-digital-invoice-item', 'NgnDigitalInvoiceItemCrudController');
    Route::crud('ngn-digital-invoice', 'NgnDigitalInvoiceCrudController');
    Route::crud('ngn-digital-invoice-item', 'NgnDigitalInvoiceItemCrudController');
    Route::crud('pcn-tol-request', PcnTolRequestCrudController::class);
    Route::get('pcn-tol-request/{id}/generate-tol-pdf', [PcnTolRequestCrudController::class, 'generateTolLetterPdf']);

    // JUDOPAY WIZARD
    Route::prefix('judopay')->name('page.judopay.')->group(function () {
        // /admin/judopay/
        Route::get('/', [RecurringController::class, 'index'])->name('index');

        // /admin/judopay/subscribe/{id}
        Route::get('subscribe/{id}', [RecurringController::class, 'subscribe'])->name('subscribe');

        // /admin/judopay/create-cit-session
        Route::post('create-cit-session', [RecurringController::class, 'createCitSession'])->name('create-cit-session');

        // /admin/judopay/generate-authorization-access
        Route::post('generate-authorization-access', [RecurringController::class, 'generateAuthorizationAccess'])->name('generate-authorization-access');
        Route::post('kill-previous-links', [RecurringController::class, 'killPreviousLinks'])->name('kill-previous-links');
        Route::post('send-authorization-email', [RecurringController::class, 'sendAuthorizationEmail'])->name('send-authorization-email');

        // MIT Dashboard route
        Route::get('mit-dashboard', [RecurringController::class, 'mitDashboard'])->name('mit-dashboard');

        // Weekly MIT Queue route
        Route::get('weekly-mit-queue', [RecurringController::class, 'weeklyMitQueue'])->name('weekly-mit-queue');

        // Direct MIT firing route
        Route::post('fire-direct-mit', [RecurringController::class, 'fireDirectMit'])->name('fire-direct-mit');

        // Add to MIT Queue route
        Route::post('add-to-queue', [RecurringController::class, 'addToQueue'])->name('add-to-queue');

        // Stop live queue item route
        Route::delete('stop-live-queue/{id}', [RecurringController::class, 'stopLiveQueue'])->name('stop-live-queue');

        // Update billing day route
        Route::post('update-billing-day', [RecurringController::class, 'updateBillingDay'])->name('update-billing-day');

        // Update amount route
        Route::post('update-amount', [RecurringController::class, 'updateAmount'])->name('update-amount');

        // Close subscription route
        Route::post('close-subscription', [RecurringController::class, 'closeSubscription'])->name('close-subscription');

        // Manual refund route
        Route::post('cit/{session}/refund', [\App\Http\Controllers\Judopay\JudopayController::class, 'manualRefund'])->name('cit-refund');

    });
    Route::get('ebike_manager', 'EbikeManagerController@index')->name('page.ebike_manager.index');
    Route::post('ebike_manager/store', [\App\Http\Controllers\Admin\EbikeManagerController::class, 'store'])
    ->name('page.ebike_manager.store');
    Route::post('ebike_manager/{id}/update', [\App\Http\Controllers\Admin\EbikeManagerController::class, 'update'])
    ->name('page.ebike_manager.update');

Route::post('ebike_manager/{id}/delete', [\App\Http\Controllers\Admin\EbikeManagerController::class, 'destroy'])
    ->name('page.ebike_manager.delete');

    // AI Chat Agent settings
    Route::get('agent-settings', [AgentSettingsController::class, 'index'])->name('page.agent_settings.index');
    Route::post('agent-settings', [AgentSettingsController::class, 'update'])->name('page.agent_settings.update');

    // ========== DEVELOPER-ONLY EMERGENCY CRUD ACCESS ==========
    // These routes are hidden from all users except developers
    // Access is controlled in each controller's setup() method
    Route::crud('dev-ngn-mit-queue', 'NgnMitQueueCrudController');
    Route::crud('dev-judopay-subscription', 'JudopaySubscriptionCrudController');
    Route::crud('dev-judopay-mit-queue', 'JudopayMitQueueCrudController');
    Route::crud('club-member-spending-payment', 'ClubMemberSpendingPaymentCrudController');

    // Queue Monitor - View Redis delayed jobs
    Route::get('queue-monitor', [QueueMonitorController::class, 'index'])->name('queue-monitor.index');
});
 