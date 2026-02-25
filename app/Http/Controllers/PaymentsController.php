<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;

class PaymentsController extends Controller
{
    public function getMethods()
    {
        $paymentMethods = PaymentMethod::where('is_enabled', true)->get();

        \Log::info('Payment methods:', $paymentMethods->toArray());

        return response()->json($paymentMethods);
    }
}
