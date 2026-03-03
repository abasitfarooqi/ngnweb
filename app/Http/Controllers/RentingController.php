<?php

namespace App\Http\Controllers;

use App\Mail\OtherChargesReceipt;
use App\Mail\RentalAgreement;
use App\Mail\RentalPaymentReceipt;
use App\Models\AgreementAccess;
use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\BookingIssuanceItem;
use App\Models\Customer;
use App\Models\DocumentType;
use App\Models\Motorbike;
use App\Models\MotorbikeMaintenanceLog;
use App\Models\PaymentMethod;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingOtherCharge;
use App\Models\RentingOtherChargesTransaction;
use App\Models\RentingPricing;
use App\Models\RentingServiceVideo;
use App\Models\RentingTransaction;
use App\Models\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\QrCodeGenerator;

class RentingController extends Controller
{
    // not active yet must be get active and remove from AgreementController
    public function generateAgreementAccess($customer_id)
    {
        $booking_id = request()->query('booking_id');

        $passcode = Str::random(12);
        $expiresAt = now()->addHours(1);

        $access = AgreementAccess::create([
            'customer_id' => $customer_id,
            'booking_id' => $booking_id,
            'passcode' => $passcode,
            'expires_at' => $expiresAt,
        ]);

        $url = route('agreement.show', ['customer_id' => $customer_id, 'passcode' => $passcode]);

        if ($access) {

            $qrBase64 = QrCodeGenerator::dataUrl($url, 200);

            return response()->json([
                'qrImage' => $qrBase64,
                'url' => $url,
            ]);
        }
    }

    // 5.0 - additional charges
    // addOtherCharges
    public function addOtherCharges(Request $request)
    {


        $validatedData = $request->validate([
            'booking_id' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
        ]);

        $otherCharge = RentingOtherCharge::create([
            'booking_id' => $request->booking_id,
            'description' => $request->description,
            'amount' => $request->amount,
            'is_paid' => false,
        ]);

        return response()->json([
            'other_charge' => $otherCharge,
        ]);
    }

    // 5.1 - additional charges
    public function getOtherCharges(Request $request)
    {
        $booking_id = $request->route('bookingId');

        \Log::info('Booking ID: '.$booking_id);

        $otherCharges = RentingOtherCharge::where('booking_id', $booking_id)->get();

        return response()->json([
            'other_charges' => $otherCharges,
        ]);
    }

    // // 5.2 - additional charges get paid
    // public function payOtherCharges(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    // 

    //         $validatedData = $request->validate([
    //             'charges_id' => 'required',
    //         ]);

    //         $id = $request->charges_id;

    //         $otherCharge = RentingOtherCharge::findOrFail($id);

    //         \Log::info('Other Charge: ', ['otherCharge' => $otherCharge]);

    //         \Log::info('Other Charge ID: ' . $id);

    //         $otherCharge->is_paid = true;
    //         $otherCharge->save();

    //         $transactionType = TransactionType::where('type', 'Damage Fee')->first();

    //         $paymentmethods = PaymentMethod::where('title', 'Cash')->first();

    //         $transaction = new RentingOtherChargesTransaction([
    //             'transaction_date' => now(),
    //             'charges_id' => $otherCharge->id,
    //             'transaction_type_id' => $transactionType->id,
    //             'payment_method_id' => $paymentmethods->id,
    //             'amount' => $otherCharge->amount,
    //             'user_id' => auth()->id(),
    //             'notes' => 'Other charge paid'
    //         ]);

    //         $transaction->save();

    //         DB::commit();

    //         if (DB::commit()) {

    //         }

    //         return response()->json([
    //             'transaction' => $transaction
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         \Log::error($e);
    //         return response()->json([
    //             'error' => 'An error occurred while processing the request.'
    //         ], 500);
    //     }
    // }

