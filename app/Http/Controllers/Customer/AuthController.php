<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        \Log::info('Customer API: Login attempt', ['email' => $request->email]);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::guard('customer')->attempt($request->only('email', 'password'))) {
            \Log::warning('Customer API: Invalid credentials', ['email' => $request->email]);

            return response()->json([
                'message' => 'Invalid credentials provided.',
            ], 401);
        }

        $customer = CustomerLogin::where('email', $request->email)->first();

        // Create token with customer abilities and expiration
        $token = $customer->createToken(
            'customer-token',
            ['customer'],
            now()->addDays(30)
        )->plainTextToken;

        \Log::info('Customer API: Login successful', [
            'customer_id' => $customer->id,
        ]);

        return response()->json([
            'message' => 'Login successful',
            'customer_id' => $customer->id,
            'full_name' => $customer->customer->first_name.' '.$customer->customer->last_name,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Ensure the user is authenticated
        $user = $request->user('customer');
        if ($user) {
            // Revoke all tokens for the user
            $user->tokens()->delete();

            // Log out the user from the session
            Auth::guard('customer')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to the login page after successful logout
            return redirect()->route('customer.login')->with('message', 'Logout successful');
        }

        return redirect()->route('customer.login')->with('error', 'User not authenticated');
    }
    // public function logout(Request $request)
    // {
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logout successful',
    //     ]);

    //     // // Ensure the user is authenticated
    //     // if ($request->user('customer')) {
    //     //     // Revoke all tokens for the user
    //     //     $request->user('customer')->tokens()->delete();
    //     // }

    //     // Auth::guard('customer')->logout();
    //     // $request->session()->invalidate();
    //     // $request->session()->regenerateToken();

    //     // return redirect()->route('customer.login');
    // }
}
