<?php

namespace App\Support;

use App\Mail\HireContract;
use App\Mail\RentalAgreement;
use App\Models\Customer;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\DocumentType;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/** Generate, store and email the e-bike battery safety leaflet (customer + NGN copy). */
final class EbikeBatterySafetyLeaflet
{
    public static function sendIfEbike(
        ?Motorbike $motorbike,
        Customer $customer,
        string $pdfDirectory,
        int $timestamp,
        int $randNo,
        string $today,
        mixed $booking = null,
        mixed $bookingItem = null,
        ?string $userName = null,
        bool $rentalMail = false,
        ?int $rentalBookingId = null,
    ): void {
        if (! $motorbike || ! (bool) $motorbike->is_ebike) {
            return;
        }

        try {
            $fileName = 'battery-safety-leaflet-'.$timestamp.$randNo.'.pdf';
            $absolutePath = rtrim($pdfDirectory, '/').'/'.$fileName;
            $relativePath = self::relativeStoragePath($pdfDirectory, $fileName);

            $pdf = AgreementPdfGenerator::loadView('livewire.agreements.pdf.templates.battery-safety-leaflet', [
                'today' => $today,
                'booking' => $booking,
                'customer' => $customer,
                'motorbike' => $motorbike,
                'bookingItem' => $bookingItem,
                'user_name' => $userName ?: 'NGN Staff',
            ])->setPaper('a4', 'portrait')
                ->setOption('isPhpEnabled', true)
                ->save($absolutePath);

            self::storeAgreementRecord(
                $customer,
                $fileName,
                $relativePath,
                $rentalBookingId,
                $booking instanceof FinanceApplication ? (int) $booking->id : null,
            );

            $mailData = [
                'title' => 'E-Bike Battery Safety Leaflet',
                'body' => 'Please find attached the E-Bike Battery Safety Leaflet. This is an important safety document — please read it carefully and keep it for your records.',
                'pdf' => $pdf,
                'pdf_files' => [[
                    'path' => $absolutePath,
                    'name' => 'batterySafetyDataLeaflet.pdf',
                ]],
            ];

            $mailClass = $rentalMail ? RentalAgreement::class : HireContract::class;

            if (filled($customer->email)) {
                Mail::to($customer->email)->send(new $mailClass(array_merge($mailData, [
                    'cc' => ['customerservice@neguinhomotors.co.uk'],
                ])));
            }
        } catch (Throwable $e) {
            Log::error(__FILE__.' battery safety leaflet failed: '.$e->getMessage());
        }
    }

    private static function relativeStoragePath(string $pdfDirectory, string $fileName): string
    {
        $publicRoot = storage_path('app/public/');

        if (str_starts_with($pdfDirectory, $publicRoot)) {
            return ltrim(substr(rtrim($pdfDirectory, '/').'/'.$fileName, strlen($publicRoot)), '/');
        }

        return 'customers/'.$fileName;
    }

    private static function storeAgreementRecord(
        Customer $customer,
        string $fileName,
        string $relativePath,
        ?int $rentalBookingId,
        ?int $financeApplicationId,
    ): void {
        $slug = 'ebike_battery_safety_leaflet';

        $documentType = DocumentType::firstOrCreate(
            ['code' => $slug],
            [
                'slug' => $slug,
                'name' => 'E-Bike Battery Safety Leaflet',
                'description' => 'Battery safety leaflet issued with e-bike rental or purchase. Sent by NGN — not a customer upload.',
                'is_mandatory' => false,
                'required_for' => [],
                'sort_order' => 11,
            ]
        );

        $documentType->forceFill([
            'is_mandatory' => false,
            'required_for' => [],
            'description' => 'Battery safety leaflet issued with e-bike rental or purchase. Sent by NGN — not a customer upload.',
        ])->save();

        $attributes = [
            'customer_id' => $customer->id,
            'document_type_id' => $documentType->id,
            'file_name' => $fileName,
            'file_path' => $relativePath,
            'file_format' => 'pdf',
            'document_number' => '',
            'valid_until' => null,
            'is_verified' => true,
        ];

        if ($rentalBookingId) {
            $agreement = CustomerAgreement::create($attributes + [
                'booking_id' => $rentalBookingId,
            ]);
        } else {
            $agreement = CustomerContract::create($attributes + [
                'application_id' => $financeApplicationId,
            ]);
        }

        $prefix = $rentalBookingId
            ? "{$rentalBookingId}-{$customer->id}"
            : "EBIKE-{$financeApplicationId}-{$customer->id}";

        $agreement->update([
            'document_number' => $prefix.'-'.str_pad((string) $agreement->id, 3, '0', STR_PAD_LEFT),
        ]);
    }
}
