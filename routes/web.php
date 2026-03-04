<?php

// ============================================================
// NGN WEB — routes/web.php  (v2 — Livewire + Flux + Tailwind)
// Old routes archived to routes/web-ngnweb.php for reference
// ============================================================

use App\Http\Controllers\Admin\CustomerCrudController;
use App\Http\Controllers\Admin\MotorbikesCrudController;
use App\Http\Controllers\Admin\NgnStockHandlerCrudController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ChatAgentController;
use App\Http\Controllers\ClubMemberTrackingController;
use App\Http\Controllers\CustomContractController;
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
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RentingController;
use App\Http\Controllers\RentalSignupController;
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
use App\Http\Controllers\Welcome\ContactController;
use App\Http\Controllers\Welcome\WelcomeController;
use App\Jobs\SendBatchUserCredentials;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

// ============================================================
// JUDOPAY LIVE ROUTES
// ============================================================
require __DIR__.'/judopay.php';

// ============================================================
// CUSTOMER LOGIN / REGISTER (Livewire + Fortify) — must be before other routes
// /login = customer portal; /ngn-admin/login = Backpack admin
// ============================================================
Route::middleware('web')->group(function () {
    Route::get('/login', fn () => view('auth.login'))
        ->name('login')
        ->middleware('guest:customer');
    Route::get('/register', fn () => view('auth.register'))
        ->name('register')
        ->middleware('guest:customer');
    // Customer email verification (signed link from CustomerVerifyEmailNotification)
    Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\CustomerVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('customer.verification.verify');
});

// ============================================================
// BACKPACK THEME-TABLER ASSETS (serve vendor CSS so admin has styles without Basset)
// ============================================================
Route::get('/backpack-assets/{package}/{path}', function (string $package, string $path) {
    $path = str_replace(['..', '\\'], ['', '/'], $path);
    $allowed = ['theme-tabler' => 'vendor/backpack/theme-tabler', 'crud' => 'vendor/backpack/crud/src/resources'];
    if (!isset($allowed[$package]) || !preg_match('#^[a-z0-9/._-]+\.(css|js)$#', $path)) {
        abort(404);
    }
    $base = realpath(base_path($allowed[$package]));
    $file = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
    if (!$base || !is_file($file) || !str_starts_with(realpath($file), $base)) {
        abort(404);
    }
    $mime = pathinfo($file, PATHINFO_EXTENSION) === 'css' ? 'text/css' : 'application/javascript';
    return response()->file($file, ['Content-Type' => $mime]);
})->where('path', '.*')->name('backpack.theme-tabler.asset');

// ============================================================
// UTILITY / MISC
// ============================================================
Route::post('/twilio/sms/status-callback', [SMSController::class, 'handle'])->name('twilio.status.callback');
Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]));
Route::get('/refresh-csrf-token', function (\Illuminate\Http\Request $request) {
    $lifetime = config('session.lifetime', 120);
    $last = $request->session()->get('_last_activity', time());
    if ((time() - $last) / 60 > ($lifetime - 10)) {
        $request->session()->regenerate();
    }
    $request->session()->put('_last_activity', time());
    return response()->json(['csrf_token' => csrf_token(), 'success' => true]);
})->name('refresh.csrf.token');

Route::post('/chat/agent/message', [ChatAgentController::class, 'send'])
    ->name('chat.agent.message')
    ->middleware('throttle:15,1');

// Custom contract generator
Route::get('/custom-contract-generator', [CustomContractController::class, 'showCustomContractForm'])->name('custom.contract.form');
Route::post('/generate-custom-contract', [CustomContractController::class, 'generateCustomContract'])->name('custom.contract.generate');
Route::post('/api/generate-custom-contract', [CustomContractController::class, 'generateCustomContract']);

// Trustpilot
Route::get('/trustpilot-reviews', [TrustpilotReviewsController::class, 'getReviews']);

// ============================================================
// PUBLIC SITE — Livewire Site\* + Flux + Tailwind + Alpine
// ============================================================

