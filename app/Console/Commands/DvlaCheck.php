<?php

namespace App\Console\Commands;

use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DvlaCheck extends Command
{
    protected $signature = 'dvla:check';

    protected $description = 'DVLA check for all motorbikes in the system.';

    public function handle()
    {
        $apiKey = config('services.dvla.api_key');
        if (blank($apiKey)) {
            $this->error('DVLA check aborted: DVLA API key is not configured.');
            Log::error('DVLA check aborted: DVLA API key is not configured.');

            return Command::FAILURE;
        }

        $motorbikes = Motorbike::all();
        $total = $motorbikes->count();
        $successCount = 0;
        $failureCount = 0;

        foreach ($motorbikes as $motorbike) {
            try {
                $id = $motorbike->id;

                if (blank($motorbike->reg_no)) {
                    $failureCount++;
                    Log::warning('DVLA Check skipped motorbike with missing registration number.', [
                        'motorbike_id' => $id,
                    ]);

                    continue;
                }

                $response = Http::timeout(20)->withHeaders([
                    'x-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                    'registrationNumber' => $motorbike->reg_no,
                ]);

                if (! $response->successful()) {
                    $failureCount++;
                    Log::warning('DVLA Check request failed for motorbike.', [
                        'motorbike_id' => $id,
                        'registration_number' => $motorbike->reg_no,
                        'status' => $response->status(),
                        'response' => $response->json() ?: $response->body(),
                    ]);

                    continue;
                }

                $request = $response->json();

                $complianceData = [
                    'motorbike_id' => $id,
                    'year' => now()->year,
                    'tax_due_date' => $request['taxDueDate'] ?? null,
                    'insurance_due_date' => null, // Assuming you do not have this data from the API
                    'mot_due_date' => $request['motExpiryDate'] ?? null,
                    'road_tax_status' => $request['taxStatus'] ?? 'No details held by DVLA',
                    'mot_status' => $request['motStatus'] ?? 'No details held by DVLA',
                    'insurance_status' => $request['insuranceStatus'] ?? 'No details held by DVLA',
                ];

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
                Log::error('DVLA Check failed for motorbike ID: '.$id, ['error' => $e->getMessage()]);
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

        return $successCount === 0 && $total > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
