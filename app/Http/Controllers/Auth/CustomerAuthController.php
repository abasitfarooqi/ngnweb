<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomerAuthController extends Controller
{
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

            $existingCustomer = Customer::where('email', $request->email)->first();
            $existingAuth = CustomerAuth::where('email', $request->email)->first();

            if ($existingCustomer && $existingCustomer->is_register) {
                return response()->json([
                    'message' => 'Email is already registered.',
                    'errors' => ['email' => ['Email is already registered.']],
                ], 422);
            }

            if ($existingAuth) {
                return response()->json([
                    'message' => 'Email is already registered.',
                    'errors' => ['email' => ['Email is already registered.']],
                ], 422);
            }

            // Locate an unregistered customer with the same email
            $locate_customer = Customer::where('email', $request->email)
                ->where('is_register', false)
                ->first();

            if ($locate_customer) {
                \Log::info('Customer found, updating is_register');
                $locate_customer->is_register = true;
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
                    'email' => $request->email,
                    'phone' => $request->phone,
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
                ]);
            }

            // Create authentication entry for the customer
            $customerAuth = CustomerAuth::create([
                'customer_id' => $customer->id,
                'email' => $request->email,
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

            // Log in the customer
            Auth::guard('customer')->login($customerAuth);
            \Log::info('Customer logged in');

            return response()->json([
                'user' => $customerAuth->load('customer'),
                'message' => 'Registration successful. Please check your email for verification.',
                'verified' => false,
                'redirect_url' => '/accountinformation',
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Registration failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Registration failed',
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
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

                return response()->json([
                    'user' => $user->load('customer'),
                    'message' => 'Login successful',
                    'verified' => $user->hasVerifiedEmail(),
                    'redirect_url' => '/accountinformation',
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
        $user = Auth::guard('customer')->user()->load('customer');

        return response()->json($user);
    }

    public function getUserById($id)
    {
        $user = Customer::findOrFail($id);

        return response()->json($user);
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