Route::prefix('/')->name('site.')->group(function () {

    Route::get('/', \App\Livewire\Site\Home::class)->name('home');

    // Rentals
    Route::get('/rentals', \App\Livewire\Site\Rentals\Index::class)->name('rentals');
    Route::get('/rentals/{id}', \App\Livewire\Site\Rentals\Show::class)->name('rentals.show');
    Route::get('/honda-forza-125', \App\Livewire\Site\Rentals\BikeModel::class)->defaults('slug', 'honda-forza-125')->name('rental.forza125');
    Route::get('/honda-pcx-125', \App\Livewire\Site\Rentals\BikeModel::class)->defaults('slug', 'honda-pcx-125')->name('rental.pcx125');
    Route::get('/honda-sh-125', \App\Livewire\Site\Rentals\BikeModel::class)->defaults('slug', 'honda-sh-125')->name('rental.sh125');
    Route::get('/honda-vision-125', \App\Livewire\Site\Rentals\BikeModel::class)->defaults('slug', 'honda-vision-125')->name('rental.vision125');
    Route::get('/yamaha-nmax-125', \App\Livewire\Site\Rentals\BikeModel::class)->defaults('slug', 'yamaha-nmax-125')->name('rental.nmax125');
    Route::get('/yamaha-xmax-125', \App\Livewire\Site\Rentals\BikeModel::class)->defaults('slug', 'yamaha-xmax-125')->name('rental.xmax125');

    // MOT
    Route::get('/mot', \App\Livewire\Site\Mot\Index::class)->name('mot');
    Route::get('/mot/book', \App\Livewire\Site\Mot\Book::class)->name('mot.book');

    // Repairs / Services
    Route::get('/repairs', \App\Livewire\Site\Repairs\Index::class)->name('repairs');
    Route::get('/motorbike-basic-service-london', \App\Livewire\Site\Repairs\Basic::class)->name('repairs.basic');
    Route::get('/motorbike-full-service-london', \App\Livewire\Site\Repairs\Full::class)->name('repairs.full');
    Route::get('/motorbike-repair-services', \App\Livewire\Site\Repairs\RepairServices::class)->name('repairs.repair-services');
    Route::get('/motorbike-service-comparison', \App\Livewire\Site\Repairs\Comparison::class)->name('repairs.comparison');

    // Bikes
    Route::get('/bikes', \App\Livewire\Site\Bikes\Index::class)->name('bikes');
    Route::get('/bikes/{type}/{id}', \App\Livewire\Site\Bikes\Show::class)->name('bikes.show');
    Route::get('/bikes/used', fn () => redirect('/bikes'))->name('bikes.used');
    Route::get('/bikes/new',  fn () => redirect('/bikes'))->name('bikes.new');

    // Shop & E-Bikes
    Route::get('/shop', \App\Livewire\Site\Shop\Index::class)->name('shop');
    Route::get('/ebikes', \App\Livewire\Site\Ebikes\Index::class)->name('ebikes');
    Route::get('/accessories', \App\Livewire\Site\Shop\Accessories::class)->name('accessories');
    Route::get('/spare-parts', \App\Livewire\Site\Shop\SpareParts::class)->name('spare-parts');
    Route::get('/gps-tracker', \App\Livewire\Site\Shop\GpsTracker::class)->name('gps-tracker');

    // Finance (public)
    Route::get('/finance', \App\Livewire\Site\Finance\Index::class)->name('finance');

    // Recovery
    Route::get('/recovery', \App\Livewire\Site\Recovery\Index::class)->name('recovery');

    // Club & Partner
    Route::get('/club', \App\Livewire\Site\Club\Index::class)->name('club');
    Route::get('/club/login', \App\Livewire\Site\Club\Login::class)->name('club.login');
    Route::get('/club/dashboard', \App\Livewire\Site\Club\Dashboard::class)->name('club.dashboard');
    Route::get('/partner', \App\Livewire\Site\Partner\Index::class)->name('partner');

    // Career
    Route::get('/career', \App\Livewire\Site\Career\Index::class)->name('careers.index');
    Route::get('/career/{id}', \App\Livewire\Site\Career\Show::class)->name('careers.show');

    // Survey
    Route::get('/survey/{id}', \App\Livewire\Site\Survey\Show::class)->name('survey.show');
    Route::get('/thank-you-for-survey', \App\Livewire\Site\Survey\Thanks::class)->name('survey.thanks');

    // Info pages
    Route::get('/locations', \App\Livewire\Site\Locations\Index::class)->name('locations');
    Route::get('/contact', \App\Livewire\Site\Contact::class)->name('contact');
    Route::get('/contact/call-back', \App\Livewire\Site\Contact\CallBack::class)->name('contact.callback');
    Route::get('/contact/trade-account', \App\Livewire\Site\Contact\TradeAccount::class)->name('contact.trade');
    Route::get('/service-enquiry-form', \App\Livewire\Site\Contact\ServiceBooking::class)->name('service.booking');
    Route::get('/about', \App\Livewire\Site\About::class)->name('about');
    Route::get('/reviews', \App\Livewire\Site\Reviews::class)->name('reviews');
    Route::get('/trustpilot-reviews', fn () => redirect('/reviews'))->name('trustpilot');

    // Thank you
    Route::get('/thank-you', fn () => view('thank-you'))->name('thank-you');

    // Legal
    Route::get('/legal', \App\Livewire\Site\Legal\Index::class)->name('legal');
    Route::get('/legal/{slug}', \App\Livewire\Site\Legal\Show::class)->name('legal.show');
    Route::get('/privacy-policy', \App\Livewire\Site\Legal\Privacy::class)->name('privacy');
    Route::get('/cookie-policy', \App\Livewire\Site\Legal\Privacy::class)->name('cookies');
    Route::get('/terms-and-conditions', \App\Livewire\Site\Legal\Show::class)->defaults('slug', 'terms')->name('terms');
    Route::get('/shipping-policy', \App\Livewire\Site\Legal\Shipping::class)->name('shipping.policy');
    Route::get('/refund-policy', \App\Livewire\Site\Legal\Refund::class)->name('refund.policy');
    Route::get('/return-policy', \App\Livewire\Site\Legal\Refund::class)->name('return.policy');

    // Coming Soon
    Route::get('/coming-soon', \App\Livewire\Site\ComingSoon::class)->name('coming-soon');
});

// Legacy SEO redirects
Route::permanentRedirect('/home', '/');
Route::permanentRedirect('/all-services', '/repairs');
Route::permanentRedirect('/services', '/repairs');
Route::permanentRedirect('/motorbike-recovery', '/recovery');
Route::permanentRedirect('/motorcycle-rental-hire', '/rentals');
Route::permanentRedirect('/motorcycle-sales', '/bikes');
Route::permanentRedirect('/motorcycle-sales-london', '/bikes');
Route::permanentRedirect('/used-motorcycles', '/bikes');
Route::permanentRedirect('/motorcycles-new', '/bikes');
Route::permanentRedirect('/repairs/basic', '/motorbike-basic-service-london');
Route::permanentRedirect('/repairs/full', '/motorbike-full-service-london');
Route::permanentRedirect('/terms-of-use', '/terms-and-conditions');
Route::permanentRedirect('/cookie-and-privacy-policy', '/cookie-policy');
Route::get('/legals/{slug}', fn ($slug) => redirect("/legal/{$slug}", 301));

// Route aliases used by Flux header/footer
Route::get('/rental-hire', fn () => redirect('/rentals', 301))->name('rental-hire');
Route::get('/services', fn () => redirect('/repairs', 301))->name('services');
Route::get('/motorcycles-for-sale', fn () => redirect('/bikes', 301))->name('motorcycles');
Route::get('/motorcycles/new', fn () => redirect('/bikes?filter=new', 301))->name('motorcycles.new');
Route::get('/motorcycles/used', fn () => redirect('/bikes?filter=used', 301))->name('motorcycles.used');
Route::get('/faqs', \App\Livewire\Site\Faq::class)->name('faqs');
Route::get('/accident-management', \App\Livewire\Site\AccidentManagement::class)->name('accident-management');
Route::get('/road-traffic-accidents', fn () => redirect('/accident-management', 301))->name('road-traffic-accidents');
Route::get('/about-us', fn () => redirect('/about', 301))->name('about.page');
Route::get('/get-in-touch', fn () => redirect('/contact', 301))->name('contact.me');
Route::get('/motorcycle-shop', fn () => redirect('/shop', 301))->name('shop-motorcycle');
Route::get('/ngn-club', \App\Livewire\Site\Club\Index::class)->name('ngnclub.subscribe');
Route::get('/search', fn () => redirect('/shop?q=' . urlencode(request()->get('query'))))->name('ngn_search_results');
Route::redirect('/cart', '/shop')->name('product.cart');
Route::redirect('/account/dashboard', '/account');

// (GET /login and /register defined above; POST handled by Fortify.)

