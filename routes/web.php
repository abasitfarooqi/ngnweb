<?php

// URI: NGM-WEB/routes/web.php
    use App\Http\Controllers\ChatAgentController;
use App\Http\Controllers\Admin\CustomerCrudController;
use App\Http\Controllers\Admin\FinanceApplicationCrudController;
use App\Http\Controllers\Admin\MotorbikesCrudController;
use App\Http\Controllers\Admin\NgnStockHandlerCrudController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ClubMemberTrackingController;
use App\Http\Controllers\CustomContractController;
// newAdded
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\FinancePaymentController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MotCheckerController;
use App\Http\Controllers\MotorbikeController;
use App\Http\Controllers\MotorcycleDeliveryController;
use App\Http\Controllers\NgnClubController;
use App\Http\Controllers\NgnManagerController;
use App\Http\Controllers\NgnPartnerController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\OxfordController;
use App\Http\Controllers\OxfordProductsController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentalSignupController;
use App\Http\Controllers\RentingController;
use App\Http\Controllers\Shopper\CartController;
use App\Http\Controllers\Shopper\CartrentalController;
use App\Http\Controllers\Shopper\SalesController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\SparePartsController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TrustpilotReviewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPanel\NgnOrderController;
use App\Http\Controllers\UserPanel\NgnPaymentMethodController;
use App\Http\Controllers\UserPanel\NgnProfileController;
use App\Http\Controllers\Welcome\ContactController;
use App\Http\Controllers\Welcome\WelcomeController;
use App\Jobs\SendBatchUserCredentials;
use App\Models\Motorcycle;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Spatie\Browsershot\Browsershot;

// JUDOPAY LIVE ROUTES
require __DIR__.'/judopay.php';


Route::post('/twilio/sms/status-callback', [SMSController::class, 'handle'])->name('twilio.status.callback');


// Route::get('/local-shot', function () {
//     $path = storage_path('app/public/local.png');

//     Browsershot::url('https://laravel.com')
//         ->windowSize(1200, 800)
//         ->save($path);

//     return response()->download($path);
// });

// Route::get('/pdf-full', function () {
//     $path = storage_path('app/public/fullsite.pdf');

//     Browsershot::url('http://127.0.0.1:8000')
//         ->showBackground()   // keep CSS backgrounds
//         ->margins(0, 0, 0, 0) // no margins
//         ->format('A4')       // or 'Letter', etc.
//         ->save($path);

//     return response()->download($path);
// });

// Route::get('file/{path}', function ($path) {
//     $file = "customers/$path";
//     if (Storage::disk('public')->exists($file)) {
//         Log::info('File Accessed', ['file' => $file, 'ip' => request()->ip()]);
//         return response()->file(storage_path("app/public/{$file}"));
//     }
//     Log::warning('File Not Found', ['file' => $file, 'ip' => request()->ip()]);
//     abort(404);
// })->where('path', '.*');

Route::get('/', [App\Http\Controllers\Welcome\WelcomeController::class, 'BikesForSaleHome']);

Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::post('/chat/agent/message', [ChatAgentController::class, 'send'])
    ->name('chat.agent.message')
    ->middleware('throttle:15,1');

// Web routes for the form
Route::get('/custom-contract-generator', [CustomContractController::class, 'showCustomContractForm'])->name('custom.contract.form');
Route::post('/generate-custom-contract', [CustomContractController::class, 'generateCustomContract'])->name('custom.contract.generate');

// If you want API routes instead:
Route::post('/api/generate-custom-contract', [CustomContractController::class, 'generateCustomContract']);

// ECOMMERCE / V1 API / 28/12/2024 web.php >> START
Route::get('/shop/{any?}', function () {
    return view('frontend.vue_store.app');
})->where('any', '.*')
    ->name('shop-motorcycle')
    ->middleware('ecommerce.view');

Route::get('/legals/{any?}', function () {
    return view('frontend.vue_store.app');
})->where('any', '.*')
    ->name('legals')
    ->middleware('ecommerce.view');

Route::get('/accountinformation/login', function () {
    return view('frontend.vue_store.app');
})->where('any', '.*')
    ->name('customer.login')
    ->middleware('ecommerce.view');

Route::post('/accountinformation/logout', [App\Http\Controllers\Customer\AuthController::class, 'logout'])
    ->name('customer.logout')
    ->middleware('auth:customer');

Route::get('/accountinformation/register', function () {
    return view('frontend.vue_store.app');
})->where('any', '.*')
    ->name('customer.register')
    ->middleware('ecommerce.view');

// Protected routes come after
Route::get('/accountinformation/{any?}', function () {
    return view('frontend.vue_store.app');
})->where('any', '.*')
    ->name('accountinformation')
    ->middleware(['ecommerce.view', 'auth.customer']);
// ECOMMERCE / V1 API / 28/12/2024 web.php >>> END

// // PayPal Routes
Route::get('paypal/checkout', [PayPalController::class, 'checkout'])->name('paypal.checkout');
Route::get('paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');

Route::post('/webhook/paypal/hook-52dA1x9qX3', [PayPalWebhookController::class, 'handle'])
    ->middleware('throttle:60,1');

Route::get('/paypal/direct-payment', [PayPalController::class, 'directPayment'])->name('paypal.directPayment');

// PayPal Payment Routes
// Route::get('/finance/payment/success', [FinancePaymentController::class, 'success'])->name('finance.payment.success');
// Route::get('/finance/payment/cancel', [FinancePaymentController::class, 'cancel'])->name('finance.payment.cancel');
// Route::get('/finance/pay', [FinancePaymentController::class, 'payNow'])->name('finance.pay.now');

// Route::get('/payment/success', function () {
//     return view('payment.success'); // create this view
// })->name('payment.success');

// Route::get('/payment/failure', function () {
//     return view('payment.failure'); // create this view
// })->name('payment.failure');

// Route::get('/payment/cancel', function () {
//     return view('payment.cancel'); // create this view
// })->name('payment.cancel');

Route::get('/vrm/check-vehicle', [NgnManagerController::class, 'getVehicleDetails'])->name('vrm.check-vehicle');

Route::get('/career', [CareerController::class, 'index'])->name('careers.index');
Route::get('/career/{id}', [CareerController::class, 'show'])->name('careers.show');

Route::get('/c/wm-contract', [MotorcycleDeliveryController::class, 'signatureContractNew']);

// Motorcycle Delivery Routes //
Route::get('/motorcycle-delivery', [MotorcycleDeliveryController::class, 'index'])->name('motorcycle.delivery');
Route::match(['get', 'post'], '/motorcycle-delivery/store', [MotorcycleDeliveryController::class, 'storeOrder'])->name('motorcycle.delivery.store');
Route::post('/motorcycle-delivery/complete', [MotorcycleDeliveryController::class, 'completeOrder'])->name('motorcycle.delivery.complete');
Route::get('/motorcycle-delivery/success', [MotorcycleDeliveryController::class, 'success'])->name('motorcycle.delivery.success');
Route::get('/motorcycle-delivery/refresh-csrf', [MotorcycleDeliveryController::class, 'refreshCsrfToken'])->name('motorcycle.delivery.refresh-csrf');

