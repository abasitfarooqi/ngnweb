<?php

use App\Http\Controllers\Api\CustomerDocumentController;
use App\Http\Controllers\Api\Mobile\MobileBootstrapController;
use App\Http\Controllers\Api\Mobile\MobileCatalogueController;
use App\Http\Controllers\Api\Mobile\MobileCheckoutController;
use App\Http\Controllers\Api\Mobile\MobileClubController;
use App\Http\Controllers\Api\Mobile\MobileClubParityController;
use App\Http\Controllers\Api\Mobile\MobileContentController;
use App\Http\Controllers\Api\Mobile\MobileEnquiryController;
use App\Http\Controllers\Api\Mobile\MobileExperienceController;
use App\Http\Controllers\Api\Mobile\MobilePortalAccountController;
use App\Http\Controllers\Api\Mobile\MobilePortalController;
use App\Http\Controllers\Api\Mobile\MobilePortalExperienceController;
use App\Http\Controllers\Api\Mobile\MobilePublicFormsController;
use App\Http\Controllers\Api\Mobile\MobileSparePartsController;
use App\Http\Controllers\Api\Mobile\V2\MobileBootstrapController as MobileV2BootstrapController;
use App\Http\Controllers\Api\Mobile\V2\MobileCatalogueController as MobileV2CatalogueController;
use App\Http\Controllers\Api\Mobile\V2\MobileCheckoutController as MobileV2CheckoutController;
use App\Http\Controllers\Api\Mobile\V2\MobileClubController as MobileV2ClubController;
use App\Http\Controllers\Api\Mobile\V2\MobileClubLegacyController as MobileV2ClubLegacyController;
use App\Http\Controllers\Api\Mobile\V2\MobileClubParityController as MobileV2ClubParityController;
use App\Http\Controllers\Api\Mobile\V2\MobileContentController as MobileV2ContentController;
use App\Http\Controllers\Api\Mobile\V2\MobileEnquiryController as MobileV2EnquiryController;
use App\Http\Controllers\Api\Mobile\V2\MobileExperienceController as MobileV2ExperienceController;
use App\Http\Controllers\Api\Mobile\V2\MobilePortalAccountController as MobileV2PortalAccountController;
use App\Http\Controllers\Api\Mobile\V2\MobilePortalController as MobileV2PortalController;
use App\Http\Controllers\Api\Mobile\V2\MobilePortalExperienceController as MobileV2PortalExperienceController;
use App\Http\Controllers\Api\Mobile\V2\MobilePublicFormsController as MobileV2PublicFormsController;
use App\Http\Controllers\Api\Mobile\V2\MobileSparePartsController as MobileV2SparePartsController;
use App\Http\Controllers\Api\StaffAuthController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\CustomerVerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ECommerceShop;
use App\Http\Controllers\ImageSyncController;
use App\Http\Controllers\NgnClubController;
use App\Http\Controllers\NgnManagerController;
use App\Http\Controllers\NgnVehicleDeliveryController;
use App\Http\Controllers\PayPalWebhookController;
use App\Http\Controllers\Shopper\SalesController;
use App\Http\Controllers\Welcome\ContactController;
use Illuminate\Http\Request;
// use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

Route::get('/image-sync/files', [ImageSyncController::class, 'list']);
Route::post('/image-sync/upload', [ImageSyncController::class, 'upload']);
// Sync Files A to B and B to A Endpoints - START
Route::middleware('sync.auth')->group(function () {
    Route::get('/sync/files', [\App\Http\Controllers\SyncController::class, 'index']);
    Route::post('/sync/upload', [\App\Http\Controllers\SyncController::class, 'upload']);
});

Route::post('/paypal/webhook', [PayPalWebhookController::class, 'handle']);

Route::middleware('auth:sanctum')->post('/club-members/initiate-registration', [NgnClubController::class, 'initiateRegistration']);
Route::middleware('auth:sanctum')->post('/club-members/verify-registration', [NgnClubController::class, 'verifyAndCompleteRegistration']);

// Vehicle Check API Endpoints
Route::middleware('auth:sanctum')->post('/check-vehicle', [NgnManagerController::class, 'checkVehicle']);
Route::middleware('auth:sanctum')->post('/save-vehicle-details', [NgnManagerController::class, 'saveVehicleDetails']);

// POST Purchase - Credit Top-up 10%
Route::middleware('auth:sanctum')->post('/club-member-purchases', [NgnClubController::class, 'store']);

// POST Purchase - Credit Top-up 2%
Route::middleware('auth:sanctum')->post('/club-member-purchases-mb', [NgnClubController::class, 'storeMB']);

// POST Customer Spending - 0% Credit (spending tracking only)
Route::middleware('auth:sanctum')->post('/customer-spending', [NgnClubController::class, 'storeSpending']);

// POST List Customer Spending - Get all spending records by phone or reg number
Route::middleware('auth:sanctum')->post('/list-customer-spending', [NgnClubController::class, 'listCustomerSpending']);

// POST Delete Customer Spending - Delete spending record by id or pos_invoice
Route::middleware('auth:sanctum')->post('/delete-customer-spending', [NgnClubController::class, 'deleteCustomerSpending']);

// POST Record Spending Payment - Apply payment FIFO to unpaid spendings
Route::middleware('auth:sanctum')->post('/record-spending-payment', [NgnClubController::class, 'recordSpendingPayment']);

// POST Spending Payment History - Get all payment records for a customer
Route::middleware('auth:sanctum')->post('/spending-payment-history', [NgnClubController::class, 'getSpendingPaymentHistory']);

// GET - Lookup Recent Purchases
Route::middleware('auth:sanctum')->get('/lookup-recent-purchases', [NgnClubController::class, 'lookupRecentPurchases']);

// POST - Delete a Club Member Purchase
Route::middleware('auth:sanctum')->post('/delete-purchase', [NgnClubController::class, 'deletePurchaseRequest']);

// POST - Verify Delete OTP
Route::middleware('auth:sanctum')->post('/verify-delete-otp', [NgnClubController::class, 'verifyDeleteOtp']);

// POST - Query get FULL Credit and Redeamable Credit.
Route::middleware('auth:sanctum')->post('/credit-status', [NgnClubController::class, 'creditLookup']);