// ============================================================
// CUSTOMER PORTAL — /account prefix
// ============================================================
Route::middleware(['customer'])->prefix('account')->name('account.')->group(function () {
    Route::get('/', \App\Livewire\Portal\Dashboard::class)->name('dashboard');
    Route::get('/profile', \App\Livewire\Portal\Profile::class)->name('profile');
    Route::get('/documents', \App\Livewire\Portal\Documents::class)->name('documents');
    Route::post('/documents/upload-curl', function () {
        $valid = request()->validate([
            'file' => 'required|file|max:10240',
            'document_type_id' => 'required|integer|exists:document_types,id',
        ]);
        $customerAuth = auth('customer')->user();
        if (!$customerAuth) return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        $profile = $customerAuth->profile;
        if (!$profile) return response()->json(['ok' => false, 'message' => 'Complete your profile first.'], 422);
        $file = request()->file('file');
        $path = 'customer-documents/' . \Illuminate\Support\Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        \Illuminate\Support\Facades\Storage::disk('spaces')->put($path, $file->get());
        $doc = \App\Models\CustomerDocument::create([
            'customer_id' => $profile->id,
            'document_type_id' => $valid['document_type_id'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_format' => $file->getClientOriginalExtension(),
            'document_number' => request('document_number', '') ?: '',
            'valid_until' => request('valid_until') ?: null,
            'status' => 'pending_review',
        ]);
        return response()->json(['ok' => true, 'message' => 'Document uploaded.', 'document_id' => $doc->id]);
    })->name('documents.upload-curl');
    Route::get('/security', \App\Livewire\Portal\Security::class)->name('security');
    Route::get('/club', \App\Livewire\Portal\Club::class)->name('club');
    Route::get('/bookings', \App\Livewire\Portal\Bookings\Index::class)->name('bookings');
    Route::get('/repairs', fn () => redirect()->route('account.repairs.request'))->name('repairs');
    Route::get('/repairs/request', \App\Livewire\Portal\Repairs\Request::class)->name('repairs.request');
    Route::get('/rentals', \App\Livewire\Portal\Rentals\Browse::class)->name('rentals');
    Route::get('/rentals/browse', \App\Livewire\Portal\Rentals\Browse::class)->name('rentals.browse');
    Route::get('/rentals/my-rentals', \App\Livewire\Portal\Rentals\MyRentals::class)->name('rentals.my-rentals');
    Route::get('/rentals/create/{motorbikeId}', \App\Livewire\Portal\Rentals\Create::class)->name('rentals.create');
    Route::view('/rentals/payment/{bookingId}', 'portal.rentals.payment')->name('rentals.payment');
    Route::post('/rentals/payment/{bookingId}/initialize', [\App\Http\Controllers\Portal\RentalPaymentController::class, 'initializePayment'])->name('rentals.payment.initialize');
    Route::get('/finance', fn () => redirect()->route('account.finance.browse'))->name('finance');
    Route::get('/finance/browse', \App\Livewire\Portal\Finance\Browse::class)->name('finance.browse');
    Route::get('/finance/apply/{motorbikeId}', \App\Livewire\Portal\Finance\Apply::class)->name('finance.apply');
    Route::get('/finance/my-applications', \App\Livewire\Portal\Finance\MyApplications::class)->name('finance.my-applications');
    Route::get('/mot', fn () => redirect()->route('account.mot.book'))->name('mot');
    Route::get('/mot/book', \App\Livewire\Portal\MOT\Book::class)->name('mot.book');
    Route::get('/mot/my-bookings', \App\Livewire\Portal\MOT\MyBookings::class)->name('mot.my-bookings');
    Route::get('/recovery', fn () => redirect()->route('account.recovery.request'))->name('recovery');
    Route::get('/recovery/request', \App\Livewire\Portal\Recovery\Request::class)->name('recovery.request');
    Route::get('/recovery/my-requests', \App\Livewire\Portal\Recovery\MyRequests::class)->name('recovery.my-requests');
    Route::get('/orders', \App\Livewire\Portal\Orders\Index::class)->name('orders');
});

// Judopay portal callbacks
Route::get('/judopay/success/{token}', [\App\Http\Controllers\Portal\RentalPaymentController::class, 'success'])->name('judopay.success');
Route::get('/judopay/failure/{token}', [\App\Http\Controllers\Portal\RentalPaymentController::class, 'failure'])->name('judopay.failure');

// ============================================================
// NGN CLUB (controller-based legacy)
// ============================================================
Route::prefix('ngn-club')->group(function () {
    Route::redirect('/', '/ngn-club/subscribe');
    Route::get('/subscribe', [NgnClubController::class, 'showSubscribePage'])->name('ngnclub.subscribe');
    Route::post('/subscribe', [NgnClubController::class, 'subscribe'])->name('ngnclub.subscribe.submit');
    Route::post('/send-verification-code', [NgnClubController::class, 'sendVerificationCode'])->name('ngnclub.send-verification-code');
    Route::post('/resend-verification-code', [NgnClubController::class, 'resendVerificationCode'])->name('ngnclub.resend-verification-code');
    Route::get('/terms-and-conditions', [NgnClubController::class, 'showTermsPage'])->name('ngnclub.terms');
    Route::get('/dashboard', [NgnClubController::class, 'showDashboard'])->name('ngnclub.dashboard');
    Route::post('/login', [NgnClubController::class, 'login'])->name('ngnclub.login');
    Route::post('/logout', [NgnClubController::class, 'logout'])->name('ngnclub.logout');
    Route::get('/forgot', [NgnClubController::class, 'showForgotPage'])->name('ngnclub.forgot');
    Route::post('/forgot/send-verification-code', [NgnClubController::class, 'sendForgotVerificationCode'])->name('ngnclub.forgot.sendVerificationCode');
    Route::post('/forgot/reset-passkey', [NgnClubController::class, 'resetPasskey'])->name('ngnclub.forgot.resetPasskey');
    Route::get('/referral/{id}', [NgnClubController::class, 'showReferralPage'])->name('ngnclub.referral');
    Route::post('/referral/{id}', [NgnClubController::class, 'submitReferral'])->name('ngnclub.referral.submit');
    Route::post('/feedback', [NgnClubController::class, 'storeFeedback'])->name('ngnclub.feedback');
    Route::post('/profile/update', [NgnClubController::class, 'updateProfile'])->name('ngnclub.profile.update');
});

// NGN Partner
Route::prefix('ngn-partner')->group(function () {
    Route::redirect('/', '/ngn-partner/subscribe');
    Route::get('/subscribe', [NgnPartnerController::class, 'showSubscribePage'])->name('ngnpartner.subscribe');
    Route::post('/subscribe', [NgnPartnerController::class, 'subscribe'])->name('ngnpartner.subscribe.submit');
    Route::post('/send-verification-code', [NgnPartnerController::class, 'sendVerificationCode'])->name('ngnpartner.send-verification-code');
    Route::get('/terms-and-conditions', [NgnPartnerController::class, 'showTermsPage'])->name('ngnpartner.terms');
    Route::get('/thank-you', [NgnPartnerController::class, 'showThankYouPage'])->name('ngnpartner.thankyou');
});

// ============================================================
// ECOMMERCE / STORE (Vue SPA) — kept as-is
// ============================================================
Route::get('/shop/{any?}', fn () => view('olders.frontend.vue_store.app'))
    ->where('any', '.*')->name('shop-motorcycle')->middleware('ecommerce.view');
Route::get('/legals/{any?}', fn () => view('olders.frontend.vue_store.app'))
    ->where('any', '.*')->name('legals')->middleware('ecommerce.view');
Route::get('/accountinformation/login', fn () => view('olders.frontend.vue_store.app'))
    ->where('any', '.*')->name('customer.login')->middleware('ecommerce.view');
Route::post('/accountinformation/logout', [\App\Http\Controllers\Customer\AuthController::class, 'logout'])
    ->name('customer.logout')->middleware('auth:customer');
Route::get('/accountinformation/register', fn () => view('olders.frontend.vue_store.app'))
    ->where('any', '.*')->name('customer.register')->middleware('ecommerce.view');
Route::get('/accountinformation/{any?}', fn () => view('olders.frontend.vue_store.app'))
    ->where('any', '.*')->name('accountinformation')->middleware(['ecommerce.view', 'auth.customer']);

// Store product details
Route::prefix('store')->group(function () {
    Route::get('/search', [StoreController::class, 'searchResults'])->name('ngn_search_results');
    Route::get('/{identifier}', [StoreController::class, 'productDetails'])->name('ngn_product_details');
});

// ============================================================
// PAYPAL
// ============================================================
Route::get('paypal/checkout', [PayPalController::class, 'checkout'])->name('paypal.checkout');
Route::get('paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
Route::get('paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');
Route::post('/webhook/paypal/hook-52dA1x9qX3', [PayPalWebhookController::class, 'handle'])->middleware('throttle:60,1');
Route::get('/paypal/direct-payment', [PayPalController::class, 'directPayment'])->name('paypal.directPayment');

// ============================================================
// MISC PUBLIC
// ============================================================
Route::get('/vrm/check-vehicle', [NgnManagerController::class, 'getVehicleDetails'])->name('vrm.check-vehicle');
Route::get('/c/wm-contract', [MotorcycleDeliveryController::class, 'signatureContractNew']);

// Motorcycle Delivery / Recovery (operational)
Route::get('/motorcycle-delivery', [MotorcycleDeliveryController::class, 'index'])->name('motorcycle.delivery');
Route::match(['get', 'post'], '/motorcycle-delivery/store', [MotorcycleDeliveryController::class, 'storeOrder'])->name('motorcycle.delivery.store');
Route::post('/motorcycle-delivery/complete', [MotorcycleDeliveryController::class, 'completeOrder'])->name('motorcycle.delivery.complete');
Route::get('/motorcycle-delivery/success', [MotorcycleDeliveryController::class, 'success'])->name('motorcycle.delivery.success');
Route::get('/motorcycle-delivery/refresh-csrf', [MotorcycleDeliveryController::class, 'refreshCsrfToken'])->name('motorcycle.delivery.refresh-csrf');
Route::get('/motorbike-recovery/order', [MotorcycleDeliveryController::class, 'showContactOrderForm'])->name('motorbike.recovery.order');
Route::post('/motorbike-recovery/order', [MotorcycleDeliveryController::class, 'submitOrder'])->name('submit.order');
Route::get('/motorbike-recovery/completed', [MotorcycleDeliveryController::class, 'successRecovery'])->name('motorbike.recovery.completed');

// Vehicle Estimator
Route::post('/vehicle/estimate', [NgnClubController::class, 'estimate'])->name('vehicle.estimate');
Route::post('/vehicle/estimate/feedback', [NgnClubController::class, 'estimateFeedback'])->name('vehicle.estimate.feedback');

// Subscribe
Route::post('/subscribe', [SubscriberController::class, 'subscribe']);

// SMS
Route::post('send-sms', [SMSController::class, 'sendSms'])->name('send.sms');

// Notify MOT/Tax
Route::get('notify-mottax/', [CustomerCrudController::class, 'freenotify'])->name('notify.mottax.form');
Route::match(['get', 'post'], 'mottax-notify-submit', [CustomerCrudController::class, 'notifyMotTax'])->name('notify.mottax.submit');

// Inline modals (Backpack)
Route::post('motorbike/inline/create/modal', [MotorbikesCrudController::class, 'getInlineCreateModal']);
Route::post('motorbike/inline/create', [MotorbikesCrudController::class, 'storeInlineCreate']);
Route::post('customer/inline/create/modal', [CustomerCrudController::class, 'getInlineCreateModal']);
Route::post('customer/inline/create', [CustomerCrudController::class, 'storeInlineCreate']);

// Batch emails (internal)
Route::redirect('NGN-CLUB', 'ngn-club');
Route::get('/send-batch-emails', function () {
    dispatch(new SendBatchUserCredentials);
    return Redirect::to(env('APP_URL').'/ngn-admin/club-member');
});

// Stock handler
Route::get('ngn-admin/ngn-stock-handler/fetch-product-data', [NgnStockHandlerCrudController::class, 'fetchProductData']);
Route::post('/admin/ngn-stock-handler/{id}/update-stock', [NgnStockHandlerCrudController::class, 'updateStock']);

// Survey
Route::get('/survey/{surveyId}', [\App\Http\Controllers\SurveyController::class, 'show'])->where('surveyId', '[0-9]+')->name('survey.show');
Route::get('/survey/{slug}', [\App\Http\Controllers\SurveyController::class, 'showBySlug'])->where('slug', '[a-zA-Z0-9-]+')->name('survey.showBySlug');
Route::post('/survey/submit', [\App\Http\Controllers\SurveyController::class, 'submit'])->name('survey.submit');
Route::get('/thank-you-for-survey', [\App\Http\Controllers\SurveyController::class, 'thankyou'])->name('survey.thankyou');

// Festival note (kept for now)
Route::get('/festival-note', fn () => view('olders.frontend.festival-note'))->name('festival.note');

// ============================================================
// ADMIN — /admin prefix, auth + admin middleware
// ============================================================
Route::prefix('admin')->middleware(['auth', 'admin', 'check.admin.access'])->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    Route::post('/customer/delete-document', [CustomerController::class, 'deleteDocument'])->name('admin.customer.deleteDocument');

    // --- Shop Old ---
    Route::prefix('shop-old')->group(function () {
        Route::get('/add', [MotorbikeController::class, 'add_used'])->name('admin.shop');
        Route::get('/motorbikes/fetch/{reg_no}', [MotorbikeController::class, 'fetchMotorbikeByReg'])->name('admin.motorbikes.fetch');
        Route::post('/motorbikes/update/{motorbike}', [MotorbikeController::class, 'updateMotorbike'])->name('admin.motorbikes.used.update');
        Route::get('/add-for-sale', [MotorbikeController::class, 'addForSale'])->name('admin.shop.add-for-sale');
        Route::get('/used-for-sale', [MotorbikeController::class, 'usedForSale'])->name('admin.shop.used-for-sale');
        Route::post('/motorbikes/add', [MotorbikeController::class, 'store_usedbike'])->name('admin.shop.storesale');
        Route::post('/motorbikes/sold-used', [MotorbikeController::class, 'sold_used'])->name('admin.shop.sold-used');
    });

    // --- Spareparts ---
    Route::prefix('spareparts')->group(function () {
        Route::get('/', [SparePartsController::class, 'spareparts_dashboard'])->name('admin.spareparts.index');
        Route::get('/create-pr', [SparePartsController::class, 'create_pr'])->name('admin.spareparts.create-pr');
        Route::post('/create-pr', [SparePartsController::class, 'store_pr'])->name('admin.spareparts.store-pr');
        Route::post('/add-pr-item', [SparePartsController::class, 'add_pr_item'])->name('admin.spareparts.add-pr-item');
        Route::get('/view-pr', [SparePartsController::class, 'viewPurchaseRequests'])->name('admin.spareparts.view-pr');
        Route::get('/view-all-pr', [SparePartsController::class, 'viewAllPurchaseRequests'])->name('admin.spareparts.view-all-pr');
        Route::post('/send-to-supplier', [SparePartsController::class, 'sendToSupplier'])->name('admin.spareparts.send-to-supplier');
        Route::get('/fetch-pr-items', [SparePartsController::class, 'fetch_pr_items'])->name('admin.spareparts.fetch-pr-items');
        Route::get('/get-bike-models/{brandId}', [SparePartsController::class, 'get_bike_models'])->name('admin.spareparts.get-bike-models');
        Route::post('/image/upload', [SparePartsController::class, 'upload'])->name('pr.image.upload');
    });

    // --- Finance (from NGN-WEB, deprecated / Backpack may overtake) ---
    Route::prefix('finance')->group(function () {
        Route::get('/', [FinanceController::class, 'finance_dashboard'])->name('admin.finance.index');
        Route::get('/applications', [FinanceController::class, 'finance_applications'])->name('admin.finance.applications');
        Route::get('/applications/new', [FinanceController::class, 'finance_application_new'])->name('admin.finance.application.new');
    });

    // --- Renting (structure from NGN-WEB: flat routes, static paths before {id}) ---
    Route::prefix('renting')->group(function () {
        // 7 CLOSING (POST)
        Route::post('/notice-period', [RentingController::class, 'noticePeriod']);
        Route::post('/collect-motorbike', [RentingController::class, 'collectMotorbike']);
        Route::post('/damages-cost', [RentingController::class, 'damagesCost']);
        Route::post('/pcn-pendings', [RentingController::class, 'pcnPendings']);
        Route::post('/pending-rent', [RentingController::class, 'pendingRent']);
        Route::post('/deposit-return', [RentingController::class, 'depositReturn']);
        // GET booking by id (singular path to avoid conflict with /bookings)
        Route::get('/booking/{bookingId}/closing-status', [RentingController::class, 'getClosingStatus']);
        Route::get('/booking/{bookingId}/additional-costs', [RentingController::class, 'getAdditionalCosts']);
        Route::get('/booking/{bookingId}/deposit', [RentingController::class, 'getDepositAmount']);
        Route::get('/booking/{booking_item_id}/pcn-pendings', [RentingController::class, 'getPcnPending'])->name('admin.renting.bookings.pcn-pendings');
        // Bookings list / static paths (must be before /bookings/{bookingId})
        Route::get('/bookings', [RentingController::class, 'renting_bookings'])->name('admin.renting.bookings');
        Route::get('/bookings/inactive', [RentingController::class, 'inactive_renting_bookings'])->name('admin.renting.bookings.inactive');
        Route::get('/bookings/new', [RentingController::class, 'renting_booking_new'])->name('admin.renting.booking.new');
        Route::post('/bookings/motorbike-pricing', [RentingController::class, 'getMotorbikeInvoices'])->name('admin.motorbike.pricing');
        Route::get('/bookings/history', [RentingController::class, 'all_renting_bookings'])->name('admin.renting.bookings.history');
        Route::get('/bookings/invoice-dates-all', [RentingController::class, 'invoiceDatesAllView'])->name('admin.renting.invoice.dates.all');
        Route::post('/bookings/invoice-dates/update', [RentingController::class, 'updateInvoiceDate'])->name('admin.renting.invoice.dates.update');
        Route::get('/bookings/change-start-date', [RentingController::class, 'showUpdateStartDateForm'])->name('admin.renting.bookings.showUpdateStartDateForm');
        Route::post('/bookings/change-start-date', [RentingController::class, 'updateStartDate'])->name('admin.renting.bookings.updateStartDate');
        Route::get('/motorbike-price-check', [RentingController::class, 'getMotorbikePrice'])->name('admin.motorbike.price');
        Route::post('/bookings/doc-confirm', [RentingController::class, 'docConfirm'])->name('admin.renting.bookings.doc-confirm');
        // Bookings by {bookingId}
        Route::post('/bookings/{bookingId}/startbooking', [RentingController::class, 'startbooking'])->name('admin.renting.bookings.startbooking');
        Route::post('/bookings/{bookingId}/other-charges', [RentingController::class, 'addOtherCharges'])->name('admin.renting.bookings.other-charges.pay');
        Route::get('/bookings/{bookingId}/other-charges', [RentingController::class, 'getOtherCharges'])->name('admin.renting.bookings.other-charges');
        Route::post('/bookings/other-charges/pay', [RentingController::class, 'payOtherCharges'])->name('admin.renting.bookings.other-charges.pay.post');
        Route::post('/bookings/{bookingId}/issue', [RentingController::class, 'issueMotorbike'])->name('admin.renting.bookings.issue');
        Route::post('/bookings/{bookingId}/reissue', [RentingController::class, 'issueMotorbike'])->name('admin.renting.bookings.reissue');
        Route::post('/bookings/{bookingId}/video/upload', [RentingController::class, 'uploadServiceVideo'])->name('admin.renting.bookings.video.upload');
        Route::get('/bookings/{bookingId}/videos', [RentingController::class, 'getServiceVideos'])->name('admin.renting.bookings.videos.index');
        Route::post('/bookings/{bookingId}/maintenance-logs', [RentingController::class, 'addMaintenanceLog'])->name('admin.renting.bookings.maintenance-logs.store');
        Route::get('/bookings/{bookingId}/maintenance-logs', [RentingController::class, 'getMaintenanceLogs'])->name('admin.renting.bookings.maintenance-logs.index');
        Route::delete('/bookings/maintenance-logs/{logId}', [RentingController::class, 'deleteMaintenanceLog'])->name('admin.renting.bookings.maintenance-logs.destroy');
        Route::get('/bookings/{bookingId}/summary', [RentingController::class, 'getBookingSummary']);
        Route::get('/bookings/{bookingId}/summary_view', [RentingController::class, 'getBookingSummaryView']);
        Route::post('/bookings/create-new-agreement', [AgreementController::class, 'createNewAgreement'])->name('admin.renting.bookings.createNewAgreement');
        Route::post('/bookings/create-new-agreement-ins', [AgreementController::class, 'createNewAgreementIns'])->name('admin.renting.bookings.createNewAgreement.ins');
        Route::get('/bookings/{bookingId}/customer', [RentingController::class, 'getCustomer'])->name('admin.renting.bookings.customer');
        Route::get('/bookings/{bookingId}/invoices', [RentingController::class, 'getInvoices'])->name('admin.renting.bookings.invoices');
        Route::get('/bookings/invoices/{invoiceId}/details', [RentingController::class, 'getInvoiceDetails'])->name('admin.renting.bookings.invoices.details');
        Route::post('/bookings/invoices/{invoiceId}/send-whatsapp', [RentingController::class, 'sendInvoiceWhatsappReminder'])->name('admin.renting.bookings.invoices.send-whatsapp');
        Route::put('/bookings/invoices/{invoiceId}/update-date', [RentingController::class, 'updateInvoiceDateById'])->name('admin.renting.bookings.invoices.update-date');
        Route::get('/bookings/motorbike-availability', [RentingController::class, 'checkMotorbikeAvailability'])->name('admin.renting.bookings.motorbike-availability');
        Route::post('/bookings/create', [RentingController::class, 'createBooking'])->name('admin.renting.bookings.create');
        Route::post('/bookings/update', [RentingController::class, 'updateBooking'])->name('admin.renting.bookings.update');
        Route::put('/bookings/{bookingId}/update', [RentingController::class, 'customerUpdate'])->name('admin.renting.customer.update');
        Route::post('/bookings/{bookingId}/invoice/create', [RentingController::class, 'createUpdateInvoice'])->name('admin.renting.bookings.invoice.create');
        Route::post('/bookings/{bookingId}/finalize', [RentingController::class, 'finalizeBooking'])->name('admin.renting.bookings.finalize');
        Route::post('/bookings/{bookingId}/cancel', [RentingController::class, 'cancelBooking'])->name('admin.renting.bookings.cancel');
        // Renting index and agreement templates
        Route::get('/', [RentingController::class, 'renting_index'])->name('admin.renting.index');
        Route::get('/agreement', [RentingController::class, 'renting_agreement_template'])->name('admin.renting.agreement');
        Route::get('/hire-contract', [RentingController::class, 'renting_agreement_template'])->name('admin.hire.contract');
        Route::get('/contract', [RentingController::class, 'finance_agreement_template'])->name('admin.finance.agreement');
        // Motorbikes (NGN-WEB style: showReport, create, store, edit, update, destroy, show)
        Route::get('/motorbikes', [MotorbikeController::class, 'showReport'])->name('admin.motorbikes.index');
        Route::get('/motorbikes/check-reg-no', [MotorbikeController::class, 'checkRegNo'])->name('admin.motorbikes.checkregno');
        Route::get('/motorbikes/create', [MotorbikeController::class, 'create'])->name('admin.motorbikes.create');
        Route::get('/motorbikes/pricing', [RentingController::class, 'showPricing'])->name('admin.motorbikes.pricing');
        Route::post('/motorbikes/pricing', [RentingController::class, 'storePricing'])->name('admin.motorbikes.storePricing');
        Route::post('/motorbikes/pricing/update', [RentingController::class, 'updatePricing'])->name('admin.motorbikes.updatePricing');
        Route::post('/motorbikes', [MotorbikeController::class, 'store'])->name('admin.motorbikes.store');
        Route::get('/motorbikes/{motorbike}/edit', [MotorbikeController::class, 'edit'])->name('admin.motorbikes.edit');
        Route::put('/motorbikes/{motorbike}', [MotorbikeController::class, 'update'])->name('admin.motorbikes.update');
        Route::delete('/motorbikes/{motorbike}', [MotorbikeController::class, 'destroy'])->name('admin.motorbikes.destroy');
        Route::get('/motorbikes/{motorbike}', [MotorbikeController::class, 'show'])->name('admin.motorbikes.show');
        Route::post('/motorbikes/{motorbike}/upload-image', [MotorbikeController::class, 'uploadImage'])->name('admin.motorbikes.uploadImage');
        Route::get('/motorbikes/{motorbike}/image-upload', [MotorbikeController::class, 'showImageUploadForm'])->name('admin.motorbikes.showImageUploadForm');
        Route::post('/motorbikes/vehiclecheck', [MotorbikeController::class, 'vehicleCheck'])->name('admin.motorbikes.vehicleCheck');
        Route::post('/customers/{customer_id}/documents/upload', [CustomerController::class, 'upload'])->name('customers.documents.upload');
        Route::get('/customers', [CustomerController::class, 'customers'])->name('admin.customers.index');
        Route::get('/customers/create', [CustomerController::class, 'create'])->name('admin.customers.create');
        Route::post('/customers', [CustomerController::class, 'store'])->name('admin.customers.store');
    });

    Route::get('/payment-methods', [PaymentsController::class, 'getMethods'])->name('payment.methods');
    Route::get('/customers/upload-links', [AgreementController::class, 'link_Logs'])->name('admin.upload-links');
    Route::get('/customers/agreements-links', [AgreementController::class, 'agreement_Logs'])->name('admin.agreements-links');
    Route::post('/customers/documents/left', [CustomerController::class, 'uploadLeftDocument'])->name('customers.documents.left');
    Route::post('/customers/documents/list', [CustomerController::class, 'uploadListDocument'])->name('customers.documents.list');
    Route::post('/customers/documents/motorbikeleft', [CustomerController::class, 'uploadLeftMotorbikeDocument'])->name('customers.motorbike.documents.left');
    Route::post('/customers/documents/{documentTypeId}/verify', [CustomerController::class, 'verifyDocument'])->name('customers.documents.verify');
    Route::post('/customers/documents/{documentTypeId}/verifyAgreement', [CustomerController::class, 'verifyAgreementDocument'])->name('customers.documents.verifyAgreement');

    Route::get('/rotas-view', [AdminController::class, 'rotas'])->name('admin.rotas');
    Route::get('/bookings/invoices/{invoice}/print', [InvoicePdfController::class, 'print'])->name('invoices.print');
});