Route::get('/motorbike-maintenance-and-servicing', [WelcomeController::class, 'RepairsIndex'])->name('repairs.index');
Route::get('/motorbike-basic-service-london', [WelcomeController::class, 'BasicServices'])->name('repairs.basic');
Route::get('/motorbike-full-service-london', [WelcomeController::class, 'MajorServices'])->name('repairs.major');
Route::get('/motorbike-repair-services', [WelcomeController::class, 'RepairServices'])->name('repairs.repair');
Route::get('/motorbike-service-comparison', [WelcomeController::class, 'ServiceComparison'])->name('repairs.comparison');

Route::get('/motorbike-recovery', [MotorcycleDeliveryController::class, 'index'])->name('motorbike.recovery');
// Route::get('/motorbike-recovery', [MotorcycleDeliveryController::class, 'showMotorbikeRecoveryPage'])->name('motorbike.recovery');
Route::get('/motorbike-recovery/order', [MotorcycleDeliveryController::class, 'showContactOrderForm'])->name('motorbike.recovery.order');
Route::post('/motorbike-recovery/order', [MotorcycleDeliveryController::class, 'submitOrder'])->name('submit.order');
Route::get('/motorbike-recovery/completed', [MotorcycleDeliveryController::class, 'successRecovery'])->name('motorbike.recovery.completed');

Route::get('/festival-note', function () {
    return view('frontend.festival-note');
})->name('festival.note');

// newAdded v.1.0.0
Route::prefix('store')->group(function () {
    // Store Home and Shop Routes

    Route::get('/search', [StoreController::class, 'searchResults'])->name('ngn_search_results');

    Route::get('/{identifier}', [StoreController::class, 'productDetails'])->name('ngn_product_details');
});

// // User Panel Routes
// Route::prefix('account')->group(function () {
//     // User Panel Orders Routes
//     Route::get('/', [NgnProfileController::class, 'index'])->name('userpanel_profile');
//     Route::controller(NgnOrderController::class)->group(function () {
//         Route::get('/orders', 'index')->name('userpanel_orders');
//         Route::get('/orders/{id}', 'show')->name('userpanel_order_details');
//         Route::get('/orders/{id}/tracking', 'tracking')->name('userpanel_order_tracking');
//         Route::post('/orders/{id}/confirm', 'confirmOrder')->name('userpanel_order_confirm');
//     });

//     // User Profile Routes
//     Route::controller(NgnProfileController::class)->group(function () {
//         Route::get('/profile', 'index')->name('userpanel_profile');
//         Route::post('/profile/update', 'update')->name('userpanel_profile_update');
//         Route::get('/profile/change-password', 'changePassword')->name('userpanel_change_password');
//         Route::post('/profile/change-password', 'updatePassword')->name('userpanel_update_password');
//     });

//     // Payment Method Management Routes
//     Route::controller(NgnPaymentMethodController::class)->group(function () {
//         Route::get('/payment-methods', 'index')->name('userpanel_payment_methods');
//         Route::post('/payment-methods/manage', 'manage')->name('userpanel_payment_manage');
//     });

//     // Logout Route
//     Route::post('/logout', [Auth\LoginController::class, 'logout'])->name('userpanel_logout');
// });

Route::redirect('NGN-CLUB', 'ngn-club');
Route::get('/send-batch-emails', function () {
    dispatch(new SendBatchUserCredentials);

    // Redirect back to the club member page
    return Redirect::to(env('APP_URL').'/ngn-admin/club-member');
});

Route::get('ngn-admin/ngn-stock-handler/fetch-product-data', [NgnStockHandlerCrudController::class, 'fetchProductData']);
Route::post('/admin/ngn-stock-handler/{id}/update-stock', [NgnStockHandlerCrudController::class, 'updateStock']);

// Vehicle Estimator Routes
Route::post('/vehicle/estimate', [NgnClubController::class, 'estimate'])->name('vehicle.estimate');
Route::post('/vehicle/estimate/feedback', [NgnClubController::class, 'estimateFeedback'])->name('vehicle.estimate.feedback');

Route::prefix('ngn-club')->group(function () {
    Route::redirect('/', '/ngn-club/subscribe');
    Route::get('/subscribe', [NgnClubController::class, 'showSubscribePage'])->name('ngnclub.subscribe');
    Route::post('/subscribe', [NgnClubController::class, 'subscribe'])->name('ngnclub.subscribe.submit');
    Route::post('/send-verification-code', [NgnClubController::class, 'sendVerificationCode'])->name('ngnclub.send-verification-code');
    Route::post('/resend-verification-code', [NgnClubController::class, 'resendVerificationCode'])->name('ngnclub.resend-verification-code'); // New route
    Route::get('/terms-and-conditions', [NgnClubController::class, 'showTermsPage'])->name('ngnclub.terms');
    Route::get('/dashboard', [NgnClubController::class, 'showDashboard'])->name('ngnclub.dashboard');
    Route::post('/login', [NgnClubController::class, 'login'])->name('ngnclub.login');
    Route::post('/logout', [NgnClubController::class, 'logout'])->name('ngnclub.logout');

    Route::get('/forgot', [NgnClubController::class, 'showForgotPage'])->name('ngnclub.forgot');
    Route::post('/forgot/send-verification-code', [NgnClubController::class, 'sendForgotVerificationCode'])->name('ngnclub.forgot.sendVerificationCode');
    Route::post('/forgot/reset-passkey', [NgnClubController::class, 'resetPasskey'])->name('ngnclub.forgot.resetPasskey');

    Route::get('/referral/{id}', [NgnClubController::class, 'showReferralPage'])->name('ngnclub.referral');
    Route::post('/referral/{id}', [NgnClubController::class, 'submitReferral'])->name('ngnclub.referral.submit');

    // Add route for feedback submission
    Route::post('/feedback', [NgnClubController::class, 'storeFeedback'])->name('ngnclub.feedback');

    Route::post('/profile/update', [NgnClubController::class, 'updateProfile'])->name('ngnclub.profile.update');
});

// SMS route if needed //
Route::post('send-sms', [SMSController::class, 'sendSms'])->name('send.sms');

Route::get('/trustpilot-reviews', [TrustpilotReviewsController::class, 'getReviews']);

Route::get('notify-mottax/', [CustomerCrudController::class, 'freenotify'])->name('notify.mottax.form');
// Route::post('mottax-notify-submit', [CustomerCrudController::class, 'notifyMotTax'])->name('notify.mottax.submit');
Route::match(['get', 'post'], 'mottax-notify-submit', [CustomerCrudController::class, 'notifyMotTax'])->name('notify.mottax.submit');

Route::post('motorbike/inline/create/modal', [MotorbikesCrudController::class, 'getInlineCreateModal']);
Route::post('motorbike/inline/create', [MotorbikesCrudController::class, 'storeInlineCreate']);

Route::post('customer/inline/create/modal', [CustomerCrudController::class, 'getInlineCreateModal']);
Route::post('customer/inline/create', [CustomerCrudController::class, 'storeInlineCreate']);

// Route::post('customer-inline-create', [CustomerCrudController::class, 'storeInline'])->name('customer-inline-create');
// Route::post('customer-inline-create-save', [CustomerCrudController::class, 'storeInline'])->name('customer-inline-create-save');
// Route::get('finance-application/fetch/customer', [CustomerCrudController::class, 'fetchCustomer'])->name('finance-application.fetch.customer');
// Route::get('finance-application/fetch/customer', [FinanceApplicationCrudController::class, 'fetchCustomer'])->name('finance-application.fetch.customer');
// Route::get('finance-application/fetch/customer', [FinanceApplicationCrudController::class, 'fetchCustomer'])->name('finance-application.fetch.customer');

