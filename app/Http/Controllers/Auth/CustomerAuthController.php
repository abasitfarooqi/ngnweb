<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Http\Controllers\Controller;
use App\Models\ClubMember;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomerAuthController extends Controller
{
    private function normaliseEmail(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    private function normalisePhone(?string $phone): string
    {
        $normalised = preg_replace('/\s+/', '', trim((string) $phone));

        return (string) preg_replace('/^\+44/', '0', $normalised);
    }

    protected function issueApiToken(CustomerAuth $customerAuth, Request $request): string
    {
        $deviceName = (string) ($request->input('device_name') ?: $request->userAgent() ?: 'customer-api');

        return $customerAuth->createToken($deviceName)->plainTextToken;
    }

    public function register(Request $request)
    {
        try {
            // Move validation to the start and separate it for better error handling
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/',
                ],
                'phone' => 'required|string',
            ], [
                'password.min' => 'Password must be at least 8 characters long.',
                'password.regex' => 'Password must contain at least one letter and one number.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $email = $this->normaliseEmail($request->email);
            $phone = $this->normalisePhone($request->phone);
            $existingCustomerByEmail = Customer::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();
            $existingCustomerByPhone = Customer::whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])->first();
            $existingAuth = CustomerAuth::whereRaw('LOWER(TRIM(email)) = ?', [$email])->first();

            if ($existingAuth) {
                return response()->json([
                    'message' => 'Email is already registered.',
                    'errors' => ['email' => ['Email is already registered.']],
                ], 422);
            }

            if ($existingCustomerByEmail && $existingCustomerByPhone && (int) $existingCustomerByEmail->id !== (int) $existingCustomerByPhone->id) {
                return response()->json([
                    'message' => 'Email and phone belong to different customer records.',
                    'errors' => ['phone' => ['Email and phone must match the same customer record.']],
                ], 422);
            }

            $locate_customer = $existingCustomerByEmail ?: $existingCustomerByPhone;
            if ($locate_customer && $locate_customer->is_register) {
                return response()->json([
                    'message' => 'Customer already registered. Please login instead.',
                    'errors' => ['email' => ['Customer already registered.']],
                ], 422);
            }

            if ($locate_customer) {
                \Log::info('Customer found, updating is_register');
                $locate_customer->is_register = true;
                $locate_customer->email = $email;
                $locate_customer->phone = $phone;
                $locate_customer->is_club = (bool) $locate_customer->is_club;
                $locate_customer->save();
                $customer = $locate_customer;
            } else {
                \Log::info('Creating new customer');

                $request->validate([
                    'email' => 'unique:customers,email',
                ]);

                $customer = Customer::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'dob' => $request->dob ?? '2000-01-01',
                    'address' => $request->address ?? 'Not Provided',
                    'postcode' => $request->postcode ?? 'Not Provided',
                    'emergency_contact' => $request->emergency_contact ?? 'Not Provided',
                    'whatsapp' => $request->whatsapp ?? $request->phone,
                    'city' => $request->city ?? 'Not Provided',
                    'country' => $request->country ?? 'Not Provided',
                    'nationality' => $request->nationality ?? 'Not Provided',
                    'reputation_note' => null,
                    'rating' => 0,
                    'license_number' => $request->license_number ?? 'Not Provided',
                    'license_expiry_date' => $request->license_expiry_date ?? now()->addYears(1),
                    'license_issuance_authority' => $request->license_issuance_authority ?? 'Not Provided',
                    'license_issuance_date' => $request->license_issuance_date ?? now()->subYears(1),
                    'is_register' => true,
                    'is_club' => false,
                ]);
            }

            // Create authentication entry for the customer
            $customerAuth = CustomerAuth::create([
                'customer_id' => $customer->id,
                'email' => $email,
                'password' => Hash::make($request->password),
            ]);

            $customerAddress = CustomerAddress::create([
                'customer_id' => $customer->id,
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'company_name' => '-',
                'street_address' => '-',
                'street_address_plus' => '-',
                'postcode' => '-',
                'city' => '-',
                'phone_number' => '-',
                'is_default' => true,
                'type' => 'shipping',
                'country_id' => 3,
            ]);

            // Send verification email
            $customerAuth->sendEmailVerificationNotification();

            // Strict link with an existing club member by email + phone.
            $clubMember = ClubMember::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                ->whereRaw("REPLACE(REPLACE(phone, ' ', ''), '+44', '0') = ?", [$phone])
                ->first();
            if ($clubMember) {
                $clubMember->customer_id = $customer->id;
                $clubMember->save();
                $customer->is_club = true;
                $customer->save();
            }

            // Log in the customer
            Auth::guard('customer')->login($customerAuth);
            \Log::info('Customer logged in');
            $token = $this->issueApiToken($customerAuth, $request);

            return response()->json([
                'user' => $customerAuth->load('customer'),
                'token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Registration successful. Please check your email for verification.',
                'verified' => false,
                'redirect_url' => '/account',
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Registration failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Registration failed',
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }

    public function sendPortalCredentials(Request $request, int $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $email = $this->normaliseEmail($customer->email);
        $phone = $this->normalisePhone($customer->phone);

        $temporaryPassword = (string) random_int(10000000, 99999999);
        $auth = CustomerAuth::firstOrCreate(
            ['email' => $email],
            [
                'customer_id' => $customer->id,
                'password' => Hash::make($temporaryPassword),
            ]
        );

        if (! $auth->customer_id) {
            $auth->customer_id = $customer->id;
            $auth->save();
        }

        $customer->is_register = true;
        $customer->save();

        try {
            Mail::raw(
                "Welcome to NGN customer portal.\n\nLogin email: {$email}\nTemporary password: {$temporaryPassword}\nPortal: ".url('/login')."\n\nPlease change your password after login.",
                function ($message) use ($email): void {
                    $message->to($email)->subject('Your NGN Portal Access Credentials');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Failed to send portal credentials email', ['customer_id' => $customer->id, 'error' => $e->getMessage()]);
        }

        app(\App\Http\Controllers\SMSController::class)->sendSms($phone, "NGN Portal login\nEmail: {$email}\nPassword: {$temporaryPassword}\n".url('/login'));

        return back()->with('success', 'Portal credentials sent to customer email and phone.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            if (Auth::guard('customer')->attempt($request->only('email', 'password'))) {
                $user = Auth::guard('customer')->user();
                $request->session()->regenerate();
                // Broadcast the UserLoggedIn event
                event(new UserLoggedIn($user));
                $token = $this->issueApiToken($user, $request);

                return response()->json([
                    'user' => $user->load('customer'),
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'message' => 'Login successful',
                    'verified' => $user->hasVerifiedEmail(),
                    'redirect_url' => '/account',
                ]);
            }

            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => ['email' => ['The provided credentials are incorrect.']],
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Login failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Clear specific session data first
        $request->session()->forget([
            'cart',
            'checkout_state',
            'shipping_details',
            'previous_product',
            'cart_items',
        ]);

        // deliberate redudant code
        session()->forget('cart');
        session()->forget('checkout_state');
        session()->forget('shipping_details');
        session()->forget('previous_product');
        session()->forget('cart_items');

        // Perform standard logout operations
        $customer = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if ($customer?->currentAccessToken()) {
            $customer->currentAccessToken()->delete();
        }

        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Broadcast the UserLoggedOut event
        event(new UserLoggedOut);

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function getUser(Request $request)
    {
        $user = ($request->user('sanctum') ?: Auth::guard('customer')->user())->load('customer');

        return response()->json($user);
    }

    public function getUserById(Request $request, $id)
    {
        $authUser = $request->user('sanctum') ?: Auth::guard('customer')->user();
        $customer = $authUser?->customer;

        if (! $customer || (int) $customer->id !== (int) $id) {
            return response()->json([
                'message' => 'You are not allowed to access this customer record.',
            ], 403);
        }

        return response()->json($customer);
    }

    /**
     * Handle password reset request for customers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string'],
            'type' => ['required', Rule::in(['email', 'phone'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $identifier = $request->identifier;
        $type = $request->type;

        try {
            if ($type === 'email') {
                // More comprehensive email validation
                $emailValidator = Validator::make(['email' => $identifier], [
                    'email' => 'required|email|exists:customer_auths,email',
                ], [
                    'email.exists' => 'No account found with this email address.',
                ]);

                if ($emailValidator->fails()) {
                    \Log::warning('Password reset email validation failed', [
                        'email' => $identifier,
                        'errors' => $emailValidator->errors(),
                    ]);

                    return response()->json([
                        'message' => 'Invalid email',
                        'errors' => $emailValidator->errors(),
                    ], 422);
                }

                // Explicitly use the customers broker
                config(['auth.defaults.passwords' => 'customers']);

                // Send password reset link
                $status = Password::broker('customers')->sendResetLink(
                    ['email' => $identifier]
                );

                \Log::info('Password reset link request', [
                    'email' => $identifier,
                    'status' => $status,
                ]);

                return match ($status) {
                    Password::RESET_LINK_SENT => response()->json([
                        'message' => 'Password reset link sent to your email',
                    ]),
                    Password::INVALID_USER => response()->json([
                        'message' => 'Unable to find a user with that email address',
                    ], 404),
                    default => response()->json([
                        'message' => 'Unable to send password reset link',
                    ], 400)
                };
            }

            return response()->json([
                'message' => 'Invalid reset type specified',
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Handle the actual password reset for customers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Explicitly use the customers broker
            config(['auth.defaults.passwords' => 'customers']);

            // Here we will attempt to reset the user's password using the customer broker
            $status = Password::broker('customers')->reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();
                }
            );

            \Log::info('Password reset attempt', [
                'email' => $request->email,
                'status' => $status,
            ]);

            return match ($status) {
                Password::PASSWORD_RESET => response()->json([
                    'message' => 'Password has been reset successfully',
                ]),
                default => response()->json([
                    'message' => 'Unable to reset password. Please try again.',
                ], 400)
            };

        } catch (\Exception $e) {
            \Log::error('Password reset error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
