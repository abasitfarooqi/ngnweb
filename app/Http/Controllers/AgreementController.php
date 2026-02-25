<?php

namespace App\Http\Controllers;

use App\Http\Requests\RentalTerminationRequest;
use App\Mail\CustomerDocumentRequest;
use App\Mail\EmployeeNDA;
use App\Mail\HireContract;
use App\Mail\PurchaseInvoice;
use App\Mail\RentalAgreement;
use App\Mail\RentalAgreementNgn;
use App\Mail\LoyaltySchemePolicy;
use App\Mail\RentalTerminateEmail;
use App\Models\AgreementAccess;
use App\Models\ApplicationItem;
use App\Models\ContractAccess;
use App\Models\Customer;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\DocumentType;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\PurchaseAgreementAccess;
use App\Models\PurchaseUsedVehicle;
use App\Models\RentalTerminateAccess;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\UploadDocumentAccess;
use Carbon\Carbon;
use DateTime;
// use File;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mail;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class AgreementController extends Controller
{
    public function link_Logs()
    {
        $access = UploadDocumentAccess::join('customers', 'upload_document_accesses.customer_id', '=', 'customers.id')
            ->orderBy('upload_document_accesses.created_at', 'desc')
            ->get()
            ->map(function ($access) {
                $access->link = url("/upload-doc/{$access->customer_id}/{$access->passcode}");
                $access->name = $access->first_name.' '.$access->last_name;

                return $access;
            });

        return view('admin.customers.upload-links', compact('access'));
    }

    public function agreement_Logs()
    {
        $access = AgreementAccess::join('customers', 'agreement_accesses.customer_id', '=', 'customers.id')
            ->orderBy('agreement_accesses.created_at', 'desc')
            ->get()
            ->map(function ($access) {
                $access->link = url("/agreement/{$access->customer_id}/{$access->passcode}");
                $access->name = $access->first_name.' '.$access->last_name;

                return $access;
            });

        // admin/customers/agreement-links
        return view('admin.customers.agreement-links', compact('access'));
    }

    public function generateAgreementAccess($customer_id)
    {
        \Log::info('Generating Agreement Access for customer: '.$customer_id);

        $booking_id = request()->query('booking_id');

        \Log::info('Booking ID: '.$booking_id);

        if (! $booking_id) {
            return response()->json([
                'message' => 'Unauthorized access',
            ], 400);
        }

        $rentingBooking = RentingBooking::with('customer')->findOrFail($booking_id);
        $customer = $rentingBooking->customer;

        \Log::info('Renting/Customer Booking Obj: ', [$rentingBooking, $customer]);

        if (! $customer) {
            abort(404, 'Customer not found');
        }

        $passcode = Str::random(12);
        $expiresAt = now()->addDays(1);

        $access = AgreementAccess::create([
            'customer_id' => $customer_id,
            'booking_id' => $booking_id,
            'passcode' => $passcode,
            'expires_at' => $expiresAt,
        ]);

        // $url = route('agreement.show', ['customer_id' => $customer_id, 'passcode' => $passcode]);
        $url = route('agreement.show.ins.5m.extended', ['customer_id' => $customer_id, 'passcode' => $passcode]);

        if ($access) {

            $qrBase64 = '';

            // Temporary block.
            // try {
            //     // Attempt to generate the QR code
            //     $qrImage = QrCode::format('png')->size(200)->generate($url);
            //     $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);
            // } catch (\Exception $e) {
            //     // Log the error and use a default or blank image
            //     \Log::error("The Library is not installed on Server. for QR Generation. Failed to generate QR code: " . $e->getMessage());
            //     $qrBase64 = 'data:image/png;base64,'; // A fallback QR image or keep it empty
            // }

            $data['email'] = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
            $data['title'] = 'Rental Agreement';
            $data['body'] = 'Dear valued customer,

            We kindly request your attention to finalize your booking with Neguinho Motors. To proceed, please click the following link to review and sign the agreement: '.$url.'

            Completing this step is essential to move forward. Thank you for choosing Neguinho Motors for your motorcycle rental needs.';

            $mailData = [
                'title' => $data['title'],
                'body' => $data['body'],
                'url' => $url,
            ];

            try {
                Mail::to($data['email'])->send(new RentalAgreement($mailData));
            } catch (RfcComplianceException $e) {
                Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
            }

            return response()->json([
                'qrImage' => $qrBase64,
                'url' => $url,
            ]);
        }
    }

    // 4.2.3 - Upload Documents Link Generation >>> //
    public function generateDocumentUploadAccess($customer_id)
    {
        \Log::info('Generating Document Upload Link'.$customer_id);
        $booking_id = request()->query('booking_id');
        \Log::info('Booking ID: '.$booking_id);
        if (! $booking_id) {
            return response()->json([
                'message' => 'Unauthorized access',
            ], 400);
        }
        $rentingBooking = RentingBooking::with('customer')->findOrFail($booking_id);
        $customer = $rentingBooking->customer;
        if (! $customer) {
            abort(404, 'Customer not found');
        }
        \Log::info('Customer Obj: ', [$customer]);
        $passcode = Str::random(12);
        $expiresAt = now()->addDays(1);
        $access = UploadDocumentAccess::create([
            'customer_id' => $customer_id,
            'booking_id' => $booking_id,
            'passcode' => $passcode,
            'expires_at' => $expiresAt,
        ]);

        $url = route('uploaddoc.showUploadDocPage.show', ['customer_id' => $customer_id, 'passcode' => $passcode]);

        if ($access) {

            $data['email'] = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
            $data['title'] = 'Documents Upload';
            $data['body'] = 'Dear valued customer,

            We kindly request your attention to finalize your booking with Neguinho Motors. To proceed, please click the following link to upload all pending documents: '.$url.'

            Completing this step is essential to move forward. Thank you for choosing Neguinho Motors for your motorcycle rental needs.';

            $mailData = [
                'title' => $data['title'],
                'body' => $data['body'],
                'url' => $url,
            ];

            try {
                Mail::to($data['email'])->send(new CustomerDocumentRequest($mailData));
            } catch (Exception $e) {

                Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());

                return response()->json(['error' => 'Failed to send email, please check the email address and try again.'], 400);
            }

            return response()->json([
                'success' => true,
                'uploadLink' => $url,
            ]);
        }
    }

    public function show(Request $request, $customer_id, $passcode)
    {
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = Rentingbooking::findOrFail($access->booking_id);
        $customer = Customer::findOrFail($booking->customer_id);
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        // V1
        // return view('signature', compact(

        // V3
        return view('signature-v3', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showV6(Request $request, $customer_id, $passcode)
    {
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = Rentingbooking::findOrFail($access->booking_id);
        $customer = Customer::findOrFail($booking->customer_id);
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        // V1
        // return view('signature', compact(

        // V6
        return view('signature-v6', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showIns(Request $request, $customer_id, $passcode)
    {
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = Rentingbooking::findOrFail($access->booking_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        // V1
        // return view('signature', compact(

        // V3
        return view('signature-v3-ins', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showInsV6(Request $request, $customer_id, $passcode)
    {
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = Rentingbooking::findOrFail($access->booking_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        // V1
        // return view('signature', compact(

        // V3
        return view('signature-v6-ins', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showIns6m(Request $request, $customer_id, $passcode)
    {
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = Rentingbooking::findOrFail($access->booking_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        // V1
        // return view('signature', compact(

        // V3
        return view('signature-v3-ins-6m', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showIns5mExtended(Request $request, $customer_id, $passcode)
    {
        \Log::info('Received Customers store request:', $request->all());
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = Rentingbooking::findOrFail($access->booking_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        // V1
        // return view('signature', compact(

        // V3
        return view('signature-v3-ins-5m-extended', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showRentalTermination(RentalTerminationRequest $request)
    {
        $validated = $request->validated();

        $customer_id = $request->customer_id;
        $booking_id = $request->booking_id;
        $passcode = $request->passcode;

        $access = RentalTerminateAccess::where('customer_id', $customer_id)
            ->where('booking_id', $booking_id)
            ->where('passcode', $passcode)
            ->where('expire_at', '>', now())
            ->first();

        if ($access) {
            // Collect Booking ONE details.
            $Booking = RentingBooking::where('id', $booking_id)->first();

            // BOOKING item
            $bookingItem = RentingBookingItem::where('booking_id', $Booking->id)->first();

            // Motorbike
            $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();

            // Collect Booking Items details.
            $Customer = Customer::where('id', $customer_id)->first();

            // Collect Booking Issuance Details.

            return view('rental-termination-v1', [
                'customer_id' => $customer_id,
                'booking_id' => $booking_id,
                'booking' => $Booking,
                'customer' => $Customer,
                'access' => $access,
                'passcode' => $passcode,
                'bookingItem' => $bookingItem,
                'user_name' => '$Booking->user_id',
                'motorbike' => $motorbike,
            ]);
        } else {

            return response()->view('errors.404', $validated, 404); // Return 404 view with response
        }
    }

    public function postRentalTermination(RentalTerminationRequest $request)
    {
        \Log::info('PostRentalTermination '.
            $request->customer_id.' - '.
            $request->booking_id.' - '.
            $request->passcode.' - '.
            mb_substr($request->sign, 0, 30));

        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'booking_id' => 'required|integer|exists:renting_bookings,id',
            'passcode' => 'required|string',
            'expire_at' => 'require|datetime',
            'sign' => 'required|string|starts_with:data:image/png;base64,', // Ensure the signature is a base64 image
        ]);

        \Log::info('Validation Pass ');

        $customer_id = $request->customer_id;
        $booking_id = $request->booking_id;
        $passcode = $request->passcode;

        $access = RentalTerminateAccess::where('booking_id', $request->booking_id)
            ->where('expire_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        } else {
            \Log::info('VALID?');
        }

        $Booking = RentingBooking::where('id', $booking_id)->first();

        // BOOKING item
        $bookingItem = RentingBookingItem::where('booking_id', $Booking->id)->first();
        // Motorbike
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        // Collect Booking Items details.
        $Customer = Customer::where('id', $customer_id)->first();

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);

        $fileName = $Customer->first_name.'-'.$Customer->last_name.'-'.Carbon::now()->format('Y-m-d H-i-s').'.'.'jpg';

        Storage::disk('private')->put($fileName, base64_decode($file_data));

        \Log::info('Creating New Termination Letter');

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/private/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Contract Termination';
        $data['body'] = '';

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdf = Pdf::loadView('pdf.rental-termination-v1', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $motorbike,
            'bookingItem' => $bookingItem,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/Rental-Termination-'.$tm.$rand_no.'.pdf');

        $documentType = DocumentType::where('name', 'Rental Termination')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-".$tm.$rand_no.'.pdf';

        $customerAgreement = new CustomerAgreement([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => 1,
            'file_name' => 'finance-contract-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->save();

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new RentalTerminateEmail($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = RentalTerminateAccess::where('booking_id', $request->booking_id)
            ->first();

        \Log::info('Access Obj: ', [$access]);

        $access->expire_at = new DateTime;

        $access->save();

        return response()->json([
            'message' => 'Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function showContract(Request $request, $customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);

        $customer = Customer::findOrFail($booking->customer_id);

        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();

        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();

        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        if ($booking->is_used) {
            return view('signature-contract-v3-used', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        }
        // dd($booking->is_used);

        // New Version - After -SEP-2024
        return view('signature-contract-v3', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showContractIns(Request $request, $customer_id, $passcode)
    {
        \Log::info('Received Customers store request:', $request->all());

        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        if ($booking->is_used) {
            return view('signature-contract-v3-ins-used', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        }

        // Old Version - Before 22-JULY-2024
        // return view('signature-contract', compact(

        // New Version - After 22-JULY-2024
        return view('signature-contract-v3-ins', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showContract18mExtended(Request $request, $customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        if ($booking->is_used_extended) {
            return view('signature-contract-v5-used-18-extended', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        }

        // Old Version - Before 22-JULY-2024
        // return view('signature-contract', compact(

        // New Version - After -SEP-2024
        return view('signature-contract-v5-18-extended', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // show contract for insurance / pcn 18/Months Extended
    public function showContractIns18mExtended(Request $request, $customer_id, $passcode)
    {
        \Log::info('Received Customers store request:', $request->all());

        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        if ($booking->is_used_extended) {
            return view('signature-contract-v3-ins-used-18-extended', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        }

        // Old Version - Before 22-JULY-2024
        // return view('signature-contract', compact(

        // New Version - After 22-JULY-2024
        return view('signature-contract-v3-ins-18-extended', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // show contract for insurance / pcn 18/Months Extended
    public function showContractIns18mExtendedCustom(Request $request, $customer_id, $passcode)
    {
        \Log::info('Received Customers store request:', $request->all());

        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        if ($booking->is_used_extended) {
            return view('signature-contract-v3-ins-used-18-extended', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        } elseif ($booking->is_used_extended_custom) {
            return view('signature-contract-v3-ins-used-18-extended-custom', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        }

        // Old Version - Before 22-JULY-2024
        // return view('signature-contract', compact(

        // New Version - After 22-JULY-2024
        return view('signature-contract-v3-ins-18-extended', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showContractIns5mExtended(Request $request, $customer_id, $passcode)
    {
        \Log::info('Received Customers store request:', $request->all());

        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);
        \Log::info('booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        if (! $bookingItem) {
            abort(404, 'Motocycle is not selected?');
        }
        \Log::info('booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        if ($booking->is_used) {
            return view('signature-contract-v3-ins-5m-extended-used', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        } elseif ($booking->is_used_extended_custom) {
            return view('signature-contract-v3-ins-5m-extended-used-custom-18m-t3', compact(
                'booking',
                'customer',
                'bookingItem',
                'SIGFILE',
                'user_name',
                'today',
                'motorbike',
                'customer_id',
                'passcode',
                'access'
            ));
        }

        // Old Version - Before 22-JULY-2024
        // return view('signature-contract', compact(

        // New Version - After 22-JULY-2024
        return view('signature-contract-v3-ins-5m-extended', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // 2025 12-SEP-2025 - Latest Contract

    public function showContractLatest($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        return view('signature-contract-v6-latest', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // 2025 12-SEP-2025 - Latest Contract
    public function createNewContractLatest(Request $request)
    {
        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        $Booking = FinanceApplication::findOrFail($request->booking_id);
        $Customer = Customer::findOrFail($Booking->customer_id);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Sale Contract - Latest';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // Determine PDF template
        $pdf_name = 'pdf.contract-v6-latest';

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-latest-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-latest-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-latest-'.$tm.$rand_no.'.pdf');

        // // SFTP upload
        $absoluteLocalPath = storage_path('app/public/customers/' . $Booking->customer_id . '/finance-contract-latest-' . $tm . $rand_no . '.pdf');
        \Log::info("📁 Local file saved at: " . $absoluteLocalPath);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info("📤 Actual remote mirror path: " . $success);

        if (!$success) {
            \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        // Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            // SFTP upload for battery safety leaflet
            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/' . $Booking->customer_id . '/battery-safety-leaflet-' . $tm . $rand_no . '.pdf');
            \Log::info("📁 Battery Safety Leaflet saved at: " . $batterySafetyAbsoluteLocalPath);
            $batterySafetySuccess = $syncService->uploadFile($batterySafetyAbsoluteLocalPath);
            \Log::info("📤 Battery Safety Leaflet remote mirror path: " . $batterySafetySuccess);

            if (!$batterySafetySuccess) {
                \Log::warning("Uploaded battery safety leaflet locally but failed to sync to remote domain: $batterySafetyAbsoluteLocalPath");
            }

            // Send Battery Safety Leaflet PDF to customer only
            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email_company'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new HireContract($batterySafetyData));
                Mail::to($batterySafetyData['email_company'])->send(new HireContract($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Latest Agreement signed successfully. You can close this window. A copy will be sent to your email.',
        ]);
    }

    public function showContractInsLatest($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        return view('signature-contract-v6-ins-latest', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // 2025 12-SEP-2025 - Latest Contract
    public function createNewContractInsLatest(Request $request)
    {
        \Log::info('Creating new Insurance agreement - Latest', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorised access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        \Log::info('Image saved at: '.storage_path('app/public/'.$fileName));

        \Log::info('Creating new Latest Insurance Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        $Customer = Customer::findOrFail($Booking->customer_id);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = now()->format('d/m/Y');

        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Sale Contract - Latest';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdf_name = $Booking->is_used_latest
            ? 'pdf.contract-v6-ins-used-latest'
            : 'pdf.contract-v6-ins-latest';

        // Customer And Us Single PDF
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();
        $path = "customers/{$Booking->customer_id}/finance-contract-ins-latest-".$tm.$rand_no.'.pdf';
        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-latest-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);
        // Customer And Us Single PDF Document Number
        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Customer And Us Single PDF
        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-latest-'.$tm.$rand_no.'.pdf');

        // Customer And Us Single PDF
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-latest-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        // Customer And Us Single Email Settings
        $data['title'] = 'Sale Contract Latest';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! Find Attached your rental agreement. ';
        $data['pdf'] = $pdf;

        // Three PDF Generation
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        $less_terms_pdf_name = $Booking->is_used_latest
            ? 'pdf.contract-v6-ins-used-latest-less-terms'
            : 'pdf.contract-v6-ins-latest-less-terms';

        $contractDates = [
            ['start' => $contractStartDate, 'end' => $contractEndDate1],
            ['start' => $contractEndDate1, 'end' => $contractEndDate2],
            ['start' => $contractEndDate2, 'end' => $contractEndDate3],
        ];

        $less_terms_pdf_data = [];
        foreach ($contractDates as $index => $dates) {
            $pdfFileName = '/finance-contract-ins-latest-less-terms-'.$index.'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = Pdf::loadView($less_terms_pdf_name, [
                'today' => $dates['start'], // Carbon instance
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$customerAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'], // Carbon instance
                'contractEndDate' => $dates['end'],   // Carbon instance
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);

            $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.$pdfFileName);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absoluteLocalPath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
            }

            $less_terms_pdf_data[] = $less_terms_pdf;
        }

        $email_data = [];
        $email_data['title'] = 'Sale Contract - PCN/INS - Internal';
        $email_data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! <br> Find Attached your rental agreement. ';
        $email_data['pdf'] = $less_terms_pdf_data;

        try {
            // sending finance contract pdfs to customer
            Mail::to($data['email'])->send(new HireContract($data));

            // sending ins pdfs to only customerservice@neguinhomotors.co.uk
            Mail::to(['customerservice@neguinhomotors.co.uk'])->send(new HireContract($email_data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        // Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            // SFTP upload for battery safety leaflet
            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            \Log::info('📁 Battery Safety Leaflet saved at: '.$batterySafetyAbsoluteLocalPath);
            $batterySafetySuccess = $syncService->uploadFile($batterySafetyAbsoluteLocalPath);
            \Log::info('📤 Battery Safety Leaflet remote mirror path: '.$batterySafetySuccess);

            if (! $batterySafetySuccess) {
                \Log::warning("Uploaded battery safety leaflet locally but failed to sync to remote domain: $batterySafetyAbsoluteLocalPath");
            }

            // Send Battery Safety Leaflet PDF to customer only
            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email_company'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new HireContract($batterySafetyData));
                Mail::to($batterySafetyData['email_company'])->send(new HireContract($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Insurance Latest Agreement signed successfully. You can close this window. A copy will be sent to your email.',
        ]);
    }

    // 2025 12-SEP-2025 - Latest Insurance Contract (Used Vehicle)
    public function showContractInsUsedLatest($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        return view('signature-contract-v6-ins-used-latest', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // 2025 12-SEP-2025 - Create Latest Insurance Contract (Used Vehicle)
    public function createNewContractInsUsedLatest(Request $request)
    {
        \Log::info('Creating new Insurance agreement - Used Vehicle Latest createNewContractInsUsedLatest');

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        \Log::info('Image saved at: '.storage_path('app/public/'.$fileName));

        $Booking = FinanceApplication::findOrFail($request->booking_id);
        $Customer = Customer::findOrFail($Booking->customer_id);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = now()->format('d/m/Y');

        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Sale Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdf_name = 'pdf.contract-v6-ins-used-latest';

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-ins-used-latest-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-used-latest-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-used-latest-'.$tm.$rand_no.'.pdf');

        // SFTP upload
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-used-latest-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        // Three PDF Generation
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        $less_terms_pdf_name = 'pdf.contract-v6-ins-used-latest-less-terms';

        $contractDates = [
            ['start' => $contractStartDate, 'end' => $contractEndDate1],
            ['start' => $contractEndDate1, 'end' => $contractEndDate2],
            ['start' => $contractEndDate2, 'end' => $contractEndDate3],
        ];

        $less_terms_pdf_data = [];
        foreach ($contractDates as $index => $dates) {
            $pdfFileName = '/finance-contract-ins-latest-less-terms-'.$index.'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = Pdf::loadView($less_terms_pdf_name, [
                'today' => $dates['start'], // Carbon instance
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$customerAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'], // Carbon instance
                'contractEndDate' => $dates['end'],   // Carbon instance
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);

            $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.$pdfFileName);
            \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
            $syncService = app(\App\Services\FtpSyncService::class);
            $success = $syncService->uploadFile($absoluteLocalPath);
            \Log::info('📤 Actual remote mirror path: '.$success);

            if (! $success) {
                \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
            }

            $less_terms_pdf_data[] = $less_terms_pdf;
        }

        $email_data = [];
        $email_data['title'] = 'Sale Contract - PCN/INS - Internal';
        $email_data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! <br> Find Attached your rental agreement. ';
        $email_data['pdf'] = $less_terms_pdf_data;

        try {
            Mail::to($data['email'])->send(new HireContract($data));

            // sending ins pdfs to only customerservice@neguinhomotors.co.uk
            Mail::to(['customerservice@neguinhomotors.co.uk'])->send(new HireContract($email_data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        // Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            // SFTP upload for battery safety leaflet
            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            \Log::info('📁 Battery Safety Leaflet saved at: '.$batterySafetyAbsoluteLocalPath);
            $batterySafetySuccess = $syncService->uploadFile($batterySafetyAbsoluteLocalPath);
            \Log::info('📤 Battery Safety Leaflet remote mirror path: '.$batterySafetySuccess);

            if (! $batterySafetySuccess) {
                \Log::warning("Uploaded battery safety leaflet locally but failed to sync to remote domain: $batterySafetyAbsoluteLocalPath");
            }

            // Send Battery Safety Leaflet PDF to customer only
            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email_company'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new HireContract($batterySafetyData));
                Mail::to($batterySafetyData['email_company'])->send(new HireContract($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Insurance Used Latest Agreement signed successfully. You can close this window. A copy will be sent to your email.',
        ]);
    }

    // 2025 12-SEP-2025 - Latest Contract (Used Vehicle)
    public function showContractUsedLatest($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        return view('signature-contract-v6-used-latest', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    // 2025 12-SEP-2025 - Create Latest Contract (Used Vehicle)
    public function createNewContractUsedLatest(Request $request)
    {
        \Log::info('Creating new agreement - Used Vehicle Latest', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.now()->format('Y-m-d_H-i-s').'.jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        \Log::info('Image saved at: '.storage_path('app/public/'.$fileName));

        $Booking = FinanceApplication::findOrFail($request->booking_id);
        $Customer = Customer::findOrFail($Booking->customer_id);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = now()->format('d/m/Y');

        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Sale Contract - Used Latest';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdf_name = 'pdf.contract-v6-used-latest';

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-used-latest-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-used-latest-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-used-latest-'.$tm.$rand_no.'.pdf');

        // SFTP upload
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-used-latest-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("Uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

            // Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            // SFTP upload for battery safety leaflet
            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            \Log::info('📁 Battery Safety Leaflet saved at: '.$batterySafetyAbsoluteLocalPath);
            $batterySafetySuccess = $syncService->uploadFile($batterySafetyAbsoluteLocalPath);
            \Log::info('📤 Battery Safety Leaflet remote mirror path: '.$batterySafetySuccess);

            if (! $batterySafetySuccess) {
                \Log::warning("Uploaded battery safety leaflet locally but failed to sync to remote domain: $batterySafetyAbsoluteLocalPath");
            }

            // Send Battery Safety Leaflet PDF to customer only
            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email_company'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new HireContract($batterySafetyData));
                Mail::to($batterySafetyData['email_company'])->send(new HireContract($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Used Latest Agreement signed successfully. You can close this window. A copy will be sent to your email.',
        ]);
    }

    /**
     * Get subscription option details
     * TODO: Replace with database query when subscription options table is identified
     */
    private function getSubscriptionOptionDetails($option)
    {
        $options = [
            'A' => ['price' => 299.99, 'text' => 'Group A - £299.99/month'],
            'B' => ['price' => 399.99, 'text' => 'Group B - £399.99/month'],
            'C' => ['price' => 549.99, 'text' => 'Group C - £549.99/month'],
            'D' => ['price' => 649.99, 'text' => 'Group D - £649.99/month'],
        ];

        return $options[$option] ?? null;
    }

    /**
     * Show merged contracts view (Sale + Subscription) - New Vehicle, Without Insurance
     */
    public function showMergedContractsNew($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        
        // Verify subscription is enabled
        if (!$booking->is_subscription) {
            abort(403, 'Subscription contract is not enabled for this application.');
        }

        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        // Get subscription option details
        $subscriptionOption = $this->getSubscriptionOptionDetails($booking->subscription_option);

        return view('signature-contract-v6-merged-new', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access',
            'subscriptionOption'
        ));
    }

    /**
     * Show merged contracts view (Sale + Subscription) - Used Vehicle, Without Insurance
     */
    public function showMergedContractsUsed($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        
        // Verify subscription is enabled
        if (!$booking->is_subscription) {
            abort(403, 'Subscription contract is not enabled for this application.');
        }

        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        // Get subscription option details
        $subscriptionOption = $this->getSubscriptionOptionDetails($booking->subscription_option);

        return view('signature-contract-v6-merged-used', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access',
            'subscriptionOption'
        ));
    }

    /**
     * Show merged contracts view (Sale + Subscription) - New Vehicle, With Insurance
     */
    public function showMergedContractsNewIns($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        
        // Verify subscription is enabled
        if (!$booking->is_subscription) {
            abort(403, 'Subscription contract is not enabled for this application.');
        }

        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        // Get subscription option details
        $subscriptionOption = $this->getSubscriptionOptionDetails($booking->subscription_option);

        return view('signature-contract-v6-merged-new-ins', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access',
            'subscriptionOption'
        ));
    }

    /**
     * Show merged contracts view (Sale + Subscription) - Used Vehicle, With Insurance
     */
    public function showMergedContractsUsedIns($customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $booking = FinanceApplication::findOrFail($access->application_id);
        
        // Verify subscription is enabled
        if (!$booking->is_subscription) {
            abort(403, 'Subscription contract is not enabled for this application.');
        }

        $customer = $booking->customer;
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::find($bookingItem->motorbike_id);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;
        $today = now()->format('d/m/Y');
        $SIGFILE = '#';

        // Get subscription option details
        $subscriptionOption = $this->getSubscriptionOptionDetails($booking->subscription_option);

        return view('signature-contract-v6-merged-used-ins', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access',
            'subscriptionOption'
        ));
    }

    /**
     * Create merged contracts (Sale + Subscription) - Without Insurance
     */
    public function createMergedContracts(Request $request)
    {
        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        $Booking = FinanceApplication::findOrFail($request->booking_id);
        
        // Verify subscription is enabled
        if (!$Booking->is_subscription) {
            abort(403, 'Subscription contract is not enabled for this application.');
        }

        $Customer = Customer::findOrFail($Booking->customer_id);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = now()->format('d/m/Y');
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractStartTime = $Booking->contract_date ? Carbon::parse($Booking->contract_date)->format('H:i') : '';

        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (!File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $rand_no = rand(1, 99999);
        $tm = time();

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        // Get subscription option details
        $subscriptionOption = $this->getSubscriptionOptionDetails($Booking->subscription_option);

        // 1. Generate Sale Contract PDF
        $salePdfName = $Booking->is_used_latest ? 'pdf.contract-v6-used-latest' : 'pdf.contract-v6-latest';
        $salePath = "customers/{$Booking->customer_id}/finance-contract-latest-".$tm.$rand_no.'.pdf';
        
        $saleAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-latest-'.time().$rand_no.'.pdf',
            'file_path' => $salePath,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $saleAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($saleAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $salePdf = Pdf::loadView($salePdfName, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $saleAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-latest-'.$tm.$rand_no.'.pdf');

        // SFTP upload for sale contract
        $saleAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-latest-'.$tm.$rand_no.'.pdf');
        $syncService = app(\App\Services\FtpSyncService::class);
        $syncService->uploadFile($saleAbsoluteLocalPath);

        // 2. Generate Subscription Contract PDF
        $subscriptionPath = "customers/{$Booking->customer_id}/subscription-contract-".$tm.$rand_no.'.pdf';
        
        $subscriptionAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'subscription-contract-'.time().$rand_no.'.pdf',
            'file_path' => $subscriptionPath,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $subscriptionAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-SUB-".str_pad($subscriptionAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $subscriptionPdf = Pdf::loadView('pdf.contract-v6-subscription', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $subscriptionAgreement->document_number,
            'subscriptionOption' => $subscriptionOption,
            'contractStartDate' => $contractStartDate,
            'contractStartTime' => $contractStartTime,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/subscription-contract-'.$tm.$rand_no.'.pdf');

        // SFTP upload for subscription contract
        $subscriptionAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/subscription-contract-'.$tm.$rand_no.'.pdf');
        $syncService->uploadFile($subscriptionAbsoluteLocalPath);

        // 3. Generate PCN PDFs (3 PDFs for company - always generate regardless of ins/non-ins)
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        $less_terms_pdf_name = $Booking->is_used_latest
            ? 'pdf.contract-v6-ins-used-latest-less-terms'
            : 'pdf.contract-v6-ins-latest-less-terms';

        $contractDates = [
            ['start' => $contractStartDate, 'end' => $contractEndDate1],
            ['start' => $contractEndDate1, 'end' => $contractEndDate2],
            ['start' => $contractEndDate2, 'end' => $contractEndDate3],
        ];

        $less_terms_pdf_data = [];
        foreach ($contractDates as $index => $dates) {
            $pdfFileName = '/finance-contract-ins-latest-less-terms-'.$index.'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = Pdf::loadView($less_terms_pdf_name, [
                'today' => $dates['start'],
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$saleAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'],
                'contractEndDate' => $dates['end'],
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);

            $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.$pdfFileName);
            $syncService->uploadFile($absoluteLocalPath);
            $less_terms_pdf_data[] = $less_terms_pdf;
        }

        // 4. Send emails with both PDFs to customer
        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Sale Contract & 12-Month Subscription Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Please find attached your Sale Contract and 12-Month Subscription Contract. Ride safe and enjoy the journey!';
        $data['pdf'] = [$salePdf, $subscriptionPdf];

        // Send 3 PCN PDFs to company
        $email_data = [];
        $email_data['email'] = ['customerservice@neguinhomotors.co.uk'];
        $email_data['title'] = 'Sale Contract - PCN/INS - Internal';
        $email_data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! <br> Find Attached your rental agreement.';
        $email_data['pdf'] = $less_terms_pdf_data;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
            Mail::to($email_data['email'])->send(new HireContract($email_data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        // 4. Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            $syncService->uploadFile($batterySafetyAbsoluteLocalPath);

            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email_company'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new HireContract($batterySafetyData));
                Mail::to($batterySafetyData['email_company'])->send(new HireContract($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Merged contracts signed successfully. You can close this window. Copies will be sent to your email.',
        ]);
    }

    /**
     * Create merged contracts (Sale + Subscription) - With Insurance
     */
    public function createMergedContractsIns(Request $request)
    {
        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        $Booking = FinanceApplication::findOrFail($request->booking_id);
        
        // Verify subscription is enabled
        if (!$Booking->is_subscription) {
            abort(403, 'Subscription contract is not enabled for this application.');
        }

        $Customer = Customer::findOrFail($Booking->customer_id);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = now()->format('d/m/Y');
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractStartTime = $Booking->contract_date ? Carbon::parse($Booking->contract_date)->format('H:i') : '';

        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (!File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $rand_no = rand(1, 99999);
        $tm = time();

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        // Get subscription option details
        $subscriptionOption = $this->getSubscriptionOptionDetails($Booking->subscription_option);

        // 1. Generate Sale Contract PDF (with insurance)
        $salePdfName = $Booking->is_used_latest 
            ? 'pdf.contract-v6-ins-used-latest' 
            : 'pdf.contract-v6-ins-latest';
        $salePath = "customers/{$Booking->customer_id}/finance-contract-ins-latest-".$tm.$rand_no.'.pdf';
        
        $saleAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-latest-'.time().$rand_no.'.pdf',
            'file_path' => $salePath,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $saleAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($saleAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $salePdf = Pdf::loadView($salePdfName, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $saleAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-latest-'.$tm.$rand_no.'.pdf');

        $saleAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-latest-'.$tm.$rand_no.'.pdf');
        $syncService = app(\App\Services\FtpSyncService::class);
        $syncService->uploadFile($saleAbsoluteLocalPath);

        // 2. Generate Subscription Contract PDF
        $subscriptionPath = "customers/{$Booking->customer_id}/subscription-contract-".$tm.$rand_no.'.pdf';
        
        $subscriptionAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'subscription-contract-'.time().$rand_no.'.pdf',
            'file_path' => $subscriptionPath,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $subscriptionAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-SUB-".str_pad($subscriptionAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $subscriptionPdf = Pdf::loadView('pdf.contract-v6-subscription', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $subscriptionAgreement->document_number,
            'subscriptionOption' => $subscriptionOption,
            'contractStartDate' => $contractStartDate,
            'contractStartTime' => $contractStartTime,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/subscription-contract-'.$tm.$rand_no.'.pdf');

        $subscriptionAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/subscription-contract-'.$tm.$rand_no.'.pdf');
        $syncService->uploadFile($subscriptionAbsoluteLocalPath);

        // 3. Generate PCN PDFs (3 PDFs for insurance contracts)
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        $less_terms_pdf_name = $Booking->is_used_latest
            ? 'pdf.contract-v6-ins-used-latest-less-terms'
            : 'pdf.contract-v6-ins-latest-less-terms';

        $contractDates = [
            ['start' => $contractStartDate, 'end' => $contractEndDate1],
            ['start' => $contractEndDate1, 'end' => $contractEndDate2],
            ['start' => $contractEndDate2, 'end' => $contractEndDate3],
        ];

        $less_terms_pdf_data = [];
        foreach ($contractDates as $index => $dates) {
            $pdfFileName = '/finance-contract-ins-latest-less-terms-'.$index.'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = Pdf::loadView($less_terms_pdf_name, [
                'today' => $dates['start'],
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$saleAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'],
                'contractEndDate' => $dates['end'],
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);

            $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.$pdfFileName);
            $syncService->uploadFile($absoluteLocalPath);
            $less_terms_pdf_data[] = $less_terms_pdf;
        }

        // 4. Send emails with both PDFs to customer
        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Sale Contract & 12-Month Subscription Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Please find attached your Sale Contract and 12-Month Subscription Contract. Ride safe and enjoy the journey!';
        $data['pdf'] = [$salePdf, $subscriptionPdf];

        // Send 3 PCN PDFs to company
        $email_data = [];
        $email_data['email'] = ['customerservice@neguinhomotors.co.uk'];
        $email_data['title'] = 'Sale Contract - PCN/INS - Internal';
        $email_data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! <br> Find Attached your rental agreement.';
        $email_data['pdf'] = $less_terms_pdf_data;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
            Mail::to($email_data['email'])->send(new HireContract($email_data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        // 5. Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            $syncService->uploadFile($batterySafetyAbsoluteLocalPath);

            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email_company'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new HireContract($batterySafetyData));
                Mail::to($batterySafetyData['email_company'])->send(new HireContract($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Merged contracts signed successfully. You can close this window. Copies will be sent to your email.',
        ]);
    }

    public function showContractInsM(Request $request, $customer_id, $passcode)
    {
        $access = ContractAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail($access->application_id);
        if (! $booking->is_monthly) {
            abort(403, 'You cannot do monthly. Please contact sales');
        }

        $customer = Customer::findOrFail($booking->customer_id);
        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        return view('signature-contract-v4-ins-m', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function showPurchaseInvoice(Request $request, $purchase_id, $passcode)
    {
        \Log::info("Received Customer's invoice request:", $request->all());

        $access = PurchaseAgreementAccess::where('purchase_id', $purchase_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $purchase_id = $purchase_id;

        $sell = PurchaseUsedVehicle::find($purchase_id);

        if (! $sell) {
            abort(404, 'Sale information not found.');
        }

        return view('purchase-invoice-review', compact('sell', 'access', 'purchase_id'));
    }

    public function showUploadDocPage(Request $request, $customer_id, $passcode)
    {
        $access = UploadDocumentAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';
        $booking = Rentingbooking::findOrFail($access->booking_id);
        
        $customer = Customer::findOrFail($booking->customer_id);
        
        $bookingItem = RentingbookingItem::where('booking_id', $booking->id)->first();
        
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        return view('upload_documents', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function employeeNda(Request $request)
    {
        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);

        $fileName = $request->employeeName.'-'.Carbon::now()->format('YmdHis').'.jpg';
        $filePath = 'employee/'.$fileName;

        // Attempt to save the image and log success or failure
        try {
            $saved = Storage::disk('public')->put($filePath, base64_decode($file_data));
            if ($saved) {
                Log::info('Signature image saved successfully at '.Storage::disk('public')->path($filePath));
            } else {
                Log::error('Failed to save signature image.');
            }
        } catch (\Exception $e) {
            Log::error('Error saving signature image: '.$e->getMessage());
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdfPath = storage_path('app/public/employee/');

        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $pdf = Pdf::loadView('pdf.employee-sign', [
            'today' => $toDay,
            'date' => $request->date,
            'customer' => $request->employeeName,
            'address' => $request->address,
            'email' => $request->email,
            'SIGFILE' => $filePath,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/nda-employee-'.$request->employeeName.$tm.$rand_no.'.pdf');

        $path = 'employee/nda-employee-'.$request->employeeName.$tm.$rand_no.'.pdf';

        $data['pdf'] = $pdf;

        try {
            Mail::to('thiago@neguinhomotors.co.uk', $request->email)->send(new EmployeeNDA($data));
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        return response()->json([
            'message' => 'Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function showContractTest(Request $request)
    {
        \Log::info('Creating new Finance agreement', $request->all());

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = FinanceApplication::findOrFail(21);

        $customer = Customer::findOrFail($booking->customer_id);

        $bookingItem = ApplicationItem::where('application_id', $booking->id)->first();

        $motorbike = Motorbike::where('id', 46)->first();

        $user_name = 'DEMO NAME'; // $booking->user->first_name . ' ' . $booking->user->last_name;

        return view('pdf.contract-v4-test-print', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            // 'customer_id',
        ));
    }

    public function createNewContract(Request $request)
    {
        \Log::info('Creating new Finance agreement', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now().'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Sale Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // // 19Feb 2025 - Added Used Vehicle where template is different
        // // 19Feb 2025 - Added Used Vehicle where template is different
        if ($Booking->is_used) {
            $pdf_name = 'pdf.contract-v5-used';
        } else {
            $pdf_name = 'pdf.contract-v5';
        }

        // $dompdf->set_option("isPhpEnabled", true);

        // OLD VERSION - Before 22-JULY-2024
        // $pdf = Pdf::loadView('pdf.contract', [

        // NEW VERSION - After X-SEP-2024
        // $pdf = Pdf::loadView('pdf.contract-v3', [

        // 12-20-2024 12-dec-2024 Added Watermark

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);
        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-'.$tm.$rand_no.'.pdf');

        // SFTP upload for finance contract PDF (same as other places)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // Purchase invoice PDF
    public function createNewInvoice(Request $request)
    {
        \Log::info('Creating new Purchase INVOICE', $request->all());

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);

        $fileName = 'inv'.'-'.Carbon::now().'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        $purchase_id = $request->purchase_id;
        $sell = PurchaseUsedVehicle::findOrFail($purchase_id);

        $sell->account_name = $request->account_holder_name;
        $sell->account_number = $request->account_number;
        $sell->sort_code = $request->sort_code;
        $sell->save();

        $data['email'] = [$sell->email, 'customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$sell->email];
        $data['title'] = 'Purchase Invoice';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';
        $data['purchase_id'] = $purchase_id;

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdfPath = storage_path('app/public/'.$purchase_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        $pdf = Pdf::loadView('pdf.purchase-invoice-pdf', [
            'SIGFILE' => $fileName,
            'today' => $toDay,
            'req' => $request,
            'sell' => $sell,
            'purchase_id' => $purchase_id,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/purchase-invoice-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$purchase_id.'/purchase-invoice-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new PurchaseInvoice($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // For Insurance and PCN Finance contract
    public function createNewContractIns(Request $request)
    {
        \Log::info('Creating new agreement', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        $filePath = 'public/'.$fileName; // Corrected file path format
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // Log the image save location
        \Log::info('Image saved at: '.storage_path('app/'.$filePath));

        // log
        \Log::info('Creating new Contract');

        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        // insurance contract only we get.
        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Hire / Sale Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        if ($Booking->is_used) {
            $pdf_name = 'pdf.contract-v3-ins-used';
        } else {
            $pdf_name = 'pdf.contract-v3-ins';
        }

        // OLD VERSION - Before 22-JULY-2024
        // $pdf = Pdf::loadView('pdf.contract', [

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-ins-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // NEW VERSION - After -SEP-2024
        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // For Finance contract 18/Months Extended
    public function createNewContract18mExtended(Request $request)
    {
        \Log::info('Creating new Finance agreement 18/Months Extended', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now().'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Hire / Sale Contract 18/Months Extended';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // // 22-May-2025 - Added Used Vehicle where template is different
        if ($Booking->is_used_extended) {
            $pdf_name = 'pdf.contract-v5-used-18m-extended';
        } else {
            $pdf_name = 'pdf.contract-v5-18m-extended';
        }

        // $dompdf->set_option("isPhpEnabled", true);

        // OLD VERSION - Before 22-JULY-2024
        // $pdf = Pdf::loadView('pdf.contract', [

        // NEW VERSION - After X-SEP-2024
        // $pdf = Pdf::loadView('pdf.contract-v3', [

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-18m-extended-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-18m-extended-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // 12-20-2024 12-dec-2024 Added Watermark
        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-18m-extended-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-18m-extended-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // For Insurance and PCN Finance contract 18/Months Extended
    public function createNewContractIns18mExtended(Request $request)
    {
        \Log::info('Creating new agreement', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        $filePath = 'public/'.$fileName; // Corrected file path format
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // Log the image save location
        \Log::info('Image saved at: '.storage_path('app/'.$filePath));

        // log
        \Log::info('Creating new Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        // insurance contract only we get.
        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Hire / Sale Contract 18/Months Extended';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        if ($Booking->is_used) {
            $pdf_name = 'pdf.contract-v3-ins-used-18m-extended';
        } elseif ($Booking->is_used_extended_custom) {
            $pdf_name = 'pdf.contract-v3-ins-used-18m-extended-custom';
        } else {
            $pdf_name = 'pdf.contract-v3-ins-18m-extended';
        }

        // OLD VERSION - Before 22-JULY-2024
        // $pdf = Pdf::loadView('pdf.contract', [

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-ins-18m-extended-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-18m-extended-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // NEW VERSION - After -SEP-2024
        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-18m-extended-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // For Insurance and PCN Finance contract 18/Months Extended
    public function createNewContractIns18mExtendedCustom(Request $request)
    {
        \Log::info('Creating new agreement', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        $filePath = 'public/'.$fileName; // Corrected file path format
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // Log the image save location
        \Log::info('Image saved at: '.storage_path('app/'.$filePath));

        // log
        \Log::info('Creating new Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        // insurance contract only we get.
        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Hire / Sale Contract 18/Months Extended';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        if ($Booking->is_used) {
            $pdf_name = 'pdf.contract-v3-ins-used-18m-extended';
        } elseif ($Booking->is_used_extended_custom) {
            $pdf_name = 'pdf.contract-v3-ins-used-18m-extended-custom';
        } else {
            $pdf_name = 'pdf.contract-v3-ins-18m-extended';
        }

        // OLD VERSION - Before 22-JULY-2024
        // $pdf = Pdf::loadView('pdf.contract', [

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-ins-18m-extended-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-18m-extended-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // NEW VERSION - After -SEP-2024
        $pdf = Pdf::loadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-18m-extended-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-18m-extended-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // For Insurance and PCN Finance contract 5 months extended
    public function createNewContractIns5mExtended(Request $request)
    {
        \Log::info('Creating new agreement', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.jpg';
        $filePath = 'public/'.$fileName; // Corrected file path format
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // Log the image save location
        \Log::info('Image saved at: '.storage_path('app/'.$filePath));

        // log
        \Log::info('Creating new Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        // $toDay = new DateTime();
        // $today = Carbon::parse($toDay)->format('d/m/Y');
        // $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Define the initial contract start date
        $contractStartDate = Carbon::parse($Booking->contract_date); // Example: 1st January 2025, 10:00 AM
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        // 1st 5 months + 2nd 5 months + 3rd 5 months = three contracts 5 + 5 + 5 with different contract dates and expiry dates
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        // Set up email data for customer and customer service
        $customerEmail = $Customer->email;
        $serviceEmail = 'customerservice@neguinhomotors.co.uk';
        $data['title'] = 'Hire / Sale Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // Determine the PDF template based on whether the booking is for a used motorbike or a used extended custom contract
        if ($Booking->is_used) {
            $pdf_name = 'pdf.contract-v3-ins-5m-extended-used';
        } elseif ($Booking->is_used_extended_custom) {
            $pdf_name = 'pdf.contract-v3-ins-5m-extended-used-custom-18m-t3';
        } else {
            $pdf_name = 'pdf.contract-v3-ins-5m-extended';
        }

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        // Start of Selection
        $path1 = "customers/{$Booking->customer_id}/1st-finance-contract-ins-".$tm.$rand_no.'-1.pdf';
        $path2 = "customers/{$Booking->customer_id}/2nd-finance-contract-ins-".$tm.$rand_no.'-2.pdf';
        $path3 = "customers/{$Booking->customer_id}/3rd-finance-contract-ins-".$tm.$rand_no.'-3.pdf';

        $customerAgreement1 = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => '1st-finance-contract-ins-'.time().$rand_no.'-1.pdf',
            'file_path' => $path1,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);
        $customerAgreement1->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement1->id, 3, '0', STR_PAD_LEFT),
        ]);

        $customerAgreement2 = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => '2nd-finance-contract-ins-'.time().$rand_no.'-2.pdf',
            'file_path' => $path2,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);
        $customerAgreement2->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement2->id, 3, '0', STR_PAD_LEFT),
        ]);

        $customerAgreement3 = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => '3rd-finance-contract-ins-'.time().$rand_no.'-3.pdf',
            'file_path' => $path3,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);
        $customerAgreement3->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement3->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Generate the first contract (5 months)
        $pdf1 = Pdf::loadView($pdf_name, [
            'contractStartDate' => $contractStartDate->format('d-F-Y H:i'),
            'contractEndDate' => $contractEndDate1->format('d-F-Y H:i'),
            'today' => $contractStartDate->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement1->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/1st-finance-contract-ins-'.$tm.$rand_no.'-1.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/1st-finance-contract-ins-'.$tm.$rand_no.'-1.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        // Generate the second contract (next 5 months)
        $pdf2 = Pdf::loadView($pdf_name, [
            'contractStartDate' => $contractEndDate1->format('d-F-Y H:i'),
            'contractEndDate' => $contractEndDate2->format('d-F-Y H:i'),
            'today' => $contractEndDate1->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement2->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/2nd-finance-contract-ins-'.$tm.$rand_no.'-2.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/2nd-finance-contract-ins-'.$tm.$rand_no.'-2.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        // Generate the third contract (final 5 months)
        $pdf3 = Pdf::loadView($pdf_name, [
            'contractStartDate' => $contractEndDate2->format('d-F-Y H:i'),
            'contractEndDate' => $contractEndDate3->format('d-F-Y H:i'),
            'today' => $contractEndDate2->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement3->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/3rd-finance-contract-ins-'.$tm.$rand_no.'-3.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/3rd-finance-contract-ins-'.$tm.$rand_no.'-3.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        // Send the first PDF to the customer
        $data['pdf'] = $pdf1;
        try {
            Mail::to($customerEmail)->send(new HireContract($data));
            \Log::info('First contract PDF sent to customer: '.$customerEmail);
        } catch (Exception $e) {
            \Log::error('Failed to send first contract PDF to customer: '.$e->getMessage());
        }

        // Send all three PDFs to customer service
        $mailable = new HireContract($data);
        $mailable->attachData($pdf2->output(), '2nd-finance-contract-ins-'.$tm.$rand_no.'-2.pdf');
        $mailable->attachData($pdf3->output(), '3rd-finance-contract-ins-'.$tm.$rand_no.'-3.pdf');

        try {
            Mail::to($serviceEmail)->send($mailable);
            \Log::info('All contract PDFs sent to customer service: '.$serviceEmail);
        } catch (Exception $e) {
            \Log::error('Failed to send contract PDFs to customer service: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // For Insurance and PCN Finance contract
    public function createNewContractInsM(Request $request)
    {
        \Log::info('Creating new agreement', $request->all());

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now().'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new Contract');


        $Booking = FinanceApplication::findOrFail($request->booking_id);
        \Log::info('Finance Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = ApplicationItem::where('application_id', $Booking->id)->first();
        \Log::info('Application Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        // insurance contract only we get.
        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];

        // $data["email"] = [$Customer->email];
        $data['title'] = 'Hire / Sale Contract';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // $dompdf->set_option("isPhpEnabled", true);

        // OLD VERSION - Before 22-JULY-2024
        // $pdf = Pdf::loadView('pdf.contract', [

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/finance-contract-ins-m-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'finance-contract-ins-m-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'application_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // NEW VERSION - After -SEP-2024
        $pdf = Pdf::loadView('pdf.contract-v4-ins-m', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/finance-contract-ins-m-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/finance-contract-ins-m-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new HireContract($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = ContractAccess::where('application_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        $access->expires_at = new DateTime;
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function createNewAgreement(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new agreement');


        $Booking = RentingBooking::findOrFail($request->booking_id);
        \Log::info('Booking Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();
        \Log::info('Booking Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');


        $agreementStartDate = $Booking->start_date;
        $agreementEndDate1 = $agreementStartDate->copy()->addMonths(5);
        $agreementEndDate2 = $agreementEndDate1->copy()->addMonths(5);
        $agreementEndDate3 = $agreementEndDate2->copy()->addMonths(5);

        $pdf_name = 'pdf.agreement-v3-ins-5m-extended';

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        //  // Send email with PDF to client
        // TEMP

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$Customer->email];
        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // $customerAgreement = new CustomerAgreement();
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        
        $path = "customers/{$Booking->customer_id}/rental-agreement-".$tm.$rand_no.'.pdf';

        // 5m
        $path1 = "customers/{$Booking->customer_id}/1st-{$pdf_name}-".$tm.$rand_no.'.pdf';
        $path2 = "customers/{$Booking->customer_id}/2nd-{$pdf_name}-".$tm.$rand_no.'.pdf';
        $path3 = "customers/{$Booking->customer_id}/3rd-{$pdf_name}-".$tm.$rand_no.'.pdf';



        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        //5m

        $customerAgreement1 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "1st-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path1,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement1->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement1->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Create and save second agreement
        $customerAgreement2 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "2nd-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path2,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement2->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement2->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Create and save third agreement
        $customerAgreement3 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "3rd-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path3,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement3->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement3->id, 3, '0', STR_PAD_LEFT),
        ]);


        // V1
        // $pdf = Pdf::loadView('pdf.agreement', [

        // V3
        $pdf = Pdf::loadView('pdf.agreement-v3', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/rental-agreement-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }


        // 5m
        // Generate PDFs with document_number passed to views
        $pdf1 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementStartDate->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate1->format('d/m/Y H:i'),
            'today' => $agreementStartDate->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement1->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/1st-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/1st-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $pdf2 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementEndDate1->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate2->format('d/m/Y H:i'),
            'today' => $agreementEndDate1->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement2->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/2nd-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/2nd-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $pdf3 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementEndDate2->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate3->format('d/m/Y H:i'),
            'today' => $agreementEndDate2->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement3->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/3rd-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/3rd-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;
        $data['pdf1'] = $pdf1;
        $data['pdf2'] = $pdf2;
        $data['pdf3'] = $pdf3;

        try {
            //  $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk']; going to customer service and customers
            Mail::to($data['email'])->send(new RentalAgreement($data));
            
            // only send to customer service and not customers 5ms send to customer service sending on pdf 1 2 and 3 by array
            Mail::to(['customerservice@neguinhomotors.co.uk'])->send(new RentalAgreementNgn($data));


        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        // $access->expires_at = new DateTime();
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function createNewAgreementV6(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign'); // your base64 encoded
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new agreement');


        $Booking = RentingBooking::findOrFail($request->booking_id);
        \Log::info('Booking Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();
        \Log::info('Booking Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');


        $agreementStartDate = $Booking->start_date;
        $agreementEndDate1 = $agreementStartDate->copy()->addMonths(5);
        $agreementEndDate2 = $agreementEndDate1->copy()->addMonths(5);
        $agreementEndDate3 = $agreementEndDate2->copy()->addMonths(5);

        // V6 for 5 month copies INS
        $pdf_name = 'pdf.agreement-v6-ins';

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        //  // Send email with PDF to client
        // TEMP

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$Customer->email];
        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // $customerAgreement = new CustomerAgreement();
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        
        $path = "customers/{$Booking->customer_id}/rental-agreement-".$tm.$rand_no.'.pdf';

        // 5m
        $path1 = "customers/{$Booking->customer_id}/1st-{$pdf_name}-".$tm.$rand_no.'.pdf';
        $path2 = "customers/{$Booking->customer_id}/2nd-{$pdf_name}-".$tm.$rand_no.'.pdf';
        $path3 = "customers/{$Booking->customer_id}/3rd-{$pdf_name}-".$tm.$rand_no.'.pdf';



        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        //5m

        $customerAgreement1 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "1st-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path1,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement1->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement1->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Create and save second agreement
        $customerAgreement2 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "2nd-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path2,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement2->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement2->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Create and save third agreement
        $customerAgreement3 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "3rd-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path3,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement3->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement3->id, 3, '0', STR_PAD_LEFT),
        ]);


        // V1
        // $pdf = Pdf::loadView('pdf.agreement', [

        // V6 // for five year v6 simple
        $pdf = Pdf::loadView('pdf.agreement-v6', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-v6-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/rental-agreement-v6-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }


        // 5mnth copies
        // Generate PDFs with document_number passed to views
        $pdf1 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementStartDate->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate1->format('d/m/Y H:i'),
            'today' => $agreementStartDate->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement1->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/1st-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/1st-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $pdf2 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementEndDate1->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate2->format('d/m/Y H:i'),
            'today' => $agreementEndDate1->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement2->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/2nd-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/2nd-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $pdf3 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementEndDate2->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate3->format('d/m/Y H:i'),
            'today' => $agreementEndDate2->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement3->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/3rd-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/3rd-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;
        $data['pdf1'] = $pdf1;
        $data['pdf2'] = $pdf2;
        $data['pdf3'] = $pdf3;

        try {
            //  $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk']; going to customer service and customers
            // customer and us will receive the rental agreement v6 of 5 years simple
            Mail::to($data['email'])->send(new RentalAgreement($data));
            
            // We are receiving also 5 months copies to customer service
            // only send to customer service and not customers 5ms send to customer service sending on pdf 1 2 and 3 by array
            Mail::to(['customerservice@neguinhomotors.co.uk'])->send(new RentalAgreementNgn($data));


        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        // Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            // SFTP upload for battery safety leaflet
            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            \Log::info('📁 Battery Safety Leaflet saved at: '.$batterySafetyAbsoluteLocalPath);
            $batterySafetySyncService = app(\App\Services\FtpSyncService::class);
            $batterySafetySuccess = $batterySafetySyncService->uploadFile($batterySafetyAbsoluteLocalPath);
            \Log::info('📤 Battery Safety Leaflet remote mirror path: '.$batterySafetySuccess);

            if (! $batterySafetySuccess) {
                \Log::warning("Uploaded battery safety leaflet locally but failed to sync to remote domain: $batterySafetyAbsoluteLocalPath");
            }

            // Send Battery Safety Leaflet PDF to customer only
            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new RentalAgreement($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        // $access->expires_at = new DateTime();
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function createNewAgreementIns(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new agreement INS');


        $Booking = RentingBooking::findOrFail($request->booking_id);

        $Customer = Customer::findOrFail($Booking->customer_id);

        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();

        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        //  // Send email with PDF to client
        // TEMP

        $data['email'] = ['customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$Customer->email];
        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // $customerAgreement = new CustomerAgreement();
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/rental-agreement-ins-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-ins-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // V3
        $pdf = Pdf::loadView('pdf.agreement-v3-ins', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-ins-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/rental-agreement-ins-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            // sent to customer service
            Mail::to($data['email'])->send(new RentalAgreement($data));

            // sent to customers
            Mail::to($Customer->email)->send(new RentalAgreement($data));
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        // $access->expires_at = new DateTime();
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function createNewAgreementInsV6(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new agreement INS');


        $Booking = RentingBooking::findOrFail($request->booking_id);

        $Customer = Customer::findOrFail($Booking->customer_id);

        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();

        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        //  // Send email with PDF to client
        // TEMP

        $data['email'] = ['customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$Customer->email];
        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // $customerAgreement = new CustomerAgreement();
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/rental-agreement-ins-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-ins-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // V6
        $pdf = Pdf::loadView('pdf.agreement-v6-ins', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-ins-v6-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/rental-agreement-ins-v6-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            // sent to customer service
            Mail::to($data['email'])->send(new RentalAgreement($data));

            // sent to customers
            Mail::to($Customer->email)->send(new RentalAgreement($data));
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        // Generate Battery Safety Leaflet PDF (only for e-bikes)
        if ($Motorbike && $Motorbike->is_ebike) {
            $batterySafetyPdfForCustomer = Pdf::loadView('pdf.battery-safety-leaflet', [
                'today' => $toDay,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');

            // SFTP upload for battery safety leaflet
            $batterySafetyAbsoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/battery-safety-leaflet-'.$tm.$rand_no.'.pdf');
            \Log::info('📁 Battery Safety Leaflet saved at: '.$batterySafetyAbsoluteLocalPath);
            $batterySafetySyncService = app(\App\Services\FtpSyncService::class);
            $batterySafetySuccess = $batterySafetySyncService->uploadFile($batterySafetyAbsoluteLocalPath);
            \Log::info('📤 Battery Safety Leaflet remote mirror path: '.$batterySafetySuccess);

            if (! $batterySafetySuccess) {
                \Log::warning("Uploaded battery safety leaflet locally but failed to sync to remote domain: $batterySafetyAbsoluteLocalPath");
            }

            // Send Battery Safety Leaflet PDF to customer only
            $batterySafetyData = [];
            $batterySafetyData['email'] = [$Customer->email];
            $batterySafetyData['email'] = ['customerservice@neguinhomotors.co.uk'];
            $batterySafetyData['title'] = 'E-Bike Battery Safety Leaflet';
            $batterySafetyData['body'] = 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document - please read it carefully.';
            $batterySafetyData['pdf'] = $batterySafetyPdfForCustomer;

            try {
                Mail::to($batterySafetyData['email'])->send(new RentalAgreement($batterySafetyData));
            } catch (Exception $e) {
                Log::error(__FILE__.' at line '.__LINE__.' Failed to send battery safety leaflet email: '.$e->getMessage());
            }
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        // $access->expires_at = new DateTime();
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function createNewAgreementIns6m(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new agreement INS');


        $Booking = RentingBooking::findOrFail($request->booking_id);

        $Customer = Customer::findOrFail($Booking->customer_id);

        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();

        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        //  // Send email with PDF to client
        // TEMP

        $data['email'] = ['customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$Customer->email];
        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        // $customerAgreement = new CustomerAgreement();
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/rental-agreement-ins-6m-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-ins-6m-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        // V3
        $pdf = Pdf::loadView('pdf.agreement-v3-ins-6m', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-ins-6m-'.$tm.$rand_no.'.pdf');

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/rental-agreement-ins-6m-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            // sent to customer service
            Mail::to($data['email'])->send(new RentalAgreement($data));

            // sent to customers
            Mail::to($Customer->email)->send(new RentalAgreement($data));
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        // $access->expires_at = new DateTime();
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function createNewAgreementIns5mExtended(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        // log
        \Log::info('Creating new agreement INS');


        $Booking = RentingBooking::findOrFail($request->booking_id);

        $Customer = Customer::findOrFail($Booking->customer_id);

        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();

        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        $agreementStartDate = $Booking->start_date;
        $agreementEndDate1 = $agreementStartDate->copy()->addMonths(5);
        $agreementEndDate2 = $agreementEndDate1->copy()->addMonths(5);
        $agreementEndDate3 = $agreementEndDate2->copy()->addMonths(5);

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        //  // Send email with PDF to client
        // TEMP
        $customerEmail = $Customer->email;
        $serviceEmail = 'customerservice@neguinhomotors.co.uk';

        // $data["email"] = ['customerservice@neguinhomotors.co.uk'];
        // $data["email"] = [$Customer->email];
        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdf_name = 'pdf.agreement-v3-ins-5m-extended';

        // $customerAgreement = new CustomerAgreement();
        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        // Start of Selection
        // $path1 = "customers/{$Booking->customer_id}/{$pdf_name}-" . $tm . $rand_no . '-1.pdf';
        // $path2 = "customers/{$Booking->customer_id}/{$pdf_name}-" . $tm . $rand_no . '-2.pdf';
        // $path3 = "customers/{$Booking->customer_id}/{$pdf_name}-" . $tm . $rand_no . '-3.pdf';
        $path1 = "customers/{$Booking->customer_id}/1st-{$pdf_name}-".$tm.$rand_no.'.pdf';
        $path2 = "customers/{$Booking->customer_id}/2nd-{$pdf_name}-".$tm.$rand_no.'.pdf';
        $path3 = "customers/{$Booking->customer_id}/3rd-{$pdf_name}-".$tm.$rand_no.'.pdf';

        // $path = "customers/{$Booking->customer_id}/rental-agreement-ins-5m-extended-" . $tm . $rand_no . '.pdf';

        // --

        // $customerAgreement = new CustomerAgreement([
        //     'customer_id' => $Booking->customer_id,
        //     'document_type_id' => $documentType->id,
        //     'file_name' => "1st-{$pdf_name}-" . $tm . $rand_no . ".pdf",
        //     'file_path' => $path1,
        //     'file_format' => 'pdf',
        //     'document_number' => '',
        //     'valid_until' => null,
        //     'is_verified' => false,
        //     'booking_id' => $request->booking_id,
        // ]);

        // $customerAgreement->save();

        // --

        $customerAgreement1 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "1st-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path1,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement1->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement1->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Create and save second agreement
        $customerAgreement2 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "2nd-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path2,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement2->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement2->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Create and save third agreement
        $customerAgreement3 = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => "3rd-{$pdf_name}-{$tm}{$rand_no}.pdf",
            'file_path' => $path3,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);
        $customerAgreement3->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement3->id, 3, '0', STR_PAD_LEFT),
        ]);

        // Generate PDFs with document_number passed to views
        $pdf1 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementStartDate->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate1->format('d/m/Y H:i'),
            'today' => $agreementStartDate->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement1->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/1st-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/1st-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $pdf2 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementEndDate1->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate2->format('d/m/Y H:i'),
            'today' => $agreementEndDate1->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement2->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/2nd-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/2nd-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $pdf3 = Pdf::loadView($pdf_name, [
            'agreementStartDate' => $agreementEndDate2->format('d/m/Y H:i'),
            'agreementEndDate' => $agreementEndDate3->format('d/m/Y H:i'),
            'today' => $agreementEndDate2->format('d/m/Y'),
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement3->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath."/3rd-{$pdf_name}-{$tm}{$rand_no}.pdf");

        // LOGGING & SFTP SYNC LOGIC (Add these lines)
        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id."/3rd-{$pdf_name}-{$tm}{$rand_no}.pdf");
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        // --- SFTP Sync Logic ---
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf1;

        try {
            Mail::to($customerEmail)->send(new RentalAgreement($data));
            \Log::info('First contract PDF sent to customer: '.$customerEmail);
            // Mail::to($data["email"])->send(new RentalAgreement($data));
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        // Send all three PDFs to support
        $mailable = new RentalAgreement($data);
        $mailable->attachData($pdf2->output(), "2nd-{$pdf_name}-".$tm.$rand_no.'.pdf');
        $mailable->attachData($pdf3->output(), "3rd-{$pdf_name}-".$tm.$rand_no.'.pdf');

        try {
            Mail::to($serviceEmail)->send($mailable);
            \Log::info('All contract PDFs sent to customer service: '.$serviceEmail);
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        // log access
        \Log::info('Access Obj: ', [$access]);

        // $access->expires_at = new DateTime();
        $access->save();

        return response()->json([
            'message' => 'Agreement Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    public function showLoyaltyScheme(Request $request, $customer_id, $passcode)
    {
        $access = AgreementAccess::where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();
        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }
        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';

        $booking = RentingBooking::findOrFail($access->booking_id);
        \Log::info('Booking Obj: ', [$booking]);
        $customer = Customer::findOrFail($booking->customer_id);
        \Log::info('Customer Obj: ', [$customer]);
        $bookingItem = RentingBookingItem::where('booking_id', $booking->id)->first();
        \Log::info('Booking Item Obj: ', [$bookingItem]);
        $motorbike = Motorbike::where('id', $bookingItem->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$motorbike]);
        $user_name = $booking->user->first_name.' '.$booking->user->last_name;

        return view('signature-loyalty-scheme', compact(
            'booking',
            'customer',
            'bookingItem',
            'SIGFILE',
            'user_name',
            'today',
            'motorbike',
            'customer_id',
            'passcode',
            'access'
        ));
    }

    public function createLoyaltyScheme(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $base64_image = $request->input('sign');
        @[$type, $file_data] = explode(';', $base64_image);
        @[, $file_data] = explode(',', $file_data);
        $fileName = $request->first_name.'-'.$request->last_name.'-'.Carbon::now()->format('Y-m-d_H-i-s').'.'.'jpg';
        Storage::disk('public')->put($fileName, base64_decode($file_data));

        \Log::info('Creating loyalty scheme policy document');

        $Booking = RentingBooking::findOrFail($request->booking_id);
        \Log::info('Booking Obj: ', [$Booking]);
        $Customer = Customer::findOrFail($Booking->customer_id);
        \Log::info('Customer Obj: ', [$Customer]);
        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();
        \Log::info('Booking Item Obj: ', [$BookingItems]);
        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();
        \Log::info('Motorbike Obj: ', [$Motorbike]);

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        $documentType = DocumentType::where('name', 'Loyalty Scheme Policy')->first();
        
        if (! $documentType) {
            $documentType = DocumentType::create([
                'name' => 'Loyalty Scheme Policy',
            ]);
        }

        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['email'] = [$Customer->email, 'customerservice@neguinhomotors.co.uk'];
        $data['title'] = 'Loyalty Upgrade Scheme Policy';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Your signed Loyalty Upgrade Scheme Policy is attached.';
        $data['customer'] = $Customer;

        $rand_no = rand(1, 99999);
        $tm = time();

        $path = "customers/{$Booking->customer_id}/loyalty-scheme-policy-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'loyalty-scheme-policy-'.time().$rand_no.'.pdf',
            'file_path' => $path,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => false,
            'booking_id' => $request->booking_id,
        ]);

        $customerAgreement->update([
            'document_number' => "{$Booking->id}-{$Booking->customer_id}-".str_pad($customerAgreement->id, 3, '0', STR_PAD_LEFT),
        ]);

        $pdf = Pdf::loadView('pdf.loyalty-scheme', [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/loyalty-scheme-policy-'.$tm.$rand_no.'.pdf');

        $absoluteLocalPath = storage_path('app/public/customers/'.$Booking->customer_id.'/loyalty-scheme-policy-'.$tm.$rand_no.'.pdf');
        \Log::info('📁 Local file saved at: '.$absoluteLocalPath);
        
        $syncService = app(\App\Services\FtpSyncService::class);
        $success = $syncService->uploadFile($absoluteLocalPath);
        \Log::info('📤 Actual remote mirror path: '.$success);

        if (! $success) {
            \Log::warning("uploaded file locally but failed to sync to remote domain: $absoluteLocalPath");
        }

        $data['pdf'] = $pdf;

        try {
            Mail::to($data['email'])->send(new LoyaltySchemePolicy($data));
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->first();

        \Log::info('Access Obj: ', [$access]);
        $access->save();

        return response()->json([
            'message' => 'Loyalty Scheme Policy signed successfully. You can close this window. We will send you a copy to your email.',
        ]);
    }
}