// //// NEW ADMIN DASHBOARD RESOURCES ////  29-02-2024  ///////////////
// //// Motorbike Management Routes ///////////////////////////////////
Route::prefix('admin')->middleware(['auth', 'admin', 'check.admin.access'])->group(function () {

    // // process payment
    // Route::get('/admin/process-payment-form', function () {
    //     return view('vendor.backpack.crud.forms.process_payment');
    // })->name('admin.processPaymentForm');

    //                    url: '/admin/customer/delete-document',

    Route::post('/customer/delete-document', [CustomerController::class, 'deleteDocument'])->name('admin.customer.deleteDocument');

    // Admin Dashboard Routes
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    Route::prefix('shop-old')->group(function () {

        Route::get('/add', [MotorbikeController::class, 'add_used'])->name('admin.shop');
        // post updates on used motorbike existing records
        // -- FETCH MOTORBIKE BY REG NO - Used Motorbike, Edit Motorbike by Reg No
        Route::get('/motorbikes/fetch/{reg_no}', [MotorbikeController::class, 'fetchMotorbikeByReg'])->name('admin.motorbikes.fetch');

        // -- POSTED UPDATES Existing Record Update
        Route::post('/motorbikes/update/{motorbike}', [MotorbikeController::class, 'updateMotorbike'])->name('admin.motorbikes.used.update');

        // Add used motorbiike on Motorbike Sale - RETIRED
        // Route::post('/motorbikes/add', [MotorbikeController::class, 'store_used'])->name('admin.shop.store');
        // route /add-for-sale" get view page in MotorbikeController
        Route::get('/add-for-sale', [MotorbikeController::class, 'addForSale'])->name('admin.shop.add-for-sale');
        Route::get('/used-for-sale', [MotorbikeController::class, 'usedForSale'])->name('admin.shop.used-for-sale');

        // this is only for New entry for USED motorbike that is not exits on any table.
        Route::post('/motorbikes/add', [MotorbikeController::class, 'store_usedbike'])->name('admin.shop.storesale');

        // sale it now
        Route::post('/motorbikes/sold-used', [MotorbikeController::class, 'sold_used'])->name('admin.shop.sold-used');
    });

    // Spareparts Group Routes
    Route::prefix('spareparts')->group(function () {

        // Dashboard Route for Spareparts
        Route::get('/', [SparePartsController::class, 'spareparts_dashboard'])->name('admin.spareparts.index');

        // Create Purchase Requisition Page
        Route::get('/create-pr', [SparePartsController::class, 'create_pr'])->name('admin.spareparts.create-pr');

        // Create Purchase Requisition
        Route::post('/create-pr', [SparePartsController::class, 'store_pr'])->name('admin.spareparts.store-pr');

        // Add Purchase Requisition Item in PR
        Route::post('/add-pr-item', [SparePartsController::class, 'add_pr_item'])->name('admin.spareparts.add-pr-item');

        // Add Purchase Requisition Item in PR
        Route::post('/add-pr-item', [SparePartsController::class, 'add_pr_item'])->name('admin.spareparts.add-pr-item');

        // Load PR and Items
        Route::get('/view-pr', [SparePartsController::class, 'viewPurchaseRequests'])->name('admin.spareparts.view-pr');

        // / View All PR
        Route::get('/view-all-pr', [SparePartsController::class, 'viewAllPurchaseRequests'])->name('admin.spareparts.view-all-pr');

        // make post request to approve pr '/admin/spareparts/send-to-supplier/'
        Route::post('/send-to-supplier', [SparePartsController::class, 'sendToSupplier'])->name('admin.spareparts.send-to-supplier');

        // Fetch PR Items
        Route::get('/fetch-pr-items', [SparePartsController::class, 'fetch_pr_items'])->name('admin.spareparts.fetch-pr-items');

        // Fetch PR Items
        Route::get('/get-bike-models/{brandId}', [SparePartsController::class, 'get_bike_models'])->name('admin.spareparts.get-bike-models');

        // RETIRE or DELETE ROUTE
        Route::post('/image/upload', [SparePartsController::class, 'upload'])->name('pr.image.upload');
    });

    // Finance Group Routes - Deprecated and will discard as Backpack will overtake it
    Route::prefix('finance')->group(function () {
        Route::get('/', [FinanceController::class, 'finance_dashboard'])->name('admin.finance.index');
        Route::get('/applications', [FinanceController::class, 'finance_applications'])->name('admin.finance.applications');
        Route::get('/applications/new', [FinanceController::class, 'finance_application_new'])->name('admin.finance.application.new');
        Route::post('/applications/store', [FinanceController::class, 'finance_application_store'])->name('admin.finance.application.store');
    });

    // Renting Groupt Routes
    Route::prefix('renting')->group(function () {
        // 7 CLOSING
        Route::post('/notice-period', [RentingController::class, 'noticePeriod']);
        Route::post('/collect-motorbike', [RentingController::class, 'collectMotorbike']);
        Route::post('/damages-cost', [RentingController::class, 'damagesCost']);
        Route::post('/pcn-pendings', [RentingController::class, 'pcnPendings']);
        Route::post('/pending-rent', [RentingController::class, 'pendingRent']);
        Route::post('/deposit-return', [RentingController::class, 'depositReturn']);
        // GET
        Route::get('/booking/{bookingId}/closing-status', [RentingController::class, 'getClosingStatus']);
        // GET - 7.3 GET ADDITIONAL COSTS
        Route::get('/booking/{bookingId}/additional-costs', [RentingController::class, 'getAdditionalCosts']);
        // GET - 7.4 ADD ADDITIONAL COSTS

        // 7.4 PCN PENDINGS
        Route::get('/booking/{bookingId}/closing-status', [RentingController::class, 'getClosingStatus']);

        Route::get('/booking/{bookingId}/deposit', [RentingController::class, 'getDepositAmount']);

        // 7.6 - GET DEPOSIT COSTS
        Route::get('/booking/{booking_item_id}/pcn-pendings', [RentingController::class, 'getPcnPending'])->name('admin.renting.bookings.pcn-pendings');

        // 7 CLOSING
        // Renting Booking
        Route::get('/bookings', [RentingController::class, 'renting_bookings'])->name('admin.renting.bookings');
        Route::get('/bookings/inactive', [RentingController::class, 'inactive_renting_bookings'])->name('admin.renting.bookings.inactive');
        Route::get('/bookings/new', [RentingController::class, 'renting_booking_new'])->name('admin.renting.booking.new');
        Route::post('/bookings/motorbike-pricing', [RentingController::class, 'getMotorbikeInvoices'])->name('admin.motorbike.pricing');

        // newly added
        Route::get('/bookings/history', [RentingController::class, 'all_renting_bookings'])->name('admin.renting.bookings.history');

        // All invoice date management (flat list, filterable)
        Route::get('/bookings/invoice-dates-all', [RentingController::class, 'invoiceDatesAllView'])->name('admin.renting.invoice.dates.all');
        Route::post('/bookings/invoice-dates/update', [RentingController::class, 'updateInvoiceDate'])->name('admin.renting.invoice.dates.update');

        Route::get('/bookings/change-start-date', [RentingController::class, 'showUpdateStartDateForm'])->name('admin.renting.bookings.showUpdateStartDateForm');

        Route::post('/bookings/change-start-date', [RentingController::class, 'updateStartDate'])->name('admin.renting.bookings.updateStartDate');
        // /motorbike-price-check
        Route::get('/motorbike-price-check', [RentingController::class, 'getMotorbikePrice'])->name('admin.motorbike.price');

        // Document is confirmed
        Route::post('/bookings/doc-confirm', [RentingController::class, 'docConfirm'])->name('admin.renting.bookings.doc-confirm');

        // 4.3.2 - Update Record upon Rental Agreement Generation Signature
        Route::post('/bookings/{bookingId}/startbooking', [RentingController::class, 'startbooking'])->name('admin.renting.bookings.startbooking');

        // 5.0 - add additoinal charges on top of rent price on renting booking i.e., damages,
        Route::post('/bookings/{bookingId}/other-charges', [RentingController::class, 'addOtherCharges'])->name('admin.renting.bookings.other-charges.pay');

        // 5.1 - make get request same as above to fetch other charges all of booking id
        Route::get('/bookings/{bookingId}/other-charges', [RentingController::class, 'getOtherCharges'])->name('admin.renting.bookings.other-charges');

        // 5.2 - make get request same as above to fetch other charges all of booking id
        Route::post('/bookings/other-charges/pay', [RentingController::class, 'payOtherCharges'])->name('admin.renting.bookings.other-charges.pay.post');

        // 1.0.3 - Issue Motorbike
        Route::post('/bookings/{bookingId}/issue', [RentingController::class, 'issueMotorbike'])->name('admin.renting.bookings.issue');

        // 1.0.4 - Reissue Motorbike
        Route::post('/bookings/{bookingId}/reissue', [RentingController::class, 'issueMotorbike'])->name('admin.renting.bookings.reissue');

        // Step 1: Single route for uploading a single video
        Route::post('/bookings/{bookingId}/video/upload', [RentingController::class, 'uploadServiceVideo'])
            ->name('admin.renting.bookings.video.upload');
        // Step 2: Route for fetching all videos for a booking
        Route::get('/bookings/{bookingId}/videos', [RentingController::class, 'getServiceVideos'])
            ->name('admin.renting.bookings.videos.index');

        // Step 3: Route for adding a maintenance log
        Route::post('/bookings/{bookingId}/maintenance-logs', [RentingController::class, 'addMaintenanceLog'])
            ->name('admin.renting.bookings.maintenance-logs.store');

        // Step 4: Route for fetching maintenance logs
        Route::get('/bookings/{bookingId}/maintenance-logs', [RentingController::class, 'getMaintenanceLogs'])
            ->name('admin.renting.bookings.maintenance-logs.index');

        Route::delete('/bookings/maintenance-logs/{logId}', [RentingController::class, 'deleteMaintenanceLog'])
            ->name('admin.renting.bookings.maintenance-logs.destroy');

        Route::get('/bookings/{bookingId}/summary', [RentingController::class, 'getBookingSummary']);

        Route::get('/bookings/{bookingId}/summary_view', [RentingController::class, 'getBookingSummaryView']);

        // GEN PDF
        Route::post('/bookings/create-new-agreement', [RentingController::class, 'createNewAgreement'])->name('admin.renting.bookings.createNewAgreement');

        // GEN PDF INS
        Route::post('/bookings/create-new-agreement-ins', [RentingController::class, 'createNewAgreementIns'])->name('admin.renting.bookings.createNewAgreement.ins');

        // get customer id and motorbike id url: '/admin/renting/bookings/' + bookingId + '/customer',
        Route::get('/bookings/{bookingId}/customer', [RentingController::class, 'getCustomer'])->name('admin.renting.bookings.customer');

        // 1.0.2 - load paid invoices of booking
        Route::get('/bookings/{bookingId}/invoices', [RentingController::class, 'getInvoices'])->name('admin.renting.bookings.invoices');
        Route::get('/bookings/invoices/{invoiceId}/details', [RentingController::class, 'getInvoiceDetails'])->name('admin.renting.bookings.invoices.details');
        Route::post('/bookings/invoices/{invoiceId}/send-whatsapp', [RentingController::class, 'sendInvoiceWhatsappReminder'])->name('admin.renting.bookings.invoices.send-whatsapp');
        Route::put('/bookings/invoices/{invoiceId}/update-date', [RentingController::class, 'updateInvoiceDateById'])->name('admin.renting.bookings.invoices.update-date');

        // bookings/motorbike-availability get to function checkMotorbikeAvailability
        Route::get('/bookings/motorbike-availability', [RentingController::class, 'checkMotorbikeAvailability'])->name('admin.renting.bookings.motorbike-availability');

        // // //// //// ////
        // Renting ing / Transactions
        Route::post('/bookings/create', [RentingController::class, 'createBooking'])->name('admin.renting.bookings.create');
        // 3.1 - Payment Section > Confirm Amount >>>
        Route::post('/bookings/update', [RentingController::class, 'updateBooking'])->name('admin.renting.bookings.update');
        // update booking post
        Route::put('/bookings/{bookingId}/update', [RentingController::class, 'customerUpdate'])->name('admin.renting.customer.update');
        // Add Item to  Booking
        Route::post('/bookings/{bookingId}/items/add', [RentingController::class, 'addBookingItem'])->name('admin.renting.bookings.items.add');
        // Create/Update Invoice for Booking
        Route::post('/bookings/{bookingId}/invoice/create', [RentingController::class, 'createUpdateInvoice'])->name('admin.renting.bookings.invoice.create');
        // Finalize  Booking
        Route::post('/bookings/{bookingId}/finalize', [RentingController::class, 'finalizeBooking'])->name('admin.renting.bookings.finalize');
        // Cancel  Booking
        Route::post('/bookings/{bookingId}/cancel', [RentingController::class, 'cancelBooking'])->name('admin.renting.bookings.cancel');
        // // //// //// ////

        Route::get('/document-types', [RentingController::class, 'document_types'])->name('admin.renting.document_types');

        Route::post('/customers/{customer_id}/documents/upload', [CustomerController::class, 'upload'])->name('customers.documents.upload');

        //
        Route::get('/', [RentingController::class, 'renting_index'])->name('admin.renting.index');

        Route::get('/agreement', [RentingController::class, 'renting_agreement_template'])->name('admin.renting.agreement');
        Route::get('/hire-contract', [RentingController::class, 'hire_agreement_template'])->name('admin.hire.contract');

        // FINANCE CONTRACT:
        Route::get('/contract', [RentingController::class, 'finance_agreement_template'])->name('admin.finance.agreement');

        // Motorbikes All

        Route::get('/motorbikes', [MotorbikeController::class, 'showReport'])->name('admin.motorbikes.index');
        Route::get('/motorbikes/check-reg-no', [MotorbikeController::class, 'checkRegNo'])->name('admin.motorbikes.checkregno');
        Route::get('/motorbikes/create', [MotorbikeController::class, 'create'])->name('admin.motorbikes.create');

        // Motorbikes pricing page and set motorbi/addkes price page
        Route::get('/motorbikes/pricing', [RentingController::class, 'showPricing'])->name('admin.motorbikes.pricing');

        // SET MOTORBIKE PRICE
        Route::post('/motorbikes/pricing', [RentingController::class, 'storePricing'])->name('admin.motorbikes.storePricing');

        // UPDATE MOTORBIKE PRICE
        Route::post('/motorbikes/pricing/update', [RentingController::class, 'updatePricing'])->name('admin.motorbikes.updatePricing');

        Route::post('/motorbikes', [MotorbikeController::class, 'store'])->name('admin.motorbikes.store');
        Route::get('/motorbikes/{motorbike}/edit', [MotorbikeController::class, 'edit'])->name('admin.motorbikes.edit');
        Route::put('/motorbikes/{motorbike}', [MotorbikeController::class, 'update'])->name('admin.motorbikes.update');
        Route::delete('/motorbikes/{motorbike}', [MotorbikeController::class, 'destroy'])->name('admin.motorbikes.destroy');
        Route::get('/motorbikes/{motorbike}', [MotorbikeController::class, 'show'])->name('admin.motorbikes.show');
        Route::post('/motorbikes/{motorbike}/upload-image', [MotorbikeController::class, 'uploadImage'])->name('admin.motorbikes.uploadImage');
        Route::get('/motorbikes/{motorbike}/image-upload', [MotorbikeController::class, 'showImageUploadForm'])->name('admin.motorbikes.showImageUploadForm');
        Route::post('/motorbikes/vehiclecheck', [MotorbikeController::class, 'vehicleCheck'])->name('admin.motorbikes.vehicleCheck');
        // Customer Management Routes
        Route::get('/customers', [CustomerController::class, 'customers'])->name('admin.customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');

        // INSERTING NEW CUSTOMER - Modified for update
        Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    }); // RENTING END

    Route::get('/payment-methods', [PaymentsController::class, 'getMethods'])->name('payment.methods');
    // upload documents logs all
    Route::get('/customers/upload-links', [AgreementController::class, 'link_Logs'])->name('admin.upload-links');
    // /admin/customers/agreements-links
    Route::get('/customers/agreements-links', [AgreementController::class, 'agreement_Logs'])->name('admin.agreements-links');
    Route::post('/customers/documents/left', [CustomerController::class, 'uploadLeftDocument'])->name('customers.documents.left');
    Route::post('/customers/documents/list', [CustomerController::class, 'uploadListDocument'])->name('customers.documents.list');
    Route::post('/customers/documents/motorbikeleft', [CustomerController::class, 'uploadLeftMotorbikeDocument'])->name('customers.motorbike.documents.left');
    Route::post('/customers/documents/{documentTypeId}/verify', [CustomerController::class, 'verifyDocument'])->name('customers.documents.verify');
    Route::post('/customers/documents/{documentTypeId}/verifyAgreement', [CustomerController::class, 'verifyAgreementDocument'])->name('customers.documents.verifyAgreement');

    // Rota
    Route::get('/rotas-view', [AdminController::class, 'rotas'])->name('admin.rotas');

    //
    Route::get('/bookings/invoices/{invoice}/print', [InvoicePdfController::class, 'print'])->name('invoices.print');
});