Route::get('/ebikes', function () {
    return view('frontend.ebike-landing');
})->name('ebike.landing');


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
// ============================================================
// AUTH — Fortify handles GET/POST /login, /register (customer guard). Logout below for web guard.
// ============================================================
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ============================================================
// AGREEMENTS / CONTRACTS / PDFS
// ============================================================
Route::get('/agreement/{customer_id}/{passcode}', [AgreementController::class, 'show'])->name('agreement.show');
Route::get('/agreement-ins/{customer_id}/{passcode}', [AgreementController::class, 'showIns'])->name('agreement.show.ins');
Route::get('/rental-agreement/{customer_id}/{passcode}', [AgreementController::class, 'showV6'])->name('agreement.show.v6');
Route::get('/rental-agreement-ins/{customer_id}/{passcode}', [AgreementController::class, 'showInsV6'])->name('agreement.show.ins.v6');
Route::get('/agreement-ins-6m/{customer_id}/{passcode}', [AgreementController::class, 'showIns6m'])->name('agreement.show.ins.6m');
Route::get('/agreement-ins-5m-extended/{customer_id}/{passcode}', [AgreementController::class, 'showIns5mExtended'])->name('agreement.show.ins.5m.extended');
Route::get('/loyalty-scheme/{customer_id}/{passcode}', [AgreementController::class, 'showLoyaltyScheme'])->name('loyalty.scheme.show');
Route::get('/finance/{customer_id}/{passcode}', [AgreementController::class, 'showContract'])->name('finance.show');
Route::get('/finance-ins/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns'])->name('finance.ins.show');
Route::get('/finance-18m-extended/{customer_id}/{passcode}', [AgreementController::class, 'showContract18mExtended'])->name('finance.show.18m.extended');
Route::get('/finance-ins-18m-extended/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns18mExtended'])->name('finance.ins.show.18m.extended');
Route::get('/finance-ins-18m-extended-custom/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns18mExtendedCustom'])->name('finance.ins.show.18m.extended.custom');
Route::get('/finance-ins-5m/{customer_id}/{passcode}', [AgreementController::class, 'showContractIns5mExtended'])->name('finance.ins.show.5m.extended');
Route::get('/finance-ins-m/{customer_id}/{passcode}', [AgreementController::class, 'showContractInsM'])->name('finance.ins.m.show');
Route::get('/sale-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractLatest'])->name('finance.show.latest');
Route::get('/sale-used-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractUsedLatest'])->name('finance.show.used.latest');
Route::get('/sale-ins-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractInsLatest'])->name('finance.ins.show.latest');
Route::get('/sale-ins-used-latest/{customer_id}/{passcode}', [AgreementController::class, 'showContractInsUsedLatest'])->name('finance.ins.show.used.latest');
Route::get('/sale-subscription-merged-new/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsNew'])->name('finance.show.merged.new');
Route::get('/sale-subscription-merged-used/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsUsed'])->name('finance.show.merged.used');
Route::get('/sale-subscription-merged-new-ins/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsNewIns'])->name('finance.ins.show.merged.new');
Route::get('/sale-subscription-merged-used-ins/{customer_id}/{passcode}', [AgreementController::class, 'showMergedContractsUsedIns'])->name('finance.ins.show.merged.used');
Route::get('/delivery-contract/{customer_id}/{passcode}', [AgreementController::class, 'showDeliveryContract'])->name('delivery.contract.show');
Route::get('/rental-terminate/{customer_id}/{booking_id}/{passcode}', [AgreementController::class, 'showRentalTermination'])->name('rental.termination.show');
Route::post('/rental-terminate/{customer_id}/{booking_id}/{passcode}', [AgreementController::class, 'postRentalTermination'])->name('rental.termination.post');
Route::get('/purchase/{purchase_id}/{passcode}', [AgreementController::class, 'showPurchaseInvoice'])->name('purchase.invoice.show');
Route::get('/finance-contract-test-pdf/', [AgreementController::class, 'showContractTest'])->name('finance.contract.show.test');