// POST - Query get FULL Credit and Redeamable Credit.
Route::middleware('auth:sanctum')->post('/credit-status-get-time', [NgnClubController::class, 'creditLookupGetTime']);

// -- NGN MANAGER --
// GET - Locate all products
Route::middleware('auth:sanctum')->get('/products', [NgnManagerController::class, 'getAllProducts']);
Route::middleware('auth:sanctum')->get('/products/category/{category}', [NgnManagerController::class, 'getProductsByCategory']);
Route::middleware('auth:sanctum')->get('/products/branch/{branch}', [NgnManagerController::class, 'getProductsByBranch']);
Route::middleware('auth:sanctum')->get('/products/{id}/{branch_id}', [NgnManagerController::class, 'getProductById']);
Route::middleware('auth:sanctum')->post('/products/transfer', [NgnManagerController::class, 'transferProduct']);
// POST - Add Stock
Route::middleware('auth:sanctum')->post('/add_stock', [NgnManagerController::class, 'addStock']);

// POST - Adjust Stock
Route::middleware('auth:sanctum')->post('/adjust_stock', [NgnManagerController::class, 'adjustStock']);

Route::middleware('auth:sanctum')->delete('/harsh_p_s_delete', [NgnManagerController::class, 'harshDelete']);

// GET - Retrieve all brands
Route::middleware('auth:sanctum')->get('/brands', [NgnManagerController::class, 'getAllBrands']);

// GET - Retrieve all categories
Route::middleware('auth:sanctum')->get('/categories', [NgnManagerController::class, 'getAllCategories']);

// GET - Retrieve all models
Route::middleware('auth:sanctum')->get('/models', [NgnManagerController::class, 'getAllModels']);

// CRUD Operations for NgnProduct
// GET - Retrieve all NgnProducts with pagination and search
Route::middleware('auth:sanctum')->get('/ngnproducts', [NgnManagerController::class, 'getAllNgnProducts']);

// GET - Retrieve a specific NgnProduct by ID
Route::middleware('auth:sanctum')->get('/ngnproducts/{id}', [NgnManagerController::class, 'getNgnProductById']);

// POST - Create a new NgnProduct
Route::middleware('auth:sanctum')->post('/ngnproducts', [NgnManagerController::class, 'createNgnProduct']);

// PUT - Update an existing NgnProduct by ID
Route::middleware('auth:sanctum')->put('/ngnproducts/{id}', [NgnManagerController::class, 'updateNgnProduct']);

// DELETE - Delete a NgnProduct by ID
Route::middleware('auth:sanctum')->delete('/ngnproducts/{id}', [NgnManagerController::class, 'deleteNgnProduct']);

// POST - Submit Redeem Amount
Route::middleware('auth:sanctum')->post('/submit-redeem-amount', [NgnClubController::class, 'postRedeem']);
Route::middleware('auth:sanctum')->post('/initiate-redeem', [NgnClubController::class, 'initiateRedeem']);
Route::middleware('auth:sanctum')->post('/verify-otp-and-redeem', [NgnClubController::class, 'verifyOtpAndRedeem']);

Route::middleware('auth:sanctum')->post('/club-member-transactions', [NgnClubController::class, 'getAllUserTransactions']);

Route::middleware('auth:sanctum')->get('/ngnclub-insight', [NgnClubController::class, 'getNgnClubPurchasesAndRedeems']);

Route::middleware('auth:sanctum')->post('/update-redeem-invoice', [NgnClubController::class, 'updateRedeemInvoice']);