Route::get('/service-enquiry-form', [ContactController::class, 'showBookingForm'])->name('book-service');
Route::post('/service-enquiry-form', [ContactController::class, 'handleBookingForm'])->name('handle-booking');
Route::post('/service-enquiry-form-vue', [ContactController::class, 'handleEnquiryFormVue'])->name('handle-enquiry-form-vue');

Route::prefix('finance')->group(function () {

    Route::get('/', function () {
        return view('finance.index');
    });

    Route::get('/finance/apply', function () {
        return view('frontend.finance-apply');
    });

    Route::get('/finance/apply/complete', function () {
        return view('frontend.finance-complete');
    });
});

Route::get('/test-template', function () {
    return view('pdf.test');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Welcome Routes GET
Route::controller(WelcomeController::class)->group(function () {
    Route::get('/motorcycle-sales', 'BikesForSale')->name('sale-motorcycles');
    Route::get('/rentals-information', 'RentInformation')->name('rental-information');
    Route::get('/services', 'GetServices')->name('services');
    Route::get('/all-services', 'AllGetServices')->name('all-services');
    Route::get('/service-repairs', 'Repairs')->name('service-repairs');
    Route::get('/service-motorcycle', 'ServiceBike')->name('service-motorcycle');
    Route::get('/service-mot', 'ServiceMot')->name('service-mot');
    Route::get('/accident-management-services', 'AccidentClaim')->name('road-traffic-accidents');
    Route::get('/shop-motorcycle', 'MotorcycleShop')->name('shop');
    Route::get('/shop-accessories', 'MotorcycleAccessories')->name('shop-accessories');
    Route::get('/gps-tracker', 'GpsTracker')->name('gps-tracker');
    Route::get('/spare-parts', 'SpareParts')->name('spare-parts');
    Route::get('/about', 'AboutMethod')->name('about.page');
    // newly added
    Route::get('/accessories', 'accessories')->name('accessories');

    // Legals
    Route::get('/cookie-and-privacy-policy', 'CookiePrivacyPolicy')->name('CookiePrivacyPolicy');
    Route::get('/terms-of-use', 'TermsOfUse')->name('TermsOfUse');
    Route::get('/shipping-policy', 'ShippingPolicy')->name('ShippingPolicy');
    Route::get('/refund-policy', 'RefundPolicy')->name('RefundPolicy');
    Route::get('/return-policy', 'ReturnPolicy')->name('ReturnPolicy');
    Route::get('/mot-checker', [MotCheckerController::class, 'index']);
    Route::post('/mot-checker/submit', [MotCheckerController::class, 'submit']);

    Route::get('/coming-soon', 'SoonCome');
});

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
// Route::post('login', [AuthenticatedSessionController::class, 'store']);
// Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Oxford Product Routes
// Route::get('/category/{id}', [OxfordController::class, 'getProductCategory']);
// Route::get('/product/{id}', [OxfordController::class, 'getOxfordProduct']);
// Route::get('/helmets', [OxfordController::class, 'helmets'])->name('helmets');
// Route::get('/mt-helmets', [OxfordController::class, 'MtHelmets'])->name('mt-helmets');

// AB WORK - OXFORD PRODUCTS ADDED
// /oxford-products index page
Route::get('/oxford-products', [OxfordProductsController::class, 'index'])->name('oxfordproducts.index');

// Route for displaying products by category
Route::get('/oxford-products/category/{category}', [OxfordProductsController::class, 'productsByCategory'])->name('oxfordproducts.productsByCategory');

// /oxford-products / {cat}
// Route::get('/oxford-products/{category}/{brand}', [OxfordProductsController::class, 'productsByCategoryAndBrand'])->name('oxfordproducts.productsByCategoryAndBrand');

// Route for displaying individual product details
Route::get('/oxford-products/product/{id}', [OxfordProductsController::class, 'showProduct'])->name('oxfordproducts.showProduct');

// Route for fetching and displaying brands
Route::get('/oxford-products/brands', [OxfordProductsController::class, 'brands'])->name('oxfordproducts.brands');

// Route for displaying categories by brand
Route::get('/oxford-products/brands/{brand}', [OxfordProductsController::class, 'categoriesByBrandF'])->name('oxfordproducts.categoriesByBrandF');

// Marketing section email notification for mot and tax
// Route::get('/marketing/mot-tax', [OxfordProductsController::class, 'motTax'])->name('oxfordproducts.motTax');

// Motorcycle Sales & Rental

Route::controller(SalesController::class)->group(function () {

    Route::get('/motorcycles-for-sale', 'NewForSale')->name('motorcycles.new');
    Route::match(['get', 'post'], '/new-motorcycle/{id}', 'NewBikeDetails')->name('new-motorcycle.detail');
    Route::get('/thank-you', [ContactController::class, 'ThankYou'])->name('thank-you');
    Route::get('/used-motorcycles', 'UsedForSale')->name('motorcycles.used');
    Route::get('/used-motorcycle/{id}', 'UsedBikeDetails')->name('detail.used-motorcycle');
    Route::get('/motorcycle-rentals', 'RentBike')->name('motorcycle.rentals');
    Route::get('/motorcycle-rental-hire', 'RentHire')->name('rental-hire');
    Route::get('/motorcycle-rental-detail', 'RentalHireDetail')->name('rental-hire-detail');
    Route::get('/rentals-motorcycle/{id}', 'RentalDetails')->name('rental-motorcycle.detail');
    Route::get('/honda-forza-125', 'Forza125')->name('forza-125');
    Route::get('/honda-pcx-125', 'Pcx125')->name('pcx-125');
    Route::get('/honda-sh-125', 'Sh125')->name('sh-125');
    Route::get('/honda-vision-125', 'Vision125')->name('vision-125');
    Route::get('/yamaha-nmax-125', 'Nmax125')->name('nmax-125');
    Route::get('/yamaha-xmax-125', 'Xmax125')->name('xmax-125');
});

// Contact All Routes
Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'Contact')->name('contact.me');
    Route::get('/contact/call-back', 'CallMeBack')->name('contact.call-back');
    Route::get('/contact/trade-account', 'TradeAccount')->name('contact.trade-account');
    Route::get('/contact/new-motorcycle/{id}', 'ContactNewSales')->name('contact.new-sales');
    Route::post('/store/message', 'StoreMessage')
        ->name('store.message')
        ->middleware(['throttle:3,1']);
    Route::get('/contact/message', 'ContactMessage')->name('contact.message');
    Route::get('/delete/message/{id}', 'DeleteMessage')->name('delete.message');
    Route::post('/accident/management', 'AccidentManagement')->name('AccidentManagement');
    Route::post('/contacts', 'ThankYou')->name('contacts.thankyou');
    Route::get('/thank-you', [ContactController::class, 'ThankYou'])->name('thank-you');

    // New route for /thank-you
    Route::get('/thank-you', 'ThankYou')->name('thank-you');
});

