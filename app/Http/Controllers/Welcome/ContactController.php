<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
use App\Models\Contact;
use App\Models\ContactQuery;
use App\Models\Motorcycle;
use App\Models\ServiceBooking;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function Contact()
    {
        return view('contacts.contact');
    }

    public function CallMeBack()
    {
        return view('contacts.contactCallBack');
    }

    public function TradeAccount()
    {
        return view('contacts.contactTradeAccount');
    }

    public function ContactNewSales($id)
    {
        $motorcycle = Motorcycle::findOrFail($id);

        return view('contacts.contactNewSales', compact('motorcycle'));
    }

    public function AccidentManagement(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'reg_no' => 'required',
            'language' => 'required',
            'vehicle_type' => 'required',
            'privacy_policy' => 'required',
        ]);

        $mail = new MailController;
        // $mail->AccidentManagement($request);

        $contact = new Contact;
        $contact->name = $request->name;
        $contact->phone = $request->phone;
        $contact->reg_no = $request->reg_no;
        $contact->email = $request->email;
        $contact->subject = 'Accident Management Request';
        $contact->message = 'Vehicle Type: '.$request->vehicle_type.' - '.'Referal: '.$request->referal;
        $contact->save();

        $notification = [
            'message' => 'Your Message Submited Successfully',
            'alert-type' => 'success',
        ];

        return to_route('road-traffic-accidents')
            ->with('success', 'Your request has been submitted');
    }

    public function StoreMessage(Request $request)
    {
        $validated = $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => [
                'required',
                'regex:/^[0-9\s\-\+\(\)]{10,20}$/',
            ],
            'subject' => 'nullable|max:255',
            'message' => 'required',
        ]);

        try {
            // Create contact record
            $contact = Contact::create($validated);

            $contactQuery = new ContactQuery;
            $contactQuery->name = $request->name;
            $contactQuery->email = $request->email;
            $contactQuery->subject = $request->subject;
            $contactQuery->phone = $request->phone;
            $contactQuery->message = $request->message;
            $contactQuery->save();

            // Create data object for email
            $emailData = (object) array_merge($validated, ['id' => $contactQuery->id]);

            // Send email with object instead of string
            $mail = new MailController;
            $mail->sendMail($emailData);

            return redirect()
                ->route('thank-you')
                ->with('success', 'Your message has been submitted successfully.');
        } catch (\Exception $e) {
            \Log::error('Contact form error: '.$e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred. Please try again.']);
        }
    }

    public function ContactSales(Request $request)
    {
        // dd($request->message);
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required',
        ]);

        $contact = new Contact;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->subject = $request->subject;
        $contact->phone = $request->phone;
        $contact->message = $request->message;
        $contact->save();

        $mail = new MailController;
        // $mail->sendMail($request->name);

        // Contact::insert([
        // 'name' => $request->name,
        // 'email' => $request->email,
        // 'subject' => $request->subject,
        // 'phone' => $request->phone,
        // 'message' => $request->message,
        // 'created_at' => Carbon::now(),
        // ]);

        $notification = [
            'message' => 'Your Message Submited Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    public function ContactMessage()
    {
        $contacts = Contact::latest()->get();

        return view('admin.contact.allcontact', compact('contacts'));
    }

    public function DeleteMessage($id)
    {
        Contact::findOrFail($id)->delete();

        $notification = [
            'message' => 'Your Message Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }

    public function ThankYou()
    {
        return view('contacts.thank-you');
    }

    public function showBookingForm()
    {
        return view('frontend.service-enquiry-form');
    }

    public function handleBookingForm(Request $request)
    {
        $this->validate($request, [
            'service_type' => 'required',
            'fullname' => 'required',
            'phone' => 'required',
            'reg_no' => 'required',
            'email' => 'nullable|email',
            'booking_date' => 'nullable|date',
            'booking_time' => 'nullable|date_format:H:i',
            'description' => 'nullable',
        ]);

        $booking = ServiceBooking::create($request->all());

        // Use MailController to send emails
        $mailController = new MailController;
        $mailController->sendBookingConfirmation($booking);

        return redirect()->route('thank-you')->with('success', 'Your booking has been submitted successfully.');
    }

    public function handleEnquiryFormVue(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email',
                'fullname' => 'required',
                'phone' => 'required',
                'reg_no' => 'required',
                'description' => 'nullable',
                'booking_date' => 'nullable|date',
                'booking_time' => 'nullable|date_format:H:i',
                'cookie_policy' => 'required',
                // Add other validation rules as needed
            ]);

            $booking = ServiceBooking::create($request->all());

            // Send booking confirmation email
            $mailController = new MailController;
            $mailController->sendBookingConfirmation($booking);

            return response()->json([
                'success' => true,
                'message' => 'Your booking has been submitted successfully.',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error handling enquiry form:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit your booking. Please try again later.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Your existing contact form logic
            return redirect('/thank-you');
        } catch (ThrottleRequestsException $exception) {
            return back()
                ->withInput()
                ->withErrors(['message' => 'Too many attempts. Please try again later.']);
        }
    }
}