Route::middleware('auth:sanctum')->post('/referral-status', [NgnClubController::class, 'referralStatus']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Get branches
Route::middleware('auth:sanctum')->get('/branches', [NgnManagerController::class, 'getAllBranches']);

// Vehicle Management // //
Route::middleware('auth:sanctum')->get('/vehicles', [NgnManagerController::class, 'getAllVehicles']);
Route::middleware('auth:sanctum')->post('/update/vehicle', [NgnManagerController::class, 'updateVehicle']);

// Repair Management //
Route::middleware('auth:sanctum')->get('/repairs', [NgnManagerController::class, 'getAllRepairs']);
Route::middleware('auth:sanctum')->post('/repair', [NgnManagerController::class, 'createRepair']);
Route::middleware('auth:sanctum')->post('/repair/update', [NgnManagerController::class, 'updateRepair']);

// Vehicle Delivery API Endpoints
Route::middleware('auth:sanctum')->get('/vehicle-delivery-rates', [NgnVehicleDeliveryController::class, 'getAllVehicleDeliveryRates']);

// Retrieve all Vehicle Delivery Surcharges
Route::middleware('auth:sanctum')->get('/vehicle-delivery-surcharges', [NgnVehicleDeliveryController::class, 'getAllVehicleDeliverySurcharges']);

// Retrieve all Delivery Vehicle Types
Route::middleware('auth:sanctum')->get('/delivery-vehicle-types', [NgnVehicleDeliveryController::class, 'getAllDeliveryVehicleTypes']);

Route::middleware('auth:sanctum')->post('/calculate-distance', [NgnVehicleDeliveryController::class, 'calculateDistance']);
Route::middleware('auth:sanctum')->post('/calculate-manual-distance', [NgnVehicleDeliveryController::class, 'calculateManualDistance']);

// New Vehicle Delivery Order endpoint
Route::middleware('auth:sanctum')->post('/vehicle-delivery-orders', [NgnVehicleDeliveryController::class, 'store']);

// http://127.0.0.1:8000/api/used-for-sale?is_sold=0&reg_no=KX58XEL&model=VOLKSWAGEN&vin_number=RND-rzro3xCelmfTlfrPG&min_price=1000&max_price=5000&per_page=10
Route::get('/used-for-sale', [SalesController::class, 'getUsedForSale']);

// ECOMMERCE / V1 API / 28/12/2024 api.php >> START
Route::prefix('v1/shop')->group(function () {
    // ->shopAPI.getProducts
    Route::get('/products', [ECommerceShop::class, 'getProducts']);

    // ->shopAPI.getProductBySlug
    Route::get('/product/{slug}', [ECommerceShop::class, 'getProductBySlug']);

    // ->shopAPI.getProductAvailability
    Route::get('/product-availability/{id}', [ECommerceShop::class, 'getProductAvailability']);

    // ->shopAPI.getProductById
    Route::get('/p/{id}', [ECommerceShop::class, 'getProductById']);

    // ->shopAPI.getBrands
    Route::get('/brands', [ECommerceShop::class, 'getBrands']);

    // ->shopAPI.getCategories
    Route::get('/categories', [ECommerceShop::class, 'getCategories']);

    // Payment Methods
    Route::get('/payment-methods', [ECommerceShop::class, 'getPaymentMethods'])->middleware('auth:customer');
    Route::post('/payment-methods', [ECommerceShop::class, 'createPaymentMethod'])->middleware('auth:customer');
    Route::put('/payment-methods/{id}', [ECommerceShop::class, 'updatePaymentMethod'])->middleware('auth:customer');
    Route::delete('/payment-methods/{id}', [ECommerceShop::class, 'deletePaymentMethod'])->middleware('auth:customer');

    // Shipping Methods
    Route::get('/shipping-methods', [ECommerceShop::class, 'getShippingMethods']);
    Route::post('/shipping-methods', [ECommerceShop::class, 'createShippingMethod']);
    Route::put('/shipping-methods/{id}', [ECommerceShop::class, 'updateShippingMethod']);
    Route::delete('/shipping-methods/{id}', [ECommerceShop::class, 'deleteShippingMethod']);

    // Cart Pending Order
    Route::get('/cart-pending-order', [ECommerceShop::class, 'getCartPendingOrder'])->middleware('auth:customer');
    // add-single-item-to-cart (if logged in)
    Route::post('/order-item/add', [ECommerceShop::class, 'addOrderItem'])->middleware('auth:customer');
    // update-cart-item-quantity (if logged in)
    Route::put('/cart-item/{id}', [ECommerceShop::class, 'updateCartItemQuantity'])->middleware('auth:customer');
    // change-delivery-method (if logged in)
    Route::put('/cart-pending-order/delivery-method', [ECommerceShop::class, 'changeDeliveryMethod'])->middleware('auth:customer');

    // Orders (GET: /api/v1/shop/orders | -> shopAPI.getOrders())
    Route::get('/orders', [ECommerceShop::class, 'getOrders'])->middleware('auth:customer');

    Route::post('/orders', [ECommerceShop::class, 'createOrder'])->middleware('auth:customer');
    Route::get('/orders/{id}', [ECommerceShop::class, 'getOrderById'])->middleware('auth:customer');
    Route::put('/orders/{id}', [ECommerceShop::class, 'updateOrder'])->middleware('auth:customer');
    Route::delete('/orders/{id}', [ECommerceShop::class, 'deleteOrder'])->middleware('auth:customer');
    Route::put('/orders/{id}/cancel', [ECommerceShop::class, 'cancelOrder'])->middleware('auth:customer');

    // Order Shipping
    Route::get('/orders/{id}/shipping', [ECommerceShop::class, 'getOrderShipping'])->middleware('auth:customer');
    Route::post('/orders/{id}/shipping', [ECommerceShop::class, 'createOrderShipping'])->middleware('auth:customer');
    Route::put('/orders/{id}/shipping', [ECommerceShop::class, 'updateOrderShipping'])->middleware('auth:customer');

    // Customer Orders (GET: /api/v1/shop/customer-orders | -> shopAPI.getCustomerOrders())
    Route::get('/customer-orders', [ECommerceShop::class, 'getCustomerOrders'])->middleware('auth:customer');

    // Customer Addresses
    Route::get('/customer-addresses', [ECommerceShop::class, 'getCustomerAddresses'])->middleware('auth:customer');
    Route::post('/customer-addresses', [ECommerceShop::class, 'createCustomerAddress'])->middleware('auth:customer');
    Route::put('/customer-addresses/{id}', [ECommerceShop::class, 'updateCustomerAddress'])->middleware('auth:customer');
    Route::delete('/customer-addresses/{id}', [ECommerceShop::class, 'deleteCustomerAddress'])->middleware('auth:customer');

    // Countries
    Route::get('/countries', [ECommerceShop::class, 'getCountries']);

    // // Terms Agreements
    Route::get('/terms', [ECommerceShop::class, 'getTerms']);
    Route::post('/terms', [ECommerceShop::class, 'createTerms']);
    Route::put('/terms/{id}', [ECommerceShop::class, 'updateTerms']);
    Route::delete('/terms/{id}', [ECommerceShop::class, 'deleteTerms']);

    // Branch Management
    Route::get('/branches', [ECommerceShop::class, 'getBranches']);
    Route::post('/branches', [ECommerceShop::class, 'createBranch']);
    Route::get('/branches/{name}', [ECommerceShop::class, 'getBranchByName']);
    Route::put('/branches/{name}', [ECommerceShop::class, 'updateBranch']);
    Route::delete('/branches/{name}', [ECommerceShop::class, 'deleteBranch']);

    // GET ('/api/v1/shop/blog/posts', ECommerceShop.BlogIndex)
    Route::get('/blog/posts', [ECommerceShop::class, 'BlogIndex']);

    // GET ('/api/v1/shop/blog/posts/{slug}', ECommerceShop.BlogShow)
    Route::get('/blog/posts/{slug}', [ECommerceShop::class, 'BlogShow']);

    // Order Summary
    Route::get('/order-summary', [ECommerceShop::class, 'getOrderSummary'])->middleware('auth:customer');

    // Add new route to check pending order status
    Route::get('/check-pending-order-status', [ECommerceShop::class, 'checkPendingOrderStatus'])
        ->middleware('auth:customer');

    // Service Enquiry Form submit
    Route::post('/service-enquiry-form-vue', [ContactController::class, 'handleEnquiryFormVue'])->name('handle-enquiry-form-vue');
});
// ECOMMERCE / V1 API / 28/12/2024 api.php >> END
// ECOMMERCE Customer Auth Routes / V1 API / 28/12/2024 >> START
Route::prefix('v1/customer')->group(function () {
    Route::post('register', [CustomerAuthController::class, 'register']);
    Route::post('login', [CustomerAuthController::class, 'login']);
    Route::post('logout', [CustomerAuthController::class, 'logout'])->middleware('auth:customer,sanctum');
    Route::get('user', [CustomerAuthController::class, 'getUser'])->middleware('auth:customer,sanctum');
    Route::get('user/{id}', [CustomerAuthController::class, 'getUserById'])->middleware('auth:customer,sanctum');
    Route::post('forgot-password', [CustomerAuthController::class, 'resetPassword']);
    Route::post('confirm-reset-password', [CustomerAuthController::class, 'confirmResetPassword']);
    Route::get('documents/requirements', [CustomerDocumentController::class, 'requirements'])->middleware('auth:customer,sanctum');
    Route::post('documents', [CustomerDocumentController::class, 'store'])->middleware('auth:customer,sanctum');

    Route::prefix('support')->middleware('auth:customer,sanctum')->group(function () {
        Route::get('conversations', [\App\Http\Controllers\Api\SupportConversationController::class, 'index']);
        Route::post('conversations', [\App\Http\Controllers\Api\SupportConversationController::class, 'store']);
        Route::get('conversations/{uuid}', [\App\Http\Controllers\Api\SupportConversationController::class, 'show']);
        Route::get('conversations/{uuid}/messages', [\App\Http\Controllers\Api\SupportConversationController::class, 'messages']);
        Route::get('conversations/{uuid}/latest-message', [\App\Http\Controllers\Api\SupportConversationController::class, 'latestMessage']);
        Route::post('conversations/{uuid}/messages', [\App\Http\Controllers\Api\SupportConversationController::class, 'sendMessage']);
        Route::get('attachments/{attachmentId}', [\App\Http\Controllers\Api\SupportMessageController::class, 'showAttachment'])->name('api.customer.support.attachments.show');
        Route::post('messages/{messageId}/attachments', [\App\Http\Controllers\Api\SupportMessageController::class, 'attachFiles']);
    });

});
// ECOMMERCE Customer Auth Routes / V1 API / 28/12/2024 >> END

Route::prefix('v1/staff')->middleware('auth:sanctum')->group(function () {
    Route::get('me', [StaffAuthController::class, 'me']);
    Route::post('logout', [StaffAuthController::class, 'logout']);

    Route::prefix('support')->group(function () {
        Route::get('inbox', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inbox']);
        Route::get('inbox/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inboxThread']);
        Route::post('inbox/{conversationId}/messages', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inboxSendMessage']);
        Route::patch('inbox/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inboxUpdateMeta']);
        Route::get('assignees', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'assignees']);
        Route::get('conversations', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'index']);
        Route::get('conversations/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'show']);
        Route::get('conversations/{conversationId}/messages', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'messages']);
        Route::get('conversations/{conversationId}/latest-message', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'latestMessage']);
        Route::post('conversations/{conversationId}/messages', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'sendMessage']);
        Route::patch('conversations/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'updateConversation']);
        Route::get('attachments/{attachmentId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'showAttachment'])->name('api.staff.support.attachments.show');
    });
});