// Cart Routes
Route::redirect('/cart', '/shop/basket')->name('product.cart');

Route::get('/add-product', [CartController::class, 'add'])->name('addproduct.cart');
Route::post('/cart/{id}', [CartController::class, 'store'])->name('store.cart');
Route::post('/cart-rental/{id}', [CartrentalController::class, 'storeRental'])->name('storeRental.cart');
Route::get('/cart-remove/{id}', [CartController::class, 'delete']);

Route::prefix('club-member/{clubMemberId}')->group(function () {
    Route::post('/start-session', [ClubMemberTrackingController::class, 'storeSession']);
    Route::put('/end-session/{sessionId}', [ClubMemberTrackingController::class, 'endSession']);
    Route::post('/feedback', [ClubMemberTrackingController::class, 'storeFeedback']);
    Route::put('/segment', [ClubMemberTrackingController::class, 'updateSegment']);
    Route::get('/data', [ClubMemberTrackingController::class, 'getMemberData']);
});

// Subscriber Route
Route::post('/subscribe', [SubscriberController::class, 'subscribe']);

// Motorcycle Rental SignUp
Route::controller(RentalSignupController::class)->group(function () {
    Route::get('/rental-signup/{id}', 'rentalSignUp');
    Route::post('/rentalsignup', 'storeSignUp')->name('store.signup');
    Route::post('/customerrentalsignup', 'customerAddRental')->name('customer.add.rental');
    Route::get('/rental-agreement', 'showAgreement');
    Route::post('/signature-post', 'signedAgreement')->name('sign.agreement');
    Route::get('/pdf-agreement', 'PdfAgreement')->name('pdf.agreement');
});

