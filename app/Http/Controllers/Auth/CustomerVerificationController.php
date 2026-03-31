<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Ecommerce\CustomerRegisterMailer;
use App\Models\CustomerAuth;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CustomerVerificationController extends Controller
{
    public function sendVerificationEmail(Request $request)
    {
        if ($request->user('customer')->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        $request->user('customer')->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent'], 200);
    }

    public function verify(Request $request): RedirectResponse
    {
        $customer = CustomerAuth::find($request->route('id'));

        if (! $customer) {
            return redirect()->route('login')->with('error', 'Invalid user');
        }

        if (! URL::hasValidSignature($request)) {
            return redirect()->route('account.security')->with('error', 'Invalid verification link or link has expired');
        }

        if (! hash_equals(
            (string) $request->route('hash'),
            sha1($customer->getEmailForVerification())
        )) {
            return redirect()->route('account.security')->with('error', 'Invalid verification link');
        }

        if ($customer->hasVerifiedEmail()) {
            Auth::guard('customer')->login($customer);

            return redirect()->route('account.security', ['verified' => 1])->with('success', 'Your email is already verified.');
        }

        if ($customer->markEmailAsVerified()) {
            event(new Verified($customer));
            Auth::guard('customer')->login($customer);

            // Send welcome email after successful verification
            try {
                $customerData = $customer->customer;
                Mail::to($customer->email)->send(new CustomerRegisterMailer([
                    'customer' => [
                        'first_name' => $customerData->first_name,
                        'last_name' => $customerData->last_name,
                        'email' => $customer->email,
                    ],
                ]));
            } catch (\Exception $e) {
                \Log::error('Welcome email sending failed', [
                    'error' => $e->getMessage(),
                    'email' => $customer->email,
                ]);
            }
        }

        return redirect()->route('account.security', ['verified' => 1])->with('success', 'Your email has been verified successfully.');
    }
}