Route::post('v1/staff/login', [StaffAuthController::class, 'login']);

Route::prefix('v1/mobile')->group(function () {
    Route::get('system-map', [MobileBootstrapController::class, 'systemMap']);
    Route::get('forms-blueprint', [MobileBootstrapController::class, 'formsBlueprint']);
    Route::get('content/page-manifest', [MobileExperienceController::class, 'pageManifest']);
    Route::get('content/experience-blueprint', [MobileExperienceController::class, 'experienceBlueprint']);
    Route::get('content/full-app-map', [MobileExperienceController::class, 'fullAppMap']);
    Route::get('content/db-link-map', [MobileExperienceController::class, 'dbLinkMap']);
    Route::get('content/frontend-parity-map', [MobileExperienceController::class, 'frontendParityMap']);
    Route::get('presentation/views', [MobileExperienceController::class, 'presentationViews']);
    Route::get('presentation/views/{segment}/{path}', [MobileExperienceController::class, 'presentationViewPayload'])
        ->where('path', '.*');

    Route::get('home-feed', [MobileCatalogueController::class, 'homeFeed']);
    Route::get('branches', [MobileCatalogueController::class, 'branches']);
    Route::get('bikes', [MobileCatalogueController::class, 'bikes']);
    Route::get('bikes/new/{id}', [MobileCatalogueController::class, 'newBikeDetail']);
    Route::get('bikes/used/{id}', [MobileCatalogueController::class, 'usedBikeDetail']);
    Route::get('rentals', [MobileCatalogueController::class, 'rentals']);
    Route::get('services', [MobileCatalogueController::class, 'services']);
    Route::get('shop/products', [MobileCatalogueController::class, 'shopProducts']);
    Route::get('shop/products/{idOrSlug}', [MobileCatalogueController::class, 'shopProductDetail']);
    Route::get('shop/filters', [MobileCatalogueController::class, 'shopFilters']);
    Route::get('spare-parts', [MobileCatalogueController::class, 'spareParts']);
    Route::get('spare-parts/manufacturers', [MobileSparePartsController::class, 'manufacturers']);
    Route::get('spare-parts/models/{manufacturer}', [MobileSparePartsController::class, 'models']);
    Route::get('spare-parts/years/{manufacturer}/{model}', [MobileSparePartsController::class, 'years']);
    Route::get('spare-parts/countries/{manufacturer}/{model}/{year}', [MobileSparePartsController::class, 'countries']);
    Route::get('spare-parts/colours/{manufacturer}/{model}/{year}/{country}', [MobileSparePartsController::class, 'colours']);
    Route::get('spare-parts/assemblies/{manufacturer}/{model}/{year}/{country}/{colour}', [MobileSparePartsController::class, 'assemblies']);
    Route::get('spare-parts/parts/{manufacturer}/{model}/{year}/{country}/{colour}/{assembly}', [MobileSparePartsController::class, 'parts']);
    Route::get('spare-parts/part/{partNumber}', [MobileSparePartsController::class, 'part']);
    Route::get('spare-parts/part/{partNumber}/detail', [MobileSparePartsController::class, 'partDetail']);
    Route::get('ebikes/experience', [MobileExperienceController::class, 'ebikesExperience']);
    Route::get('rentals/{id}', [MobileCatalogueController::class, 'rentalDetail']);
    Route::get('careers', [MobileExperienceController::class, 'careers']);
    Route::get('careers/{id}', [MobileExperienceController::class, 'careerDetail']);
    Route::get('partners', [MobileExperienceController::class, 'partners']);
    Route::get('legal/pages', [MobileExperienceController::class, 'legalPages']);
    Route::get('legal/pages/{slug}', [MobileExperienceController::class, 'legalPageDetail']);
    Route::get('blog/posts', [MobileExperienceController::class, 'blogPosts']);
    Route::get('blog/posts/{slug}', [MobileExperienceController::class, 'blogPostDetail']);
    Route::get('reviews', [MobileExperienceController::class, 'reviews']);
    Route::get('auth/blueprint', [MobileExperienceController::class, 'authBlueprint']);
    Route::get('content/website-navigation', [MobileContentController::class, 'websiteNavigation']);
    Route::get('content/portal-navigation', [MobileContentController::class, 'portalNavigation']);
    Route::get('content/home-blocks', [MobileContentController::class, 'homeBlocks']);
    Route::get('content/service-modules', [MobileContentController::class, 'serviceModules']);
    Route::get('services/mot', [MobilePublicFormsController::class, 'serviceContent'])->defaults('slug', 'mot');
    Route::get('services/repairs/basic', [MobilePublicFormsController::class, 'serviceContent'])->defaults('slug', 'repairs/basic');
    Route::get('services/repairs/full', [MobilePublicFormsController::class, 'serviceContent'])->defaults('slug', 'repairs/full');
    Route::get('services/repairs/comparison', [MobilePublicFormsController::class, 'serviceContent'])->defaults('slug', 'repairs/comparison');
    Route::get('services/recovery', [MobilePublicFormsController::class, 'serviceContent'])->defaults('slug', 'recovery');
    Route::get('services/rentals', [MobilePublicFormsController::class, 'serviceContent'])->defaults('slug', 'rentals');
    Route::post('mot/check', [MobilePublicFormsController::class, 'motCheck']);
    Route::post('mot/alerts', [MobilePublicFormsController::class, 'motAlerts']);
    Route::get('finance/content', [MobilePublicFormsController::class, 'financeContent']);
    Route::post('finance/calculate', [MobilePublicFormsController::class, 'financeCalculate']);
    Route::post('finance/apply', [MobilePublicFormsController::class, 'financeApply']);
    Route::post('contact/call-back', [MobilePublicFormsController::class, 'contactCallback']);
    Route::post('contact/trade-account', [MobilePublicFormsController::class, 'contactTradeAccount']);
    Route::post('contact/service-booking', [MobilePublicFormsController::class, 'contactServiceBooking']);
    Route::get('club/content', [MobileClubController::class, 'content']);
    Route::post('club/register', [MobileClubController::class, 'register']);
    Route::post('club/login', [MobileClubController::class, 'login']);
    Route::post('club/login-by-customer-match', [MobileClubParityController::class, 'loginByCustomerMatch']);
    Route::post('club/passkey/request-reset', [MobileClubParityController::class, 'requestPasskeyReset']);
    Route::post('club/passkey/confirm-reset', [MobileClubParityController::class, 'confirmPasskeyReset']);
    Route::get('club/dashboard', [MobileClubController::class, 'dashboard']);
    Route::post('club/referral', [MobileClubController::class, 'referral']);
    Route::get('club/dashboard/parity', [MobileClubParityController::class, 'dashboard']);
    Route::patch('club/profile', [MobileClubParityController::class, 'updateProfile']);
    Route::post('club/estimator/quote', [MobileClubParityController::class, 'estimateQuote']);
    Route::post('club/estimator/feedback', [MobileClubParityController::class, 'estimateFeedback']);

    Route::middleware('auth:customer,sanctum')->group(function () {
        Route::get('portal/overview', [MobilePortalController::class, 'overview']);
        Route::get('portal/full-state', [MobilePortalController::class, 'fullState']);
        Route::get('portal/orders', [MobilePortalController::class, 'myOrders']);
        Route::get('portal/orders/{id}', [MobilePortalAccountController::class, 'orderDetail']);
        Route::get('portal/rentals', [MobilePortalController::class, 'myRentals']);
        Route::get('portal/rentals/{id}', [MobilePortalController::class, 'rentalDetail']);
        Route::get('portal/mot-bookings', [MobilePortalController::class, 'myMotBookings']);
        Route::post('portal/mot-bookings', [MobilePortalController::class, 'createMotBooking']);
        Route::get('portal/bookings', [MobilePortalController::class, 'bookingsUnified']);
        Route::get('portal/recovery-requests', [MobilePortalController::class, 'myRecoveryRequests']);
        Route::get('portal/page-blueprint', [MobilePortalExperienceController::class, 'pageBlueprint']);

        Route::get('portal/addresses', [MobilePortalExperienceController::class, 'addresses']);
        Route::get('portal/addresses/countries', [MobilePortalExperienceController::class, 'addressCountries']);
        Route::post('portal/addresses', [MobilePortalExperienceController::class, 'createAddress']);
        Route::patch('portal/addresses/{id}', [MobilePortalExperienceController::class, 'updateAddress']);
        Route::delete('portal/addresses/{id}', [MobilePortalExperienceController::class, 'deleteAddress']);
        Route::post('portal/addresses/{id}/default', [MobilePortalExperienceController::class, 'setAddressDefault']);

        Route::get('portal/documents', [MobilePortalExperienceController::class, 'documents']);
        Route::get('portal/documents/types', [MobilePortalAccountController::class, 'documentTypes']);
        Route::post('portal/documents/upload', [MobilePortalExperienceController::class, 'uploadDocument']);

        Route::get('portal/payments/recurring', [MobilePortalExperienceController::class, 'recurringPayments']);
        Route::get('portal/profile', [MobilePortalAccountController::class, 'profile']);
        Route::patch('portal/profile', [MobilePortalAccountController::class, 'updateProfile']);
        Route::post('portal/security/change-password', [MobilePortalAccountController::class, 'changePassword']);
        Route::get('portal/payment-methods', [MobilePortalAccountController::class, 'paymentMethods']);
        Route::post('portal/payment-methods', [MobilePortalAccountController::class, 'selectPaymentMethod']);
        Route::delete('portal/payment-methods', [MobilePortalAccountController::class, 'clearPaymentMethod']);
        Route::get('cart', [MobileCheckoutController::class, 'cart']);
        Route::post('cart/items', [MobileCheckoutController::class, 'addItem']);
        Route::patch('cart/items/{id}', [MobileCheckoutController::class, 'updateItem']);
        Route::delete('cart/items/{id}', [MobileCheckoutController::class, 'removeItem']);
        Route::post('checkout/quote', [MobileCheckoutController::class, 'quote']);
        Route::post('checkout/place-order', [MobileCheckoutController::class, 'placeOrder']);
        Route::get('portal/rentals/browse/options', [MobilePortalExperienceController::class, 'rentalBrowseOptions']);
        Route::get('portal/rentals/available', [MobilePortalExperienceController::class, 'rentalAvailable']);
        Route::get('portal/rentals/create/{motorbikeId}/blueprint', [MobilePortalExperienceController::class, 'rentalCreateBlueprint']);
        Route::post('portal/rentals/create/{motorbikeId}', [MobilePortalExperienceController::class, 'rentalCreateRequest']);

        Route::get('portal/repairs/appointment/options', [MobilePortalExperienceController::class, 'repairsAppointmentOptions']);
        Route::post('portal/repairs/appointments', [MobilePortalExperienceController::class, 'createRepairsAppointment']);

        Route::get('portal/recovery/options', [MobilePortalExperienceController::class, 'recoveryOptions']);
        Route::post('portal/recovery/quote', [MobilePortalExperienceController::class, 'recoveryQuote']);
        Route::post('portal/recovery/requests', [MobilePortalExperienceController::class, 'createRecoveryRequest']);
        Route::get('enquiries', [MobileEnquiryController::class, 'index']);
        Route::post('enquiries', [MobileEnquiryController::class, 'store']);
        Route::get('enquiries/{id}', [MobileEnquiryController::class, 'show']);
    });
});