Route::get('/rental-motorcycle/{motorcycle_id}/{user_id}', [RentalSignupController::class, 'customerBikeLink'])->name('rental.motorcycle.rental');

require __DIR__.'/auth.php';

// Roles Gage Routes
Route::get('/dashboard', function () {
    return redirect('/accountinformation');
})->name('dashboard');

Route::get('/admindashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
Route::get('customer/dashboard', [DashboardController::class, 'customerDashboard'])->name('customer.dashboard');

// Customer Dashboard Resources // newlyAdded (remove)
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/client-motorcycle/{id}', [DashboardController::class, 'ClientMotorcycle'])->name('client.motorcycles');
Route::get('/client-upload-files/{id}', [DashboardController::class, 'createForm']);
Route::post('/client-upload-file/{id}', [DashboardController::class, 'fileUpload'])->name('client.fileUpload');
Route::get('/client-file-dl-back/{id}', [DashboardController::class, 'createDlBack'])->name('client. createDlBack');
Route::post('/client-upload-back/{id}', [DashboardController::class, 'DlBack'])->name('client. DlBack');
Route::get('/client-file-dl-front/{id}', [DashboardController::class, 'createDlFront'])->name('client. frontUpload');
Route::post('/client-upload-front/{id}', [DashboardController::class, 'DlFront'])->name('client.DlFront');
Route::get('/client-file-poid/{id}', [DashboardController::class, 'createIdProof'])->name('client.createIdProof');
Route::post('/client-upload-poid/{id}', [DashboardController::class, 'IdProof'])->name('client.IdProof');
Route::get('/client-file-poadd/{id}', [DashboardController::class, 'createAddProof'])->name('client.createAddProof');
Route::post('/client-upload-poadd/{id}', [DashboardController::class, 'AddressProof'])->name('client.AddressProof');
Route::get('/client-file-poins/{id}', [DashboardController::class, 'createInsProof'])->name('client.createInsProof');
Route::post('/client-upload-poins/{id}', [DashboardController::class, 'InsuranceCertificate'])->name('client.InsuranceCertificate');
Route::get('/client-file-pocbt/{id}', [DashboardController::class, 'createCbt'])->name('client.createCbt');
Route::post('/client-upload-pocbt/{id}', [DashboardController::class, 'CbtProof'])->name('client.CbtProof');
Route::get('/client-file-statementfact/{id}', [DashboardController::class, 'createStatementOfFact'])->name('createStatementOfFact');
Route::post('/client-upload-statementoffact/{id}', [DashboardController::class, 'statementOfFact'])->name('statementOfFact');
Route::get('/client-file-ni/{id}', [DashboardController::class, 'createNationalInsurance'])->name('createStatementOfFact.id');
Route::post('/client-upload-ni/{id}', [DashboardController::class, 'nationalInsurance'])->name('statementOfFact.id');

// PDFs
Route::get('generate-pdf', [PdfController::class, 'generatePdf'])->name('generate-pdf');
Route::get('invoices/{invoice}/print', [InvoicePdfController::class, 'print'])->name('invoices.print');

// Email
Route::post('/mail', [MailController::class, 'sendMail']);

// User Resources
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::get('/users/{id}', [UserController::class, 'show'])->name('show.user');
Route::get('/users-create', [UserController::class, 'create'])->name('create.user');
Route::post('/users', [UserController::class, 'store'])->name('store.users');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('edit.user');
Route::patch('/users/{id}', [UserController::class, 'update'])->name('update.users');

// BARCODE AGREEMNET SHARE // . tht must move to admin prefix //
Route::get('/agreement/{customer_id}/{passcode}', [AgreementController::class, 'show'])->name('agreement.show');

// BARCODE AGREEMNET SHARE // . tht must move to admin prefix //
Route::get('/agreement-ins/{customer_id}/{passcode}', [AgreementController::class, 'showIns'])->name('agreement.show.ins');

Route::get('/rental-agreement/{customer_id}/{passcode}', [AgreementController::class, 'showV6'])->name('agreement.show.v6');


Route::get('/rental-agreement-ins/{customer_id}/{passcode}', [AgreementController::class, 'showInsV6'])->name('agreement.show.ins.v6');



Route::get('/agreement-ins-6m/{customer_id}/{passcode}', [AgreementController::class, 'showIns6m'])->name('agreement.show.ins.6m');

// show the one of generated 5 month x 3 = 15 month
Route::get('/agreement-ins-5m-extended/{customer_id}/{passcode}', [AgreementController::class, 'showIns5mExtended'])->name('agreement.show.ins.5m.extended');

Route::get('/loyalty-scheme/{customer_id}/{passcode}', [AgreementController::class, 'showLoyaltyScheme'])->name('loyalty.scheme.show');

// CONTRACT - Contract View before signing FINANCE CONTRACT INSURANCE
Route::get('/finance/{customer_id}/{passcode}', [AgreementController::class, 'showContract'])->name('finance.show');
Route::get('/finance-ins/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns'])->name('finance.ins.show');