// POST contract generation
Route::post('/signed/bookings/create-new-agreement', [AgreementController::class, 'createNewAgreement'])->name('admin.renting.bookings.createNewAgreement.signed');
Route::post('/signed/bookings/create-new-agreement-v6', [AgreementController::class, 'createNewAgreementV6'])->name('admin.renting.bookings.createNewAgreement.signed.v6');
Route::post('/signed/bookings/create-new-agreement-ins', [AgreementController::class, 'createNewAgreementIns'])->name('admin.renting.bookings.createNewAgreement.signed.ins');
Route::post('/signed/bookings/create-new-agreement-ins-v6', [AgreementController::class, 'createNewAgreementInsV6'])->name('admin.renting.bookings.createNewAgreement.signed.ins.v6');
Route::post('/signed/bookings/create-new-agreement-ins-6m', [AgreementController::class, 'createNewAgreementIns6m'])->name('admin.renting.bookings.createNewAgreement.signed.ins.6m');
Route::post('/signed/bookings/create-new-agreement-ins-5m-extended', [AgreementController::class, 'createNewAgreementIns5mExtended'])->name('admin.renting.bookings.createNewAgreement.signed.ins.5m.extended');
Route::post('/signed/bookings/create-loyalty-scheme', [AgreementController::class, 'createLoyaltyScheme'])->name('loyalty.scheme.create');
Route::post('/delivery-contract', [AgreementController::class, 'createNewDeliveryContract'])->name('delivery.contract.create');
Route::post('/signed/bookings/create-new-contract', [AgreementController::class, 'createNewContract'])->name('admin.finance.createNewAgreement');
Route::post('/signed/bookings/create-new-contract-ins', [AgreementController::class, 'createNewContractIns'])->name('admin.finance.createNewAgreement.ins');
Route::post('/signed/bookings/create-new-contract-18m-extended', [AgreementController::class, 'createNewContract18mExtended'])->name('admin.finance.createNewAgreement.18m.extended');
Route::post('/signed/bookings/create-new-contract-ins-18m-extended', [AgreementController::class, 'createNewContractIns18mExtended'])->name('admin.finance.createNewAgreement.ins.18m.extended');
Route::post('/signed/bookings/create-new-contract-ins-18m-extended-custom', [AgreementController::class, 'createNewContractIns18mExtendedCustom'])->name('admin.finance.createNewAgreement.ins.18m.extended.custom');
Route::post('/signed/bookings/create-new-contract-ins-5m-extended', [AgreementController::class, 'createNewContractIns5mExtended'])->name('admin.finance.createNewAgreement.ins.5m.extended');
Route::post('/signed/bookings/create-new-contract-ins-m', [AgreementController::class, 'createNewContractInsM'])->name('admin.finance.createNewAgreement.m.ins');
Route::post('/signed/bookings/create-new-contract-latest', [AgreementController::class, 'createNewContractLatest'])->name('admin.finance.createNewAgreement.latest');
Route::post('/signed/bookings/create-new-contract-ins-latest', [AgreementController::class, 'createNewContractInsLatest'])->name('admin.finance.createNewAgreement.ins.latest');
Route::post('/signed/bookings/create-new-contract-ins-used-latest', [AgreementController::class, 'createNewContractInsUsedLatest'])->name('admin.finance.createNewAgreement.ins.used.latest');
Route::post('/signed/bookings/create-new-contract-used-latest', [AgreementController::class, 'createNewContractUsedLatest'])->name('admin.finance.createNewAgreement.used.latest');
Route::post('/signed/bookings/create-merged-contracts', [AgreementController::class, 'createMergedContracts'])->name('admin.finance.createMergedContracts');
Route::post('/signed/bookings/create-merged-contracts-ins', [AgreementController::class, 'createMergedContractsIns'])->name('admin.finance.createMergedContractsIns');
Route::post('/signed/purchase-invoice/create-new-invoice', [AgreementController::class, 'createNewInvoice'])->name('admin.purchase.createNewInvoice');
Route::post('/signed/emp-nda/signed', [AgreementController::class, 'employeeNda'])->name('employee.nda');

