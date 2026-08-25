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
use App\Support\AgreementDateTime;
use App\Support\DocumentUploadAccessGenerator;
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
use App\Support\EbikeBatterySafetyLeaflet;
use App\Support\QrCodeGenerator;
use App\Support\AgreementPdfGenerator;
use App\Support\BrowsershotPdfAdapter;
use App\Support\SignatureUploadStore;
use Symfony\Component\Mime\Exception\RfcComplianceException;

use App\Livewire\Agreements\LegacyMigratedDocument;

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

        return LegacyMigratedDocument::toResponse('livewire.agreements.migrated.admin.customers.upload-links', compact('access'));
    }

    public function agreement_Logs()
    {
        $access = AgreementAccess::join('customers', 'agreement_accesses.customer_id', '=', 'customers.id')
            ->orderBy('agreement_accesses.created_at', 'desc')
            ->get()
            ->map(function ($access) {
                $urls = AgreementAccess::rentalUrlsFor($access->customer_id, $access->passcode);
                $access->link = $urls['customer'];
                $access->link_standard = $urls['standard'];
                $access->link_ins = $urls['customer'];
                $access->name = $access->first_name.' '.$access->last_name;

                return $access;
            });

        // admin/customers/agreement-links
        return LegacyMigratedDocument::toResponse('livewire.agreements.migrated.admin.customers.agreement-links', compact('access'));
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

        $url = AgreementAccess::customerSigningUrl((int) $customer_id, $passcode);

        if ($access) {

            $qrBase64 = '';

            try {
                $qrBase64 = QrCodeGenerator::dataUrl($url, 200);
            } catch (\Exception $e) {
                \Log::error('QR generation failed: ' . $e->getMessage());
                $qrBase64 = '';
            }

            $data['email'] = [$customer->email, 'customerservice@neguinhomotors.co.uk'];
            $data['title'] = 'Rental Agreement';
            $data['body'] = 'Dear valued customer,

            We kindly request your attention to finalize your booking with Neguinho Motors. To proceed, please click the following link to review and sign the agreement: '.$url.'

            Completing this step is essential to move forward. Thank you for choosing Neguinho Motors for your motorcycle rental needs.';

            $mailData = [
                'title' => $data['title'],
                'body' => $data['body'],
                'url' => $url,
                'actionLabel' => 'Open rental agreement',
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
        $booking_id = request()->query('booking_id');

        if (! $booking_id) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'booking_id is required.'], 400);
            }

            abort(400, 'booking_id is required.');
        }

        try {
            $result = app(DocumentUploadAccessGenerator::class)->create(
                (int) $customer_id,
                (int) $booking_id,
                sendEmail: true,
            );
        } catch (InvalidArgumentException|RuntimeException $e) {
            if (request()->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            abort(400, $e->getMessage());
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success'    => true,
                'uploadLink' => $result['uploadLink'],
            ]);
        }

        return redirect()->away($result['uploadLink']);
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

        // Full HTML signing page — include via legacy-host (not Livewire mount).
        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-v6']));
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

        // Full HTML signing page — include via legacy-host (not Livewire mount).
        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-v6-ins']));
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
            ->whereNull('signed_at')
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

            return view('livewire.agreements.legacy-host', [
                'legacyView' => 'livewire.agreements.migrated.rental-termination-v1',
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
            'expire_at' => 'nullable|date',
            'sign' => 'required|string|starts_with:data:image/png;base64,', // Ensure the signature is a base64 image
        ]);

        \Log::info('Validation Pass ');

        $customer_id = $request->customer_id;
        $booking_id = $request->booking_id;
        $passcode = $request->passcode;

        $access = RentalTerminateAccess::where('booking_id', $request->booking_id)
            ->where('customer_id', $request->customer_id)
            ->where('passcode', $request->passcode)
            ->whereNull('signed_at')
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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $Customer->first_name,
            (string) $Customer->last_name,
            'private'
        );

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

        $pdf = $this->pdfLoadView('livewire.agreements.pdf.templates.rental-termination-v1', [
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
        $data = $this->withSavedPdfFiles($data);

        try {
            Mail::to($data['email'])->send(new RentalTerminateEmail($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

        $access = RentalTerminateAccess::where('booking_id', $request->booking_id)
            ->first();

        \Log::info('Access Obj: ', [$access]);

        $access->expire_at = now();
        $access->signed_at = now();

        $access->save();

        return response()->json([
            'message' => 'Signed successfully, for review. You can close this window. We will send you a copy of the agreement to your email.',
        ]);
    }

    // 13-JUL-2026 - Latest Contract (New)
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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-latest']));
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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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

        $path = "customers/{$Booking->customer_id}/sale-contract-latest-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'sale-contract-latest-'.time().$rand_no.'.pdf',
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

        $pdf = $this->pdfLoadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'subs_payment_date' => $Booking->subs_payment_date,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/sale-contract-latest-'.$tm.$rand_no.'.pdf');

        $data['pdf'] = $pdf;

        // Always generate 3× INS/PCN less-terms for customerservice (council PCN handling).
        $lessTermsPdfs = $this->buildInternalPcnInsCopies(
            $Booking,
            $Customer,
            $Motorbike,
            $BookingItems,
            $fileName,
            (string) $customerAgreement->document_number,
            $pdfPath,
            (int) $tm,
            (int) $rand_no,
            (bool) $Booking->is_used_latest
        );

        try {
            $this->mailFinanceContractToCustomer($Customer, $data);
            $this->mailInternalPcnInsCopies($lessTermsPdfs);
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems);

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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-ins-latest']));
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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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
        $path = "customers/{$Booking->customer_id}/sale-contract-ins-latest-".$tm.$rand_no.'.pdf';
        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'sale-contract-ins-latest-'.time().$rand_no.'.pdf',
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
        $pdf = $this->pdfLoadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'subs_payment_date' => $Booking->subs_payment_date,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/sale-contract-ins-latest-'.$tm.$rand_no.'.pdf');


        // Customer And Us Single Email Settings
        $data['title'] = 'Sale Contract Latest';
        $data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! Find Attached your rental agreement. ';
        $data['pdf'] = $pdf;

        // Always: 3× INS/PCN less-terms → customerservice only (council PCN).
        $lessTermsPdfs = $this->buildInternalPcnInsCopies(
            $Booking,
            $Customer,
            $Motorbike,
            $BookingItems,
            $fileName,
            (string) $customerAgreement->document_number,
            $pdfPath,
            (int) $tm,
            (int) $rand_no,
            (bool) $Booking->is_used_latest
        );

        try {
            $this->mailFinanceContractToCustomer($Customer, $data);
            $this->mailInternalPcnInsCopies($lessTermsPdfs);
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems);

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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-ins-used-latest']));
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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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

        $path = "customers/{$Booking->customer_id}/sale-contract-ins-used-latest-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'sale-contract-ins-used-latest-'.time().$rand_no.'.pdf',
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

        $pdf = $this->pdfLoadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'subs_payment_date' => $Booking->subs_payment_date,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/sale-contract-ins-used-latest-'.$tm.$rand_no.'.pdf');



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
            $pdfFileName = '/sale-contract-'.($index + 1).'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = $this->pdfLoadView($less_terms_pdf_name, [
                'today' => $dates['start'], // Carbon instance
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'subs_payment_date' => $Booking->subs_payment_date,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$customerAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'], // Carbon instance
                'contractEndDate' => $dates['end'],   // Carbon instance
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);



            $less_terms_pdf_data[] = $less_terms_pdf;
        }

        $email_data = [];
        $email_data['title'] = 'Sale Contract - PCN/INS - Internal';
        $email_data['body'] = 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! <br> Find Attached your rental agreement. ';
        $email_data['pdf'] = $less_terms_pdf_data;

        try {
            $this->mailFinanceContractToCustomer($Customer, $data);
            $this->mailInternalPcnInsCopies($less_terms_pdf_data);
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems);

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Insurance Used Latest Agreement signed successfully. You can close this window. A copy will be sent to your email.',
        ]);
    }

    // 13-JUL-2026 - Latest Contract (Used)
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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-used-latest']));
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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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

        $path = "customers/{$Booking->customer_id}/sale-contract-used-latest-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'sale-contract-used-latest-'.time().$rand_no.'.pdf',
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

        $pdf = $this->pdfLoadView($pdf_name, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'subs_payment_date' => $Booking->subs_payment_date,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/sale-contract-used-latest-'.$tm.$rand_no.'.pdf');



        $data['pdf'] = $pdf;

        // Always generate 3× INS/PCN less-terms for customerservice (council PCN handling).
        $lessTermsPdfs = $this->buildInternalPcnInsCopies(
            $Booking,
            $Customer,
            $Motorbike,
            $BookingItems,
            $fileName,
            (string) $customerAgreement->document_number,
            $pdfPath,
            (int) $tm,
            (int) $rand_no,
            true
        );

        try {
            $this->mailFinanceContractToCustomer($Customer, $data);
            $this->mailInternalPcnInsCopies($lessTermsPdfs);
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems);

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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-merged-new']));
    }

    /**
     * 13-JUL-2026 - Latest Contract (Subs-Used: Sale + Subscription, Used Vehicle)
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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-merged-used']));
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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-merged-new-ins']));
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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-contract-v6-merged-used-ins']));
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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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
        $salePath = "customers/{$Booking->customer_id}/sale-contract-latest-".$tm.$rand_no.'.pdf';
        
        $saleAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'sale-contract-latest-'.time().$rand_no.'.pdf',
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

        $salePdf = $this->pdfLoadView($salePdfName, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'subs_payment_date' => $Booking->subs_payment_date,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $saleAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/sale-contract-latest-'.$tm.$rand_no.'.pdf');


        // 2. Generate Subscription Contract PDF
        $subscriptionPath = "customers/{$Booking->customer_id}/12-month-subscription-contract-".$tm.$rand_no.'.pdf';
        
        $subscriptionAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => '12-month-subscription-contract-'.time().$rand_no.'.pdf',
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

        $subscriptionPdf = $this->pdfLoadView('livewire.agreements.pdf.templates.contract-v6-subscription', [
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
            'subs_payment_date' => $Booking->subs_payment_date,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/12-month-subscription-contract-'.$tm.$rand_no.'.pdf');


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
            $pdfFileName = '/sale-contract-'.($index + 1).'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = $this->pdfLoadView($less_terms_pdf_name, [
                'today' => $dates['start'],
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'subs_payment_date' => $Booking->subs_payment_date,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$saleAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'],
                'contractEndDate' => $dates['end'],
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);

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
            $this->mailFinanceContractToCustomer($Customer, [
                'title' => 'Sale Contract & 12-Month Subscription Contract',
                'body' => 'Thank you for choosing Neguinho Motors Ltd. Please find attached your Sale Contract and 12-Month Subscription Contract. Ride safe and enjoy the journey!',
                'pdf' => [$salePdf, $subscriptionPdf],
            ]);
            $this->mailInternalPcnInsCopies($less_terms_pdf_data);
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems);

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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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
        $salePath = "customers/{$Booking->customer_id}/sale-contract-ins-latest-".$tm.$rand_no.'.pdf';
        
        $saleAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'sale-contract-ins-latest-'.time().$rand_no.'.pdf',
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

        $salePdf = $this->pdfLoadView($salePdfName, [
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'subs_payment_date' => $Booking->subs_payment_date,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $saleAgreement->document_number,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/sale-contract-ins-latest-'.$tm.$rand_no.'.pdf');


        // 2. Generate Subscription Contract PDF
        $subscriptionPath = "customers/{$Booking->customer_id}/12-month-subscription-contract-".$tm.$rand_no.'.pdf';
        
        $subscriptionAgreement = CustomerContract::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => '12-month-subscription-contract-'.time().$rand_no.'.pdf',
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

        $subscriptionPdf = $this->pdfLoadView('livewire.agreements.pdf.templates.contract-v6-subscription', [
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
            'subs_payment_date' => $Booking->subs_payment_date,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/12-month-subscription-contract-'.$tm.$rand_no.'.pdf');


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
            $pdfFileName = '/sale-contract-'.($index + 1).'-'.$tm.$rand_no.'.pdf';
            $less_terms_pdf = $this->pdfLoadView($less_terms_pdf_name, [
                'today' => $dates['start'],
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'subs_payment_date' => $Booking->subs_payment_date,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => "{$saleAgreement->document_number}-".($index + 1),
                'contractStartDate' => $dates['start'],
                'contractEndDate' => $dates['end'],
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);

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
            $this->mailFinanceContractToCustomer($Customer, [
                'title' => 'Sale Contract & 12-Month Subscription Contract',
                'body' => 'Thank you for choosing Neguinho Motors Ltd. Please find attached your Sale Contract and 12-Month Subscription Contract. Ride safe and enjoy the journey!',
                'pdf' => [$salePdf, $subscriptionPdf],
            ]);
            $this->mailInternalPcnInsCopies($less_terms_pdf_data);
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.' Failed to send email: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems);

        $access->expires_at = now();
        $access->save();

        return response()->json([
            'message' => 'Merged contracts signed successfully. You can close this window. Copies will be sent to your email.',
        ]);
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

        return view('livewire.agreements.legacy-host', array_merge(compact('sell', 'access', 'purchase_id'), ['legacyView' => 'livewire.agreements.migrated.purchase-invoice-review']));
    }

    public function showUploadDocPage(Request $request, $customer_id, $passcode)
    {
        $access = UploadDocumentAccess::query()
            ->where('customer_id', $customer_id)
            ->where('passcode', $passcode)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'This upload link has expired or is invalid. Ask us to generate a fresh link from your booking.');
        }

        $toDay = new DateTime;
        $today = Carbon::parse($toDay)->format('d/m/Y');
        $SIGFILE = '#';
        $booking = RentingBooking::with('user')->findOrFail($access->booking_id);
        $customer = Customer::findOrFail($booking->customer_id);
        $bookingItem = RentingBookingItem::where('booking_id', $booking->id)->first();
        $motorbike = $bookingItem ? Motorbike::find($bookingItem->motorbike_id) : null;
        $user_name = $booking->user
            ? trim($booking->user->first_name.' '.$booking->user->last_name)
            : 'NGN';

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.upload_documents']));
    }

    public function employeeNda(Request $request)
    {
        $filePath = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->employeeName,
            'nda',
            'public',
            'employee'
        );
        Log::info('Signature image saved successfully at '.Storage::disk('public')->path($filePath));

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

        $pdf = $this->pdfLoadView('livewire.agreements.pdf.templates.employee-sign', [
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

    // Purchase invoice PDF
    public function createNewInvoice(Request $request)
    {
        \Log::info('Creating new Purchase INVOICE', $request->all());

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            'inv',
            (string) $request->purchase_id
        );

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

        $pdf = $this->pdfLoadView('livewire.agreements.pdf.templates.purchase-invoice-pdf', [
            'SIGFILE' => $fileName,
            'today' => $toDay,
            'req' => $request,
            'sell' => $sell,
            'purchase_id' => $purchase_id,
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/purchase-invoice-'.$tm.$rand_no.'.pdf');



        $data['pdf'] = $pdf;
        $data = $this->withSavedPdfFiles($data);

        try {
            Mail::to($data['email'])->send(new PurchaseInvoice($data));
        } catch (Exception $e) {

            Log::error(__FILE__.' at line '.__LINE__.'Failed to send email: '.$e->getMessage());
        }

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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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


        $twelveMonthDates = AgreementDateTime::rentalTwelveMonthDateStrings($Booking);

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/rental-agreement-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-'.$tm.$rand_no.'.pdf',
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

        $pdf = $this->pdfLoadView('livewire.agreements.pdf.templates.agreement-v6', array_merge([
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ], $twelveMonthDates))->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-'.$tm.$rand_no.'.pdf');

        $pcnPdfs = $this->buildRentalPcnInsCopies(
            $Booking,
            $Customer,
            $Motorbike,
            $BookingItems,
            $fileName,
            $pdfPath,
            (int) $tm,
            (int) $rand_no,
            (int) $request->booking_id,
            $documentType,
        );

        try {
            $this->mailRentalAgreementToCustomer($Customer, [
                'title' => $data['title'],
                'body' => $data['body'],
                'pdf' => $pdf,
            ]);
            $this->mailRentalPcnCopiesToCustomerService($pcnPdfs);
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems, rentalMail: true, rentalBookingId: (int) $request->booking_id);

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

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

        // log
        \Log::info('Creating new agreement INS');


        $Booking = RentingBooking::findOrFail($request->booking_id);

        $Customer = Customer::findOrFail($Booking->customer_id);

        $BookingItems = RentingBookingItem::where('booking_id', $Booking->id)->first();

        $Motorbike = Motorbike::where('id', $BookingItems->motorbike_id)->first();

        $toDay = new DateTime;
        $toDay = Carbon::parse($toDay)->format('d/m/Y');

        $twelveMonthDates = AgreementDateTime::rentalTwelveMonthDateStrings($Booking);

        // Check if directory exists and create if not
        $pdfPath = storage_path('app/public/customers/'.$Booking->customer_id);
        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $data['title'] = 'Rental Agreement';
        $data['body'] = 'Thank you for choosing Neguinho Motors. Ride safe and enjoy the journey!';

        $rand_no = rand(1, 99999);
        $tm = time();

        $documentType = DocumentType::where('name', 'Rental Agreement')->first();

        $path = "customers/{$Booking->customer_id}/rental-agreement-".$tm.$rand_no.'.pdf';

        $customerAgreement = CustomerAgreement::create([
            'customer_id' => $Booking->customer_id,
            'document_type_id' => $documentType->id,
            'file_name' => 'rental-agreement-'.$tm.$rand_no.'.pdf',
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

        $pdf = $this->pdfLoadView('livewire.agreements.pdf.templates.agreement-v6-ins', array_merge([
            'today' => $toDay,
            'SIGFILE' => $fileName,
            'booking' => $Booking,
            'customer' => $Customer,
            'motorbike' => $Motorbike,
            'bookingItem' => $BookingItems,
            'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
            'document_number' => $customerAgreement->document_number,
        ], $twelveMonthDates))->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true)
            ->save($pdfPath.'/rental-agreement-'.$tm.$rand_no.'.pdf');

        $pcnPdfs = $this->buildRentalPcnInsCopies(
            $Booking,
            $Customer,
            $Motorbike,
            $BookingItems,
            $fileName,
            $pdfPath,
            (int) $tm,
            (int) $rand_no,
            (int) $request->booking_id,
            $documentType,
        );

        try {
            $this->mailRentalAgreementToCustomer($Customer, [
                'title' => $data['title'],
                'body' => $data['body'],
                'pdf' => $pdf,
            ]);
            $this->mailRentalPcnCopiesToCustomerService($pcnPdfs);
        } catch (RfcComplianceException $e) {
            Log::error(__FILE__.' at line '.__LINE__.'RFC Compliance Error: '.$e->getMessage());
        } catch (Exception $e) {
            Log::error(__FILE__.' at line '.__LINE__.'Email sending failed: '.$e->getMessage());
        }

        $this->sendEbikeBatterySafetyLeafletIfNeeded($Motorbike, $Customer, $pdfPath, $tm, $rand_no, $toDay, $Booking, $BookingItems, rentalMail: true, rentalBookingId: (int) $request->booking_id);

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

        return view('livewire.agreements.legacy-host', array_merge(compact(
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
        ), ['legacyView' => 'livewire.agreements.migrated.signature-loyalty-scheme']));
    }

    public function createLoyaltyScheme(Request $request)
    {
        $access = AgreementAccess::where('booking_id', $request->booking_id)
            ->where('expires_at', '>', now())
            ->first();

        if (! $access) {
            abort(403, 'Unauthorized access or the link has expired.');
        }

        $fileName = SignatureUploadStore::store(
            (string) $request->input('sign'),
            (string) $request->first_name,
            (string) $request->last_name
        );

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

        $pdf = $this->pdfLoadView('livewire.agreements.pdf.templates.loyalty-scheme', [
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

        


        $data['pdf'] = $pdf;
        $data = $this->withSavedPdfFiles($data);

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
    private function pdfLoadView(string $view, array $data = []): mixed
    {
        return AgreementPdfGenerator::loadView($view, $data);
    }

    public function showContractTest(Request $request)
    {
        $template = (string) $request->query('template', 'latest');
        $templates = [
            'latest' => 'pdf.contract-v6-latest',
            'used-latest' => 'pdf.contract-v6-used-latest',
            'ins-latest' => 'pdf.contract-v6-ins-latest',
            'ins-used-latest' => 'pdf.contract-v6-ins-used-latest',
            'subscription' => 'livewire.agreements.pdf.templates.contract-v6-subscription',
            'ins-latest-less-terms' => 'pdf.contract-v6-ins-latest-less-terms',
            'ins-used-latest-less-terms' => 'pdf.contract-v6-ins-used-latest-less-terms',
            'battery-safety-leaflet' => 'livewire.agreements.pdf.templates.battery-safety-leaflet',
        ];

        if (! isset($templates[$template])) {
            abort(404, 'Unknown finance contract test template.');
        }

        $query = FinanceApplication::query()
            ->with(['customer', 'user', 'application_items.motorbike'])
            ->whereHas('application_items.motorbike');

        if ($request->integer('application_id') > 0) {
            $query->whereKey($request->integer('application_id'));
        } elseif (str_contains($template, 'used')) {
            $query->where('is_used_latest', true);
        } elseif ($template === 'subscription') {
            $query->where('is_subscription', true);
        } elseif ($template === 'battery-safety-leaflet') {
            $query->whereHas('application_items.motorbike', fn ($bike) => $bike->where('is_ebike', true));
        } else {
            $query->where('is_new_latest', true);
        }

        $booking = $query->latest('id')->firstOrFail();
        $customer = $booking->customer;
        $bookingItem = $booking->application_items->first();
        $motorbike = $bookingItem?->motorbike;

        if (! $customer || ! $bookingItem || ! $motorbike || ! $booking->user) {
            abort(404, 'Finance contract test data is incomplete.');
        }

        $contractStartDate = Carbon::parse($booking->contract_date ?? now());
        $contractEndDate = $contractStartDate->copy()->addMonths(5);
        $userName = trim($booking->user->first_name.' '.$booking->user->last_name);

        $pdf = $this->pdfLoadView($templates[$template], [
            'today' => now()->format('d/m/Y'),
            'SIGFILE' => '#',
            'booking' => $booking,
            'customer' => $customer,
            'motorbike' => $motorbike,
            'bookingItem' => $bookingItem,
            'subs_payment_date' => $booking->subs_payment_date,
            'user_name' => $userName,
            'document_number' => 'TEST-'.$booking->id,
            'subscriptionOption' => $this->getSubscriptionOptionDetails($booking->subscription_option),
            'contractStartDate' => $contractStartDate,
            'contractEndDate' => $contractEndDate,
            'contractStartTime' => $contractStartDate->format('H:i'),
        ])->setPaper('a4', 'portrait')
            ->setOption('isPhpEnabled', true);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="sale-contract-test-'.$template.'.pdf"',
        ]);
    }

    /**
     * Sale CONTRACT only (not rental agreement).
     * Build the 3× sale-contract INS/PCN less-terms PDFs for council PCN handling.
     * These must always go to customerservice only — never to the customer.
     *
     * Templates: contract-v6-ins-latest-less-terms / contract-v6-ins-used-latest-less-terms
     *
     * @return list<mixed>
     */
    private function buildInternalPcnInsCopies(
        $Booking,
        $Customer,
        $Motorbike,
        $BookingItems,
        string $fileName,
        string $documentNumber,
        string $pdfPath,
        int $tm,
        int $rand_no,
        bool $isUsedLatest
    ): array {
        $contractStartDate = Carbon::parse($Booking->contract_date);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        $lessTermsPdfName = $isUsedLatest
            ? 'pdf.contract-v6-ins-used-latest-less-terms'
            : 'pdf.contract-v6-ins-latest-less-terms';

        $contractDates = [
            ['start' => $contractStartDate, 'end' => $contractEndDate1],
            ['start' => $contractEndDate1, 'end' => $contractEndDate2],
            ['start' => $contractEndDate2, 'end' => $contractEndDate3],
        ];

        $lessTermsPdfs = [];
        foreach ($contractDates as $index => $dates) {
            $pdfFileName = '/sale-contract-'.($index + 1).'-'.$tm.$rand_no.'.pdf';
            $lessTermsPdfs[] = $this->pdfLoadView($lessTermsPdfName, [
                'today' => $dates['start'],
                'SIGFILE' => $fileName,
                'booking' => $Booking,
                'customer' => $Customer,
                'motorbike' => $Motorbike,
                'bookingItem' => $BookingItems,
                'subs_payment_date' => $Booking->subs_payment_date,
                'user_name' => $Booking->user->first_name.' '.$Booking->user->last_name,
                'document_number' => $documentNumber.'-'.($index + 1),
                'contractStartDate' => $dates['start'],
                'contractEndDate' => $dates['end'],
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.$pdfFileName);
        }

        return $lessTermsPdfs;
    }

    private function mailInternalPcnInsCopies(array $lessTermsPdfs): void
    {
        if ($lessTermsPdfs === []) {
            return;
        }

        Mail::to([self::CUSTOMER_SERVICE_EMAIL])->send(new HireContract([
            'title' => 'Sale Contract - PCN/INS - Internal',
            'body' => 'Thank you for choosing Neguinho Motors Ltd. Ride safe and enjoy the journey! <br> Find Attached your rental agreement. ',
            'pdf' => $lessTermsPdfs,
        ]));
    }

    private const CUSTOMER_SERVICE_EMAIL = 'customerservice@neguinhomotors.co.uk';

    private function mailFinanceContractToCustomer(Customer $customer, array $mailData): void
    {
        if (! filled($customer->email)) {
            return;
        }

        Mail::to($customer->email)->send(new HireContract(array_merge($this->withSavedPdfFiles($mailData), [
            'cc' => [self::CUSTOMER_SERVICE_EMAIL],
        ])));
    }

    private function mailRentalAgreementToCustomer(Customer $customer, array $mailData): void
    {
        if (! filled($customer->email)) {
            return;
        }

        Mail::to($customer->email)->send(new RentalAgreement(array_merge($this->withSavedPdfFiles($mailData), [
            'cc' => [self::CUSTOMER_SERVICE_EMAIL],
        ])));
    }

    /**
     * Keep disk copies of generated PDFs so the communication snapshot can store attachments
     * after the mailable has already rendered them.
     *
     * @param  array<string, mixed>  $mailData
     * @return array<string, mixed>
     */
    private function withSavedPdfFiles(array $mailData): array
    {
        $files = is_array($mailData['pdf_files'] ?? null) ? array_values($mailData['pdf_files']) : [];
        $items = $mailData['pdf'] ?? null;
        $list = $items === null ? [] : (is_array($items) ? array_values($items) : [$items]);

        foreach ($list as $item) {
            if (! is_object($item) || ! method_exists($item, 'savedPath')) {
                continue;
            }

            $path = (string) $item->savedPath();
            if ($path === '' || ! is_file($path) || (int) filesize($path) < 512) {
                continue;
            }

            $files[] = [
                'path' => $path,
                'name' => basename($path),
            ];
        }

        $unique = [];
        foreach ($files as $file) {
            if (! is_array($file)) {
                continue;
            }

            $path = (string) ($file['path'] ?? '');
            if ($path === '' || isset($unique[$path])) {
                continue;
            }

            $unique[$path] = $file;
        }

        if ($unique !== []) {
            $mailData['pdf_files'] = array_values($unique);
        }

        return $mailData;
    }

    private function mailRentalPcnCopiesToCustomerService(array $pcnPdfs): void
    {
        Mail::to([self::CUSTOMER_SERVICE_EMAIL])->send(new RentalAgreementNgn($pcnPdfs));
    }

    /**
     * Build 3× rental PCN/INS period copies for customerservice only (council PCN).
     *
     * @return array{pdf1: mixed, pdf2: mixed, pdf3: mixed}
     */
    private function buildRentalPcnInsCopies(
        RentingBooking $booking,
        Customer $customer,
        Motorbike $motorbike,
        RentingBookingItem $bookingItems,
        string $fileName,
        string $pdfPath,
        int $tm,
        int $randNo,
        int $bookingId,
        DocumentType $documentType,
    ): array {
        $pcnPdfName = 'pdf.agreement-v6-ins';
        $segments = AgreementDateTime::rentalPcnSegments(AgreementDateTime::rentalStart($booking));
        $pdfs = [];
        $userName = $booking->user->first_name.' '.$booking->user->last_name;

        foreach ($segments as $index => $segment) {
            $pcnFileName = 'rental-agreement-'.($index + 1).'-'.$tm.$randNo.'.pdf';
            $relativePath = "customers/{$booking->customer_id}/{$pcnFileName}";

            $agreement = CustomerAgreement::create([
                'customer_id' => $booking->customer_id,
                'document_type_id' => $documentType->id,
                'file_name' => $pcnFileName,
                'file_path' => $relativePath,
                'file_format' => 'pdf',
                'document_number' => '',
                'valid_until' => null,
                'is_verified' => false,
                'booking_id' => $bookingId,
            ]);

            $agreement->update([
                'document_number' => "{$booking->id}-{$booking->customer_id}-".str_pad((string) $agreement->id, 3, '0', STR_PAD_LEFT),
            ]);

            $pdfs[] = $this->pdfLoadView($pcnPdfName, [
                'agreementStartDate' => $segment['start']->format('d/m/Y H:i'),
                'agreementEndDate' => $segment['end']->format('d/m/Y H:i'),
                'today' => $segment['start']->format('d/m/Y'),
                'SIGFILE' => $fileName,
                'booking' => $booking,
                'customer' => $customer,
                'motorbike' => $motorbike,
                'bookingItem' => $bookingItems,
                'user_name' => $userName,
                'document_number' => $agreement->document_number,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($pdfPath.'/'.$pcnFileName);
        }

        return [
            'pdf1' => $pdfs[0],
            'pdf2' => $pdfs[1],
            'pdf3' => $pdfs[2],
        ];
    }

    private function sendEbikeBatterySafetyLeafletIfNeeded(
        ?Motorbike $motorbike,
        Customer $customer,
        string $pdfPath,
        int $tm,
        int $randNo,
        string $today,
        mixed $booking,
        mixed $bookingItem,
        bool $rentalMail = false,
        ?int $rentalBookingId = null,
    ): void {
        $userName = null;
        if (is_object($booking) && isset($booking->user) && $booking->user) {
            $userName = trim($booking->user->first_name.' '.$booking->user->last_name);
        }

        EbikeBatterySafetyLeaflet::sendIfEbike(
            $motorbike,
            $customer,
            $pdfPath,
            $tm,
            $randNo,
            $today,
            $booking,
            $bookingItem,
            $userName,
            $rentalMail,
            $rentalBookingId,
        );
    }

}