// CONTRACT - Contract View before signing FINANCE CONTRACT INSURANCE 18/Months Extended
Route::get('/finance-18m-extended/{customer_id}/{passcode}', [AgreementController::class, 'showContract18mExtended'])->name('finance.show.18m.extended');

// CONTRACT - Contract View before signing FINANCE CONTRACT INSURANCE 18/Months Extended
Route::get('/finance-ins-18m-extended/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns18mExtended'])->name('finance.ins.show.18m.extended');

// CONTRACT - Contract View before signing FINANCE CONTRACT INSURANCE 18/Months Extended
Route::get('/finance-ins-18m-extended-custom/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns18mExtendedCustom'])->name('finance.ins.show.18m.extended.custom');

// 5 Month + 5 Month + 5 Month = 15 Month -------------------------- X
Route::get('/finance-ins-5m/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns5mExtended'])->name('finance.ins.show.5m.extended');

// Standard Latest Finance Contracts

// -------------------- GET Routes --------------------
// Latest Finance Contract
Route::get('/sale-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractLatest'])
    ->name('finance.show.latest');

// Latest Insurance Contract (Used Vehicle)
Route::get('/sale-used-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractUsedLatest'])
    ->name('finance.show.used.latest');

// Latest Insurance Contract (New Vehicle)
Route::get('/sale-ins-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractInsLatest'])
    ->name('finance.ins.show.latest');

// Latest Insurance Contract (Used Vehicle)
Route::get('/sale-ins-used-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractInsUsedLatest'])
    ->name('finance.ins.show.used.latest');

// Merged Contract (Sale + Subscription) - New Vehicle, Without Insurance
Route::get('/sale-subscription-merged-new/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsNew'])
    ->name('finance.show.merged.new');

// Merged Contract (Sale + Subscription) - Used Vehicle, Without Insurance
Route::get('/sale-subscription-merged-used/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsUsed'])
    ->name('finance.show.merged.used');

// Merged Contract (Sale + Subscription) - New Vehicle, With Insurance
Route::get('/sale-subscription-merged-new-ins/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsNewIns'])
    ->name('finance.ins.show.merged.new');

// Merged Contract (Sale + Subscription) - Used Vehicle, With Insurance
Route::get('/sale-subscription-merged-used-ins/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsUsedIns'])
    ->name('finance.ins.show.merged.used');

// -------------------- POST Routes --------------------
// Create New Latest Finance Contract
Route::post('/signed/bookings/create-new-contract-latest', [AgreementController::class, 'createNewContractLatest'])
    ->name('admin.finance.createNewAgreement.latest');

// Create New Latest Insurance Contract (New Vehicle)
Route::post('/signed/bookings/create-new-contract-ins-latest', [AgreementController::class, 'createNewContractInsLatest'])
    ->name('admin.finance.createNewAgreement.ins.latest');

// Create New Latest Insurance Contract (Used Vehicle)
Route::post('/signed/bookings/create-new-contract-ins-used-latest', [AgreementController::class, 'createNewContractInsUsedLatest'])
    ->name('admin.finance.createNewAgreement.ins.used.latest');

// Create New Latest Finance Contract (Used Vehicle)
Route::post('/signed/bookings/create-new-contract-used-latest', [AgreementController::class, 'createNewContractUsedLatest'])
    ->name('admin.finance.createNewAgreement.used.latest');

// Create Merged Contracts (Sale + Subscription) - Without Insurance
Route::post('/signed/bookings/create-merged-contracts', [AgreementController::class, 'createMergedContracts'])
    ->name('admin.finance.createMergedContracts');

// Create Merged Contracts (Sale + Subscription) - With Insurance
Route::post('/signed/bookings/create-merged-contracts-ins', [AgreementController::class, 'createMergedContractsIns'])
    ->name('admin.finance.createMergedContractsIns');

Route::get('/delivery-contract/{customer_id}/{passcode}', [AgreementController::class, 'showDeliveryContract'])->name('delivery.contract.show');

// Rental Contract Termination .
Route::get('/rental-terminate/{customer_id}/{booking_id}/{passcode}', [AgreementController::class, 'showRentalTermination'])->name('rental.termination.show');

// POST Rental Contract Termination
Route::post('/rental-terminate/{customer_id}/{booking_id}/{passcode}', [AgreementController::class, 'postRentalTermination'])->name('rental.termination.post');

// IT IS MONTHLY TEMPORARY maybe someone forge so disontinue
Route::get('/finance-ins-m/{customer_id}/{passcode}', [AgreementController::class, 'showContractInsM'])->name('finance.ins.m.show');
Route::post('/signed/bookings/create-new-contract-ins-m', [AgreementController::class, 'createNewContractInsM'])->name('admin.finance.createNewAgreement.m.ins');

// Test Finance Contract PDF
Route::get('/finance-contract-test-pdf/', [AgreementController::class, 'showContractTest'])->name('finance.contract.show.test');

Route::get('/generate-agreement-access/{customer_id}', [AgreementController::class, 'generateAgreementAccess'])->name('agreement.access.generate');

// Route::get('/generate-delivery-agreement-access/{customer_id}', [AgreementController::class, 'generateDeliveryAgreementAccess'])->name('deliveryagreement.access.generate');

Route::get('/generate-delivery-agreement-access/{customer_id}', [AgreementController::class, 'generateDeliveryAgreementAccess'])->name('deliveryagreement.access.generate');

// POST Rental PDF Generates /
Route::post('/signed/bookings/create-new-agreement-ins', [AgreementController::class, 'createNewAgreementIns'])->name('admin.renting.bookings.createNewAgreement.signed.ins');