Route::get('/generate-agreement-access/{customer_id}', [AgreementController::class, 'generateAgreementAccess'])->name('agreement.access.generate');
Route::get('/generate-delivery-agreement-access/{customer_id}', [AgreementController::class, 'generateDeliveryAgreementAccess'])->name('deliveryagreement.access.generate');
Route::get('/generate-docs-upload-link-access/{customer_id}', [AgreementController::class, 'generateDocumentUploadAccess'])->name('agreement.access.generate.upload');
Route::get('/upload-doc/{customer_id}/{passcode}', [AgreementController::class, 'showUploadDocPage'])->name('uploaddoc.showUploadDocPage.show');
Route::get('/employee-nda-review', function () {
    return view('employee-sign', ['user' => Auth::user()]);
});

// Documents
Route::post('/customers/{customer_id}/documents/upload', [CustomerController::class, 'uploadCustomerViaLink'])->name('customers.documents.upload.link');
Route::post('/customers/documents/left', [CustomerController::class, 'uploadLeftDocumentViaLink'])->name('customers.documents.left.link');
Route::post('/customers/documents/motorbikeleft', [CustomerController::class, 'uploadLeftMotorbikeDocumentViaLink'])->name('customers.motorbike.documents.left.link');

// PDF + Invoices
Route::get('generate-pdf', [PdfController::class, 'generatePdf'])->name('generate-pdf');
Route::get('invoices/{invoice}/print', [InvoicePdfController::class, 'print'])->name('invoices.print');

