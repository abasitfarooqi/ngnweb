<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\JudopayAuthorizationHelper;
use App\Helpers\JudopayCit;
use App\Helpers\JudopayReference;
use App\Helpers\JudopayOnboardingInit;
use App\Helpers\JudopaySmsHelper;
use App\Helpers\JudopayWeeklyMitSummary;
use App\Mail\BillingChangeNotification;
use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\RentingBooking;
use App\Models\JudopayCitAccess;
use App\Models\JudopayCitPaymentSession;
use App\Models\JudopayOnboarding;
use App\Models\JudopaySubscription;
use App\Models\NgnMitQueue;
use App\Services\JudopayService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecurringController extends Controller
{
    // /judopay
    public function index()
    {
        JudopayOnboardingInit::init();

        // Get customers with onboarding records who have active services
        $customers = JudopayOnboarding::where('onboardable_type', Customer::class)
            ->with([
                'onboardable' => function ($q) {
                    $q->with([
                        // Active rentals - use static method
                        'renting_bookings' => function ($rentals) {
                            $rentals->where('is_posted', true)
                                ->whereHas('rentingBookingItems', fn ($items) => $items->whereNull('end_date'))
                                ->with(['rentingBookingItems' => function ($items) {
                                    $items->whereNull('end_date')
                                        ->with('motorbike:id,reg_no,make,model');
                                }]);
                        },
                        // Active finance applications - use static method
                        'financeApplications' => function ($finance) {
                            $finance->where('is_posted', true)
                                ->where(function ($q) {
                                    $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
                                })
                                ->where(function ($q) {
                                    $q->where('log_book_sent', false)->orWhereNull('log_book_sent');
                                })
                                ->with(['application_items' => function ($items) {
                                    $items->with('motorbike:id,reg_no,make,model');
                                }]);
                        },
                    ]);
                },
                // Load subscriptions for each onboarding
                'subscriptions' => function ($q) {
                    $q->with('subscribable');
                },
            ])
            ->get();

        return view('admin.judopay', [
            'customers' => $customers,
        ]);
    }

    // /judopay/subscribe/{id}
    public function subscribe($id)
    {
        // Clear any volatile session warnings when viewing the page directly
        session()->forget('active_session_warning');

        // Get the subscription with related data including payment outcomes
        $subscription = JudopaySubscription::with([
            'judopayOnboarding.onboardable', // Customer data
            'subscribable', // Rental or Finance application
            'citPaymentSessions.paymentSessionOutcomes' => function ($query) {
                $query->where('status', 'success')->latest('occurred_at');
            },
            'mitPaymentSessions.paymentSessionOutcomes' => function ($query) {
                $query->where('status', 'success')->latest('occurred_at');
            },
        ])->findOrFail($id);

        // Get customer data
        $customer = $subscription->judopayOnboarding->onboardable;

        // Get subscription type and related service data
        $serviceData = null;
        if ($subscription->subscribable_type === RentingBooking::class) {
            $serviceData = $subscription->subscribable->load(['rentingBookingItems.motorbike']);
        } elseif ($subscription->subscribable_type === FinanceApplication::class) {
            $serviceData = $subscription->subscribable->load(['application_items.motorbike']);
        }

        // Get the most recent successful payment outcome
        $latestPaymentOutcome = null;
        foreach ($subscription->citPaymentSessions as $session) {
            foreach ($session->paymentSessionOutcomes as $outcome) {
                if (! $latestPaymentOutcome || $outcome->occurred_at > $latestPaymentOutcome->occurred_at) {
                    $latestPaymentOutcome = $outcome;
                }
            }
        }
        foreach ($subscription->mitPaymentSessions as $session) {
            foreach ($session->paymentSessionOutcomes as $outcome) {
                if (! $latestPaymentOutcome || $outcome->occurred_at > $latestPaymentOutcome->occurred_at) {
                    $latestPaymentOutcome = $outcome;
                }
            }
        }

        // Prepare MIT payment data (avoid inline PHP in Blade)
        $hasCardToken = !empty($subscription->card_token);
        $isActive = $subscription->status === 'active';
        $mitPaymentsToday = $subscription->mitPaymentSessions()
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get(); // Show all payments today

        return view('admin.judopay-subscribe', [
            'subscription' => $subscription,
            'customer' => $customer,
            'serviceData' => $serviceData,
            'latestPaymentOutcome' => $latestPaymentOutcome,
            'citAccesses' => $subscription->citAccesses()->orderBy('created_at', 'desc')->get(),
            // MIT data
            'hasCardToken' => $hasCardToken,
            'isActive' => $isActive,
            'mitPaymentsToday' => $mitPaymentsToday,
        ]);
    }

    /**
     * Create CIT session for payment setup
     * POST /admin/judopay/create-cit-session
     */
    public function createCitSession(Request $request)
    {
        // Store form data in session for consent form
        $consentData = [
            'subscription_id' => $request->input('subscription_id'),
            'consumer_reference' => $request->input('consumer_reference'),
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_mobile' => $request->input('customer_mobile'),
            'card_holder_name' => $request->input('card_holder_name'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'postcode' => $request->input('postcode'),
            'amount' => $request->input('amount'),
            'order_reference' => 'CIT-SETUP-'.$request->input('subscription_id'),
            'description' => 'Admin Payment Setup',
            'source' => 'admin',
        ];

        // Store consent data in session
        session(['cit_consent_data' => $consentData]);

        // Redirect to subscription page
        return redirect()->route('page.judopay.subscribe', $request->input('subscription_id'));
    }

    /**
     * Send SMS verification code (for authorization link flow)
     * POST /payment/authorize/{customer_id}/{passcode}/{subscription_id}/send-verification-code
     */
    public function sendAuthorizationSms(Request $request, $customer_id, $passcode, $subscription_id)
    {
        try {
            \Log::channel('judopay')->info('sendAuthorizationSms called', [
                'customer_id' => $customer_id,
                'passcode' => $passcode,
                'subscription_id' => $subscription_id,
                'request_data' => $request->all(),
            ]);

            // Validate authorization access first
            $validation = JudopayAuthorizationHelper::validateAccess($customer_id, $passcode, $subscription_id);

            \Log::channel('judopay')->info('Authorization access validation result', [
                'valid' => $validation['valid'],
                'message' => $validation['message'] ?? 'Unknown',
            ]);

            if (! $validation['valid']) {
                return redirect()->back()->with('error', $validation['message']);
            }

            $access = $validation['access'];
            $data = JudopayAuthorizationHelper::getAuthorizationData($access);
            $customer = $data['customer'];

            // Get admin form data (preferred) or fallback to customer data
            $adminFormData = $access->admin_form_data;

            if ($adminFormData) {
                // Use admin-entered data for SMS/email
                $phoneNumber = $adminFormData['customer_mobile'];
                $emailAddress = $adminFormData['customer_email'];
                $customerName = $adminFormData['customer_name'];

                \Log::channel('judopay')->info('Using admin form data for SMS', [
                    'customer_id' => $customer->id,
                    'admin_phone' => $phoneNumber,
                    'admin_email' => $emailAddress,
                    'admin_name' => $customerName,
                ]);
            } else {
                // Fallback to customer database data
                $phoneNumber = $customer->phone;
                $emailAddress = $customer->email;
                $customerName = $customer->first_name.' '.$customer->last_name;

                \Log::channel('judopay')->info('Using customer database data for SMS', [
                    'customer_id' => $customer->id,
                    'phone' => $phoneNumber,
                    'email' => $emailAddress,
                    'name' => $customerName,
                ]);
            }

            // Track SMS code request
            $access->increment('sms_request_count');
            $access->update(['sms_requested_at' => now()]);

            \Log::channel('judopay')->info('Customer requested SMS verification code', [
                'access_id' => $access->id,
                'customer_id' => $customer->id,
                'subscription_id' => $access->subscription_id,
                'request_count' => $access->sms_request_count,
                'requested_at' => now(),
            ]);

            // Send SMS verification code (SMS only, no email for consent form)
            $result = JudopaySmsHelper::sendVerificationCode(
                $phoneNumber,
                'consent',
                $emailAddress,
                $customerName,
                'recurring payment authorization',
                false // Don't send email for consent form
            );

            \Log::channel('judopay')->info('SMS sending result', [
                'success' => $result['success'],
                'message' => $result['message'] ?? 'Unknown',
                'result_data' => $result,
            ]);

            if ($result['success']) {
                // Append SMS SID to array for audit trail
                $currentSids = $access->sms_sids ?? [];
                $currentSids[] = $result['sid'] ?? null;
                $access->update(['sms_sids' => $currentSids]);
                return redirect()->back()->with('sms_sent', 'Verification code sent successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to send SMS: '.$result['message']);
            }

        } catch (\Exception $e) {
            \Log::channel('judopay')->error('Failed to send authorization SMS', [
                'customer_id' => $customer_id,
                'subscription_id' => $subscription_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error. Please try again.');
        }
    }

    /**
     * Process authorization consent and create CIT session
     * POST /payment/authorize/{customer_id}/{passcode}/{subscription_id}/verify-code
     */
    public function processAuthorizationConsent(Request $request, $customer_id, $passcode, $subscription_id)
    {
        try {
            // Validate authorization access first
            $validation = JudopayAuthorizationHelper::validateAccess($customer_id, $passcode, $subscription_id);

            if (! $validation['valid']) {
                return redirect()->back()->with('error', $validation['message']);
            }

            // Validate consent form data
            $request->validate([
                'consent_checkbox' => 'required',
                'sms_code' => 'required|string|size:6',
            ]);

            // Verify SMS code
            $verificationResult = JudopaySmsHelper::verifyCode($request->input('sms_code'));
            if (! $verificationResult['valid']) {
                return redirect()->back()->withErrors(['sms_verification' => $verificationResult['message']]);
            }

            // Extract SMS SID for audit trail
            $smsSid = $verificationResult['sms_sid'] ?? null;

            $access = $validation['access'];
            $data = JudopayAuthorizationHelper::getAuthorizationData($access);
            $customer = $data['customer'];

            $subscription = $access->subscription;

            // Retrieve consent version and hash from session (with fallback)
            $consentVersion = session('consent_version', 'v1.0-judopay-cit');
            $consentHash = session('consent_hash', null);

            // Ensure subscribable is loaded to resolve placeholders
            $subscription->loadMissing('subscribable');

            // Derive consumer reference via helper (handles placeholders and fallbacks)
            $consumerReference = JudopayReference::buildConsumerReference(
                $subscription->subscribable_type,
                $subscription->subscribable,
                $subscription->consumer_reference
            );

            // Generate payment reference using standard format
            $paymentRef = JudopayService::generatePaymentReference('cit', $consumerReference);

            // Get stored admin form data (preferred) or fallback to customer data
            $adminFormData = $access->admin_form_data;

            if ($adminFormData) {
                // Use admin-entered form data
                $formCustomerName = $adminFormData['customer_name'];
                $formCustomerEmail = $adminFormData['customer_email'];
                $formCustomerMobile = $adminFormData['customer_mobile'];
                $formCardHolderName = $adminFormData['card_holder_name'];
                $formAddress1 = $adminFormData['address1'];
                $formAddress2 = $adminFormData['address2'];
                $formCity = $adminFormData['city'];
                $formPostcode = $adminFormData['postcode'];
                $formAmount = $adminFormData['amount'];

                \Log::channel('judopay')->info('Using admin form data for CIT session', [
                    'subscription_id' => $subscription_id,
                    'admin_data' => $adminFormData,
                ]);
            } else {
                // Fallback to customer database data
                $formCustomerName = $customer->first_name.' '.$customer->last_name;
                $formCustomerEmail = $customer->email;
                $formCustomerMobile = $customer->phone;
                $formCardHolderName = $customer->first_name.' '.$customer->last_name;
                $formAddress1 = $customer->address;
                $formAddress2 = $customer->city;
                $formCity = $customer->city;
                $formPostcode = $customer->postcode;
                $formAmount = '0.00'; // Default amount for consent form

                \Log::channel('judopay')->warning('No admin form data found, using customer database data', [
                    'subscription_id' => $subscription_id,
                ]);
            }

            // Create CIT session request with admin form data (fallback to customer data)
            $citRequest = new Request;
            $citRequest->merge([
                'subscription_id' => $subscription_id,
                'consumer_reference' => $subscription->consumer_reference,
                'customer_email' => $formCustomerEmail,
                'customer_mobile' => $formCustomerMobile,
                'customer_name' => $formCustomerName,
                'card_holder_name' => $formCardHolderName,
                'address1' => $formAddress1,
                'address2' => $formAddress2, // Admin-entered or customer city fallback
                'city' => $formCity,
                'postcode' => $formPostcode,
                'amount' => $formAmount,
                'judopay_payment_reference' => $paymentRef,
                'order_reference' => 'CIT-'.$subscription->consumer_reference,
                'description' => 'Customer Payment Authorization',
                'source' => 'consent_form', // Indicate this is from consent form
                // Consent tracking data
                'consent_given_at' => now(),
                'consent_ip_address' => $request->ip(),
                'consent_terms_version' => $consentVersion,
                'sms_verification_sid' => $smsSid,
                'sms_verified_at' => now(),
                'consent_content_sha256' => $consentHash,
            ]);

            // Create CIT session and redirect to JudoPay
            $response = JudopayCit::initialiseCitSession($citRequest);

            if ($response instanceof \Illuminate\Http\JsonResponse) {
                $responseData = $response->getData(true);

                if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data']['paylink_url'])) {

                    // Retire the internal authorization link after successful consent
                    $access->update([
                        'expires_at' => now()->subMinute(), // Expire immediately
                    ]);

                    // Redact PII data for GDPR compliance
                    $access->redactPiiData();

                    \Log::channel('judopay')->info('Internal authorization link retired after consent', [
                        'access_id' => $access->id,
                        'customer_id' => $customer_id,
                        'subscription_id' => $subscription_id,
                        'retired_at' => now(),
                    ]);

                    return redirect($responseData['data']['paylink_url']);
                } else {
                    return redirect()->back()->with('error', 'Failed to create payment link: '.($responseData['message'] ?? 'Unknown error'));
                }
            }

            return $response;

        } catch (\Exception $e) {
            \Log::channel('judopay')->error('Failed to process authorization consent', [
                'customer_id' => $customer_id,
                'subscription_id' => $subscription_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error. Please try again.');
        }
    }

    /**
     * Generate JudoPay authorization access link
     * POST /admin/judopay/generate-authorization-access
     */
    public function generateAuthorizationAccess(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
            'expires_in_hours' => 'nullable|integer|min:1|max:168', // Max 7 days
        ]);

        $subscriptionId = $request->input('subscription_id');

        // Check if there's already an active CIT session for this subscription (excluding expired ones)
        $activeSession = JudopayCitPaymentSession::where('subscription_id', $subscriptionId)
            ->where('is_active', true)
            ->whereIn('status', ['created', 'success'])
            ->where('expiry_date', '>', now()) // Only consider non-expired sessions
            ->first();

        // Auto-expire any expired sessions
        $expiredSessions = JudopayCitPaymentSession::where('subscription_id', $subscriptionId)
            ->where('is_active', true)
            ->whereIn('status', ['created'])
            ->where('expiry_date', '<=', now())
            ->get();

        foreach ($expiredSessions as $expiredSession) {
            $expiredSession->update([
                'status' => 'expired',
                'is_active' => false,
                'failure_reason' => 'Session expired automatically',
            ]);

            \Log::channel('judopay')->info('Auto-expired CIT session', [
                'session_id' => $expiredSession->id,
                'subscription_id' => $subscriptionId,
                'expired_at' => now(),
            ]);
        }

        if ($activeSession) {
            \Log::channel('judopay')->warning('Attempted to create authorization link with existing active CIT session', [
                'subscription_id' => $subscriptionId,
                'existing_session_id' => $activeSession->id,
                'existing_status' => $activeSession->status,
                'created_at' => $activeSession->created_at,
            ]);

            return redirect()->route('page.judopay.subscribe', $subscriptionId)
                ->with('active_session_warning', [
                    'session_id' => $activeSession->id,
                    'status' => $activeSession->status,
                    'created_at' => $activeSession->created_at->format('d/m/Y H:i:s'),
                ]);
        }

        // Store admin form data for use in consent form
        $adminFormData = [
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'customer_mobile' => $request->input('customer_mobile'),
            'card_holder_name' => $request->input('card_holder_name'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'postcode' => $request->input('postcode'),
            'amount' => $request->input('amount'),
            'subscription_id' => $subscriptionId,
        ];

        $result = JudopayAuthorizationHelper::generateAuthorizationLink(
            $request->input('customer_id'),
            $subscriptionId,
            $request->input('expires_in_hours', 24),
            $adminFormData
        );

        if ($result['success']) {
            return redirect()->route('page.judopay.subscribe', $request->input('subscription_id'))
                ->with([
                    'link_generated' => true,
                    'generated_link' => $result['url'],
                    'expires_at' => $result['access']->expires_at->format('d/m/Y, H:i:s'),
                    'customer_email' => $adminFormData['customer_email'],
                    'customer_phone' => $adminFormData['customer_mobile'],
                ]);
        } else {
            return redirect()->route('page.judopay.subscribe', $request->input('subscription_id'))
                ->with('error', $result['message']);
        }
    }

    /**
     * Show JudoPay authorization form via access link
     * GET /judopay/consent-form/{customer_id}/{passcode}/{subscription_id}
     */
    public function showAuthorizationForm(Request $request, $customer_id, $passcode, $subscription_id)
    {
        $validation = JudopayAuthorizationHelper::validateAccess($customer_id, $passcode, $subscription_id);

        if (! $validation['valid']) {
            abort(403, $validation['message']);
        }

        try {
            $access = $validation['access'];
            $data = JudopayAuthorizationHelper::getAuthorizationData($access);

            // Track that customer accessed the authorization link
            if (! $access->last_accessed_at) {
                // Only track first access (not subsequent page refreshes)
                $access->update([
                    'last_accessed_at' => now(),
                    'access_ip_address' => $request->ip(),
                ]);

                \Log::channel('judopay')->info('Customer accessed authorization link', [
                    'access_id' => $access->id,
                    'customer_id' => $customer_id,
                    'subscription_id' => $subscription_id,
                    'ip_address' => $request->ip(),
                    'accessed_at' => now(),
                ]);
            }

            // Get admin form data (preferred) or fallback to customer data
            $adminFormData = $access->admin_form_data;

            if ($adminFormData) {
                // Use admin-entered data for consent form display
                $customerName = $adminFormData['customer_name'];
                $customerEmail = $adminFormData['customer_email'];
                $customerPhone = $adminFormData['customer_mobile'];
                $address1 = $adminFormData['address1'];
                $address2 = $adminFormData['address2'];
                $city = $adminFormData['city'];
                $postcode = $adminFormData['postcode'];

                \Log::channel('judopay')->info('Using admin form data for consent form display', [
                    'customer_id' => $customer_id,
                    'admin_data' => $adminFormData,
                ]);
            } else {
                // Fallback to customer database data
                $customerName = $data['customer']->first_name.' '.$data['customer']->last_name;
                $customerEmail = $data['customer']->email;
                $customerPhone = $data['customer']->phone;
                $address1 = $data['customer']->address;
                $address2 = $data['customer']->city;
                $city = $data['customer']->city;
                $postcode = $data['customer']->postcode;

                \Log::channel('judopay')->info('Using customer database data for consent form display', [
                    'customer_id' => $customer_id,
                ]);
            }

            try {
                // Determine version to show
                $subscription = $access->subscription;

                // For now, always use current version (can be modified for grandfathering)
                $version = config('judopay.consent.current_version', 'v1.0-judopay-cit');

                // Alternative: Keep existing customer's version (uncomment to enable grandfathering)
                // if ($subscription && $subscription->hasActiveConsent()) {
                //     $version = $subscription->getActiveConsentVersion();
                // }

                // Get version configuration
                $versions = config('judopay.consent.versions', []);
                $versionConfig = $versions[$version] ?? null;

                if (!$versionConfig) {
                    // Fallback to v1.0 if config missing
                    $version = 'v1.0-judopay-cit';
                    $versionConfig = [
                        'blade_file' => 'judopay-authorisation-concent-form-v1',
                        'effective_date' => '2025-10-07',
                        'hash' => null,
                    ];
                }

                $bladeFile = $versionConfig['blade_file'] ?? 'judopay-authorisation-concent-form-v1';
                $consentHash = $versionConfig['hash'] ?? null;

                // Store in session for verification
                session([
                    'consent_version' => $version,
                    'consent_hash' => $consentHash,
                ]);

                // Render the appropriate blade file
                return view($bladeFile, [
                    'customer' => $data['customer'],
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'customer_address' => $address1 . ', ' . $city,
                    'customer_city' => $city,
                    'customer_postcode' => $postcode,
                    'subscription_id' => $subscription_id,
                    'customer_id' => $customer_id,
                    'passcode' => $passcode,
                    'access' => $access,
                    'service_data' => $data['service_data'],
                    'user_name' => $data['user_name'],
                    'motorbike' => $data['motorbike'],
                    'today' => now()->format('d/m/Y'),
                    'link_expires_at' => $access->expires_at,
                    'consent_version' => $version,
                    'consent_effective_date' => $versionConfig['effective_date'] ?? '7 October 2025',
                ]);

            } catch (\Exception $e) {
                \Log::channel('judopay')->error('Failed to determine consent version, using default', [
                    'error' => $e->getMessage(),
                ]);

                // Ultimate fallback - use v1 blade with minimal data
                return view('judopay-authorisation-concent-form-v1', [
                    'customer' => $data['customer'],
                    'customer_name' => $customerName,
                    'customer_email' => $customerEmail,
                    'customer_phone' => $customerPhone,
                    'customer_address' => $address1 . ', ' . $city,
                    'customer_city' => $city,
                    'customer_postcode' => $postcode,
                    'subscription_id' => $subscription_id,
                    'customer_id' => $customer_id,
                    'passcode' => $passcode,
                    'access' => $access,
                    'service_data' => $data['service_data'],
                    'user_name' => $data['user_name'],
                    'motorbike' => $data['motorbike'],
                    'today' => now()->format('d/m/Y'),
                    'link_expires_at' => $access->expires_at,
                    'consent_version' => 'v1.0-judopay-cit',
                    'consent_effective_date' => '7 October 2025',
                ]);
            }

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to display authorization form', [
                'customer_id' => $customer_id,
                'subscription_id' => $subscription_id,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Failed to load authorization form');
        }
    }

    /**
     * Kill previous authorization links for a subscription
     * POST /ngn-admin/judopay/kill-previous-links
     */
    public function killPreviousLinks(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
            ]);

            $subscriptionId = $request->input('subscription_id');

            // Cancel previous CIT payment sessions
            $cancelledSessions = JudopayCitPaymentSession::where('subscription_id', $subscriptionId)
                ->where('is_active', true)
                ->where('status', 'created')
                ->update([
                    'status' => 'cancelled',
                    'is_active' => false,
                    'failure_reason' => 'Cancelled by admin - replaced with new authorization link',
                ]);

            // Cancel previous authorization access links
            $cancelledAccesses = JudopayCitAccess::where('subscription_id', $subscriptionId)
                ->where('expires_at', '>', now())
                ->delete();

            $totalCancelled = $cancelledSessions + $cancelledAccesses;

            Log::channel('judopay')->info('Killed previous authorization links', [
                'subscription_id' => $subscriptionId,
                'cancelled_sessions' => $cancelledSessions,
                'cancelled_accesses' => $cancelledAccesses,
                'total_cancelled' => $totalCancelled,
            ]);

            return redirect()->route('page.judopay.subscribe', $subscriptionId)
                ->with([
                    'links_killed' => true,
                    'killed_count' => $totalCancelled,
                ]);

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to kill previous authorization links', [
                'subscription_id' => $request->input('subscription_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('page.judopay.subscribe', $subscriptionId)
                ->with('error', 'Failed to kill previous links: '.$e->getMessage());
        }
    }

    /**
     * Send authorization link via email manually
     * POST /admin/judopay/send-authorization-email
     */
    public function sendAuthorizationEmail(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|integer|exists:customers,id',
                'authorization_link' => 'required|url',
            ]);

            $customer = Customer::findOrFail($request->input('customer_id'));
            $authorizationLink = $request->input('authorization_link');

            // Extract passcode and subscription_id from the authorization link
            $linkParts = explode('/', $authorizationLink);
            $passcode = $linkParts[count($linkParts) - 2] ?? null;
            $subscriptionId = $linkParts[count($linkParts) - 1] ?? null;

            // Get admin form data from the authorization access
            $adminFormData = null;
            if ($passcode && $subscriptionId) {
                $access = JudopayCitAccess::where('passcode', $passcode)
                    ->where('subscription_id', $subscriptionId)
                    ->where('customer_id', $customer->id)
                    ->first();

                if ($access && $access->admin_form_data) {
                    $adminFormData = $access->admin_form_data;
                }
            }

            // Use admin form data (preferred) or fallback to customer data
            if ($adminFormData) {
                $emailAddress = $adminFormData['customer_email'];
                $customerName = $adminFormData['customer_name'];

                \Log::channel('judopay')->info('Using admin form data for email', [
                    'customer_id' => $customer->id,
                    'admin_email' => $emailAddress,
                    'admin_name' => $customerName,
                ]);
            } else {
                $emailAddress = $customer->email;
                $customerName = $customer->first_name.' '.$customer->last_name;

                \Log::channel('judopay')->info('Using customer database data for email', [
                    'customer_id' => $customer->id,
                    'email' => $emailAddress,
                    'name' => $customerName,
                ]);
            }

            // Send email notification with authorization link
            $result = JudopaySmsHelper::sendAuthorizationLink(
                $authorizationLink,
                $emailAddress,
                $customerName,
                'recurring payment authorization',
                '24 hours' // Default expiry
            );

            \Log::channel('judopay')->info('Manual authorization email sent', [
                'customer_id' => $customer->id,
                'customer_email' => $emailAddress,
                'authorization_link' => $authorizationLink,
                'success' => $result['success'],
            ]);

            if ($result['success']) {
                return redirect()->back()->with([
                    'email_sent' => true,
                    'sent_email_address' => $emailAddress,
                ]);
            } else {
                return redirect()->back()->with('error', 'Failed to send email: '.$result['message']);
            }

        } catch (\Exception $e) {
            \Log::channel('judopay')->error('Failed to send authorization email manually', [
                'customer_id' => $request->input('customer_id'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'System error: '.$e->getMessage());
        }
    }

    /**
     * Update billing frequency and day for finance subscriptions
     * POST /admin/judopay/update-billing-day
     */
    public function updateBillingDay(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
                'billing_frequency' => 'required|string|in:weekly,monthly',
                'billing_day' => 'required_if:billing_frequency,monthly|nullable|integer|in:1,15,28',
            ]);

            $subscription = JudopaySubscription::findOrFail($request->input('subscription_id'));

            // Validate that subscription is finance type only
            if ($subscription->subscribable_type !== FinanceApplication::class) {
                return redirect()->route('page.judopay.subscribe', $subscription->id)
                    ->with('error', 'Billing settings can only be modified for finance subscriptions.');
            }

            // Store old values for logging
            $oldBillingFrequency = $subscription->billing_frequency;
            $oldBillingDay = $subscription->billing_day;

            // Update billing frequency
            $subscription->billing_frequency = $request->input('billing_frequency');

            // Update billing day based on frequency
            if ($request->input('billing_frequency') === 'weekly') {
                // Weekly finance is always Saturday (6)
                $subscription->billing_day = 6;
            } else {
                // Monthly - use the selected day
                $subscription->billing_day = $request->input('billing_day');
            }

            $subscription->save();

            // Check if changes were made
            $frequencyChanged = $oldBillingFrequency !== $subscription->billing_frequency;
            $dayChanged = $oldBillingDay !== $subscription->billing_day;

            // Send email notification if any changes were made
            if ($frequencyChanged || $dayChanged) {
                try {
                    // Get staff user details
                    $staffUser = backpack_user();
                    $staffUserId = $staffUser ? $staffUser->id : null;
                    $staffName = $staffUser ? trim(($staffUser->first_name ?? '') . ' ' . ($staffUser->last_name ?? '')) : 'Unknown';

                    // Load subscription relationships
                    $subscription->load('subscribable.customer');
                    $subscribable = $subscription->subscribable;
                    $customer = $subscribable ? $subscribable->customer : null;

                    // Determine service type
                    $serviceType = $subscription->subscribable_type === FinanceApplication::class ? 'Finance' : 'Rental';

                    // Prepare email data
                    $emailData = [
                        'subscription_id' => $subscription->id,
                        'old_billing_frequency' => $oldBillingFrequency,
                        'new_billing_frequency' => $subscription->billing_frequency,
                        'old_billing_day' => $oldBillingDay,
                        'new_billing_day' => $subscription->billing_day,
                        'old_subscription_amount' => $subscription->amount,
                        'new_subscription_amount' => $subscription->amount,
                        'amount_changed' => false,
                        'staff_user_id' => $staffUserId,
                        'staff_name' => $staffName,
                        'service_type' => $serviceType,
                        'subscription_amount' => $subscription->amount,
                        'customer_id' => $customer ? $customer->id : 'N/A',
                        'customer_name' => $customer ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) : 'N/A',
                        'customer_email' => $customer ? $customer->email : null,
                        'customer_phone' => $customer ? $customer->phone : null,
                        'change_date_time' => Carbon::now()->format('d/m/Y H:i:s'),
                    ];

                    // Send email to customer service
                    Mail::to('customerservice@neguinhomotors.co.uk')
                        ->send(new BillingChangeNotification($emailData));

                    Log::channel('judopay')->info('Billing change notification email sent', [
                        'subscription_id' => $subscription->id,
                        'staff_user_id' => $staffUserId,
                    ]);

                    // TODO: Add SMS notification to boss phone number
                    // Example structure for future implementation:
                    // $bossPhoneNumber = config('services.boss_phone_number'); // Add to config/services.php
                    // $smsMessage = "Billing change: Subscription #{$subscription->id} - {$staffName} (ID: {$staffUserId}) changed billing from {$oldBillingFrequency} to {$subscription->billing_frequency}";
                    // $smsService = app(\App\Services\SmsNotificationService::class);
                    // $smsService->sendSms($bossPhoneNumber, $smsMessage);

                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to send billing change notification email', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't fail the request if email fails
                }
            }

            Log::channel('judopay')->info('Billing settings updated for subscription', [
                'subscription_id' => $subscription->id,
                'old_billing_frequency' => $oldBillingFrequency,
                'new_billing_frequency' => $subscription->billing_frequency,
                'old_billing_day' => $oldBillingDay,
                'new_billing_day' => $subscription->billing_day,
                'updated_by' => backpack_user() ? backpack_user()->id : null,
            ]);

            // Build success message
            if ($subscription->billing_frequency === 'weekly') {
                $successMessage = 'Billing frequency updated successfully to Weekly (Saturday).';
            } else {
                $ordinal = match($subscription->billing_day) {
                    1 => 'st',
                    15 => 'th',
                    28 => 'th',
                    default => 'th'
                };
                $successMessage = 'Billing frequency updated successfully to Monthly on the '.$subscription->billing_day.$ordinal.'.';
            }

            return redirect()->route('page.judopay.subscribe', $subscription->id)
                ->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed: '.implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to update billing settings', [
                'subscription_id' => $request->input('subscription_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error: '.$e->getMessage());
        }
    }

    public function updateAmount(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $subscription = JudopaySubscription::findOrFail($request->input('subscription_id'));

            // Validate that subscription is finance type only
            if ($subscription->subscribable_type !== FinanceApplication::class) {
                return redirect()->route('page.judopay.subscribe', $subscription->id)
                    ->with('error', 'Amount can only be modified for finance subscriptions.');
            }

            // Store old value for logging
            $oldAmount = $subscription->amount;

            // Update amount
            $subscription->amount = $request->input('amount');
            $subscription->save();

            // Check if changes were made
            $amountChanged = $oldAmount != $subscription->amount;

            // Send email notification if amount changed
            if ($amountChanged) {
                try {
                    // Get staff user details
                    $staffUser = backpack_user();
                    $staffUserId = $staffUser ? $staffUser->id : null;
                    $staffName = $staffUser ? trim(($staffUser->first_name ?? '') . ' ' . ($staffUser->last_name ?? '')) : 'Unknown';

                    // Load subscription relationships
                    $subscription->load('subscribable.customer');
                    $subscribable = $subscription->subscribable;
                    $customer = $subscribable ? $subscribable->customer : null;

                    // Determine service type
                    $serviceType = $subscription->subscribable_type === FinanceApplication::class ? 'Finance' : 'Rental';

                    // Prepare email data
                    $emailData = [
                        'subscription_id' => $subscription->id,
                        'old_billing_frequency' => $subscription->billing_frequency,
                        'new_billing_frequency' => $subscription->billing_frequency,
                        'old_billing_day' => $subscription->billing_day,
                        'new_billing_day' => $subscription->billing_day,
                        'old_subscription_amount' => $oldAmount,
                        'new_subscription_amount' => $subscription->amount,
                        'amount_changed' => true,
                        'staff_user_id' => $staffUserId,
                        'staff_name' => $staffName,
                        'service_type' => $serviceType,
                        'subscription_amount' => $subscription->amount,
                        'customer_id' => $customer ? $customer->id : 'N/A',
                        'customer_name' => $customer ? trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')) : 'N/A',
                        'customer_email' => $customer ? $customer->email : null,
                        'customer_phone' => $customer ? $customer->phone : null,
                        'change_date_time' => Carbon::now()->format('d/m/Y H:i:s'),
                    ];

                    // Send email to customer service
                    Mail::to('customerservice@neguinhomotors.co.uk')
                        ->send(new BillingChangeNotification($emailData));

                    Log::channel('judopay')->info('Amount change notification email sent', [
                        'subscription_id' => $subscription->id,
                        'staff_user_id' => $staffUserId,
                        'old_amount' => $oldAmount,
                        'new_amount' => $subscription->amount,
                    ]);

                } catch (\Exception $e) {
                    Log::channel('judopay')->error('Failed to send amount change notification email', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't fail the request if email fails
                }
            }

            Log::channel('judopay')->info('Amount updated for subscription', [
                'subscription_id' => $subscription->id,
                'old_amount' => $oldAmount,
                'new_amount' => $subscription->amount,
                'updated_by' => backpack_user() ? backpack_user()->id : null,
            ]);

            $successMessage = 'Amount updated successfully from £'.number_format($oldAmount, 2).' to £'.number_format($subscription->amount, 2).'.';

            return redirect()->route('page.judopay.subscribe', $subscription->id)
                ->with('success', $successMessage);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed: '.implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to update amount', [
                'subscription_id' => $request->input('subscription_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error: '.$e->getMessage());
        }
    }

    public function closeSubscription(Request $request)
    {
        try {
            $request->validate([
                'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
            ]);

            $subscription = JudopaySubscription::findOrFail($request->input('subscription_id'));

            // Check if subscription is already closed/cancelled
            if (in_array($subscription->status, ['completed', 'cancelled', 'inactive'])) {
                return redirect()->route('page.judopay.subscribe', $subscription->id)
                    ->with('error', 'Subscription is already closed or cancelled.');
            }

            // Store old status for logging
            $oldStatus = $subscription->status;

            // Update subscription status to cancelled
            $subscription->status = 'completed';
            // $subscription->card_token = "Removed";
            $subscription->save();

            // Log the action
            Log::channel('judopay')->info('Subscription closed', [
                'subscription_id' => $subscription->id,
                'old_status' => $oldStatus,
                'new_status' => $subscription->status,
                'closed_by' => backpack_user() ? backpack_user()->id : null,
                'closed_at' => now(),
            ]);

            return redirect()->route('page.judopay.subscribe', $subscription->id)
                ->with('success', 'Subscription has been closed successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed: '.implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to close subscription', [
                'subscription_id' => $request->input('subscription_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error: '.$e->getMessage());
        }
    }


    /**
     * MIT Dashboard - dedicated MIT management page
     * GET /admin/judopay/mit-dashboard
     */
    public function mitDashboard()
    {
        // Get all subscriptions with MIT eligibility
        $subscriptions = JudopaySubscription::with([
            'judopayOnboarding.onboardable', // Customer data
            'subscribable', // Rental or Finance application
            'mitPaymentSessions' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            },
        ])
        ->where('status', 'active')
        ->whereNotNull('card_token')
        ->whereNotNull('judopay_receipt_id')
        ->get();

        // Get recent MIT payments across all subscriptions
        $recentMitPayments = \App\Models\JudopayMitPaymentSession::with([
            'subscription.judopayOnboarding.onboardable',
            'subscription.subscribable'
        ])
            ->join('judopay_subscriptions', 'judopay_mit_payment_sessions.subscription_id', '=', 'judopay_subscriptions.id')
            ->orderBy('judopay_mit_payment_sessions.created_at', 'desc')
            ->orderBy('judopay_subscriptions.consumer_reference', 'asc')
            ->select('judopay_mit_payment_sessions.*')
            ->paginate(20);

        // Eager load VRM relationships based on subscribable type to avoid N+1
        $collection = $recentMitPayments->getCollection();

        $rentingIds = $collection->filter(fn($s) => $s->subscription->subscribable_type === 'App\Models\RentingBooking')
            ->pluck('subscription.subscribable_id')->filter()->unique();

        $financeIds = $collection->filter(fn($s) => $s->subscription->subscribable_type === 'App\Models\FinanceApplication')
            ->pluck('subscription.subscribable_id')->filter()->unique();

        if ($rentingIds->isNotEmpty()) {
            $bookings = \App\Models\RentingBooking::whereIn('id', $rentingIds)
                ->with(['rentingBookingItems.motorbike:id,reg_no'])
                ->get()
                ->keyBy('id');

            $collection->each(function ($session) use ($bookings) {
                if ($session->subscription->subscribable && isset($bookings[$session->subscription->subscribable->id])) {
                    $session->subscription->setRelation('subscribable', $bookings[$session->subscription->subscribable->id]);
                }
            });
        }

        if ($financeIds->isNotEmpty()) {
            $applications = \App\Models\FinanceApplication::whereIn('id', $financeIds)
                ->with(['application_items.motorbike:id,reg_no'])
                ->get()
                ->keyBy('id');

            $collection->each(function ($session) use ($applications) {
                if ($session->subscription->subscribable && isset($applications[$session->subscription->subscribable->id])) {
                    $session->subscription->setRelation('subscribable', $applications[$session->subscription->subscribable->id]);
                }
            });
        }

        return view('admin.judopay-mit-dashboard', [
            'subscriptions' => $subscriptions,
            'recentMitPayments' => $recentMitPayments,
        ]);
    }

    /**
     * Fire MIT payment directly without using queue tables
     * POST /admin/judopay/fire-direct-mit
     */
    public function fireDirectMit(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'subscription_id' => 'required|integer|exists:judopay_subscriptions,id',
            ]);

            // Get authenticated user
            $userId = auth()->user()->id ?? null;

            if (!$userId) {
                return redirect()->back()->with('error', 'Authentication required');
            }

            $subscriptionId = $request->input('subscription_id');

            // Fire direct MIT using helper
            $result = \App\Helpers\JudopayMit::fireDirectMit($subscriptionId, $userId);

            if ($result['success']) {
                return redirect()->back()->with('success', $result['message']);
            } else {
                return redirect()->back()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            \Log::channel('judopay')->error('Failed to fire direct MIT from dashboard', [
                'subscription_id' => $request->input('subscription_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error: ' . $e->getMessage());
        }
    }

    /**
     * Add NGN MIT Queue items to live firing chamber (judopay_mit_queues)
     * Handles both single and bulk operations
     * POST /admin/judopay/add-to-queue
     */
    public function addToQueue(Request $request)
    {
        try {
            // Get authenticated user
            $userId = auth()->user()->id ?? null;

            if (!$userId) {
                return redirect()->back()->with('error', 'Authentication required');
            }

            // Detect single or bulk operation
            $isBulk = $request->has('ngn_mit_queue_ids');

            if ($isBulk) {
                // Bulk operation
                $request->validate([
                    'ngn_mit_queue_ids' => 'required|array|min:1',
                    'ngn_mit_queue_ids.*' => 'integer|exists:ngn_mit_queues,id',
                ]);

                $queueIds = $request->input('ngn_mit_queue_ids');
            } else {
                // Single operation
                $request->validate([
                    'ngn_mit_queue_id' => 'required|integer|exists:ngn_mit_queues,id',
                ]);

                $queueIds = [$request->input('ngn_mit_queue_id')];
            }

            $successCount = 0;
            $failedCount = 0;
            $errors = [];
            $successInvoices = [];

            // Process each queue item
            foreach ($queueIds as $queueId) {
                $result = \App\Helpers\JudopayMit::addToLiveChamber($queueId, $userId);

                if ($result['success']) {
                    $successCount++;
                    $successInvoices[] = $result['data']['invoice_number'] ?? "#{$queueId}";
                } else {
                    $failedCount++;
                    $errors[] = $result['message'];
                }
            }

            // Prepare response messages
            if ($successCount > 0 && $failedCount === 0) {
                // All succeeded
                $message = $isBulk
                    ? "Successfully added {$successCount} " . \Illuminate\Support\Str::plural('payment', $successCount) . " to the queue"
                    : "Payment {$successInvoices[0]} successfully added to the queue";

                Log::channel('judopay')->info('MIT Queue items added successfully', [
                    'success_count' => $successCount,
                    'is_bulk' => $isBulk,
                    'user_id' => $userId,
                ]);

                return redirect()->back()->with('success', $message);

            } elseif ($successCount > 0 && $failedCount > 0) {
                // Partial success
                $message = "Added {$successCount} " . \Illuminate\Support\Str::plural('payment', $successCount)
                         . " to queue, but {$failedCount} failed. Errors: " . implode('; ', array_slice($errors, 0, 3));

                Log::channel('judopay')->warning('MIT Queue items partially added', [
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'errors' => $errors,
                    'user_id' => $userId,
                ]);

                return redirect()->back()->with('error', $message);

            } else {
                // All failed
                $message = $isBulk
                    ? "Failed to add items to queue. Errors: " . implode('; ', array_slice($errors, 0, 3))
                    : "Failed to add payment to queue. " . ($errors[0] ?? 'Unknown error');

                Log::channel('judopay')->error('MIT Queue items failed to add', [
                    'failed_count' => $failedCount,
                    'errors' => $errors,
                    'user_id' => $userId,
                ]);

                return redirect()->back()->with('error', $message);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed: ' . implode(', ', $e->validator->errors()->all()));

        } catch (\Exception $e) {
            Log::channel('judopay')->error('Failed to process add to queue request', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'System error: ' . $e->getMessage());
        }
    }

    /**
     * Stop a live queue item - remove it from judopay_mit_queues
     * Reverses all flags and cleans up related records
     * DELETE /admin/judopay/stop-live-queue/{id}
     */
    public function stopLiveQueue(Request $request, $id)
    {
        try {
            // Get authenticated user
            $userId = auth()->user()->id ?? null;

            if (!$userId) {
                return redirect()->back()->with('error', 'Authentication required');
            }

            // Find the live queue item with relationships
            $liveQueueItem = \App\Models\JudopayMitQueue::with('ngnMitQueue')->findOrFail($id);

            // Check if it can be stopped (must not be fired and fire date must be in future)
            if (!$liveQueueItem->canBeStopped()) {
                return redirect()->back()->with('error', 'Cannot stop this payment. It has either already fired or the fire date has passed.');
            }

            // Get reference information before deleting
            $invoiceNumber = $liveQueueItem->ngnMitQueue->invoice_number ?? 'N/A';
            $paymentRef = substr($liveQueueItem->judopay_payment_reference, -8);
            $paymentReference = $liveQueueItem->judopay_payment_reference;

            // 1. Reverse NGN MIT Queue status back to 'generated' (reverse line 438 of JudopayMit.php)
            $liveQueueItem->ngnMitQueue->update([
                'status' => 'generated',
            ]);

            // 2. Cancel/mark the JudopayMitPaymentSession as cancelled (cleanup line 422-435 of JudopayMit.php)
            $mitSession = \App\Models\JudopayMitPaymentSession::where('judopay_payment_reference', $paymentReference)->first();
            if ($mitSession) {
                $mitSession->update([
                    'status' => 'cancelled',
                    'failure_reason' => "Manually stopped by user {$userId}",
                ]);
            }

            // 3. Delete the live queue item (the scheduled job will handle the missing record gracefully)
            $liveQueueItem->delete();

            \Log::channel('judopay')->info('Live MIT queue item stopped', [
                'live_queue_id' => $id,
                'ngn_mit_queue_id' => $liveQueueItem->ngnMitQueue->id,
                'invoice_number' => $invoiceNumber,
                'payment_reference' => $paymentRef,
                'status_reverted' => 'generated',
                'session_cancelled' => $mitSession ? true : false,
                'stopped_by' => $userId,
                'stopped_at' => now(),
            ]);

            return redirect()->back()->with('success', "Payment stopped successfully. Invoice: {$invoiceNumber}, Ref: {$paymentRef}");

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Live queue item not found.');

        } catch (\Exception $e) {
            \Log::channel('judopay')->error('Failed to stop live MIT queue item', [
                'live_queue_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'System error: ' . $e->getMessage());
        }
    }

    public function weeklyMitQueue(Request $request)
    {
        // Get week parameter, default to current week start (Monday)
        $weekParam = $request->get('week');
        $sortParam = $request->get('sort');

        if ($weekParam) {
            try {
                $currentWeekStart = Carbon::parse($weekParam)->startOfWeek();
            } catch (\Exception $e) {
                // Invalid date, redirect to current week
                return redirect()->route('page.judopay.weekly-mit-queue');
            }
        } else {
            $currentWeekStart = Carbon::now()->startOfWeek();
        }

        $currentWeekEnd = $currentWeekStart->copy()->endOfWeek();

        // Calculate navigation dates
        $previousWeek = $currentWeekStart->copy()->subWeek();
        $nextWeek = $currentWeekStart->copy()->addWeek();

        // Get MIT queue items for the selected week
        $queueItemsQuery = NgnMitQueue::with([
            'subscribable.judopayOnboarding.onboardable', // Customer data
            'subscribable.subscribable', // RentingBooking or FinanceApplication
        ])
        ->whereHas('subscribable') // Ensure subscription exists
        ->whereBetween('invoice_date', [$currentWeekStart->format('Y-m-d'), $currentWeekEnd->format('Y-m-d')]);

        // Apply sorting based on sort parameter (don't filter - show all items)
        if ($sortParam === 'success') {
            // Sort by SUCCESS: cleared items first, but show all items
            $queueItemsQuery->orderBy('cleared', 'desc')
                           ->orderBy('invoice_date', 'asc');
        } elseif ($sortParam === 'decline') {
            // Sort by DECLINE: declined items (sent but not cleared) first, but show all items
            $queueItemsQuery->orderByRaw("CASE WHEN status = 'sent' AND cleared = 0 THEN 0 ELSE 1 END")
                           ->orderBy('invoice_date', 'asc');
        } else {
            // Default: order by invoice date
            $queueItemsQuery->orderBy('invoice_date', 'asc');
        }

        $queueItems = $queueItemsQuery->get();

        // Load additional relationships for VRM display
        foreach ($queueItems as $item) {
            if ($item->subscribable->subscribable_type === 'App\Models\FinanceApplication') {
                $item->subscribable->subscribable->load('application_items.motorbike');
            } elseif ($item->subscribable->subscribable_type === 'App\Models\RentingBooking') {
                $item->subscribable->subscribable->load('rentingBookingItems.motorbike');
            }
        }

        // Get live queue items (judopay_mit_queues) for the same week
        $liveQueueItems = \App\Models\JudopayMitQueue::with([
            'ngnMitQueue.subscribable.judopayOnboarding.onboardable',
            'ngnMitQueue.subscribable.subscribable',
            'authorizedBy'
        ])
        ->whereHas('ngnMitQueue', function($query) use ($currentWeekStart, $currentWeekEnd) {
            $query->whereBetween('invoice_date', [$currentWeekStart->format('Y-m-d'), $currentWeekEnd->format('Y-m-d')]);
        })
        ->orderBy('mit_fire_date', 'asc')
        ->get();

        // Load additional relationships for live queue VRM display
        foreach ($liveQueueItems as $liveItem) {
            if ($liveItem->ngnMitQueue->subscribable->subscribable_type === 'App\Models\FinanceApplication') {
                $liveItem->ngnMitQueue->subscribable->subscribable->load('application_items.motorbike');
            } elseif ($liveItem->ngnMitQueue->subscribable->subscribable_type === 'App\Models\RentingBooking') {
                $liveItem->ngnMitQueue->subscribable->subscribable->load('rentingBookingItems.motorbike');
            }
        }

        // Get weekly summary using helper function
        $summary = JudopayWeeklyMitSummary::getWeeklySummary($weekParam);

        return view('admin.judopay-weekly-mit-queue', [
            'queueItems' => $queueItems,
            'liveQueueItems' => $liveQueueItems,
            'currentWeekStart' => $currentWeekStart,
            'currentWeekEnd' => $currentWeekEnd,
            'previousWeek' => $previousWeek,
            'nextWeek' => $nextWeek,
            'summary' => $summary,
            'sortParam' => $sortParam,
        ]);
    }

}