Route::post('/signed/bookings/create-new-agreement-ins-6m', [AgreementController::class, 'createNewAgreementIns6m'])->name('admin.renting.bookings.createNewAgreement.signed.ins.6m');

// POST Rental PDF Generates / 5 Month + 5 Month + 5 Month = 15 Month -------------------------- X
Route::post('/signed/bookings/create-new-agreement-ins-5m-extended', [AgreementController::class, 'createNewAgreementIns5mExtended'])->name('admin.renting.bookings.createNewAgreement.signed.ins.5m.extended');

Route::post('/signed/bookings/create-new-agreement', [AgreementController::class, 'createNewAgreement'])->name('admin.renting.bookings.createNewAgreement.signed');

Route::post('/signed/bookings/create-new-agreement-v6', [AgreementController::class, 'createNewAgreementV6'])->name('admin.renting.bookings.createNewAgreement.signed.v6');

Route::post('/signed/bookings/create-new-agreement-ins-v6', [AgreementController::class, 'createNewAgreementInsV6'])->name('admin.renting.bookings.createNewAgreement.signed.ins.v6');

Route::post('/signed/bookings/create-loyalty-scheme', [AgreementController::class, 'createLoyaltyScheme'])->name('loyalty.scheme.create');

Route::post('/delivery-contract', [AgreementController::class, 'createNewDeliveryContract'])->name('delivery.contract.create');

// Legal owner finance contract
Route::post('/signed/bookings/create-new-contract', [AgreementController::class, 'createNewContract'])->name('admin.finance.createNewAgreement');
// finance contract for insurance / pcn
Route::post('/signed/bookings/create-new-contract-ins', [AgreementController::class, 'createNewContractIns'])->name('admin.finance.createNewAgreement.ins');

// finance contract 18/Months Extended
Route::post('/signed/bookings/create-new-contract-18m-extended', [AgreementController::class, 'createNewContract18mExtended'])->name('admin.finance.createNewAgreement.18m.extended');

// finance contract for insurance / pcn 18/Months Extended
Route::post('/signed/bookings/create-new-contract-ins-18m-extended', [AgreementController::class, 'createNewContractIns18mExtended'])->name('admin.finance.createNewAgreement.ins.18m.extended');

// finance contract for insurance / pcn 18/Months Extended
Route::post('/signed/bookings/create-new-contract-ins-18m-extended-custom', [AgreementController::class, 'createNewContractIns18mExtendedCustom'])->name('admin.finance.createNewAgreement.ins.18m.extended.custom');

// 5 Month + 5 Month + 5 Month = 15 Month -------------------------- X connect to this get method Route::get('/finance-ins-5m
Route::post('/signed/bookings/create-new-contract-ins-5m-extended', [AgreementController::class, 'createNewContractIns5mExtended'])->name('admin.finance.createNewAgreement.ins.5m.extended');

// Purchase invoice View
Route::get('/purchase/{purchase_id}/{passcode}', [AgreementController::class, 'showPurchaseInvoice'])->name('purchase.invoice.show');

// Purchase invoice PDF
Route::post('/signed/purchase-invoice/create-new-invoice', [AgreementController::class, 'createNewInvoice'])->name('admin.purchase.createNewInvoice');

Route::get('/employee-nda-review', function () {
    $user = Auth::user();

    return view('employee-sign', compact('user'));
});

Route::post('/signed/emp-nda/signed', [AgreementController::class, 'employeeNda'])->name('employee.nda');

// 4.2.3 - Upload Documents Link Generation >>> //
Route::get('/generate-docs-upload-link-access/{customer_id}', [AgreementController::class, 'generateDocumentUploadAccess'])->name('agreement.access.generate.upload');

// THE DOC upload LINK
Route::get('/upload-doc/{customer_id}/{passcode}', [AgreementController::class, 'showUploadDocPage'])->name('uploaddoc.showUploadDocPage.show');

// Danger Zone
Route::post('/customers/{customer_id}/documents/upload', [CustomerController::class, 'uploadCustomerViaLink'])->name('customers.documents.upload.link');
Route::post('/customers/documents/left', [CustomerController::class, 'uploadLeftDocumentViaLink'])->name('customers.documents.left.link');
Route::post('/customers/documents/motorbikeleft', [CustomerController::class, 'uploadLeftMotorbikeDocumentViaLink'])->name('customers.motorbike.documents.left.link');

// Future Danger //  must think to hide this from public as it is exposing docs
Route::get('storage/{path}', [FileController::class, 'show'])->where('path', '.*');

// Rental Resources
Route::get('/rentals', [RentalController::class, 'index'])->name('rentals');

// Notes
Route::get('/notes', [NotesController::class, 'index'])->name('notes');
Route::post('/notes', [NotesController::class, 'UserNote'])->name('notes.post');
Route::post('/user-notes/{id}', [NotesController::class, 'UserNote'])->name('user-note');

// NGN Partner Routes
Route::prefix('ngn-partner')->group(function () {
    Route::redirect('/', '/ngn-partner/subscribe');
    Route::get('/subscribe', [NgnPartnerController::class, 'showSubscribePage'])->name('ngnpartner.subscribe');
    Route::post('/subscribe', [NgnPartnerController::class, 'subscribe'])->name('ngnpartner.subscribe.submit');
    Route::post('/send-verification-code', [NgnPartnerController::class, 'sendVerificationCode'])->name('ngnpartner.send-verification-code');
    Route::get('/terms-and-conditions', [NgnPartnerController::class, 'showTermsPage'])->name('ngnpartner.terms');
    Route::get('/thank-you', [NgnPartnerController::class, 'showThankYouPage'])->name('ngnpartner.thankyou');
});

Route::get('/survey/{surveyId}', [SurveyController::class, 'show'])->where('surveyId', '[0-9]+')->name('survey.show');
Route::get('/survey/{slug}', [SurveyController::class, 'showBySlug'])->where('slug', '[a-zA-Z0-9-]+')->name('survey.showBySlug');
Route::post('/survey/submit', [SurveyController::class, 'submit'])->name('survey.submit');

Route::get('/thank-you-for-survey', [SurveyController::class, 'thankyou'])->name('survey.thankyou');

Route::get('/ebikes', function () {
    return view('frontend.ebike-landing');
})->name('ebike.landing');

// Production BugSnag Test Route - Remove after testing
Route::get('/test-bugsnag-prod', function () {
    Bugsnag::notifyException(new RuntimeException("Production BugSnag test - " . now()));
    return response()->json([
        'message' => 'Production test error sent to BugSnag',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'release_stage' => config('bugsnag.release_stage')
    ]);
})->name('test.bugsnag.prod');

// Generic CSRF token refresh for all forms
Route::get('/refresh-csrf-token', function(\Illuminate\Http\Request $request) {
    // Only regenerate if session is actually expired or about to expire
    $sessionLifetime = config('session.lifetime', 120); // minutes
    $lastActivity = $request->session()->get('_last_activity', time());
    $timeSinceActivity = (time() - $lastActivity) / 60; // minutes
    
    // Only regenerate if session is close to expiring (within 10 minutes)
    if ($timeSinceActivity > ($sessionLifetime - 10)) {
        $request->session()->regenerate();
    }
    
    // Update last activity
    $request->session()->put('_last_activity', time());
    
    return response()->json([
        'csrf_token' => csrf_token(),
        'success' => true
    ]);
})->name('refresh.csrf.token');
