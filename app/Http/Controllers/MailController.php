<?php

namespace App\Http\Controllers;

use App\Mail\AccidentManagement;
use App\Mail\BookingConfirmation;
use App\Mail\BookingInternal;
use App\Mail\ContactUs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendMail($data)
    {
        if (app()->environment('production')) {
            Mail::to('support@neguinhomotors.co.uk')
                ->send(new ContactUs($data));

            Mail::to('customerservice@neguinhomotors.co.uk')
                ->send(new ContactUs($data));
        }

        return redirect()
            ->route('thank-you');
    }

    public function AccidentManagement($request): RedirectResponse
    {
        if (app()->environment('production')) {
            Mail::to('support@neguinhomotors.co.uk')
                ->send(new AccidentManagement($request));

            Mail::to('customerservice@neguinhomotors.co.uk')
                ->send(new AccidentManagement($request));
        }

        return redirect()
            ->route('thank-you');
    }

    // New method for sending booking confirmation emails
    public function sendBookingConfirmation($booking): void
    {
        // if (app()->environment('production')) {
        // Send client-facing confirmation to the customer (if provided)
        if (! empty($booking->email)) {
            Mail::to($booking->email)
                ->send(new BookingConfirmation($booking));
        }

        // Send internal notification to NGN team
        Mail::to('customerservice@neguinhomotors.co.uk')
            ->send(new BookingInternal($booking));
        // }
    }
}
