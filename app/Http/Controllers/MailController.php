<?php

namespace App\Http\Controllers;

use App\Mail\AccidentManagement;
use App\Mail\BookingConfirmation;
use App\Mail\BookingInternal;
use App\Mail\ContactSubmission;
use App\Mail\ContactUs;
use App\Models\ServiceBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function storeMessage(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'min:7'],
            'subject' => ['required', 'string', 'min:3'],
            'message' => ['required', 'string', 'min:5'],
        ]);

        $customerAuth = auth('customer')->user();
        $customerId = $customerAuth?->customer_id;

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerId,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => $customerAuth ? 'authenticated_customer' : 'guest',
            'enquiry_type' => ServiceBooking::inferEnquiryType('Sales enquiry', $data['subject'].' '.$data['message']),
            'service_type' => 'Sales enquiry',
            'subject' => trim($data['subject']),
            'description' => trim($data['subject']).' | '.trim($data['message']),
            'requires_schedule' => false,
            'booking_date' => null,
            'booking_time' => null,
            'status' => 'Pending',
            'fullname' => trim($data['name']),
            'phone' => trim($data['phone']),
            'reg_no' => (string) $request->input('reg_no', 'N/A'),
            'email' => trim((string) ($data['email'] ?? '')) ?: null,
        ]);

        $this->sendBookingConfirmation($booking, internalUseContactSubmission: true);

        return redirect()->back()->with('success', 'Your enquiry has been sent successfully.');
    }

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
    public function sendBookingConfirmation($booking, bool $internalUseContactSubmission = false): void
    {
        if (! empty($booking->email)) {
            Mail::to($booking->email)
                ->send(new BookingConfirmation($booking));
        }

        $inbox = config('mail.contact_inbox', 'customerservice@neguinhomotors.co.uk');

        if ($internalUseContactSubmission) {
            $topic = trim((string) $booking->subject);
            $desc = (string) $booking->description;
            $messageBody = str_starts_with($desc, $topic.' | ')
                ? substr($desc, strlen($topic) + 3)
                : $desc;
            $replyEmail = trim((string) ($booking->email ?? '')) ?: (string) config('mail.from.address');

            Mail::to($inbox)->send(new ContactSubmission(
                senderName: trim((string) $booking->fullname),
                senderEmail: $replyEmail,
                phone: trim((string) $booking->phone),
                topic: $topic !== '' ? $topic : 'Website enquiry',
                messageBody: trim($messageBody) !== '' ? trim($messageBody) : $desc,
                branchName: '',
            ));

            return;
        }

        Mail::to($inbox)->send(new BookingInternal($booking));
    }
}