// Storage file access
Route::get('storage/{path}', [FileController::class, 'show'])->where('path', '.*');

// ============================================================
// DASHBOARD / USERS / INTERNAL
// ============================================================
Route::get('/dashboard', fn () => redirect('/accountinformation'))->name('dashboard');
Route::get('/admindashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
Route::get('customer/dashboard', [DashboardController::class, 'customerDashboard'])->name('customer.dashboard');
Route::get('/client-motorcycle/{id}', [DashboardController::class, 'ClientMotorcycle'])->name('client.motorcycles');
Route::get('/client-upload-files/{id}', [DashboardController::class, 'createForm']);
Route::post('/client-upload-file/{id}', [DashboardController::class, 'fileUpload'])->name('client.fileUpload');

Route::post('/mail', [MailController::class, 'sendMail']);
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::get('/users/{id}', [UserController::class, 'show'])->name('show.user');
Route::get('/users-create', [UserController::class, 'create'])->name('create.user');
Route::post('/users', [UserController::class, 'store'])->name('store.users');
Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('edit.user');
Route::patch('/users/{id}', [UserController::class, 'update'])->name('update.users');

// Rentals (internal)
Route::get('/rentals', [RentalController::class, 'index'])->name('rentals');
Route::get('/rental-motorcycle/{motorcycle_id}/{user_id}', [RentalSignupController::class, 'customerBikeLink'])->name('rental.motorcycle.rental');
Route::controller(RentalSignupController::class)->group(function () {
    Route::get('/rental-signup/{id}', 'rentalSignUp');
    Route::post('/rentalsignup', 'storeSignUp')->name('store.signup');
    Route::post('/customerrentalsignup', 'customerAddRental')->name('customer.add.rental');
    Route::get('/rental-agreement', 'showAgreement');
    Route::post('/signature-post', 'signedAgreement')->name('sign.agreement');
    Route::get('/pdf-agreement', 'PdfAgreement')->name('pdf.agreement');
});

// Cart
Route::redirect('/cart', '/shop/basket')->name('product.cart');
Route::get('/add-product', [CartController::class, 'add'])->name('addproduct.cart');
Route::post('/cart/{id}', [CartController::class, 'store'])->name('store.cart');
Route::post('/cart-rental/{id}', [CartrentalController::class, 'storeRental'])->name('storeRental.cart');
Route::get('/cart-remove/{id}', [CartController::class, 'delete']);

// Club member tracking
Route::prefix('club-member/{clubMemberId}')->group(function () {
    Route::post('/start-session', [ClubMemberTrackingController::class, 'storeSession']);
    Route::put('/end-session/{sessionId}', [ClubMemberTrackingController::class, 'endSession']);
    Route::post('/feedback', [ClubMemberTrackingController::class, 'storeFeedback']);
    Route::put('/segment', [ClubMemberTrackingController::class, 'updateSegment']);
    Route::get('/data', [ClubMemberTrackingController::class, 'getMemberData']);
});

// Notes
Route::get('/notes', [NotesController::class, 'index'])->name('notes');
Route::post('/notes', [NotesController::class, 'UserNote'])->name('notes.post');
Route::post('/user-notes/{id}', [NotesController::class, 'UserNote'])->name('user-note');

// Finance legacy redirects (GET pages now served by Livewire)
Route::permanentRedirect('/finance/apply', '/finance');
Route::permanentRedirect('/finance/apply/complete', '/finance');

// Service-enquiry legacy alias (GET served by Livewire site.service.booking, POST still handled by controller)
Route::get('/service-enquiry-form', fn () => redirect('/service-enquiry-form', 301))->name('book-service');
Route::post('/service-enquiry-form', [\App\Http\Controllers\Welcome\ContactController::class, 'handleBookingForm'])->name('handle-booking');
Route::post('/service-enquiry-form-vue', [\App\Http\Controllers\Welcome\ContactController::class, 'handleEnquiryFormVue'])->name('handle-enquiry-form-vue');
