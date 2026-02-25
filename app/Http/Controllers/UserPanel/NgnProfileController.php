<?php

namespace App\Http\Controllers\UserPanel;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class NgnProfileController extends Controller
{
    public function __construct()
    {
        // Apply the 'auth:customer' middleware to ensure only authenticated customers can access these methods
        $this->middleware('auth:customer');
    }

    public function index()
    {
        // Retrieve the authenticated customer
        $customerAuth = Auth::guard('customer')->user();

        if (! $customerAuth) {
            return redirect()->route('customer.login')->with('error', 'You must be logged in to view your profile.');
        }

        // Fetch the customer details based on the customer_auth record
        $customer = $customerAuth->customer; // Assuming CustomerAuth has a 'customer' relationship

        return view('frontend.ngnstore.user_panel.profile.index', compact('customerAuth', 'customer'));
    }

    public function update(Request $request)
    {
        // Retrieve the authenticated customer
        $customerAuth = Auth::guard('customer')->user();

        if (! $customerAuth) {
            return redirect()->route('customer.login')->with('error', 'Unauthorized access.');
        }

        // Validate the incoming request
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:customers,email,'.$customerAuth->customer_id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        // Update the customer_auth email if changed
        if ($customerAuth->email !== $request->email) {
            $customerAuth->email = $request->email;
            $customerAuth->save();
        }

        // Update the customer record
        $customer = $customerAuth->customer;
        $customer->update($request->only('first_name', 'last_name', 'phone', 'email', 'address', 'city', 'country'));

        return redirect()->route('userpanel_profile')->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('frontend.ngnstore.user_panel.profile.change_password');
    }

    public function updatePassword(Request $request)
    {
        // Retrieve the authenticated customer
        $customerAuth = Auth::guard('customer')->user();

        if (! $customerAuth) {
            return redirect()->route('customer.login')->with('error', 'Unauthorized access.');
        }

        // Validate the incoming request
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Check if the current password is correct
        if (! Hash::check($request->current_password, $customerAuth->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update the password
        $customerAuth->password = Hash::make($request->new_password);
        $customerAuth->save();

        return redirect()->route('userpanel_profile')->with('success', 'Password changed successfully.');
    }
}