    public function payOtherCharges(Request $request)
    {
        DB::beginTransaction();

        try {
    

            $validatedData = $request->validate([
                'charges_id' => 'required',
            ]);

            $id = $request->charges_id;

            $otherCharge = RentingOtherCharge::findOrFail($id);
            \Log::info('Other Charge: ', ['otherCharge' => $otherCharge]);

            $otherCharge->is_paid = true;
            $otherCharge->save();

            $transactionType = TransactionType::where('type', 'Damage Fee')->firstOrFail();
            $paymentmethods = PaymentMethod::where('title', 'Cash')->firstOrFail();

            $transaction = new RentingOtherChargesTransaction([
                'transaction_date' => now(),
                'charges_id' => $otherCharge->id,
                'transaction_type_id' => $transactionType->id,
                'payment_method_id' => $paymentmethods->id,
                'amount' => $otherCharge->amount,
                'user_id' => auth()->id(),
                'notes' => 'Other charge paid',
            ]);

            $transaction->save();

            DB::commit();

            // NEW EMAIL NOTIFICATION THAT FOR CHARGE ADDITIONAL AMOUNT
            $Booking = $otherCharge->booking()->firstOrFail();
            $Customer = $Booking->customer()->firstOrFail();

            $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
            $data['title'] = 'Rental Other Charges Receipt';
            $data['body'] = 'Find your payment details:';
            $data['customer_name'] = $Customer->first_name.' '.$Customer->last_name;
            $data['booking_id'] = $Booking->id;
            $data['charges_id'] = $otherCharge->id;
            $data['charges_description'] = $otherCharge->description;
            $data['charges_date'] = now();
            $data['transaction_date'] = now();
            $data['amount'] = $otherCharge->amount;

            try {
                Mail::to($data['email'])->send(new OtherChargesReceipt($data));
            } catch (Exception $e) {
                Log::error('Failed to send email: '.$e->getMessage());
            }
            // NEW EMAIL NOTIFICATION THAT FOR CHARGE ADDITIONAL AMOUNT

            return response()->json([
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e);

            return response()->json([
                'error' => 'An error occurred while processing the request.',
            ], 500);
        }
    }

    // BI.id BOOKING_ID, BI.invoice_date INVOICE_DATE,
    // BI.amount INVOICE_AMOUNT,
    // BI.paid_date PAID_DATE, BI.state INV_STATE,
    // BI.deposit DEPOSIT, BI.is_posted IS_POSTED, BI.is_paid  IS_PAID,
    // RT.transaction_date TRANSACTION_DATE, RT.id TRANSACTION_NO, RT.amount PAID_AMOUNT,
    // RT.user_id RECEIVED_BY,
    // RT.created_at TRANSACTION_DATETIME,
    // RT.updated_at TRANSACTION_DATETIME_UPDATE
    // - getInvoices //
    public function getInvoices(Request $request)
    {
        $bookingId = $request->route('bookingId');

        $invoices = DB::table('booking_invoices as BI')
            ->leftJoin('renting_transactions as RT', 'RT.invoice_id', '=', 'BI.id')
            ->leftJoin('users as U', 'RT.user_id', '=', 'U.id')
            ->leftJoin('renting_bookings as RB', 'RB.id', '=', 'BI.booking_id')
            ->leftJoin('customers as C', 'C.id', '=', 'RB.customer_id')
            ->leftJoin('renting_booking_items as RBI', 'RBI.booking_id', '=', 'RB.id')
            ->leftJoin('motorbikes as M', 'M.id', '=', 'RBI.motorbike_id')
            ->select(
                'BI.id as INVOICE_ID',
                'BI.invoice_date as INVOICE_DATE',
                'BI.amount as INVOICE_AMOUNT',
                'BI.paid_date as PAID_DATE',
                'BI.state as INV_STATE',
                'BI.deposit as DEPOSIT',
                'BI.is_posted as IS_POSTED',
                'BI.is_paid as IS_PAID',
                'BI.is_whatsapp_sent as IS_WHATSAPP_SENT',
                'BI.whatsapp_last_reminder_sent_at as WHATSAPP_LAST_REMINDER_SENT_AT',
                'RT.transaction_date as TRANSACTION_DATE',
                'RT.id as TRANSACTION_NO',
                'RT.amount as PAID_AMOUNT',
                'RT.user_id as RECEIVED_BY',
                'RT.created_at as TRANSACTION_DATETIME',
                'RT.updated_at as TRANSACTION_DATETIME_UPDATE',
                'U.first_name as FIRST_NAME',
                DB::raw("CONCAT(C.first_name, ' ', C.last_name) AS CUSTOMER_NAME"),
                'C.whatsapp as CUSTOMER_WHATSAPP',
                'C.phone as CUSTOMER_PHONE',
                'M.reg_no as MOTORBIKE_REG_NO',
                'RBI.weekly_rent as WEEKLY_RENT'
            )
            ->where('BI.is_posted', 1)
            ->where('BI.amount', '>', 0)
            ->where('BI.booking_id', $bookingId)
            ->get();

        return response()->json([
            'invoices' => $invoices,
        ]);
    }

    public function getInvoiceDetails(Request $request, $invoiceId)
    {
        $invoice = DB::table('booking_invoices as BI')
            ->leftJoin('renting_bookings as RB', 'RB.id', '=', 'BI.booking_id')
            ->leftJoin('customers as C', 'C.id', '=', 'RB.customer_id')
            ->leftJoin('renting_booking_items as RBI', 'RBI.booking_id', '=', 'RB.id')
            ->leftJoin('motorbikes as M', 'M.id', '=', 'RBI.motorbike_id')
            ->select(
                'BI.id as invoice_id',
                'BI.invoice_date',
                'BI.amount',
                'BI.is_paid',
                'BI.is_whatsapp_sent',
                'BI.whatsapp_last_reminder_sent_at',
                DB::raw("CONCAT(C.first_name, ' ', C.last_name) AS customer_name"),
                'C.whatsapp as customer_whatsapp',
                'C.phone as customer_phone',
                'M.reg_no as motorbike_reg_no',
                'RBI.weekly_rent as weekly_rent'
            )
            ->where('BI.id', $invoiceId)
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
            ], 404);
        }

        // Format phone number for WhatsApp
        $number = $invoice->customer_whatsapp ?: $invoice->customer_phone;
        $number = preg_replace('/\s+|^0/', '', $number);
        $number = preg_replace('/^(\+44)+/', '', $number);
        $number = preg_replace('/^44/', '', $number);
        $number = '+44'.$number;
        $number = preg_replace('/\s+/', '', $number);

        // Generate WhatsApp message
        $invoiceDate = \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y');
        $message = "Dear {$invoice->customer_name}, this is a reminder regarding your Weekly Rental payment for motorbike {$invoice->motorbike_reg_no}. The outstanding amount of £".number_format($invoice->weekly_rent, 2)." is due on {$invoiceDate}. Please ensure payment is made as soon as possible to avoid late fees. If you have already paid, please contact us immediately at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors.";

        $whatsappUrl = "https://wa.me/{$number}?text=".urlencode($message);

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->invoice_id,
                'invoice_date' => $invoice->invoice_date,
                'amount' => $invoice->amount,
                'is_paid' => $invoice->is_paid,
                'is_whatsapp_sent' => $invoice->is_whatsapp_sent,
                'whatsapp_last_reminder_sent_at' => $invoice->whatsapp_last_reminder_sent_at,
                'customer_name' => $invoice->customer_name,
                'customer_phone' => $invoice->customer_phone,
                'customer_whatsapp' => $invoice->customer_whatsapp,
                'motorbike_reg_no' => $invoice->motorbike_reg_no,
                'weekly_rent' => $invoice->weekly_rent,
                'whatsapp_number' => $number,
                'whatsapp_url' => $whatsappUrl,
                'whatsapp_message' => $message,
            ],
        ]);
    }

    public function sendInvoiceWhatsappReminder(Request $request, $invoiceId)
    {
        \Log::info('sendInvoiceWhatsappReminder called', [
            'invoice_id' => $invoiceId,
            'request_method' => $request->method(),
            'request_data' => $request->all(),
            'csrf_token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing',
        ]);

        try {
            $invoice = BookingInvoice::findOrFail($invoiceId);
            \Log::info('Invoice found', [
                'invoice_id' => $invoice->id,
                'booking_id' => $invoice->booking_id,
                'current_is_whatsapp_sent' => $invoice->is_whatsapp_sent,
                'current_whatsapp_last_reminder_sent_at' => $invoice->whatsapp_last_reminder_sent_at,
            ]);

            $invoice->is_whatsapp_sent = true;
            $invoice->whatsapp_last_reminder_sent_at = now();
            $invoice->save();

            \Log::info('Invoice updated successfully', [
                'invoice_id' => $invoice->id,
                'new_is_whatsapp_sent' => $invoice->is_whatsapp_sent,
                'new_whatsapp_last_reminder_sent_at' => $invoice->whatsapp_last_reminder_sent_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp reminder marked as sent.',
                'whatsapp_last_reminder_sent_at' => $invoice->whatsapp_last_reminder_sent_at,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in sendInvoiceWhatsappReminder', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateInvoiceDateById(Request $request, $invoiceId)
    {
        $request->validate([
            'invoice_date' => 'required|date',
        ]);

        $invoice = BookingInvoice::findOrFail($invoiceId);
        $invoice->invoice_date = $request->invoice_date;
        $invoice->save();

        return response()->json([
            'success' => true,
            'message' => 'Invoice date updated successfully.',
            'invoice_date' => $invoice->invoice_date,
        ]);
    }

    public function docConfirm(Request $request)
    {
        \Log::info('Document Confirmed.');
        $bookingId = $request->booking_id;
        \Log::info('Booking ID: '.$bookingId);

        $booking = RentingBooking::where('id', $bookingId)->first();
        \Log::info('Booking: '.$booking);
        $booking->save();
        if ($booking->state == 'Awaiting Documents & Payment') {
            $booking->state = 'Awaiting Payment';
            $booking->save();
            // }else if ($booking->state == 'Awaiting Payment') {
            //     $booking->state = 'Completed';
            //     $booking->save();
        } elseif ($booking->state == 'Awaiting Documents') {
            $booking->state = 'Completed';
            $booking->save();
        }

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'status' => 'success',
            'message' => 'Booking updated successfully',
        ]);
    }

    // 7 - CLOSING THE BOOKING
    // 7.1 - Notice Period
    public function noticePeriod(Request $request)
    {
        $noticeDetails = $request->input('noticeDetails');
        $isChecked = $request->input('isChecked');

        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);

        \Log::info('Incoming request data:', $request->all());

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id',
                'noticeDetails' => 'nullable|string',
                'isChecked' => 'required|boolean',
            ]);

            $bookingClosing = BookingClosing::updateOrCreate(
                ['booking_id' => $validatedData['booking_id']],
                [
                    'notice_details' => $validatedData['noticeDetails'],
                    'notice_checked' => $validatedData['isChecked'],
                ]
            );

            return response()->json(['success' => true, 'data' => $bookingClosing]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            \Log::error('Validation errors:', $e->errors());

            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 7.2 - Collect Motorbike
    public function collectMotorbike(Request $request)
    {
        // Convert isChecked to a boolean value
        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);

        \Log::info('Incoming request data:', $request->all());

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id',
                'booking_item_id' => 'required|exists:renting_booking_items,id',
                'collectDetails' => 'nullable|string',
                'collectDate' => 'nullable|date',
                'collectTime' => 'nullable|date_format:H:i',
                'isChecked' => 'required|boolean',
            ]);

            $bookingClosing = BookingClosing::updateOrCreate(
                ['booking_id' => $validatedData['booking_id']],
                [
                    'collect_details' => $validatedData['collectDetails'],
                    'collect_date' => $validatedData['collectDate'],
                    'collect_time' => $validatedData['collectTime'],
                    'collect_checked' => $validatedData['isChecked'],
                ]
            );

            //    protected $table = 'renting_booking_items';
            // protected $fillable = [
            //     'booking_id',
            //     'motorbike_id',
            //     'user_id',
            //     'weekly_rent',
            //     'start_date',
            //     'due_date',
            //     'end_date',
            //     'is_posted',
            // ];

            // where booking_id = $validatedData['booking_id']
            $bookingItem = RentingBookingItem::where('booking_id', $validatedData['booking_id'])
                ->where('id', $validatedData['booking_item_id'])
                ->first();

            $bookingItem->end_date = now();
            $bookingItem->updated_at = now();

            $bookingItem->save();

            $booking = RentingBooking::where('id', $validatedData['booking_id'])->first();
            $booking->updated_at = now();
            $booking->save();

            return response()->json(['success' => true, 'data' => $bookingClosing]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation errors:', $e->errors());

            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 7.3 - Damages Cost
    public function damagesCost(Request $request)
    {
        // Convert isChecked to a boolean value
        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);

        \Log::info('Incoming request data:', $request->all());

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id',
                'isChecked' => 'required|boolean',
            ]);

            $bookingClosing = BookingClosing::updateOrCreate(
                ['booking_id' => $validatedData['booking_id']],
                ['damages_checked' => $validatedData['isChecked']]
            );

            return response()->json(['success' => true, 'data' => $bookingClosing]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation errors:', $e->errors());

            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 7.4 - PCN Pendings
    public function pcnPendings(Request $request)
    {
        // Convert isChecked to a boolean value
        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);

        \Log::info('Incoming request data:', $request->all());

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id', // Ensure booking_id exists in renting_bookings table
                'isChecked' => 'required|boolean',
            ]);

            $bookingClosing = BookingClosing::updateOrCreate(
                ['booking_id' => $validatedData['booking_id']],
                ['pcn_checked' => $validatedData['isChecked']]
            );

            return response()->json(['success' => true, 'data' => $bookingClosing]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation errors:', $e->errors());

            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 7.5 - Pending Rent
    public function pendingRent(Request $request)
    {
        // Convert isChecked to a boolean value
        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);

        \Log::info('Incoming request data:', $request->all());

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id',
                'isChecked' => 'required|boolean',
            ]);

            $pendingRent = 0;

            if ($pendingRent > 0) {
                return response()->json(['success' => false, 'message' => 'Pending rent must be zero before proceeding.'], 422);
            }

            $bookingClosing = BookingClosing::updateOrCreate(
                ['booking_id' => $validatedData['booking_id']],
                ['pending_checked' => $validatedData['isChecked']]
            );

            return response()->json(['success' => true, 'data' => $bookingClosing]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation errors:', $e->errors());

            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 7.6 - Deposit Return
    public function depositReturn(Request $request)
    {
        // Convert isChecked to a boolean value
        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);

        \Log::info('Incoming request data:', $request->all());

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id',
                'isChecked' => 'required|boolean',
            ]);

            $bookingClosing = BookingClosing::updateOrCreate(
                ['booking_id' => $validatedData['booking_id']],
                ['deposit_checked' => $validatedData['isChecked']]
            );

            return response()->json(['success' => true, 'data' => $bookingClosing]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation errors:', $e->errors());

            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // 7 - CLOSING THE BOOKING
    public function getClosingStatus($bookingId)
    {
        $closingStatus = BookingClosing::where('booking_id', $bookingId)->first();

        if (! $closingStatus) {
            return response()->json([
                'message' => 'success',
                'data' => 'no data found',
            ]);
        } else {
            return response()->json([
                'notice_details' => $closingStatus->notice_details,
                'notice_checked' => $closingStatus->notice_checked,
                'collect_details' => $closingStatus->collect_details,
                'collect_date' => $closingStatus->collect_date,
                'collect_time' => $closingStatus->collect_time,
                'collect_checked' => $closingStatus->collect_checked,
                'damages_checked' => $closingStatus->damages_checked,
                'pcn_checked' => $closingStatus->pcn_checked,
                'pending_checked' => $closingStatus->pending_checked,
                'deposit_checked' => $closingStatus->deposit_checked,
            ]);
        }
    }

    // 7.3 - GET ADDITIONAL CHARGES
    public function getAdditionalCosts(Request $request)
    {
        $bookingId = $request->route('bookingId');

        $otherCharges = RentingOtherCharge::where('booking_id', $bookingId)->get();

        \Log::info('Other Charges: '.$otherCharges);

        $totalAmount = $otherCharges->sum('amount');

        $paidAmount = $otherCharges->where('is_paid', 'Yes')->sum('amount');

        \Log::info('XXTotal Amount: '.$totalAmount);
        \Log::info('XXPaid Amount: '.$paidAmount);

        return response()->json([
            'other_charges' => $otherCharges,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
        ]);
    }

    // 7.3 - GET DEPOSIT
    public function getDepositAmount(Request $request)
    {
        $bookingId = $request->route('bookingId');

        $booking = RentingBooking::findOrFail($bookingId);

        return response()->json([
            'deposit' => $booking->deposit,
        ]);
    }

    // 7.4 - GET PCN PENDING
    public function getPcnPending(Request $request)
    {
        $id = $request->route('booking_item_id');
        $bookingItemId = RentingBookingItem::findOrFail($id);
        \Log::info('Booking Item ID: '.$bookingItemId);

        $pcnCases = PcnCase::where('motorbike_id', $bookingItemId->motorbike_id)->count();
        \Log::info('IID: '.$bookingItemId->id);
        \Log::info('PCN Case1: '.$pcnCases);

        if ($pcnCases > 0) {
            \Log::info('PCN FOUND: '.$pcnCases);

            // Building the unpaid query
            $unpaidQueryBuilder = DB::table('renting_booking_items')
                ->join('pcn_cases', 'pcn_cases.motorbike_id', '=', 'renting_booking_items.motorbike_id')
                ->join('renting_bookings', 'renting_bookings.id', '=', 'renting_booking_items.booking_id')
                ->select(DB::raw('SUM(pcn_cases.reduced_amount) AS REDUCE_AMOUNT'))
                ->where('renting_booking_items.id', $bookingItemId->id)
                ->whereDate('pcn_cases.date_of_contravention', '>=', DB::raw('renting_bookings.start_date'));
            // ->where('pcn_cases.isClosed', 0);  // Only include cases that are not closed

            \Log::info('Unpaid Query SQL: '.$unpaidQueryBuilder->toSql());
            \Log::info('Unpaid Query Bindings: '.json_encode($unpaidQueryBuilder->getBindings()));

            $unpaidQuery = $unpaidQueryBuilder->first();
            $unpaidAmount = $unpaidQuery->REDUCE_AMOUNT ?? 0;

            $paidQueryBuilder = DB::table('renting_booking_items')
                ->join('pcn_cases', 'pcn_cases.motorbike_id', '=', 'renting_booking_items.motorbike_id')
                ->join('renting_bookings', 'renting_bookings.id', '=', 'renting_booking_items.booking_id')
                ->select(DB::raw('SUM(pcn_cases.reduced_amount) AS REDUCE_AMOUNT'))
                ->whereDate('pcn_cases.date_of_contravention', '>=', DB::raw('renting_bookings.start_date'))
                ->where('renting_booking_items.id', $bookingItemId->id)
                ->where('pcn_cases.isClosed', 1);

            \Log::info('Paid Query SQL: '.$paidQueryBuilder->toSql());
            \Log::info('Paid Query Bindings: '.json_encode($paidQueryBuilder->getBindings()));

            $paidQuery = $paidQueryBuilder->first();
            $paidAmount = $paidQuery->REDUCE_AMOUNT ?? 0;

            \Log::info('PCN Case Sum: Unpaid - '.$unpaidAmount.', Paid - '.$paidAmount);

            return response()->json([
                'success' => true,
                'pcn_pending' => $unpaidAmount,
                'paid_amount' => $paidAmount,
            ]);
        } else {
            \Log::info('NO PCN FOUND');

            return response()->json([
                'success' => true,
                'pcn_pending' => 0,
                'paid_amount' => 0,
            ]);
        }
    }

    public function renting_bookings()
    {
        $bookingDetails = DB::table('renting_bookings as RB')
            ->join('renting_booking_items as RBI', 'RB.id', '=', 'RBI.booking_id')
            ->join('customers as C', 'RB.customer_id', '=', 'C.id')
            ->join('motorbikes as MB', 'RBI.motorbike_id', '=', 'MB.id')
            ->select(
                'RB.id as BOOKING_ID',
                'C.id as CUSTOMER_ID',
                // 'BI.id as INVOICE_ID',
                'RB.start_date as BOOKING_Date',
                'RB.due_date as NEXT_DUE_DATE',
                'RB.state as RBSTATE',
                'RB.is_posted as RB_POSTED',
                'RB.deposit as DEPOSIT',
                'RBI.motorbike_id as MBID',
                'RBI.id as BOOKING_ITEM_ID',
                'RBI.end_date as END_DATE',
                'MB.reg_no as REG_NO',
                'RBI.weekly_rent as WEEKLY_RENT',
                'C.first_name as FIRST_NAME',
                'C.last_name as LAST_NAME',
                'C.phone as PHONE',
                'C.email as EMAIL',
                'C.id as CUSTOMER_ID',
                // 'BI.amount as TOTAL_AMOUNT',
                // 'BI.is_paid as IS_PAID'
            )
            ->where('RB.state', '!=', 'DRAFT')
            ->whereNull('RBI.end_date')
            ->get();

        return view('olders.admin.renting.bookings', compact('bookingDetails'));
    }

    /**
     * Alias for route admin.renting.bookings.
     */
    public function bookings()
    {
        return $this->renting_bookings();
    }

    /**
     * Show a single booking (route admin.renting.bookings.show).
     */
    public function showBooking($booking)
    {
        $booking = RentingBooking::findOrFail($booking);
        return redirect()->route('admin.renting.bookings')->with('info', 'Booking #'.$booking->id);
    }

    public function inactive_renting_bookings()
    {
        $bookingDetails = DB::table('renting_bookings as RB')
            ->join('renting_booking_items as RBI', 'RB.id', '=', 'RBI.booking_id')
            ->join('customers as C', 'RB.customer_id', '=', 'C.id')
            ->join('motorbikes as MB', 'RBI.motorbike_id', '=', 'MB.id')
            ->select(
                'RB.id as BOOKING_ID',
                'C.id as CUSTOMER_ID',
                // 'BI.id as INVOICE_ID',
                'RB.start_date as BOOKING_Date',
                'RB.due_date as NEXT_DUE_DATE',
                'RB.state as RBSTATE',
                'RB.is_posted as RB_POSTED',
                'RB.deposit as DEPOSIT',
                'RBI.motorbike_id as MBID',
                'RBI.id as BOOKING_ITEM_ID',
                'RBI.end_date as END_DATE',
                'MB.reg_no as REG_NO',
                'RBI.weekly_rent as WEEKLY_RENT',
                'C.first_name as FIRST_NAME',
                'C.last_name as LAST_NAME',
                'C.phone as PHONE',
                'C.email as EMAIL',
                'C.id as CUSTOMER_ID',
                // 'BI.amount as TOTAL_AMOUNT',
                // 'BI.is_paid as IS_PAID'
            )
            ->where('RB.state', '!=', 'DRAFT')
            ->whereNotNull('RBI.end_date')
            ->get();

        return view('olders.admin.renting.inactive-bookings', compact('bookingDetails'));
    }

    public function all_renting_bookings(Request $request)
    {
        // Define the base query
        $query = DB::table('renting_bookings as RB')
            ->join('renting_booking_items as RBI', 'RB.id', '=', 'RBI.booking_id')
            ->join('customers as C', 'RB.customer_id', '=', 'C.id')
            ->join('motorbikes as MB', 'RBI.motorbike_id', '=', 'MB.id')
            ->select(
                'RB.id as BOOKING_ID',
                'C.id as CUSTOMER_ID',
                'RB.start_date as BOOKING_DATE',
                'RB.due_date as NEXT_DUE_DATE',
                'RB.state as RBSTATE',
                'RBI.motorbike_id as MBID',
                'RBI.id as BOOKING_ITEM_ID',
                'RBI.end_date as END_DATE',
                'MB.reg_no as REG_NO',
                'RBI.weekly_rent as WEEKLY_RENT',
                'C.first_name as FIRST_NAME',
                'C.last_name as LAST_NAME',
                'C.phone as PHONE',
                'C.email as EMAIL'
            );

        // Apply filters
        if ($request->filled('customer_id')) {
            $query->where('C.id', $request->customer_id);
        }
        if ($request->filled('motorbike_id')) {
            $query->where('RBI.motorbike_id', $request->motorbike_id);
        }
        if ($request->filled('westatus')) {
            if ($request->westatus === 'ONGOING') {
                // Check if end_date is null, blank, or 'N/A'
                $query->where(function ($q) {
                    $q->whereNull('RBI.end_date')
                        ->orWhere('RBI.end_date', '')
                        ->orWhere('RBI.end_date', 'N/A');
                });
            } elseif ($request->westatus === 'ENDED') {
                // Query for ended bookings (valid dates only)
                $query->whereNotNull('RBI.end_date')
                    ->where('RBI.end_date', '<>', 'N/A') // Exclude 'N/A'
                    ->where('RBI.end_date', '<>', ''); // Exclude empty strings
            } elseif ($request->westatus === 'N/A') {
                // Check if end_date is specifically 'N/A'
                $query->where('RBI.end_date', 'N/A');
            }
        }

        // New filter for booking state
        if ($request->filled('state')) {
            $query->where('RB.state', $request->state);
        }

        // Date filters
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('RB.start_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('RB.start_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('RB.start_date', '<=', $request->end_date);
        }

        $bookingHistory = $query->get();

        // Handle AJAX request for dynamic updates
        if ($request->ajax()) {
            return response()->json($bookingHistory);
        }

        // Fetch filter data
        $customers = DB::table('customers')->select('id', 'first_name', 'last_name')->get();
        $motorbikes = DB::table('motorbikes')->select('id', 'reg_no')->get();

        return view('olders.admin.renting.bookings_history', compact('bookingHistory', 'customers', 'motorbikes'));
    }

    public function renting_index()
    {
        $motorbikes = Motorbike::all();

        return view('olders.admin.renting.index', compact('motorbikes'));
    }

    // Single Motorbike ONLY
    public function getCustomer($bookingId)
    {
        \Log::info('Booking ID: '.$bookingId);
        $booking = RentingBooking::findOrFail($bookingId);
        $RentingBookingItem = RentingBookingItem::where('booking_id', $bookingId)->first();

        \Log::info('Booking: '.$RentingBookingItem);

        if (! $RentingBookingItem || ! $RentingBookingItem->motorbike_id) {
            \Log::error('RentingBookingItem or items not found');

            return response()->json(['error' => 'RentingBookingItem or items not found'], 404);
        }

        $customer = Customer::findOrFail($booking->customer_id);
        $motorbike = Motorbike::findOrFail($RentingBookingItem->motorbike_id);

        \Log::info('Customer: '.$motorbike);

        return response()->json([
            'customer_id' => $customer->id,
            'motorbike_id' => $motorbike->id,
            'booking_id' => $booking->id,
        ]);
    }

    // 1.0.3, 1.0.4 - Motorbike issuance, or Re-Issuance
    public function issueMotorbike(Request $request, $bookingId)
    {
        // Start the database transaction
        DB::beginTransaction();

        $reg_no = $request->reg_no;

        \Log::info('Booking ID: '.$bookingId);

        \Log::info('Reg No: '.$reg_no);

        try {
            // Validate and update the booking status
            $booking = RentingBooking::findOrFail($bookingId);
            $booking->state = 'Completed & Issued';
            $booking->notes = 'Issued on '.now()->toDateTimeString();

            $booking->is_posted = true;
            $booking->save();

            // Create a new issuance record
            $issuance = new BookingIssuanceItem([
                'booking_item_id' => $request->booking_item_id,
                'issued_by_user_id' => auth()->id(),
                'notes' => $request->notes,
                'is_insured' => $request->is_insured === 'true' ? 1 : 0,
                'current_mileage' => $request->current_mileage,
                'is_video_recorded' => $request->is_video_recorded === 'true' ? 1 : 0,
                'accessories_checked' => $request->accessories_checked === 'true' ? 1 : 0,
                'issuance_branch' => $request->issuance_branch,
            ]);
            $issuance->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Booking issued successfully',
                'booking' => $booking,
                'issuance' => $issuance,
            ]);
        } catch (Exception $e) {
            \Log::error('Transaction rolled back due to error: '.$e->getMessage());
            \Log::error($e->getTraceAsString());
            DB::rollback();

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // /admin/renting/bookings/create - First Time invoked when creating a new booking
    public function createBooking(Request $request)
    {
        DB::beginTransaction();
        \Log::info('Transaction started');


        if (empty($request->amount)) {
            \Log::error('Amount is null or empty');

            return response()->json(['error' => 'Weekly Rent, Deposit Details not found. Double Check the Weekly Price of Selected Vehicle in Pricing Section.'], 400);
        }

        if (empty($request->customer_id)) {
            \Log::error('Customer ID is null or empty');

            return response()->json(['error' => 'Customer ID is null or empty'], 400);
        }

        if (empty($request->motorbike_id)) {
            \Log::error('Motorbike ID is null or empty');

            return response()->json(['error' => 'Motorbike ID is null or empty'], 400);
        }

        try {
            $userId = auth()->id();
            \Log::info('User ID: '.$userId);

            $deposit = $request->input('deposit');

            // 'renting_bookings'
            $booking = RentingBooking::create([
                'customer_id' => $request->input('customer_id'),
                'user_id' => $userId,
                'deposit' => $deposit,
                'start_date' => now(),
                'due_date' => now()->addWeek(),
                'state' => 'DRAFT',
                'is_posted' => false,
            ]);

            \Log::info('Booking created: '.$booking->id);

            $motorbikeId = $request->input('motorbike_id');

            $weekly_amount = $request->input('weekly');

            // 'renting_booking_items'
            $bookingItem = RentingBookingItem::create([
                'booking_id' => $booking->id,
                'motorbike_id' => $motorbikeId,
                'user_id' => $userId,
                'weekly_rent' => $weekly_amount, // this is the weekly rent
                'start_date' => $booking->start_date,
                'due_date' => $booking->due_date,
                'is_posted' => false,
            ]);

            \Log::info('Booking item created: '.$bookingItem->id);

            // 'booking_invoices'
            $invoice = BookingInvoice::create([
                'booking_id' => $booking->id,
                'user_id' => $userId,
                'invoice_date' => now(),
                'amount' => $deposit + $weekly_amount,
                'deposit' => $deposit,
                'is_posted' => false,
                'is_paid' => false,
                'notes' => 'Initial draft invoice',
            ]);
            \Log::info('Invoice created: '.$invoice->id);

            DB::commit();
            \Log::info('Transaction committed');

            // Return booking id
            return response()->json([
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id,
                'start_date' => $booking->start_date,
                'status' => $booking->state,
                'is_posted' => $booking->is_posted,
                'message' => 'Booking created successfully',
            ]);
        } catch (\Exception $e) {

            DB::rollBack();
            \Log::error('Transaction rolled back due to error: '.$e->getMessage());

            return response()->json(['error' => 'Failed to create booking'], 500);
        }
    }

    // 1.0.2 - load invoices for a booking which paid full or partial - views.admin.renting.bookings.blade.php
    public function loadInvoices(Request $request)
    {
        $bookingId = $request->route('bookingId');
        // look for the invoices numnber and look BookingTransaction for any entry exists it mean it is paid
        $booking = RentingBooking::findOrFail($bookingId);
        $invoices = BookingInvoice::where('booking_id', $bookingId)->get();
        $transactions = RentingTransaction::where('booking_id', $bookingId)->get();

        return response()->json([
            'booking' => $booking,
            'invoices' => $invoices,
            'transactions' => $transactions,
        ]);
    }

    public function customerUpdate(Request $request)
    {
        $bookingId = $request->route('bookingId');
        // log
        \Log::info('Booking ID: '.$bookingId);


        $booking = RentingBooking::findOrFail($bookingId);
        // log booking
        \Log::info($booking);
        $booking->customer_id = $request->customer_id;

        $booking->save();

        return response()->json([
            'success' => true,
            'customer_id' => $booking->customer_id,
        ]);
    }

    // 3.1 - Payment Section > Confirm Amount >>>
    public function updateBooking(Request $request)
    {



        $bookingId = $request->input('booking_id');
        $paymentMethodId = $request->input('payment_method_id');
        $amountReceived = $request->input('amount');

        if (empty($amountReceived)) {

            \Log::error('Amount received is null or empty');

            return response()->json(['error' => 'Amount not in Correct Format '.$amountReceived], 400);
        }

        if ($amountReceived <= 0) {

            \Log::error('Invalid amount received: '.$amountReceived);

            return response()->json(['error' => 'Invalid amount received'], 400);
        }

        DB::beginTransaction();

        \Log::info('Transaction started', [$request->all()]);

        try {
            $booking = RentingBooking::findOrFail($bookingId);
            $invoice = BookingInvoice::where('booking_id', $bookingId)->where('is_paid', false)->firstOrFail();

            // check anomaly: unpaid invoice
            $pendingInvoices = BookingInvoice::where('booking_id', $bookingId)
                ->where(function ($query) {
                    $query->where('is_paid', false)
                        ->orWhere('is_posted', false);
                })->count();

            if ($pendingInvoices === 0) {
                DB::rollBack();

                return response()->json([
                    'message' => 'No pending invoices found. Payment already completed for this booking.',
                    'success' => false,
                    '',
                ], 200);
            }

            \Log::info('Pending invoices count: '.$pendingInvoices);

            if ($invoice->amount <= 0) {
                DB::rollBack();

                return response()->json(['error' => 'Invoice amount is zero or negative.'], 400);
            }

            $getPendingInvoices = BookingInvoice::where('booking_id', $bookingId)
                ->where(function ($query) {
                    $query->where('is_paid', false)
                        ->where('is_posted', true);
                })
                ->orderBy('invoice_date', 'desc')
                ->first(['id']);

            \Log::info('Pending invoices: '.$getPendingInvoices);

            $totalPayableDue = $invoice->amount;

            $totalReceived = $booking->transactions($getPendingInvoices->id)->sum('amount') ?? 0;

            $CurrentRemainingBalance = $totalPayableDue - $totalReceived;

            \Log::info('Total due: '.$totalPayableDue);
            \Log::info('Total received: '.$totalReceived);
            \Log::info('Amount received: '.$amountReceived);
            \Log::info('Current Remaining balance: '.$CurrentRemainingBalance);

            if ($amountReceived > $CurrentRemainingBalance) {
                \Log::error('Amount received is greater than the remaining balance');
                DB::rollBack();

                return response()->json(['error' => 'Amount received is greater than the remaining balance.'], 400);
            }

            // Most checks are done, now we can proceed with the payment
            if ($CurrentRemainingBalance > 0 && $CurrentRemainingBalance >= $amountReceived) {

                \Log::info('Processing payment');

                RentingBookingItem::where('booking_id', $bookingId)->update(['is_posted' => true]);

                // FULL PAY or SECOND ATTEMPT TO PAY REMAINING BALANCE IN FULL
                if ($CurrentRemainingBalance == $amountReceived) {

                    $transaction = new RentingTransaction([
                        'transaction_date' => now(),
                        'booking_id' => $bookingId,
                        'invoice_id' => $invoice->id,
                        'transaction_type_id' => 7,
                        'payment_method_id' => $paymentMethodId,
                        'amount' => $amountReceived,
                        'user_id' => auth()->id(),
                        'notes' => 'Full payment received',
                    ]);

                    $transaction->save();

                    $invoice->is_paid = true;
                    $invoice->is_posted = true;
                    $invoice->paid_date = now();
                    $invoice->state = 'Completed';
                    $invoice->updated_at = now();
                    $invoice->notes = 'Invoice paid in full';
                    $invoice->save();
                    \Log::info('Invoice fully paid');

                    // more than one invoice find having is_posted true just count
                    $pendingInvoices = BookingInvoice::where('booking_id', $bookingId)
                        ->where(function ($query) {
                            $query->where('is_posted', true);
                        })->count();

                    \Log::info('count of all posted invoices against booking id:'.$bookingId.' '.$pendingInvoices);

                    // if $pendingInvoice > 1
                    if ($pendingInvoices > 1) {
                        $rentingbooking = RentingBooking::findOrFail($bookingId);
                        if ($rentingbooking->state == 'Awaiting Documents & Payment') {
                            $rentingbooking->state = 'Awaiting Documents';
                            $rentingbooking->due_date = $rentingbooking->due_date->addWeek();
                            $rentingbooking->save();
                        } elseif ($rentingbooking->state == 'Awaiting Payment') {
                            $rentingbooking->state = 'Completed';
                            $rentingbooking->due_date = $rentingbooking->due_date->addWeek();
                            $rentingbooking->save();
                        } else {
                            $rentingbooking->due_date = $rentingbooking->due_date->addWeek();
                            $rentingbooking->save();
                        }
                    } elseif ($pendingInvoices == 1) {
                        $rentingbooking = RentingBooking::findOrFail($bookingId);
                        if ($rentingbooking->state == 'Awaiting Documents & Payment') {
                            $rentingbooking->state = 'Awaiting Documents';
                            $rentingbooking->save();
                        } elseif ($rentingbooking->state == 'Awaiting Payment') {
                            $rentingbooking->state = 'Completed';
                            $rentingbooking->save();
                        }
                    }

                    // $rentingbooking = RentingBooking::findOrFail($bookingId);
                    // if ($rentingbooking->state == 'Awaiting Documents & Payment') {
                    //     $rentingbooking->state = 'Awaiting Documents';
                    //     $rentingbooking->save();
                    // } else if ($rentingbooking->state == 'Awaiting Payment') {
                    //     $rentingbooking->state = 'Completed';
                    //     $rentingbooking->save();
                    // }

                    \Log::info('Transaction saved: '.$transaction->id);
                    DB::commit();

                    // MAIL FUNCTIONALITY - FULL PAYMENT - could be second attempt
                    $Booking = RentingBooking::findOrFail($bookingId);
                    $Customer = Customer::findOrFail($Booking->customer_id);

                    $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
                    $data['title'] = 'Hire Payment Receipt';
                    $data['body'] = 'Find your payment details:';

                    $data['booking_id'] = $bookingId;
                    $data['invoice_id'] = $invoice->id;
                    $data['invoice_date'] = $invoice->invoice_date;
                    $data['transaction_id'] = $transaction->id;
                    $data['transaction_date'] = $transaction->transaction_date;
                    $data['payment_method'] = PaymentMethod::findOrFail($paymentMethodId)->title;
                    $data['amount'] = $amountReceived;

                    // IF PDF IS NEEDED
                    // $pdf = Pdf::loadView('pdf.test',[
                    //     'today' => $toDay,
                    //     'SIGFILE'=>$fileName,
                    //     'booking' => $Booking,
                    //     'customer' => $Customer,
                    //     'motorbike' => $Motorbike,
                    //     'bookingItem' => $BookingItems,
                    //     'user_name' => $Booking->user->first_name . ' ' . $Booking->user->last_name
                    //     ])->setPaper('a4', 'portrait')
                    //     ->save(storage_path("app/customers/".$Booking->customer_id."/rental-agreement-" . time() . rand(1, 99999) . '.pdf'));
                    // $data["pdf"] = $pdf;

                    try {
                        Mail::to($data['email'])->send(new RentalPaymentReceipt($data));
                    } catch (Exception $e) {
                        Log::error('Failed to send email: '.$e->getMessage());
                        // return response()->json(['error' => 'Failed to send email, please check the email address and try again.'], 400);
                    }
                    // MAIL FUNCTIONALITY - END

                    \Log::info('Transaction committed full or remaining as full.');

                    \Log::info('Booking updated successfully', [
                        'success' => true,
                        'transaction_id' => $transaction->id,
                        'state' => 'Completed Awaiting Documents',
                        'start_date' => $booking->start_date,
                        'is_posted' => 1,
                        'message' => 'Payment processed successfully.',
                        'deposit' => (float) $booking->deposit,
                        'weekly' => (float) $booking->weekly_rent,
                        'total' => (float) $CurrentRemainingBalance,
                        'paid' => (float) $amountReceived,
                        'balance' => (float) $CurrentRemainingBalance - (float) $amountReceived,
                    ]);

                    return response()->json([
                        'success' => true,
                        'transaction_id' => $transaction->id,
                        'state' => 'Completed Awaiting Documents',
                        'start_date' => $booking->start_date,
                        'is_posted' => 1,
                        'message' => 'Payment processed successfully.',
                        'deposit' => (float) $booking->deposit,
                        'weekly' => (float) $booking->weekly_rent,
                        'total' => (float) $CurrentRemainingBalance,
                        'paid' => (float) $amountReceived,
                        'balance' => (float) $CurrentRemainingBalance - (float) $amountReceived,
                    ]);
                } elseif ($CurrentRemainingBalance > $amountReceived) {

                    $transaction = new RentingTransaction([
                        'transaction_date' => now(),
                        'booking_id' => $bookingId,
                        'invoice_id' => $invoice->id,
                        'transaction_type_id' => 7,
                        'payment_method_id' => $paymentMethodId,
                        'amount' => $amountReceived,
                        'user_id' => auth()->id(),
                        'notes' => 'Partial payment received',
                    ]);
                    $transaction->save();

                    $invoice->is_paid = false;
                    // $invoice->is_posted = false; // partial payment faking fake invoce (because eash invoice has to be is_posted=true to get paid or processed)
                    $invoice->state = 'Awaiting Payment';
                    $invoice->save();
                    \Log::info('Invoice Awaiting Payment');

                    \Log::info('Transaction saved: '.$transaction->id);
                    DB::commit();

                    // MAIL FUNCTIONALITY - FULL PAYMENT - could be second attempt
                    $Booking = RentingBooking::findOrFail($bookingId);
                    $Customer = Customer::findOrFail($Booking->customer_id);

                    $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
                    $data['title'] = 'Hire Payment Receipt';
                    $data['body'] = 'Find your payment details:';

                    $data['booking_id'] = $bookingId;
                    $data['invoice_id'] = $invoice->id;
                    $data['transaction_id'] = $transaction->id;
                    $data['transaction_date'] = $transaction->transaction_date;
                    $data['payment_method'] = PaymentMethod::findOrFail($paymentMethodId)->title;
                    $data['amount'] = $amountReceived;

                    try {
                        Mail::to($data['email'])->send(new RentalPaymentReceipt($data));
                    } catch (Exception $e) {
                        Log::error('Failed to send email: '.$e->getMessage());
                        // return response()->json(['error' => 'Failed to send email, please check the email address and try again.'], 400);
                    }
                    // MAIL FUNCTIONALITY - END

                    \Log::info('Transaction committed');

                    \Log::info('Booking updated successfully', [
                        'success' => true,
                        'transaction_id' => $transaction->id,
                        'state' => $booking->state,
                        'start_date' => $booking->start_date,
                        'is_posted' => $booking->is_posted,
                        'message' => 'Payment processed successfully.',
                        'deposit' => (float) $booking->deposit,
                        'weekly' => (float) $booking->weekly_rent,
                        'total' => (float) $CurrentRemainingBalance,
                        'paid' => (float) $amountReceived,
                        'balance' => (float) $CurrentRemainingBalance - (float) $amountReceived,
                    ]);

                    return response()->json([
                        'success' => true,
                        'transaction_id' => $transaction->id,
                        'state' => $booking->state,
                        'start_date' => $booking->start_date,
                        'is_posted' => $booking->is_posted,
                        'message' => 'Payment processed successfully.',
                        'deposit' => (float) $booking->deposit,
                        'weekly' => (float) $booking->weekly_rent,
                        'total' => (float) $CurrentRemainingBalance,
                        'paid' => (float) $amountReceived,
                        'balance' => (float) $CurrentRemainingBalance - (float) $amountReceived,
                    ]);
                } else {
                    DB::rollBack();
                    \Log::error('Amount is more than Balance');

                    return response()->json(['error' => 'Amount is more than Balance'], 400);
                }
            } elseif ($CurrentRemainingBalance == 0) {
                DB::rollBack();
                \Log::error('Amount is Received before');

                return response()->json(['error' => 'Amount is Received'], 400);
            } else {
                DB::rollBack();
                \Log::error('Other Condition');

                return response()->json(['error' => 'Amount is less than Balance'], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction rolled back due to error: '.$e->getMessage());

            return response()->json(['error' => 'An error occurred while updating the booking.', 'exception' => $e->getMessage()], 500);
        }
    }

    // 4.3.2 - Update Record Upon Rental Agreement Generation for Signature
    public function startbooking(Request $request, $bookingId)
    {

        $bookingId = $request->input('booking_id');
        $booking = RentingBooking::findOrFail($bookingId);
        $booking->state = 'Awaiting Documents & Payment';
        $booking->save();
        \Log::info('Booking updated: '.$booking->id);

        $RentingbookingItems = RentingBookingItem::where('booking_id', $bookingId)->get();
        foreach ($RentingbookingItems as $RentingbookingItem) {
            $RentingbookingItem->is_posted = true;
            $RentingbookingItem->save();
        }

        // Since it is multiple items RentingbookingItems how to log all
        \Log::info('Booking Item updated', [$RentingbookingItems]);

        $BookingInvoices = BookingInvoice::where('booking_id', $bookingId)->get();
        foreach ($BookingInvoices as $BookingInvoice) {
            $BookingInvoice->is_paid = false;
            $BookingInvoice->state = 'Awaiting Payment';
            $BookingInvoice->is_posted = true;
            $BookingInvoice->notes = 'Invoice created as unpaid';
            $BookingInvoice->save();
        }
        \Log::info('Booking Invoice updated', [$BookingInvoices]);

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'message' => 'Booking updated successfully',
        ]);
    }

    // /admin/renting/bookings/{bookingId}/invoice/create
    public function createUpdateInvoice(Request $request)
    {

        $bookingId = $request->input('booking_id');
        $dueAmount = $request->input('due_amount');

        DB::beginTransaction();
        try {
            $invoice = BookingInvoice::create([
                'booking_id' => $bookingId,
                'user_id' => auth()->id(),
                'invoice_date' => now(),
                'amount' => $dueAmount,
                'is_paid' => true,
                'is_posted' => true,
                'state' => 'Completed',
                'notes' => 'Invoice created as paid',
            ]);
            \Log::info('Invoice new created: '.$invoice->id);
            $booking = RentingBooking::findOrFail($bookingId);
            $booking->state = 'Completed';
            $booking->due_date = now()->addWeek();
            $booking->save();
            \Log::info('Booking updated: '.$booking->id);

            $transaction = new RentingTransaction([
                'transaction_date' => now(),
                'booking_id' => $bookingId,
                'invoice_id' => $invoice->id,
                'transaction_type_id' => 7,
                'payment_method_id' => 1,
                'amount' => $dueAmount,
                'user_id' => auth()->id(),
                'notes' => 'Invoice created as paid',
            ]);

            $transaction->save();

            DB::commit();
            \Log::info('Transaction committed');

            return response()->json([
                'success' => true,
                'booking_id' => $booking->id,
                'invoice_id' => $invoice->id,
                'message' => 'Invoice created successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction rolled back due to error: '.$e->getMessage());

            return response()->json(['error' => 'Failed to create invoice'], 500);
        }
    }

    // /admin/renting/bookings/{bookingId}/finalize
    public function finalizeBooking(Request $request, $bookingId)
    {
        //
    }

    // /admin/renting/bookings/{bookingId}/cancel
    public function cancelBooking(Request $request, $bookingId)
    {
        //
    }

    // 1.0.1 - getMotorbikePrice get the motorbike price check by motorbike_id
    public function getMotorbikePrice(Request $request)
    {


        $validatedData = $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
        ]);

        $pricing = RentingPricing::current()->where('motorbike_id', $request->motorbike_id)->where('iscurrent', 1)->first();

        if ($pricing) {
            return response()->json([
                'pricing' => $pricing,
            ]);
        } else {
            return response()->json([
                'message' => 'Pricing not found',
            ]);
        }
    }

    // The function that respond to get reuest to display the new booking page
    public function renting_booking_new()
    {
        \Log::info('New Booking Page Requested.');

        try {
            $motorbikes = DB::table('motorbikes as MB')
                ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
                ->rightJoin('renting_pricings as RP', 'RP.motorbike_id', '=', 'MB.id')
                ->leftJoin('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
                ->select(
                    'MB.id as MOTORBIKE_ID',
                    'MB.make as MAKE',
                    'MB.model as MODEL',
                    'MB.year as YEAR',
                    'MB.engine as ENGINE',
                    'MB.color as COLOR',
                    'MB.is_ebike as IS_EBIKE',
                    'MR.registration_number as REG_NO',
                    DB::raw("CONCAT(COALESCE(MC.mot_status,''), IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
                    DB::raw("CONCAT(COALESCE(MC.road_tax_status,''), IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
                    'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
                    'MC.insurance_status as INSURANCE_STATUS'
                )
                ->where('MB.vehicle_profile_id', 1)
                ->where('RP.iscurrent', true)

                // Availability check (same for bike + e-bike)
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('renting_booking_items')
                        ->whereColumn('renting_booking_items.motorbike_id', 'MB.id')
                        ->where('renting_booking_items.is_posted', true)
                        ->whereNull('renting_booking_items.end_date');
                })

                // Compliance rules
                ->where(function ($q) {
                    $q->where('MB.is_ebike', true) // e-bike → always allowed
                    ->orWhere(function ($q2) {
                        $q2->where('MB.is_ebike', false)
                            ->where('MC.road_tax_status', 'Taxed')
                            ->where(function ($q3) {
                                $q3->where('MC.mot_status', 'Valid')
                                    ->orWhere('MC.mot_status', 'No details held by DVLA');
                            });
                    });
                })
                ->get();

            $customers = Customer::all();

        } catch (\Exception $e) {
            \Log::error('Error: '.$e->getMessage());
            return response()->json(['error' => 'An error occurred'], 500);
        }

        $documentTypes = DocumentType::all();

        return view('olders.admin.renting.booking-new', compact(
            'motorbikes',
            'customers',
            'documentTypes'
        ));
    }

    // public function renting_booking_new()
    // {
    //     \Log::info('New Booking Page Requested.');

    //     try {
    //         $motorbikes = DB::table('motorbikes as MB')
    //             ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
    //             ->join('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
    //             ->rightjoin('renting_pricings as RP', 'RP.motorbike_id', '=', 'MB.id')
    //             ->select(
    //                 'MB.id as MOTORBIKE_ID',
    //                 'MB.make as MAKE',
    //                 'MB.model as MODEL',
    //                 'MB.year as YEAR',
    //                 'MB.engine as ENGINE',
    //                 'MB.color as COLOR',
    //                 'MR.registration_number as REG_NO',
    //                 DB::raw("CONCAT(MC.mot_status, IFNULL(CONCAT(' ', MC.mot_due_date), '')) as MOT_STATUS"),
    //                 DB::raw("CONCAT(MC.road_tax_status, IFNULL(CONCAT(' ', MC.tax_due_date), '')) as ROAD_TAX_STATUS"),
    //                 'MC.road_tax_status as ROAD_TAX_STATUS_FLAG',
    //                 'MC.insurance_status as INSURANCE_STATUS'
    //             )
    //             ->where('MB.vehicle_profile_id', 1)
    //             ->where(function ($query) {
    //                 $query->where('MC.road_tax_status', 'Taxed')
    //                     ->where(function ($subQuery) {
    //                         $subQuery->where('MC.mot_status', 'Valid')
    //                             ->orWhere('MC.mot_status', 'No details held by DVLA');
    //                     });
    //             })
    //             ->whereNotExists(function ($query) {
    //                 $query->select(DB::raw(1))
    //                     ->from('renting_booking_items')
    //                     ->whereColumn('renting_booking_items.motorbike_id', 'MB.id')
    //                     ->where('renting_booking_items.is_posted', true)->where('renting_booking_items.end_date', null);
    //             })->where('RP.iscurrent', true)
    //             ->get();

    //         $customers = Customer::all();

    //     } catch (\Exception $e) {
    //         \Log::error('Error: '.$e->getMessage());
    //         // ERRRO CATCH RELATED SQL
    //         \Log::error('SQL: '.DB::getQueryLog());

    //         return response()->json(['error' => 'An error occurred while fetching motorbikes'], 500);
    //     }

    //     $documentTypes = DocumentType::all();

    //     return view('olders.admin.renting.booking-new', [
    //         'motorbikes' => $motorbikes,
    //         'customers' => $customers,
    //         'documentTypes' => $documentTypes,
    //     ]);

    // }

    // 1.0.1 - Realtime check for Motorbike Availability
    public function checkMotorbikeAvailability(Request $request)
    {
        $validatedData = $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
        ]);

        $motorbike = Motorbike::find($request->motorbike_id);

        if (! $motorbike) {
            return response()->json(['error' => 'Motorbike not found'], 404);
        }

        // Assuming you have a method to check availability
        $isAvailable = $motorbike->isAvailable();

        if ($isAvailable) {
            return response()->json(['status' => 'Available']);
        } else {
            return response()->json(['status' => 'Not Available']);
        }
    }

    // A1.0 - Function Library - GET So Far Received Payments against a Booking
    public function lookupBookingTransactions($bookingId)
    {
        $booking = RentingBooking::findOrFail($bookingId);

        $totalPaid = $booking->transactions()->sum('amount');
        $transactionCount = $booking->transactions()->count();

        return response()->json([
            'total_paid' => $totalPaid,
            'transaction_count' => $transactionCount,
        ]);
    }

    // APPLICATION: Booking Management / GET ALL INVOICE OR PENDING INVOICES
    // - Request Came from Booking Management
    // - Get the Motorbike Price without booking_id
    public function getMotorbikeInvoices(Request $request)
    {
        \Log::info('GetMotP', [$request->all()]);

        // Validate request data right at the beginning
        $validatedData = $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
            // since it is generic some callback points are not provides booking_id
            // 'booking_id' => 'sometimes|required|exists:renting_bookings,id'
        ]);

        $totalPaid = 0;

        if ($request->booking_id != null) {

            $bookingTransactions = $this->lookupBookingTransactions($request->booking_id);
            $totalPaid = $bookingTransactions->getData()->total_paid;
            $transactionCount = $bookingTransactions->getData()->transaction_count;

            \Log::info('Booking Transactions', [$bookingTransactions->getData()]);
            \Log::info('Total Paid: '.$totalPaid);
            \Log::info('Transaction Count: '.$transactionCount);

            $pendingInvoices = BookingInvoice::where('booking_id', $request->booking_id)
                ->where(function ($query) {
                    $query->where('is_paid', false)
                        ->where('is_posted', true);
                })->count();

            \Log::info('Pending Invoices: '.$pendingInvoices);

            if ($pendingInvoices === 0) {
                return response()->json([
                    'message' => 'No pending invoices found. Payment already completed for this booking. CTRL',
                    'success' => false,
                ], 200);
            }

            $invoices = BookingInvoice::where('booking_id', $request->booking_id)->where('is_posted', true)->get();

            if ($invoices->count() > 1) {

                $pendingInvoiceNumbers = BookingInvoice::where('booking_id', $request->booking_id)
                    ->where(function ($query) {
                        $query->where('is_paid', false)
                            ->where('is_posted', true);
                    })->first();

                // \Log::info('All Pending Invoice Numbers: ' . $pendingInvoiceNumbers);

                // $pendingInvoiceNumbers does have more than
                $isValidReg = Motorbike::where('id', $request->motorbike_id)->exists();
                \Log::info($isValidReg);

                if (! $isValidReg) {
                    return response()->json(['error' => 'ANOMOLY: Invalid motorbike id'], 400);
                }

                \Log::info('Total Paid: '.$totalPaid);

                return response()->json([
                    'message' => 'Multiple invoices found. Payment already completed for this booking. CTRL',
                    'repayment' => true,
                    'success' => true,
                    'invoice_id' => $pendingInvoiceNumbers->id,
                    'booking_id' => $request->booking_id,
                    'pricing' => $pendingInvoiceNumbers->amount,
                    'totalPaid' => 0,
                ], 200);
            } else {
                \Log::info('OTHER CONDITION');
            }
        } else {

            $isValidReg = Motorbike::where('id', $request->motorbike_id)->exists();

            if (! $isValidReg) {
                return response()->json(['error' => 'ANOMOLY: Invalid motorbike id'], 400);
            }

            $pricing = RentingPricing::current()->where('motorbike_id', $request->motorbike_id)->where('iscurrent', true)->first();

            return response()->json([
                'pricing' => $pricing,
                'totalPaid' => $totalPaid,
            ]);
        }

        \Log::info('not got hit');

        $isValidReg = Motorbike::where('id', $request->motorbike_id)->exists();

        if (! $isValidReg) {
            return response()->json(['error' => 'ANOMOLY: Invalid motorbike id'], 400);
        }

        $pricing = RentingPricing::current()->where('motorbike_id', $request->motorbike_id)->where('iscurrent', true)->first();

        return response()->json([
            'pricing' => $pricing,
            'totalPaid' => $totalPaid,
        ]);
    }

    public function showPricing()
    {
        $pricing = RentingPricing::current()->get();

        $instance = new RentingPricing;

        \Log::info($instance->motorbikeNotPriced());

        // 'id', 'make', 'model', 'year', 'engine', 'color', 'fuel_type', 'reg_no'

        return view('olders.admin.motorbikes.pricing', ['pricing' => $pricing, 'motorbikes_not_priced' => $instance->motorbikeNotPriced()]);
    }

    // SET MOTORBIKE PRICE
    public function storePricing(Request $request)
    {
        $this->authorize('create', RentingPricing::class);
        $validatedData = $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
            'weekly_price' => 'required|numeric',
            'minimum_deposit' => 'required|numeric',
        ]);

        // Update old same motorbike pricing to iscurrent = false
        RentingPricing::where('motorbike_id', $validatedData['motorbike_id'])->update(['iscurrent' => false]);

        $pricing = new RentingPricing($validatedData);
        $pricing->user_id = auth()->id();
        $pricing->iscurrent = true;
        $pricing->update_date = now();

        \Log::info($pricing);

        $pricing->save();

        return redirect()->route('admin.motorbikes.pricing')->with('success', 'Pricing stored successfully');
    }

    // update bike renting price / deposit
    public function updatePricing(Request $request)
    {


        $existingPricing = RentingPricing::findOrFail($request->id);

        // Validate the incoming request
        $validated = $request->validate([
            'weekly_price' => 'required|numeric|min:0',
            'minimum_deposit' => 'required|numeric|min:0',
        ]);

        if ($validated['weekly_price'] == 0 && $validated['minimum_deposit'] == 0) {
            // delete taht record on RentingPricing
            $existingPricing->delete();

            return redirect()->route('admin.motorbikes.pricing')->with('success', 'Pricing deleted successfully');
        }

        // Start transaction to ensure data consistency write or rollback
        \DB::beginTransaction();
        try {
            // Set existing pricing record's iscurrent to false
            $existingPricing->iscurrent = false;
            $existingPricing->save();

            // Create a new pricing record with the updated values
            $newPricing = new RentingPricing([
                'motorbike_id' => $existingPricing->motorbike_id,
                'weekly_price' => $validated['weekly_price'],
                'minimum_deposit' => $validated['minimum_deposit'],
                'iscurrent' => true,
                'user_id' => auth()->id(), // Assuming you're tracking which user made the change
                'update_date' => now(), // Assuming update_date is to be set at creation
            ]);
            $newPricing->save();

            // Commit transaction
            \DB::commit();

            return redirect()->route('admin.motorbikes.pricing')->with('success', 'Pricing updated successfully');
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            \DB::rollBack();

            \Log::error('Failed to update pricing: '.$e->getMessage());

            return redirect()->route('admin.motorbikes.pricing')->with('error', 'Failed to update pricing.');
        }
    }

    // Save and Send the signed PDF document # MAIL FUNCTIONALITY - IT IS NOT SEEM Working - REDUNDAND - transfer to agreement controller
    // public function createNewAgreement(Request $request)
    // {
    //     $base64_image = $request->input('sign'); // your base64 encoded
    //     @list($type, $file_data) = explode(';', $base64_image);
    //     @list(, $file_data) = explode(',', $file_data);

    //     $fileName = $request->first_name . '-' . $request->last_name . '-' . Carbon::now() . '.' . 'jpg';

    //     Storage::disk('public')->put($fileName, base64_decode($file_data));

    //     // log
    //     \Log::info('Creating new agreement');
    //     \Log::info($request->all());

    //     $Booking = RentingBooking::findOrFail($request->booking_id);
    //     \Log::info('Booking Obj: ', [$Booking]);
    //     $Customer = Customer::findOrFail($Booking->customer_id);
    //     \Log::info('Customer Obj: ', [$Customer]);
    //     $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();
    //     \Log::info('Booking Item Obj: ', [$BookingItems]);
    //     $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
    //     \Log::info('Motorbike Obj: ', [$Motorbike]);

    //     $toDay = new DateTime();
    //     $toDay = Carbon::parse($toDay)->format('d/m/Y');

    //     //  // Send email with PDF to client
    //     $data["email"] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
    //     $data["title"] = "Rental Agreement";
    //     $data["body"] = "Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!";

    //     $pdf = Pdf::loadView('pdf.test', [
    //         'today' => $toDay,
    //         'SIGFILE' => $fileName,
    //         'booking' => $Booking,
    //         'customer' => $Customer,
    //         'motorbike' => $Motorbike,
    //         'bookingItem' => $BookingItems,
    //         'user_name' => $Booking->user->first_name . ' ' . $Booking->user->last_name
    //     ])->setPaper('a4', 'portrait')
    //         ->save(storage_path("app/customers/" . $Booking->customer_id . "/rental-agreement-" . time() . rand(1, 99999) . '.pdf'));

    //     $data["pdf"] = $pdf;

    //     Mail::to($data["email"])->send(new RentalAgreement($data));

    //     return redirect()->route('isRented');
    // }

    public function renting_agreement() {}

    public function finance_agreement_template()
    {
        return view('olders.admin.finance.agreement');
    }

    public function renting_agreement_template()
    {
        return view('olders.admin.renting.agreement');
    }

    public function updateInvoiceDate(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:booking_invoices,id',
            'new_date' => 'required|date',
        ]);

        $invoice = BookingInvoice::findOrFail($request->invoice_id);
        // $previousDate = $invoice->invoice_date;
        $invoice->invoice_date = $request->new_date;
        // $invoice->notes = "Previous date: $previousDate, changed to: {$request->new_date} by user ID: " . auth()->id();
        $invoice->save();

        return response()->json([
            'success' => true,
            'message' => 'Invoice date updated successfully',
            'invoice' => $invoice,
        ]);
    }

    public function invoiceDatesAllView(Request $request)
    {
        $search = $request->get('search');

        $invoicesQuery = \DB::table('renting_bookings as rb')
            ->join('customers as c', 'rb.customer_id', '=', 'c.id')
            ->leftJoin('renting_booking_items as bi', 'bi.booking_id', '=', 'rb.id')
            ->leftJoin('motorbikes as m', 'bi.motorbike_id', '=', 'm.id')
            ->leftJoin('booking_invoices as i', 'i.booking_id', '=', 'rb.id')
            ->select(
                'rb.id as booking_id',
                \DB::raw("CONCAT(c.first_name, ' ', c.last_name) as customer_full_name"),
                'c.email as customer_email_address',
                'c.phone as customer_phone',
                \DB::raw("COALESCE(m.reg_no, 'N/A') as motorbike_reg"),
                'i.id as invoice_id',
                'i.invoice_date',
                'i.amount',
                'i.state as invoice_state',
                'i.is_paid'
            )
            ->whereNotNull('i.id');

        if ($search) {
            $invoicesQuery->where(function ($q) use ($search) {
                $q->orWhere('rb.id', 'like', "%$search%")
                    ->orWhere(\DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', "%$search%")
                    ->orWhere('c.email', 'like', "%$search%")
                    ->orWhere('c.phone', 'like', "%$search%")
                    ->orWhere('m.reg_no', 'like', "%$search%")
                    ->orWhere('i.id', 'like', "%$search%");
            });
        }

        $invoices = $invoicesQuery->orderBy('rb.id', 'desc')->get();

        // For dropdowns if you want to keep them for future
        $bookingIds = $invoices->pluck('booking_id')->unique();

        return view('olders.admin.renting.invoice-dates-all', compact('invoices', 'bookingIds', 'search'));
    }

    public function updateStartDate(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:renting_bookings,id',
            'new_start_date' => 'required|date',
        ]);

        $booking = RentingBooking::findOrFail($request->booking_id);
        $booking->start_date = $request->new_start_date;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking start date updated successfully',
            'booking' => $booking,
        ]);
    }

    public function showUpdateStartDateForm()
    {
        $bookings = RentingBooking::with('customer')->get();

        return view('olders.admin.renting.update-start-date', compact('bookings'));
    }

    public function uploadServiceVideo(Request $request, $bookingId)
    {
        $booking = RentingBooking::findOrFail($bookingId);

        $request->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi,wmv,mkv',
        ]);

        try {
            $videoFile = $request->file('video_file');
            $timestamp = now()->format('Ymd_His');
            $extension = $videoFile->getClientOriginalExtension();
            $fileName = $bookingId.'_'.$timestamp.'.'.$extension;
            $storePath = $videoFile->storeAs('public/rental_service_videos', $fileName);

            $videoRecord = new RentingServiceVideo([
                'booking_id' => $bookingId,
                'video_path' => $storePath,
                'recorded_at' => now(),
            ]);
            $videoRecord->save();

            // --- SFTP Sync Logic ---
            $absolutePath = storage_path('app/'.$storePath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absolutePath);

            if (! $success) {
                // Optionally, you can log or return a warning about sync failure
                // Log::warning("Video uploaded locally but failed to sync to remote domain: $absolutePath");
            }

            return response()->json([
                'message' => 'Video uploaded successfully',
                'video' => $videoRecord,
                'sync' => $success ? 'Synced to remote domain' : 'Sync to remote domain failed',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getServiceVideos($bookingId)
    {
        $videos = RentingServiceVideo::where('booking_id', $bookingId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($videos);
    }

    // Add a new maintenance log
    public function addMaintenanceLog(Request $request, $bookingId)
    {
        $booking = RentingBooking::findOrFail($bookingId);

        $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
            'cost' => 'required|numeric|min:0',
            'serviced_at' => 'required|date',
            'description' => 'required|string|max:255',
            'note' => 'nullable|string',
        ]);

        try {
            $maintenanceData = $request->only([
                'motorbike_id',
                'cost',
                'serviced_at',
                'description',
                'note',
            ]);
            $maintenanceData['user_id'] = auth()->id();
            $maintenanceData['booking_id'] = $bookingId;

            $log = new MotorbikeMaintenanceLog($maintenanceData);
            $log->save();

            return response()->json([
                'message' => 'Maintenance log added successfully',
                'log' => $log,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get all maintenance logs for a booking/motorbike
    public function getMaintenanceLogs($bookingId)
    {
        $booking = RentingBooking::findOrFail($bookingId);

        // Attempt to get the first motorbike_id from booking items, if any
        $motorbike_id = optional($booking->rentingBookingItems->first())->motorbike_id;

        if (! $motorbike_id) {
            return response()->json([
                'logs' => [],
                'message' => 'No motorbike associated with this booking.',
            ]);
        }

        $logs = MotorbikeMaintenanceLog::where('motorbike_id', $motorbike_id)
            ->where('booking_id', $bookingId)
            ->orderBy('serviced_at', 'desc')
            ->get();

        return response()->json(['logs' => $logs]);
    }

    // Add this method to RentingController
    public function getBookingSummary($bookingId)
    {
        $booking = RentingBooking::with(['rentingBookingItems', 'bookingInvoices'])->findOrFail($bookingId);

        $start = $booking->start_date;
        $end = $booking->due_date;
        $now = now();

        // Use end date if set, otherwise now
        $effectiveEnd = $end ?? $now;
        $weeks = ceil($start->diffInDays($effectiveEnd) / 7);

        // Get all invoices for this booking (paid only)
        $totalIncome = $booking->bookingInvoices()->where('is_paid', true)->sum('amount');
        $paidInvoiceCount = $booking->bookingInvoices()->where('is_paid', true)->count();

        // Get the first motorbike_id
        $bookingItem = $booking->rentingBookingItems->first();
        $motorbike_id = optional($bookingItem)->motorbike_id;
        $reg_no = ($bookingItem && $bookingItem->motorbike) ? $bookingItem->motorbike->reg_no : null;

        // Get all maintenance logs for this booking/motorbike
        $totalCost = MotorbikeMaintenanceLog::where('booking_id', $bookingId)
            ->where('motorbike_id', $motorbike_id)
            ->sum('cost');

        // Get current weekly rent for this motorbike
        $currentPricing = RentingPricing::where('motorbike_id', $motorbike_id)
            ->where('iscurrent', true)
            ->first();

        // Ensure variables are always defined
        $totalWeeksAtCurrentPrice = isset($totalWeeksAtCurrentPrice) ? $totalWeeksAtCurrentPrice : 0;
        $totalAtCurrentPrice = isset($totalAtCurrentPrice) ? $totalAtCurrentPrice : 0;
        $totalAtAllPrices = isset($totalAtAllPrices) ? $totalAtAllPrices : 0;
        $pricePeriods = isset($pricePeriods) ? $pricePeriods : [];
        $paidInvoiceCount = isset($paidInvoiceCount) ? $paidInvoiceCount : 0;

        return response()->json([
            'booking_id' => $booking->id,
            'reg_no' => $reg_no,
            'start_date' => $start ? $start->format('Y-m-d') : null,
            'end_date' => $end ? $end->format('Y-m-d') : null,
            'weeks' => $weeks,
            'total_income' => $totalIncome,
            'total_cost' => $totalCost,
            'net_profit' => $totalIncome - $totalCost,
            'current_weekly_rent' => $currentPricing ? $currentPricing->weekly_price : null,
            'total_weeks_at_current_price' => $totalWeeksAtCurrentPrice,
            'total_at_current_price' => $totalAtCurrentPrice,
            'total_at_all_prices' => $totalAtAllPrices,
            'price_periods' => $pricePeriods,
            'paid_invoice_count' => $paidInvoiceCount,
        ]);
    }

    // Add this method to RentingController
    public function getBookingSummaryView($bookingId)
    {
        $booking = RentingBooking::with(['rentingBookingItems', 'bookingInvoices'])->findOrFail($bookingId);

        $start = $booking->start_date;
        $end = $booking->due_date;
        $now = now();

        // Use end date if set, otherwise now
        $effectiveEnd = $end ?? $now;
        $weeks = ceil($start->diffInDays($effectiveEnd) / 7);

        // Get all invoices for this booking (paid only)
        $totalIncome = $booking->bookingInvoices()->where('is_paid', true)->sum('amount');
        $paidInvoiceCount = $booking->bookingInvoices()->where('is_paid', true)->count();

        // Get the first motorbike_id
        $bookingItem = $booking->rentingBookingItems->first();
        $motorbike_id = optional($bookingItem)->motorbike_id;
        $reg_no = ($bookingItem && $bookingItem->motorbike) ? $bookingItem->motorbike->reg_no : null;

        // Get all maintenance logs for this booking/motorbike
        $totalCost = MotorbikeMaintenanceLog::where('booking_id', $bookingId)
            ->where('motorbike_id', $motorbike_id)
            ->sum('cost');

        // Get current weekly rent for this motorbike
        $currentPricing = RentingPricing::where('motorbike_id', $motorbike_id)
            ->where('iscurrent', true)
            ->first();

        // Ensure variables are always defined
        $totalWeeksAtCurrentPrice = isset($totalWeeksAtCurrentPrice) ? $totalWeeksAtCurrentPrice : 0;
        $totalAtCurrentPrice = isset($totalAtCurrentPrice) ? $totalAtCurrentPrice : 0;
        $totalAtAllPrices = isset($totalAtAllPrices) ? $totalAtAllPrices : 0;
        $pricePeriods = isset($pricePeriods) ? $pricePeriods : [];
        $paidInvoiceCount = isset($paidInvoiceCount) ? $paidInvoiceCount : 0;

        return view('olders.admin.renting.summary-view', compact('booking'));
    }

    // Delete a maintenance log by its ID
    public function deleteMaintenanceLog($logId)
    {
        $log = \App\Models\MotorbikeMaintenanceLog::find($logId);
        if (! $log) {
            return response()->json(['success' => false, 'message' => 'Log not found.'], 404);
        }
        $log->delete();

        return response()->json(['success' => true, 'message' => 'Maintenance log deleted.']);
    }
}
