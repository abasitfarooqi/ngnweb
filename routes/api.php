<?php

use App\Http\Controllers\Api\CustomerDocumentController;
use App\Http\Controllers\Api\Mobile\MobileBootstrapController;
use App\Http\Controllers\Api\Mobile\MobileCatalogueController;
use App\Http\Controllers\Api\Mobile\MobileContentController;
use App\Http\Controllers\Api\Mobile\MobileEnquiryController;
use App\Http\Controllers\Api\Mobile\MobilePortalController;
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

    Route::get('home-feed', [MobileCatalogueController::class, 'homeFeed']);
    Route::get('branches', [MobileCatalogueController::class, 'branches']);
    Route::get('bikes', [MobileCatalogueController::class, 'bikes']);
    Route::get('rentals', [MobileCatalogueController::class, 'rentals']);
    Route::get('services', [MobileCatalogueController::class, 'services']);
    Route::get('shop/products', [MobileCatalogueController::class, 'shopProducts']);
    Route::get('spare-parts', [MobileCatalogueController::class, 'spareParts']);
    Route::get('content/website-navigation', [MobileContentController::class, 'websiteNavigation']);
    Route::get('content/portal-navigation', [MobileContentController::class, 'portalNavigation']);
    Route::get('content/home-blocks', [MobileContentController::class, 'homeBlocks']);
    Route::get('content/service-modules', [MobileContentController::class, 'serviceModules']);

    Route::middleware('auth:customer,sanctum')->group(function () {
        Route::get('portal/overview', [MobilePortalController::class, 'overview']);
        Route::get('portal/orders', [MobilePortalController::class, 'myOrders']);
        Route::get('portal/rentals', [MobilePortalController::class, 'myRentals']);
        Route::get('portal/mot-bookings', [MobilePortalController::class, 'myMotBookings']);
        Route::get('portal/recovery-requests', [MobilePortalController::class, 'myRecoveryRequests']);
        Route::get('enquiries', [MobileEnquiryController::class, 'index']);
        Route::post('enquiries', [MobileEnquiryController::class, 'store']);
        Route::get('enquiries/{id}', [MobileEnquiryController::class, 'show']);
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
