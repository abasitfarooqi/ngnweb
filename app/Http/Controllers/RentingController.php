<?php

namespace App\Http\Controllers;

use App\Mail\OtherChargesReceipt;
use App\Mail\RentalAgreement;
use App\Mail\RentalPaymentReceipt;
use App\Mail\RentalPaymentReversedNotice;
use App\Models\AgreementAccess;
use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\BookingIssuanceItem;
use App\Models\Customer;
use App\Models\DocumentType;
use App\Models\Motorbike;
use App\Models\MotorbikeRegistration;
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
use App\Services\RentingInvoiceSyncService;
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
use App\Support\RentalAvailabilityRepair;
use App\Support\RentalBookingLifecycle;

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

        $url = AgreementAccess::customerSigningUrl((int) $customer_id, $passcode);

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
        try {
            $request->validate(['charges_id' => 'required']);
            $otherCharge = RentingOtherCharge::findOrFail($request->charges_id);
            $result = app(RentalBookingLifecycle::class)->payOtherCharge(
                (int) $otherCharge->id,
                $request->input('payment_method_id') ? (int) $request->input('payment_method_id') : null
            );

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

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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

        $latestTransactionIds = DB::table('renting_transactions')
            ->selectRaw('MAX(id) as latest_transaction_id, invoice_id')
            ->whereNotNull('invoice_id')
            ->groupBy('invoice_id');

        $invoicePaymentSums = DB::table('renting_transactions')
            ->selectRaw('invoice_id, SUM(amount) as total_paid_amount')
            ->whereNotNull('invoice_id')
            ->groupBy('invoice_id');

        $invoices = DB::table('booking_invoices as BI')
            ->leftJoinSub($latestTransactionIds, 'LRT', function ($join) {
                $join->on('LRT.invoice_id', '=', 'BI.id');
            })
            ->leftJoinSub($invoicePaymentSums, 'IPS', function ($join) {
                $join->on('IPS.invoice_id', '=', 'BI.id');
            })
            ->leftJoin('renting_transactions as RT', 'RT.id', '=', 'LRT.latest_transaction_id')
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
                DB::raw('COALESCE(IPS.total_paid_amount, 0) as TOTAL_PAID_AMOUNT'),
                DB::raw('(BI.amount - COALESCE(IPS.total_paid_amount, 0)) as OUTSTANDING_BALANCE'),
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
        $message = "Dear {$invoice->customer_name}, this is a reminder regarding your Weekly Rental payment for motorbike {$invoice->motorbike_reg_no}. The outstanding amount of £".number_format($invoice->weekly_rent, 2)." is due on {$invoiceDate}. Please ensure payment is made as soon as possible to avoid late fees. If you have already paid, please contact us immediately at 0208 314 1498 or WhatsApp us on 07951790568, NGN Motors, ".$this->buildWhatsappStaffSignature().'.';

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

        try {
            $result = app(RentingInvoiceSyncService::class)->resequenceUnpaidInvoiceDatesFrom(
                (int) $invoiceId,
                (string) $request->invoice_date
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => ((int) $result['updated'] > 1)
                ? 'Invoice date updated and upcoming unpaid invoices realigned weekly.'
                : 'Invoice date updated successfully.',
            'invoice_date' => $result['first_date'],
            'updated' => $result['updated'],
        ]);
    }

    public function docConfirm(Request $request)
    {
        $booking = RentingBooking::findOrFail($request->booking_id);
        $result = app(RentalBookingLifecycle::class)->confirmDocuments($booking);

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'status' => 'success',
            'state' => $result['state'],
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
        $request->merge(['isChecked' => filter_var($request->input('isChecked'), FILTER_VALIDATE_BOOLEAN)]);
        $request->merge(['proceed_anyway' => filter_var($request->input('proceed_anyway'), FILTER_VALIDATE_BOOLEAN)]);

        try {
            $validatedData = $request->validate([
                'booking_id' => 'required|exists:renting_bookings,id',
                'booking_item_id' => 'required|exists:renting_booking_items,id',
                'collectDetails' => 'nullable|string',
                'collectDate' => 'nullable|date',
                'collectTime' => 'nullable|date_format:H:i',
                'isChecked' => 'required|boolean',
                'proceed_anyway' => 'sometimes|boolean',
            ]);

            $booking = RentingBooking::findOrFail($validatedData['booking_id']);
            $bookingItem = RentingBookingItem::where('booking_id', $validatedData['booking_id'])
                ->where('id', $validatedData['booking_item_id'])
                ->firstOrFail();

            $closing = app(RentalBookingLifecycle::class)->endRental(
                $booking,
                $bookingItem,
                [
                    'collect_details' => $validatedData['collectDetails'],
                    'collect_date' => $validatedData['collectDate'],
                    'collect_time' => $validatedData['collectTime'],
                    'collect_checked' => $validatedData['isChecked'],
                ],
                ! empty($validatedData['proceed_anyway'])
            );

            return response()->json(['success' => true, 'data' => $closing]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
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

    /**
     * Record deposit refund with proof and optional email. Blocks if PCN open or additional charges due.
     * POST /admin/renting/deposit-refund
     */
    public function depositRefund(Request $request)
    {
        $request->merge([
            'send_email' => filter_var($request->input('send_email'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $validated = $request->validate([
            'booking_id' => 'required|exists:renting_bookings,id',
            'amount_refunded' => 'required|numeric|min:0',
            'refund_date' => 'required|date',
            'refund_method' => 'required|in:cash,bank_transfer,card,other',
            'proof_reference' => 'nullable|string|max:255',
            'send_email' => 'required|boolean',
            'email_content_type' => 'nullable|in:full,deposit_only',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);
        $validated['email_content_type'] = ($validated['send_email'] && ! empty($validated['email_content_type']))
            ? $validated['email_content_type']
            : 'full';

        $booking = RentingBooking::with(['customer', 'rentingBookingItems.motorbike'])->findOrFail($validated['booking_id']);
        $bookingItem = $booking->rentingBookingItems->first();
        $motorbikeId = $bookingItem ? $bookingItem->motorbike_id : null;

        // Block: PCN open
        if ($motorbikeId) {
            $openPcnCases = $this->pcnCasesForBooking($booking, $motorbikeId)
                ->where('isClosed', false)
                ->get();
            if ($openPcnCases->isNotEmpty()) {
                $prefix = config('backpack.base.route_prefix', 'admin');
                $pcnCases = $openPcnCases->map(function ($c) use ($prefix) {
                    return [
                        'id' => $c->id,
                        'pcn_number' => $c->pcn_number,
                        'reduced_amount' => $c->reduced_amount,
                        'date_of_contravention' => $c->date_of_contravention?->format('Y-m-d'),
                        'isClosed' => (bool) $c->isClosed,
                        'link' => url($prefix.'/pcn-case/'.$c->id.'/edit'),
                    ];
                });
                return response()->json([
                    'blocked' => true,
                    'reason' => 'pcn_open',
                    'message' => 'Refund not authorised. Open PCN case(s) must be resolved first.',
                    'pcn_cases' => $pcnCases,
                ], 422);
            }
        }

        // Block: additional charges due (unpaid)
        $unpaidAmount = (float) DB::table('renting_other_charges')
            ->where('booking_id', $booking->id)
            ->where(function ($q) {
                $q->where('is_paid', 0)->orWhereNull('is_paid');
            })
            ->sum('amount');
        if ($unpaidAmount > 0) {
            return response()->json([
                'blocked' => true,
                'reason' => 'additional_charges_due',
                'message' => 'Refund not authorised. Additional charges are due. Clear them in the CHARGES tab.',
            ], 422);
        }

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $name = $file->getClientOriginalName() ?: 'proof_'.time().'.'.$file->getClientOriginalExtension();
            $proofPath = $file->storeAs('deposit_refunds/'.$booking->id, $name, 'public');
        }

        $userId = auth()->id();
        $bookingClosing = BookingClosing::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'deposit_checked' => true,
                'deposit_refunded_at' => \Carbon\Carbon::parse($validated['refund_date']),
                'deposit_refund_method' => $validated['refund_method'],
                'deposit_refund_proof_path' => $proofPath,
                'deposit_refund_proof_reference' => $validated['proof_reference'] ?? null,
                'deposit_refund_user_id' => $userId,
                'deposit_refund_send_email' => $validated['send_email'],
            ]
        );

        if ($validated['send_email']) {
            try {
                $mailData = $this->buildDepositRefundMailData($booking, $bookingClosing, $validated);
                $mailData['email_content_type'] = $validated['email_content_type'];
                $pdf = null;
                if ($validated['email_content_type'] === 'full') {
                    try {
                        $pdf = Pdf::loadView('emails.pdf.deposit-refund-report', $mailData)
                            ->setPaper('a4', 'portrait');
                    } catch (\Throwable $e) {
                        Log::warning('Deposit refund PDF generation failed: '.$e->getMessage());
                    }
                }
                $recipients = array_filter([$booking->customer->email ?? null, 'customerservice@neguinhomotors.co.uk']);
                Mail::to($recipients)->send(new \App\Mail\DepositRefundRentalEndingMail($mailData, $pdf));
                $bookingClosing->update(['deposit_refund_email_sent_at' => now()]);
            } catch (\Throwable $e) {
                Log::error('Deposit refund email failed: '.$e->getMessage());
            }
        }

        $closingStatus = BookingClosing::where('booking_id', $booking->id)->first();
        return response()->json([
            'success' => true,
            'deposit_checked' => true,
            'message' => 'Refund recorded.',
            'closing' => $closingStatus ? [
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
            ] : null,
        ]);
    }

    private function buildDepositRefundMailData(RentingBooking $booking, BookingClosing $closing, array $validated): array
    {
        $item = $booking->rentingBookingItems->first();
        $motorbike = $item ? $item->motorbike : null;
        $customer = $booking->customer;
        $collectDate = $closing->collect_date ? \Carbon\Carbon::parse($closing->collect_date)->format('d/m/Y') : null;
        $collectTime = $closing->collect_time ? \Carbon\Carbon::parse($closing->collect_time)->format('H:i') : null;

        return [
            'booking_id' => $booking->id,
            'customer_name' => trim(($customer->first_name ?? '').' '.($customer->last_name ?? '')),
            'email' => $customer->email ?? '',
            'vehicle_reg' => $motorbike ? $motorbike->reg_no : 'N/A',
            'rental_start' => $booking->start_date ? $booking->start_date->format('d/m/Y') : 'N/A',
            'rental_end' => $item && $item->end_date ? $item->end_date->format('d/m/Y') : ($collectDate ?: 'N/A'),
            'collect_details' => $closing->collect_details,
            'collect_date' => $collectDate,
            'collect_time' => $collectTime,
            'amount_refunded' => $validated['amount_refunded'],
            'refund_date' => is_string($validated['refund_date']) ? $validated['refund_date'] : \Carbon\Carbon::parse($validated['refund_date'])->format('d/m/Y'),
            'refund_method' => $validated['refund_method'],
            'proof_reference' => $validated['proof_reference'] ?? null,
        ];
    }

    /**
     * Get pendings that block or warn when collecting motorbike (additional charges, PCN, pending rent).
     * Used to show "Proceed anyway?" modal before marking collect.
     */
    public function getClosingPendings($bookingId)
    {
        $booking = RentingBooking::with(['rentingBookingItems.motorbike'])->findOrFail($bookingId);
        $motorbikeId = $booking->rentingBookingItems->first()?->motorbike_id;
        $prefix = config('backpack.base.route_prefix', 'admin');

        $additionalChargesTotal = (float) DB::table('renting_other_charges')
            ->where('booking_id', $booking->id)
            ->sum('amount');
        $additionalChargesPaid = (float) DB::table('renting_other_charges')
            ->where('booking_id', $booking->id)
            ->where('is_paid', true)
            ->sum('amount');
        $additionalChargesDue = $additionalChargesTotal - $additionalChargesPaid;

        $pcnDue = 0.0;
        $pcnTotal = 0.0;
        $pcnPaid = 0.0;
        $pcnCases = [];
        if ($motorbikeId) {
            $pcnTotal = (float) $this->pcnCasesForBooking($booking, $motorbikeId)
                ->sum('reduced_amount');
            $pcnPaid = (float) $this->pcnCasesForBooking($booking, $motorbikeId)
                ->where('isClosed', true)
                ->sum('reduced_amount');
            $pcnDue = $pcnTotal - $pcnPaid;
            $openPcnCases = $this->pcnCasesForBooking($booking, $motorbikeId)
                ->where('isClosed', false)
                ->get();
            foreach ($openPcnCases as $c) {
                $pcnCases[] = [
                    'id' => $c->id,
                    'pcn_number' => $c->pcn_number,
                    'reduced_amount' => $c->reduced_amount,
                    'date_of_contravention' => $c->date_of_contravention?->format('Y-m-d'),
                    'link' => url($prefix.'/pcn-case/'.$c->id.'/edit'),
                ];
            }
        }

        // Only invoices with invoice_date on or before today count as required to pay (future invoices excluded)
        $today = \Carbon\Carbon::today()->toDateString();
        $pendingRentDue = (float) BookingInvoice::where('booking_id', $booking->id)
            ->where('is_paid', false)
            ->whereDate('invoice_date', '<=', $today)
            ->sum('amount');

        $hasPendings = $additionalChargesDue > 0 || $pcnDue > 0 || $pendingRentDue > 0;

        // Explicit cause per type so user knows what is blocking
        $messages = [];
        $causes = [];
        if ($pendingRentDue > 0) {
            $msg = 'Rental left to pay: £'.number_format($pendingRentDue, 2).' (see PAYMENT tab).';
            $messages[] = $msg;
            $causes[] = ['type' => 'rental', 'label' => 'Rental left to pay', 'amount' => $pendingRentDue, 'message' => $msg];
        }
        if ($additionalChargesDue > 0) {
            $msg = 'Additional charges left to pay: £'.number_format($additionalChargesDue, 2).' (see CHARGES tab).';
            $messages[] = $msg;
            $causes[] = ['type' => 'additional', 'label' => 'Additional charges left to pay', 'amount' => $additionalChargesDue, 'message' => $msg];
        }
        if ($pcnDue > 0) {
            $msg = 'PCN left to pay: £'.number_format($pcnDue, 2).'.';
            $messages[] = $msg;
            $causes[] = ['type' => 'pcn', 'label' => 'PCN left to pay', 'amount' => $pcnDue, 'message' => $msg];
        }

        return response()->json([
            'has_pendings' => $hasPendings,
            'additional_charges_total' => round($additionalChargesTotal, 2),
            'additional_charges_paid' => round($additionalChargesPaid, 2),
            'additional_charges_due' => round($additionalChargesDue, 2),
            'pcn_total' => round($pcnTotal, 2),
            'pcn_paid' => round($pcnPaid, 2),
            'pcn_due' => round($pcnDue, 2),
            'pcn_cases' => $pcnCases,
            'pending_rent_due' => round($pendingRentDue, 2),
            'messages' => $messages,
            'causes' => $causes,
        ]);
    }

    /**
     * Compute current pending amounts for a booking (rental, additional charges, PCN).
     */
    private function computePendingsForBooking($bookingId): array
    {
        $booking = RentingBooking::with(['rentingBookingItems.motorbike'])->find($bookingId);
        if (! $booking) {
            return ['rental' => 0.0, 'additional' => 0.0, 'pcn' => 0.0];
        }
        $motorbikeId = $booking->rentingBookingItems->first()?->motorbike_id;

        $additional = (float) DB::table('renting_other_charges')
            ->where('booking_id', $booking->id)
            ->where(function ($q) {
                $q->where('is_paid', 0)->orWhereNull('is_paid');
            })
            ->sum('amount');

        $pcn = 0.0;
        if ($motorbikeId) {
            $pcn = (float) $this->pcnCasesForBooking($booking, $motorbikeId)
                ->where('isClosed', false)
                ->sum('reduced_amount');
        }

        $today = \Carbon\Carbon::today()->toDateString();
        $rental = (float) BookingInvoice::where('booking_id', $booking->id)
            ->where('is_paid', false)
            ->whereDate('invoice_date', '<=', $today)
            ->sum('amount');

        return ['rental' => $rental, 'additional' => $additional, 'pcn' => $pcn];
    }

    private function pcnCasesForBooking(RentingBooking $booking, int $motorbikeId)
    {
        $bookingItem = $booking->rentingBookingItems->first();
        $startDate = optional($booking->start_date)->toDateString();
        $endDate = optional($bookingItem?->end_date)->toDateString();

        $query = PcnCase::where('motorbike_id', $motorbikeId)
            ->where('customer_id', $booking->customer_id);

        if ($startDate) {
            $query->whereDate('date_of_contravention', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date_of_contravention', '<=', $endDate);
        }

        return $query;
    }

    /**
     * List bookings that were ended with "Proceed anyway" (collect despite pendings).
     * Same handling as active/inactive: click row to open detail and collect what's left.
     */
    public function endedWithPendingsBookings()
    {
        $bookingIds = BookingClosing::whereNotNull('collect_proceeded_anyway_user_id')
            ->pluck('booking_id')
            ->unique()
            ->values()
            ->all();

        if (empty($bookingIds)) {
            $bookingDetails = collect();
            $closingsMap = collect();
            $pendingsMap = [];
        } else {
            $closings = BookingClosing::whereNotNull('collect_proceeded_anyway_user_id')
                ->with('collectProceededAnywayUser')
                ->get()
                ->keyBy('booking_id');

            $base = DB::table('renting_bookings as RB')
                ->join('renting_booking_items as RBI', 'RB.id', '=', 'RBI.booking_id')
                ->join('customers as C', 'RB.customer_id', '=', 'C.id')
                ->join('motorbikes as MB', 'RBI.motorbike_id', '=', 'MB.id')
                ->select(
                    'RB.id as BOOKING_ID',
                    'C.id as CUSTOMER_ID',
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
                    'C.email as EMAIL'
                )
                ->whereIn('RB.id', $bookingIds)
                ->whereNotNull('RBI.end_date')
                ->get();

            $pendingsMap = [];
            foreach ($bookingIds as $bid) {
                $pendingsMap[$bid] = $this->computePendingsForBooking($bid);
            }

            $bookingDetails = $base->map(function ($row) use ($closings, $pendingsMap) {
                $row = (object) (array) $row;
                $closing = $closings->get($row->BOOKING_ID);
                $p = $pendingsMap[$row->BOOKING_ID] ?? ['rental' => 0, 'additional' => 0, 'pcn' => 0];
                $row->PROCEEDED_BY = $closing && $closing->collectProceededAnywayUser
                    ? $closing->collectProceededAnywayUser->full_name
                    : '—';
                $row->PROCEEDED_AT = $closing && $closing->collect_proceeded_anyway_at
                    ? $closing->collect_proceeded_anyway_at->format('d/m/Y H:i')
                    : '—';
                $row->RENTAL_LEFT = $p['rental'];
                $row->ADDITIONAL_LEFT = $p['additional'];
                $row->PCN_LEFT = $p['pcn'];
                return $row;
            });
            $closingsMap = $closings;
        }

        return view('admin.renting.ended-with-pendings-bookings', [
            'bookingDetails' => $bookingDetails,
            'closingsMap' => $closingsMap ?? collect(),
            'pendingsMap' => $pendingsMap ?? [],
        ]);
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
            ->where('RB.is_posted', true)
            ->whereNull('RBI.end_date')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('renting_bookings as RB2')
                    ->join('renting_booking_items as RBI2', 'RB2.id', '=', 'RBI2.booking_id')
                    ->whereColumn('RB2.customer_id', 'RB.customer_id')
                    ->whereColumn('RBI2.motorbike_id', 'RBI.motorbike_id')
                    ->whereRaw('RB2.id > RB.id')
                    ->whereNotNull('RBI2.end_date');
            })
            ->get();

        return view('admin.renting.bookings', compact('bookingDetails'));
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
            ->where('RB.is_posted', true)
            ->whereNotNull('RBI.end_date')
            ->get();

        return view('admin.renting.inactive-bookings', compact('bookingDetails'));
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

        return view('admin.renting.bookings_history', compact('bookingHistory', 'customers', 'motorbikes'));
    }

    public function renting_index()
    {
        $motorbikes = Motorbike::all();

        return view('admin.renting.index', compact('motorbikes'));
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
        $invoiceId = $request->input('invoice_id');
        $paymentMethodId = $request->input('payment_method_id');
        $amountReceived = $request->input('amount');

        if (empty($invoiceId)) {
            return response()->json(['error' => 'invoice_id is required'], 422);
        }

        if (empty($amountReceived)) {
            return response()->json(['error' => 'Amount not in Correct Format '.$amountReceived], 400);
        }

        if ($amountReceived <= 0) {
            return response()->json(['error' => 'Invalid amount received'], 400);
        }

        try {
            $result = app(RentalBookingLifecycle::class)->recordPayment(
                (int) $bookingId,
                (int) $invoiceId,
                (int) $paymentMethodId,
                (float) $amountReceived
            );

            $booking = RentingBooking::findOrFail($bookingId);

            return response()->json(array_merge($result, [
                'start_date' => $booking->start_date,
                'deposit' => (float) $booking->deposit,
                'weekly' => (float) optional($booking->rentingBookingItems()->first())->weekly_rent,
                'total' => (float) $result['balance'],
                'paid' => (float) $amountReceived,
            ]));
        } catch (\Throwable $e) {
            $status = str_contains($e->getMessage(), 'pending invoices') ? 200 : 400;

            return response()->json([
                'error' => $e->getMessage(),
                'success' => false,
                'message' => $e->getMessage(),
            ], $status === 200 ? 200 : ($e instanceof \InvalidArgumentException ? 400 : 400));
        }
    }


    public function reverseInvoicePayment(Request $request, $invoiceId)
    {
        try {
            $invoice = BookingInvoice::with('booking')->findOrFail($invoiceId);
            $result = app(RentalBookingLifecycle::class)->reversePayment(
                $invoice,
                $this->resolveAuditUserId()
            );

            return response()->json($result);
        } catch (\Throwable $e) {
            $this->writeReverseInvoiceAudit('rental_invoice_payment_reverse_failed', [
                'invoice_id' => $invoiceId,
                'backpack_user_id' => $this->resolveAuditUserId(),
                'auth_user_id' => optional(auth()->user())->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], str_contains($e->getMessage(), 'No payment') ? 404 : 500);
        }
    }

    // 4.3.2 - Update Record Upon Rental Agreement Generation for Signature
    public function startbooking(Request $request, $bookingId)
    {
        try {
            $resolvedBookingId = $bookingId ?: $request->input('booking_id');
            if (! $resolvedBookingId) {
                return response()->json([
                    'success' => false,
                    'message' => 'booking_id is required',
                ], 422);
            }

            $booking = RentingBooking::findOrFail($resolvedBookingId);
            $result = app(RentalBookingLifecycle::class)->activateRental($booking, force: true);

            return response()->json(array_merge($result, [
                'success' => true,
                'status' => $result['state'],
            ]));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
        try {
            $booking = RentingBooking::findOrFail($bookingId);
            app(RentalBookingLifecycle::class)->abortUnposted($booking);

            return response()->json([
                'success' => true,
                'message' => 'Unposted intake removed.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
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

    public function upsertMotorbikePricing(Request $request)
    {
        $validatedData = $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
            'weekly_price' => 'required|numeric|min:0.01',
            'minimum_deposit' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            RentingPricing::where('motorbike_id', $validatedData['motorbike_id'])
                ->where('iscurrent', true)
                ->update(['iscurrent' => false]);

            $pricing = new RentingPricing($validatedData);
            $pricing->user_id = auth()->id();
            $pricing->iscurrent = true;
            $pricing->update_date = now();
            $pricing->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pricing saved successfully.',
                'pricing' => $pricing,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('upsertMotorbikePricing failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save pricing.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // The function that respond to get reuest to display the new booking page
    /** @return array{motorbikes: \Illuminate\Support\Collection, customers: \Illuminate\Database\Eloquent\Collection, documentTypes: \Illuminate\Database\Eloquent\Collection} */
    public function bookingNewPageData(): array
    {
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
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('renting_booking_items')
                    ->whereColumn('renting_booking_items.motorbike_id', 'MB.id')
                    ->where('renting_booking_items.is_posted', true)
                    ->whereNull('renting_booking_items.end_date');
            })
            ->where(function ($q) {
                $q->where('MB.is_ebike', true)
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

        return [
            'motorbikes' => $motorbikes,
            'customers' => Customer::all(),
            'documentTypes' => DocumentType::all(),
        ];
    }

    public function renting_booking_new()
    {
        \Log::info('New Booking Page Requested.');

        try {
            $data = $this->bookingNewPageData();
        } catch (\Exception $e) {
            \Log::error('Error: '.$e->getMessage());

            return response()->json(['error' => 'An error occurred'], 500);
        }

        return view('admin.renting.booking-new', $data);
    }

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

    public function makeMotorbikeAvailable(Request $request)
    {
        $validatedData = $request->validate([
            'motorbike_id' => 'required|exists:motorbikes,id',
        ]);

        DB::beginTransaction();
        try {
            $openItems = RentingBookingItem::with('booking')
                ->where('motorbike_id', $validatedData['motorbike_id'])
                ->where('is_posted', true)
                ->whereNull('end_date')
                ->get()
                ->filter(function ($item) {
                    return optional($item->booking)->state !== 'Completed & Issued';
                });

            $updated = 0;
            foreach ($openItems as $item) {
                $item->is_posted = false;
                $item->save();

                if ($item->booking && $item->booking->is_posted) {
                    $item->booking->is_posted = false;
                    $item->booking->save();
                }
                $updated++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'updated_count' => $updated,
                'message' => $updated > 0
                    ? 'Motorbike has been made available by clearing stale open entries.'
                    : 'No stale open entries were found. Bike availability is unchanged.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('makeMotorbikeAvailable failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to make motorbike available.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function previewMakeMotorbikeAvailable(Request $request, int $motorbikeId)
    {
        $motorbike = Motorbike::findOrFail($motorbikeId);
        $repair = app(RentalAvailabilityRepair::class);

        return response()->json([
            'success' => true,
            'motorbike_id' => $motorbike->id,
            'reg_no' => $motorbike->reg_no,
            'preview' => $repair->snapshot($motorbikeId),
            'checks' => $repair->checks($motorbikeId),
        ]);
    }

    public function executeMakeMotorbikeAvailable(Request $request, int $motorbikeId)
    {
        $request->validate([
            'confirm_force' => 'required|boolean|accepted',
        ]);

        $motorbike = Motorbike::findOrFail($motorbikeId);
        $repair = app(RentalAvailabilityRepair::class);
        $result = $repair->execute($motorbikeId, $this->resolveAuditUserId());

        return response()->json([
            'success' => true,
            'motorbike_id' => $motorbike->id,
            'reg_no' => $motorbike->reg_no,
            'items_closed' => $result['items_closed'],
            'bookings_updated' => $result['bookings_updated'],
            'repair_actions' => $result['repair_actions'],
            'repair_errors' => $result['repair_errors'],
            'remaining_blockers' => $result['checks']['blockers'],
            'checks' => $result['checks'],
            'message' => $result['message'],
        ]);
    }

    private function resolveAuditUserId(): ?int
    {
        if (function_exists('backpack_auth')) {
            return optional(backpack_auth()->user())->id ?? optional(auth()->user())->id;
        }

        return optional(auth()->user())->id;
    }

    private function writeReverseInvoiceAudit(string $event, array $context): void
    {
        try {
            Log::build([
                'driver' => 'single',
                'path' => storage_path('logs/rental-invoice-reverse.log'),
            ])->info($event, $context);
        } catch (\Throwable $e) {
            Log::warning('reverse_invoice_audit_file_write_failed', [
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }

        Log::info($event, $context);
    }

    private function buildWhatsappStaffSignature(): string
    {
        $user = function_exists('backpack_auth') ? (backpack_auth()->user() ?: auth()->user()) : auth()->user();
        $staffId = optional($user)->id;
        $staffName = trim(((string) optional($user)->first_name).' '.((string) optional($user)->last_name));

        if ($staffName === '') {
            $staffName = (string) (optional($user)->name ?: 'Staff');
        }

        return $staffId ? "{$staffName} (ID: {$staffId})" : $staffName;
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

        return view('admin.motorbikes.pricing', ['pricing' => $pricing, 'motorbikes_not_priced' => $instance->motorbikeNotPriced()]);
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
        return view('admin.finance.agreement');
    }

    public function renting_agreement_template()
    {
        return view('admin.renting.agreement');
    }

    public function updateInvoiceDate(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:booking_invoices,id',
            'new_date' => 'required|date',
        ]);

        try {
            $result = app(RentingInvoiceSyncService::class)->resequenceUnpaidInvoiceDatesFrom(
                (int) $request->invoice_id,
                (string) $request->new_date
            );
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => ((int) $result['updated'] > 1)
                ? 'Invoice date updated and upcoming unpaid invoices realigned weekly.'
                : 'Invoice date updated successfully',
            'invoice' => BookingInvoice::find($request->invoice_id),
            'updated' => $result['updated'],
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

        return view('admin.renting.invoice-dates-all', compact('invoices', 'bookingIds', 'search'));
    }

    public function updateStartDate(Request $request, RentingInvoiceSyncService $syncService)
    {
        $request->validate([
            'booking_id' => 'required|exists:renting_bookings,id',
            'new_start_date' => 'required|date',
        ]);

        $booking = RentingBooking::findOrFail($request->booking_id);

        DB::beginTransaction();
        try {
            $booking->start_date = $request->new_start_date;
            $booking->save();

            $syncResult = $syncService->syncFutureInvoicesForBooking($booking->id);

            DB::commit();

            $message = 'Booking start date updated successfully.';
            if ($syncResult['deleted'] > 0 || $syncResult['created'] > 0) {
                $message .= " {$syncResult['deleted']} invalid future invoice(s) removed, {$syncResult['created']} created.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'booking' => $booking->fresh(),
                'deleted_invoices_count' => $syncResult['deleted'],
                'created_invoices_count' => $syncResult['created'],
                'kept_invoices_count' => $syncResult['kept'],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('updateStartDate failed: '.$e->getMessage(), ['booking_id' => $booking->id]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update start date: '.$e->getMessage(),
            ], 500);
        }
    }

    public function showUpdateStartDateForm()
    {
        $bookings = RentingBooking::with('customer')->get();

        return view('admin.renting.update-start-date', compact('bookings'));
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
            $storePath = $videoFile->storeAs('rental_service_videos', $fileName, 'public');

            $videoRecord = new RentingServiceVideo([
                'booking_id' => $bookingId,
                'video_path' => $storePath,
                'recorded_at' => now(),
            ]);
            $videoRecord->save();

            return response()->json([
                'message' => 'Video uploaded successfully',
                'video' => $videoRecord,
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

        return view('admin.renting.summary-view', compact('booking'));
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
