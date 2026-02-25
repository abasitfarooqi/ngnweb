<?php

namespace App\Console\Commands;

use App\Mail\JobCompletionNotification;
use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class DvlaCheck extends Command
{
    protected $signature = 'dvla:check';

    protected $description = 'DVLA check for all motorbikes in the system.';

    public function handle()
    {
        $motorbikes = Motorbike::all();
        $total = $motorbikes->count();
        $successCount = 0;
        $failureCount = 0;

        foreach ($motorbikes as $motorbike) {
            try {
                $id = $motorbike->id;
                $response = Http::withHeaders([
                    'x-api-key' => env('DVLA_VEH_API'),
                    'Content-Type' => 'application/json',
                ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                    'registrationNumber' => $motorbike->reg_no,
                ]);

                $request = json_decode($response->body());

                $complianceData = [
                    'motorbike_id' => $id,
                    'year' => now()->year,
                    'tax_due_date' => $request->taxDueDate ?? null,
                    'insurance_due_date' => null, // Assuming you do not have this data from the API
                    'mot_due_date' => $request->motExpiryDate ?? null,
                ];

                if (isset($request->taxStatus)) {
                    $complianceData['road_tax_status'] = $request->taxStatus;
                } else {
                    $complianceData['road_tax_status'] = 'No details held by DVLA';
                }

                if (isset($request->motStatus)) {
                    $complianceData['mot_status'] = $request->motStatus;
                } else {
                    $complianceData['mot_status'] = 'No details held by DVLA';
                }

                if (isset($request->insuranceStatus)) {
                    $complianceData['insurance_status'] = $request->insuranceStatus;
                } else {
                    $complianceData['insurance_status'] = 'No details held by DVLA';
                }

                MotorbikeAnnualCompliance::updateOrCreate(
                    ['motorbike_id' => $id],
                    $complianceData
                );

                MotorbikeRegistration::updateOrCreate(
                    ['motorbike_id' => $id],
                    [
                        'registration_number' => $motorbike->reg_no,
                        'start_date' => now(), // Assuming the start date is the current date
                        'end_date' => null, // Assuming you do not have this data from the API
                    ]
                );

                $successCount++;
            } catch (\Exception $e) {
                $failureCount++;
                // Log the error for further inspection
                \Log::error('DVLA Check failed for motorbike ID: '.$id, ['error' => $e->getMessage()]);
            }
        }

        $totalProcessed = $successCount + $failureCount;

        $data = [
            'email' => 'customerservice@neguinhomotors.co.uk',
            'title' => 'DVLA Check Job Completed',
            'total' => $total,
            'totalProcessed' => $totalProcessed,
            'successCount' => $successCount,
            'failureCount' => $failureCount,
        ];

        // Temporary disable email sending
        //    Mail::to($data['email'])->send(new JobCompletionNotification($data));

        $this->info("DVLA check job completed: $successCount out of $total motorbikes updated.");
    }
}
