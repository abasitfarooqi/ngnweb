<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
// use File;

use Illuminate\Support\Facades\Storage;

class CustomContractController extends Controller
{
    public function generateCustomContract(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'contract_type' => 'required|in:regular,used,custom',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postcode' => 'required|string|max:10',
            'license_number' => 'required|string|max:50',
            'license_authority' => 'required|string|max:100',
            'license_issuance_date' => 'required|date',
            'license_expiry_date' => 'required|date',
            'reg_no' => 'required|string|max:20',
            'type_approval' => 'required|string|max:100',
            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'engine' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'booking_id' => 'required|integer',
            'contract_date' => 'required|date',
            'vehicle_price' => 'required|numeric|min:0',
            'deposit' => 'required|numeric|min:0',
            'payment_type' => 'required|in:weekly,monthly',
            'installment' => 'required|numeric|min:0',
            'extra_items' => 'nullable|string|max:500',
            'extra_cost' => 'nullable|numeric|min:0',
            'staff_name' => 'required|string|max:255',
            'signing_date' => 'required|date',
            'signature_file' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        try {
            // Handle signature file upload
            $signatureFileName = $this->handleSignatureUpload($request->file('signature_file'), $validatedData);

            // Create mock objects similar to your existing structure
            $customer = (object) [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'dob' => Carbon::parse($validatedData['dob']),
                'phone' => $validatedData['phone'],
                'whatsapp' => $validatedData['whatsapp'] ?? 'No Whatsapp number provided',
                'email' => $validatedData['email'],
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
                'postcode' => $validatedData['postcode'],
                'license_number' => $validatedData['license_number'],
                'license_issuance_authority' => $validatedData['license_authority'],
                'license_issuance_date' => Carbon::parse($validatedData['license_issuance_date']),
                'license_expiry_date' => Carbon::parse($validatedData['license_expiry_date']),
            ];

            $motorbike = (object) [
                'reg_no' => $validatedData['reg_no'],
                'type_approval' => $validatedData['type_approval'],
                'make' => $validatedData['make'],
                'model' => $validatedData['model'],
                'engine' => $validatedData['engine'],
                'color' => $validatedData['color'],
            ];

            $booking = (object) [
                'id' => $validatedData['booking_id'],
                'customer_id' => $validatedData['booking_id'], // Using booking_id as customer_id
                'contract_date' => Carbon::parse($validatedData['contract_date']),
                'motorbike_price' => $validatedData['vehicle_price'],
                'deposit' => $validatedData['deposit'],
                'is_monthly' => $validatedData['payment_type'] === 'monthly',
                'weekly_instalment' => $validatedData['installment'],
                'extra_items' => $validatedData['extra_items'] ?? '',
                'extra' => $validatedData['extra_cost'] ?? 0,
                'is_used' => $validatedData['contract_type'] === 'used',
                'is_used_extended_custom' => $validatedData['contract_type'] === 'custom',
            ];

            $bookingItem = (object) [
                'motorbike_id' => 1, // Mock ID
            ];

            // Generate document number
            $documentNumber = $this->generateDocumentNumber($booking);

            // Calculate contract dates
            $contractDates = $this->calculateContractDates($booking->contract_date);

            // Generate PDF content directly
            $pdf = $this->generateContractPDF(
                $validatedData['contract_type'],
                $customer,
                $motorbike,
                $booking,
                $bookingItem,
                $signatureFileName,
                $validatedData['staff_name'],
                $documentNumber,
                $contractDates,
                $validatedData['signing_date']
            );

            // Return the PDF directly for download
            $fileName = "Contract-{$customer->first_name}-{$customer->last_name}-".Carbon::now()->format('Y-m-d').'.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('Custom contract generation failed: '.$e->getMessage()."\n".$e->getTraceAsString());

            return response()->json(['error' => 'Failed to generate contract: '.$e->getMessage()], 500);
        }
    }

    private function handleSignatureUpload($file, $validatedData)
    {
        $fileName = $validatedData['first_name'].'-'.$validatedData['last_name'].'-'.
                   Carbon::now()->format('Y-m-d_H-i-s').'.'.$file->getClientOriginalExtension();

        // Store in a public accessible location
        $file->storeAs('public/signatures', $fileName);

        // Return the full storage path for the PDF to use
        return storage_path('app/public/signatures/'.$fileName);
    }

    private function calculateContractDates($startDate)
    {
        $contractStartDate = Carbon::parse($startDate);
        $contractEndDate1 = $contractStartDate->copy()->addMonths(5);
        $contractEndDate2 = $contractEndDate1->copy()->addMonths(5);
        $contractEndDate3 = $contractEndDate2->copy()->addMonths(5);

        return [
            'start' => $contractStartDate,
            'end1' => $contractEndDate1,
            'end2' => $contractEndDate2,
            'end3' => $contractEndDate3,
        ];
    }

    private function generateDocumentNumber($booking)
    {
        return "{$booking->id}-{$booking->customer_id}-".str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    }