Route::prefix('v2/mobile')->group(function () {
    Route::get('system-map', [MobileV2BootstrapController::class, 'systemMap']);
    Route::get('forms-blueprint', [MobileV2BootstrapController::class, 'formsBlueprint']);
    Route::get('content/page-manifest', [MobileV2ExperienceController::class, 'pageManifest']);
    Route::get('content/experience-blueprint', [MobileV2ExperienceController::class, 'experienceBlueprint']);
    Route::get('content/full-app-map', [MobileV2ExperienceController::class, 'fullAppMap']);
    Route::get('content/db-link-map', [MobileV2ExperienceController::class, 'dbLinkMap']);
    Route::get('content/frontend-parity-map', [MobileV2ExperienceController::class, 'frontendParityMap']);
    Route::get('presentation/views', [MobileV2ExperienceController::class, 'presentationViews']);
    Route::get('presentation/views/{segment}/{path}', [MobileV2ExperienceController::class, 'presentationViewPayload'])
        ->where('path', '.*');

    Route::get('home-feed', [MobileV2CatalogueController::class, 'homeFeed']);
    Route::get('branches', [MobileV2CatalogueController::class, 'branches']);
    Route::get('bikes', [MobileV2CatalogueController::class, 'bikes']);
    Route::get('bikes/new/{id}', [MobileV2CatalogueController::class, 'newBikeDetail']);
    Route::get('bikes/used/{id}', [MobileV2CatalogueController::class, 'usedBikeDetail']);
    Route::get('rentals', [MobileV2CatalogueController::class, 'rentals']);
    Route::get('services', [MobileV2CatalogueController::class, 'services']);
    Route::get('shop/products', [MobileV2CatalogueController::class, 'shopProducts']);
    Route::get('shop/products/{idOrSlug}', [MobileV2CatalogueController::class, 'shopProductDetail']);
    Route::get('shop/filters', [MobileV2CatalogueController::class, 'shopFilters']);
    Route::get('spare-parts', [MobileV2CatalogueController::class, 'spareParts']);
    Route::get('spare-parts/manufacturers', [MobileV2SparePartsController::class, 'manufacturers']);
    Route::get('spare-parts/models/{manufacturer}', [MobileV2SparePartsController::class, 'models']);
    Route::get('spare-parts/years/{manufacturer}/{model}', [MobileV2SparePartsController::class, 'years']);
    Route::get('spare-parts/countries/{manufacturer}/{model}/{year}', [MobileV2SparePartsController::class, 'countries']);
    Route::get('spare-parts/colours/{manufacturer}/{model}/{year}/{country}', [MobileV2SparePartsController::class, 'colours']);
    Route::get('spare-parts/assemblies/{manufacturer}/{model}/{year}/{country}/{colour}', [MobileV2SparePartsController::class, 'assemblies']);
    Route::get('spare-parts/parts/{manufacturer}/{model}/{year}/{country}/{colour}/{assembly}', [MobileV2SparePartsController::class, 'parts']);
    Route::get('spare-parts/part/{partNumber}', [MobileV2SparePartsController::class, 'part']);
    Route::get('spare-parts/part/{partNumber}/detail', [MobileV2SparePartsController::class, 'partDetail']);
    Route::get('ebikes/experience', [MobileV2ExperienceController::class, 'ebikesExperience']);
    Route::get('rentals/{id}', [MobileV2CatalogueController::class, 'rentalDetail']);
    Route::get('careers', [MobileV2ExperienceController::class, 'careers']);
    Route::get('careers/{id}', [MobileV2ExperienceController::class, 'careerDetail']);
    Route::get('partners', [MobileV2ExperienceController::class, 'partners']);
    Route::get('legal/pages', [MobileV2ExperienceController::class, 'legalPages']);
    Route::get('legal/pages/{slug}', [MobileV2ExperienceController::class, 'legalPageDetail']);
    Route::get('blog/posts', [MobileV2ExperienceController::class, 'blogPosts']);
    Route::get('blog/posts/{slug}', [MobileV2ExperienceController::class, 'blogPostDetail']);
    Route::get('reviews', [MobileV2ExperienceController::class, 'reviews']);
    Route::get('auth/blueprint', [MobileV2ExperienceController::class, 'authBlueprint']);
    Route::get('content/website-navigation', [MobileV2ContentController::class, 'websiteNavigation']);
    Route::get('content/portal-navigation', [MobileV2ContentController::class, 'portalNavigation']);
    Route::get('content/home-blocks', [MobileV2ContentController::class, 'homeBlocks']);
    Route::get('content/service-modules', [MobileV2ContentController::class, 'serviceModules']);
    Route::get('services/mot', [MobileV2PublicFormsController::class, 'serviceContent'])->defaults('slug', 'mot');
    Route::get('services/repairs/basic', [MobileV2PublicFormsController::class, 'serviceContent'])->defaults('slug', 'repairs/basic');
    Route::get('services/repairs/full', [MobileV2PublicFormsController::class, 'serviceContent'])->defaults('slug', 'repairs/full');
    Route::get('services/repairs/comparison', [MobileV2PublicFormsController::class, 'serviceContent'])->defaults('slug', 'repairs/comparison');
    Route::get('services/recovery', [MobileV2PublicFormsController::class, 'serviceContent'])->defaults('slug', 'recovery');
    Route::get('services/rentals', [MobileV2PublicFormsController::class, 'serviceContent'])->defaults('slug', 'rentals');
    Route::post('mot/check', [MobileV2PublicFormsController::class, 'motCheck']);
    Route::post('mot/alerts', [MobileV2PublicFormsController::class, 'motAlerts']);
    Route::get('finance/content', [MobileV2PublicFormsController::class, 'financeContent']);
    Route::post('finance/calculate', [MobileV2PublicFormsController::class, 'financeCalculate']);
    Route::post('finance/apply', [MobileV2PublicFormsController::class, 'financeApply']);
    Route::post('contact/call-back', [MobileV2PublicFormsController::class, 'contactCallback']);
    Route::post('contact/trade-account', [MobileV2PublicFormsController::class, 'contactTradeAccount']);
    Route::post('contact/service-booking', [MobileV2PublicFormsController::class, 'contactServiceBooking']);
    Route::get('club/content', [MobileV2ClubController::class, 'content']);
    Route::post('club/register', [MobileV2ClubController::class, 'register']);
    Route::post('club/login', [MobileV2ClubController::class, 'login']);
    Route::post('club/login-by-customer-match', [MobileV2ClubParityController::class, 'loginByCustomerMatch']);
    Route::post('club/passkey/request-reset', [MobileV2ClubParityController::class, 'requestPasskeyReset']);
    Route::post('club/passkey/confirm-reset', [MobileV2ClubParityController::class, 'confirmPasskeyReset']);
    Route::get('club/dashboard', [MobileV2ClubController::class, 'dashboard']);
    Route::post('club/referral', [MobileV2ClubController::class, 'referral']);
    Route::get('club/dashboard/parity', [MobileV2ClubParityController::class, 'dashboard']);
    Route::patch('club/profile', [MobileV2ClubParityController::class, 'updateProfile']);
    Route::post('club/estimator/quote', [MobileV2ClubParityController::class, 'estimateQuote']);
    Route::post('club/estimator/feedback', [MobileV2ClubParityController::class, 'estimateFeedback']);

    Route::prefix('auth/customer')->group(function () {
        Route::post('register', [CustomerAuthController::class, 'register']);
        Route::post('login', [CustomerAuthController::class, 'login']);
        Route::post('forgot-password', [CustomerAuthController::class, 'resetPassword']);
        Route::post('confirm-reset-password', [CustomerAuthController::class, 'confirmResetPassword']);
    });

    Route::post('auth/staff/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->prefix('club/legacy')->group(function () {
        Route::post('member-purchases', [MobileV2ClubLegacyController::class, 'storePurchase']);
        Route::post('member-purchases-mb', [MobileV2ClubLegacyController::class, 'storePurchaseMb']);
        Route::post('customer-spending', [MobileV2ClubLegacyController::class, 'storeSpending']);
        Route::post('list-customer-spending', [MobileV2ClubLegacyController::class, 'listSpending']);
        Route::post('delete-customer-spending', [MobileV2ClubLegacyController::class, 'deleteSpending']);
        Route::post('record-spending-payment', [MobileV2ClubLegacyController::class, 'recordSpendingPayment']);
        Route::post('spending-payment-history', [MobileV2ClubLegacyController::class, 'spendingPaymentHistory']);
        Route::post('credit-status', [MobileV2ClubLegacyController::class, 'creditStatus']);
        Route::post('credit-status-get-time', [MobileV2ClubLegacyController::class, 'creditStatusWithTime']);
        Route::post('initiate-redeem', [MobileV2ClubLegacyController::class, 'initiateRedeem']);
        Route::post('verify-otp-and-redeem', [MobileV2ClubLegacyController::class, 'verifyOtpAndRedeem']);
        Route::post('update-redeem-invoice', [MobileV2ClubLegacyController::class, 'updateRedeemInvoice']);
        Route::post('referral-status', [MobileV2ClubLegacyController::class, 'referralStatus']);
    });

    Route::middleware('auth:customer,sanctum')->group(function () {
        Route::post('auth/customer/logout', [CustomerAuthController::class, 'logout']);
        Route::get('auth/customer/user', [CustomerAuthController::class, 'getUser']);

        Route::get('portal/overview', [MobileV2PortalController::class, 'overview']);
        Route::get('portal/full-state', [MobileV2PortalController::class, 'fullState']);
        Route::get('portal/orders', [MobileV2PortalController::class, 'myOrders']);
        Route::get('portal/orders/{id}', [MobileV2PortalAccountController::class, 'orderDetail']);
        Route::get('portal/rentals', [MobileV2PortalController::class, 'myRentals']);
        Route::get('portal/rentals/{id}', [MobileV2PortalController::class, 'rentalDetail']);
        Route::get('portal/mot-bookings', [MobileV2PortalController::class, 'myMotBookings']);
        Route::post('portal/mot-bookings', [MobileV2PortalController::class, 'createMotBooking']);
        Route::get('portal/bookings', [MobileV2PortalController::class, 'bookingsUnified']);
        Route::get('portal/recovery-requests', [MobileV2PortalController::class, 'myRecoveryRequests']);
        Route::get('portal/page-blueprint', [MobileV2PortalExperienceController::class, 'pageBlueprint']);
        Route::get('portal/addresses', [MobileV2PortalExperienceController::class, 'addresses']);
        Route::get('portal/addresses/countries', [MobileV2PortalExperienceController::class, 'addressCountries']);
        Route::post('portal/addresses', [MobileV2PortalExperienceController::class, 'createAddress']);
        Route::patch('portal/addresses/{id}', [MobileV2PortalExperienceController::class, 'updateAddress']);
        Route::delete('portal/addresses/{id}', [MobileV2PortalExperienceController::class, 'deleteAddress']);
        Route::post('portal/addresses/{id}/default', [MobileV2PortalExperienceController::class, 'setAddressDefault']);
        Route::get('portal/documents', [MobileV2PortalExperienceController::class, 'documents']);
        Route::get('portal/documents/types', [MobileV2PortalAccountController::class, 'documentTypes']);
        Route::post('portal/documents/upload', [MobileV2PortalExperienceController::class, 'uploadDocument']);
        Route::get('portal/payments/recurring', [MobileV2PortalExperienceController::class, 'recurringPayments']);
        Route::get('portal/profile', [MobileV2PortalAccountController::class, 'profile']);
        Route::patch('portal/profile', [MobileV2PortalAccountController::class, 'updateProfile']);
        Route::post('portal/security/change-password', [MobileV2PortalAccountController::class, 'changePassword']);
        Route::get('portal/payment-methods', [MobileV2PortalAccountController::class, 'paymentMethods']);
        Route::post('portal/payment-methods', [MobileV2PortalAccountController::class, 'selectPaymentMethod']);
        Route::delete('portal/payment-methods', [MobileV2PortalAccountController::class, 'clearPaymentMethod']);
        Route::get('cart', [MobileV2CheckoutController::class, 'cart']);
        Route::post('cart/items', [MobileV2CheckoutController::class, 'addItem']);
        Route::patch('cart/items/{id}', [MobileV2CheckoutController::class, 'updateItem']);
        Route::delete('cart/items/{id}', [MobileV2CheckoutController::class, 'removeItem']);
        Route::post('checkout/quote', [MobileV2CheckoutController::class, 'quote']);
        Route::post('checkout/place-order', [MobileV2CheckoutController::class, 'placeOrder']);
        Route::get('portal/rentals/browse/options', [MobileV2PortalExperienceController::class, 'rentalBrowseOptions']);
        Route::get('portal/rentals/available', [MobileV2PortalExperienceController::class, 'rentalAvailable']);
        Route::get('portal/rentals/create/{motorbikeId}/blueprint', [MobileV2PortalExperienceController::class, 'rentalCreateBlueprint']);
        Route::post('portal/rentals/create/{motorbikeId}', [MobileV2PortalExperienceController::class, 'rentalCreateRequest']);
        Route::get('portal/repairs/appointment/options', [MobileV2PortalExperienceController::class, 'repairsAppointmentOptions']);
        Route::post('portal/repairs/appointments', [MobileV2PortalExperienceController::class, 'createRepairsAppointment']);
        Route::get('portal/recovery/options', [MobileV2PortalExperienceController::class, 'recoveryOptions']);
        Route::post('portal/recovery/quote', [MobileV2PortalExperienceController::class, 'recoveryQuote']);
        Route::post('portal/recovery/requests', [MobileV2PortalExperienceController::class, 'createRecoveryRequest']);
        Route::get('enquiries', [MobileV2EnquiryController::class, 'index']);
        Route::post('enquiries', [MobileV2EnquiryController::class, 'store']);
        Route::get('enquiries/{id}', [MobileV2EnquiryController::class, 'show']);

        Route::prefix('customer/support')->group(function () {
            Route::get('conversations', [\App\Http\Controllers\Api\SupportConversationController::class, 'index']);
            Route::post('conversations', [\App\Http\Controllers\Api\SupportConversationController::class, 'store']);
            Route::get('conversations/{uuid}', [\App\Http\Controllers\Api\SupportConversationController::class, 'show']);
            Route::get('conversations/{uuid}/messages', [\App\Http\Controllers\Api\SupportConversationController::class, 'messages']);
            Route::get('conversations/{uuid}/latest-message', [\App\Http\Controllers\Api\SupportConversationController::class, 'latestMessage']);
            Route::post('conversations/{uuid}/messages', [\App\Http\Controllers\Api\SupportConversationController::class, 'sendMessage']);
            Route::get('attachments/{attachmentId}', [\App\Http\Controllers\Api\SupportMessageController::class, 'showAttachment']);
            Route::post('messages/{messageId}/attachments', [\App\Http\Controllers\Api\SupportMessageController::class, 'attachFiles']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/staff/me', [StaffAuthController::class, 'me']);
        Route::post('auth/staff/logout', [StaffAuthController::class, 'logout']);
        Route::prefix('staff/support')->group(function () {
            Route::get('inbox', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inbox']);
            Route::get('inbox/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inboxThread']);
            Route::post('inbox/{conversationId}/messages', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inboxSendMessage']);
            Route::patch('inbox/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'inboxUpdateMeta']);
            Route::get('assignees', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'assignees']);
            Route::get('conversations', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'index']);
            Route::get('conversations/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'show']);
            Route::get('conversations/{conversationId}/messages', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'messages']);
            Route::get('conversations/{conversationId}/latest-message', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'latestMessage']);
            Route::post('conversations/{conversationId}/messages', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'sendMessage']);
            Route::patch('conversations/{conversationId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'updateConversation']);
            Route::get('attachments/{attachmentId}', [\App\Http\Controllers\Api\StaffSupportConversationController::class, 'showAttachment']);
        });
    });
});

// Email Verification Routes for Customers
Route::middleware(['auth:customer'])->group(function () {
    Route::post('/email/verification-notification',
        [CustomerVerificationController::class, 'sendVerificationEmail'])
        ->name('api.customer.email.verification.send');
});

Route::get('/email/verify/{id}/{hash}',
    [CustomerVerificationController::class, 'verify'])
    ->name('api.customer.verification.verify');
