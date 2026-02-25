<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('stripe');
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripeHireDeposit()
    {
        $stripePay = 20;

        return view('stripe.reserve', compact('stripePay'));
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePost(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        Charge::create([
            'amount' => 100 * 100,
            'currency' => 'gbp',
            'source' => $request->stripeToken,
            'description' => 'Test payment from NeguinhoMotors.co.uk',
        ]);

        Session::flash('success', 'Payment successful!');

        return back();
    }

    /** MOTORCYCLE RENTAL RESERVATION VIEW
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripeReserve($motorcycle_id)
    {
        $motorcycle = Motorcycle::where('id', $motorcycle_id)->first();
        $stripePay = 20;

        return view('stripe.reserve', compact('stripePay'));
    }

    /** MOTOTCYCLE RENTAL RESERVATION PROCESSOR
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function rentalReserve(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        Charge::create([
            'amount' => 20 * 100,
            'currency' => 'gbp',
            'source' => $request->stripeToken,
            'description' => 'NGN Motorcycle Reservation',
        ]);

        Session::flash('success', 'Payment successful!');

        return back();
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripePostAddress(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $customer = Customer::create([
            'address' => [
                'line1' => 'Virani Chowk',
                'postal_code' => '390008',
                'city' => 'Vadodara',
                'state' => 'GJ',
                'country' => 'IN',
            ],
            'email' => 'demo@gmail.com',
            'name' => 'Nitin Pujari',
            'source' => $request->stripeToken,
        ]);
        Charge::create([
            'amount' => 100 * 100,
            'currency' => 'gbp',
            'customer' => $customer->id,
            'description' => 'Test payment from NeguinhoMotors.co.uk',
            'shipping' => [
                'name' => 'Jenny Rosen',
                'address' => [
                    'line1' => '510 Townsend St',
                    'postal_code' => '98140',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'US',
                ],
            ],
        ]);
        Session::flash('success', 'Payment successful!');

        return back();
    }
}