    private function generateContractPDF($contractType, $customer, $motorbike, $booking, $bookingItem, $signatureFileName, $staffName, $documentNumber, $contractDates, $signingDate)
    {
        // Determine PDF template based on contract type
        $pdfTemplate = $this->getPDFTemplate($contractType);

        // Use the first contract period (first 5 months)
        $contractStartDate = $contractDates['start'];
        $contractEndDate = $contractDates['end1'];

        try {
            // Generate PDF using your existing template
            $pdf = Pdf::loadView($pdfTemplate, [
                'contractStartDate' => $contractStartDate->format('d-F-Y H:i'),
                'contractEndDate' => $contractEndDate->format('d-F-Y H:i'),
                'today' => Carbon::parse($signingDate)->format('d/m/Y'),
                'SIGFILE' => $signatureFileName,
                'booking' => $booking,
                'customer' => $customer,
                'motorbike' => $motorbike,
                'bookingItem' => $bookingItem,
                'user_name' => $staffName,
                'document_number' => $documentNumber,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true);

            \Log::info("Generated contract PDF for: {$customer->first_name} {$customer->last_name}");

            return $pdf;
        } catch (\Exception $e) {
            \Log::error('PDF generation error: '.$e->getMessage());
            throw $e;
        }
    }

    private function generateSingleContractPDF($contractType, $customer, $motorbike, $booking, $bookingItem, $signatureFileName, $staffName, $documentNumber, $contractDates, $signingDate)
    {
        // Determine PDF template based on contract type
        $pdfTemplate = $this->getPDFTemplate($contractType);

        // Create customer directory
        $customerDir = "customers/{$booking->customer_id}";
        $pdfPath = storage_path("app/public/{$customerDir}");

        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $rand_no = rand(1, 99999);
        $tm = time();

        // Use the first contract period (first 5 months)
        $contractStartDate = $contractDates['start'];
        $contractEndDate = $contractDates['end1'];

        $fileName = "custom-contract-{$customer->first_name}-{$customer->last_name}-{$tm}{$rand_no}.pdf";
        $fullPath = $pdfPath.'/'.$fileName;

        try {
            // Generate PDF using your existing template
            $pdf = Pdf::loadView($pdfTemplate, [
                'contractStartDate' => $contractStartDate->format('d-F-Y H:i'),
                'contractEndDate' => $contractEndDate->format('d-F-Y H:i'),
                'today' => Carbon::parse($signingDate)->format('d/m/Y'),
                'SIGFILE' => $signatureFileName,
                'booking' => $booking,
                'customer' => $customer,
                'motorbike' => $motorbike,
                'bookingItem' => $bookingItem,
                'user_name' => $staffName,
                'document_number' => $documentNumber,
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true);

            // Save the PDF to disk
            $pdf->save($fullPath);

            \Log::info('Generated single contract: '.$fullPath);

            return $fullPath;
        } catch (\Exception $e) {
            \Log::error('PDF generation error: '.$e->getMessage());
            throw $e;
        }
    }

    private function generateContractPDFs($contractType, $customer, $motorbike, $booking, $bookingItem, $signatureFileName, $staffName, $documentNumber, $contractDates, $signingDate)
    {
        // Determine PDF template based on contract type
        $pdfTemplate = $this->getPDFTemplate($contractType);

        // Create customer directory
        $customerDir = "customers/{$booking->customer_id}";
        $pdfPath = storage_path("app/public/{$customerDir}");

        if (! File::isDirectory($pdfPath)) {
            File::makeDirectory($pdfPath, 0777, true, true);
        }

        $rand_no = rand(1, 99999);
        $tm = time();

        $pdfPaths = [];

        // Generate 3 contracts (5 months each)
        for ($i = 1; $i <= 3; $i++) {
            $contractStartDate = $i === 1 ? $contractDates['start'] :
                                ($i === 2 ? $contractDates['end1'] : $contractDates['end2']);
            $contractEndDate = $i === 1 ? $contractDates['end1'] :
                              ($i === 2 ? $contractDates['end2'] : $contractDates['end3']);

            $fileName = "{$i}".($i === 1 ? 'st' : ($i === 2 ? 'nd' : 'rd')).
                       "-custom-contract-ins-{$tm}{$rand_no}-{$i}.pdf";

            $fullPath = $pdfPath.'/'.$fileName;

            // Generate PDF using your existing template
            $pdf = Pdf::loadView($pdfTemplate, [
                'contractStartDate' => $contractStartDate->format('d-F-Y H:i'),
                'contractEndDate' => $contractEndDate->format('d-F-Y H:i'),
                'today' => Carbon::parse($signingDate)->format('d/m/Y'),
                'SIGFILE' => $signatureFileName,
                'booking' => $booking,
                'customer' => $customer,
                'motorbike' => $motorbike,
                'bookingItem' => $bookingItem,
                'user_name' => $staffName,
                'document_number' => $documentNumber."-{$i}",
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($fullPath);

            $pdfPaths[] = $fullPath;

            \Log::info("Generated contract {$i}: ".$fullPath);
        }

        return $pdfPaths;
    }

    private function getPDFTemplate($contractType)
    {
        switch ($contractType) {
            case 'used':
                return 'pdf.contract-v3-ins-5m-extended-used';
            case 'custom':
                return 'pdf.contract-v3-ins-5m-extended-used-custom-18m-t3';
            default:
                return 'pdf.contract-v3-ins-5m-extended';
        }
    }

    private function createContractZip($pdfPaths, $customer, $booking)
    {
        $zipFileName = "contracts-{$customer->first_name}-{$customer->last_name}-{$booking->id}-".
                      Carbon::now()->format('Y-m-d_H-i-s').'.zip';
        $zipPath = storage_path("app/public/{$zipFileName}");

        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE) === true) {
            foreach ($pdfPaths as $index => $pdfPath) {
                $contractNumber = $index + 1;
                $suffix = $contractNumber === 1 ? 'st' : ($contractNumber === 2 ? 'nd' : 'rd');
                $zipEntryName = "{$contractNumber}{$suffix}-Contract-{$customer->first_name}-{$customer->last_name}.pdf";
                $zip->addFile($pdfPath, $zipEntryName);
            }
            $zip->close();

            // Clean up individual PDF files
            foreach ($pdfPaths as $pdfPath) {
                if (file_exists($pdfPath)) {
                    unlink($pdfPath);
                }
            }
        }

        return $zipPath;
    }

    // Method to show the custom contract form
    public function showCustomContractForm()
    {
        return view('olders.frontend.custom-contract-generator');
    }
}
