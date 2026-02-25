<?php

namespace App\Http\Controllers\UserPanel;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NgnPaymentMethodController extends Controller
{
    public function __construct()
    {
        // Apply the 'auth:customer' middleware to ensure only authenticated customers can access these methods
        $this->middleware('auth:customer');
    }

    public function index()
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        // Fetch payment methods related to the authenticated customer
        $paymentMethods = PaymentMethod::where('customer_id', $customer->customer_id)->get();

        return view('frontend.ngnstore.user_panel.payment_methods.index', compact('paymentMethods'));
    }

    public function manage(Request $request, $id = null)
    {
        // Retrieve the authenticated customer
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'card_number' => 'required|string|max:16',
            'expiry_date' => 'required|string|regex:/^(0[1-9]|1[0-2])\/\d{2}$/',
            'cvv' => 'required|string|size:3',
        ]);

        if ($id) {
            // Update existing payment method
            $paymentMethod = PaymentMethod::where('id', $id)
                ->where('customer_id', $customer->customer_id)
                ->firstOrFail();

            $paymentMethod->update($validated);

            return redirect()->route('userpanel_payment_methods')->with('success', 'Payment method updated successfully.');
        } else {
            // Create new payment method
            PaymentMethod::create([
                'customer_id' => $customer->customer_id,
                'card_number' => $validated['card_number'],
                'expiry_date' => $validated['expiry_date'],
                'cvv' => $validated['cvv'],
            ]);

            return redirect()->route('userpanel_payment_methods')->with('success', 'Payment method added successfully.');
        }
    }
}
