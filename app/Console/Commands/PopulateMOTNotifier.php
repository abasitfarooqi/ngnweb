<?php

namespace App\Console\Commands;

use App\Models\ClubMember;
use App\Models\Motorbike;
use App\Models\MotorbikeAnnualCompliance;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use App\Models\MotorbikeRegistration;
use App\Models\NgnMotNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PopulateMOTNotifier extends Command
{
    protected $signature = 'mot:populate-notifier';

    protected $description = 'Populates the MOT notifier table with upcoming MOT due dates from multiple sources.';

    public function handle()
    {
        try {
            Log::info('Starting MOT Notifier population with multi-source data...');

            $unifiedData = [];

            // 1. Load MOT bookings
            $bookings = DB::table('mot_bookings as mb')
                ->select(
                    'mb.vehicle_registration as reg_no',
                    'mb.customer_name',
                    'mb.customer_contact',
                    'mb.customer_email',
                    'mb.updated_at'
                )
                ->where('mb.id', function ($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('mot_bookings')
                        ->whereColumn('vehicle_registration', 'mb.vehicle_registration');
                })
                ->get();

            foreach ($bookings as $booking) {
                $reg = strtoupper(str_replace(' ', '', $booking->reg_no));
                $unifiedData[$reg] = [
                    'reg_no' => $reg,
                    'customer_name' => $booking->customer_name,
                    'customer_contact' => $booking->customer_contact,
                    'customer_email' => $booking->customer_email,
                    'source' => 'mot_booking',
                    'timestamp' => $booking->updated_at,
                ];
            }

            // 2. Load Club Members independently
            $clubMembers = ClubMember::all();
            foreach ($clubMembers as $club) {
                $reg = strtoupper(str_replace(' ', '', $club->vrm));
                if (! isset($unifiedData[$reg]) || $club->updated_at > $unifiedData[$reg]['timestamp']) {
                    $unifiedData[$reg] = [
                        'reg_no' => $reg,
                        'customer_name' => $club->full_name,
                        'customer_contact' => $club->phone,
                        'customer_email' => $club->email,
                        'source' => 'club_member',
                        'timestamp' => $club->updated_at,
                    ];
                }
            }

            // 3. Load Delivery Order Enquiries independently
            $enquiries = MotorbikeDeliveryOrderEnquiries::all();
            foreach ($enquiries as $enquiry) {
                $reg = strtoupper(str_replace(' ', '', $enquiry->vrm));
                if (! isset($unifiedData[$reg]) || $enquiry->updated_at > $unifiedData[$reg]['timestamp']) {
                    $unifiedData[$reg] = [
                        'reg_no' => $reg,
                        'customer_name' => $enquiry->full_name,
                        'customer_contact' => $enquiry->phone,
                        'customer_email' => $enquiry->email,
                        'source' => 'delivery_order_enquiry',
                        'timestamp' => $enquiry->updated_at,
                    ];
                }
            }

            // 4. Process each finalised reg_no
            foreach ($unifiedData as $record) {
                $motorbike = Motorbike::where('reg_no', $record['reg_no'])->first();

                if (! $motorbike) {
                    Log::warning("Reg {$record['reg_no']} not in motorbikes. Trying DVLA lookup...");

                    try {
                        $reg = strtoupper(str_replace(' ', '', $record['reg_no']));

                        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Z]{3}$/', $reg)) {
                            Log::warning("Skipping DVLA lookup for {$reg} - invalid format.");

                            continue;
                        }

                        $response = Http::withHeaders([
                            'x-api-key' => env('DVLA_VEH_API'),
                            'Content-Type' => 'application/json',
                        ])->post('https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles', [
                            'registrationNumber' => $reg,
                        ]);

                        if ($response->status() === 404) {
                            Log::warning("DVLA vehicle not found for {$reg}");

                            continue;
                        }

                        if ($response->successful()) {
                            $vehicle = json_decode($response->body(), true);

                            $motorbike = Motorbike::create([
                                'vin_number' => $vehicle['vin'] ?? 'ns-'.$reg,
                                'reg_no' => $reg,
                                'make' => $vehicle['make'] ?? '',
                                'model' => $vehicle['make'] ?? '',
                                'year' => $vehicle['yearOfManufacture'] ?? '',
                                'engine' => $vehicle['engineCapacity'] ?? '',
                                'color' => $vehicle['colour'] ?? '',
                                'fuel_type' => $vehicle['fuelType'] ?? '',
                                'vehicle_profile_id' => 2,
                            ]);

                            MotorbikeRegistration::create([
                                'motorbike_id' => $motorbike->id,
                                'registration_number' => $vehicle['registrationNumber'] ?? $reg,
                                'start_date' => now(),
                                'end_date' => null,
                            ]);

                            MotorbikeAnnualCompliance::create([
                                'motorbike_id' => $motorbike->id,
                                'year' => date('Y'),
                                'mot_status' => $vehicle['motStatus'] ?? '',
                                'road_tax_status' => $vehicle['taxStatus'] ?? '',
                                'insurance_status' => 'N/A',
                                'tax_due_date' => $vehicle['taxDueDate'] ?? null,
                                'insurance_due_date' => now(),
                                'mot_due_date' => $vehicle['motExpiryDate'] ?? null,
                            ]);

                            Log::info("DVLA data added for {$reg}");
                        } else {
                            Log::error("DVLA lookup failed for {$reg}: ".$response->body());

                            continue;
                        }
                    } catch (\Exception $e) {
                        Log::error("DVLA check exception for {$reg}: ".$e->getMessage());

                        continue;
                    }
                }

                // Get latest compliance data
                $compliance = $motorbike->annualCompliances()->latest()->first();
                if (! $compliance || ! $compliance->mot_due_date) {
                    continue;
                }

                // Calculate days to MOT
                $motDueDate = Carbon::parse($compliance->mot_due_date)->startOfDay();
                $daysToMot = Carbon::now()->startOfDay()->diffInDays($motDueDate, false);

                // Clean phone number
                $cleanedPhone = str_replace([' ', '-', '(', ')'], '', $record['customer_contact']);
                if (! empty($cleanedPhone) && preg_match('/^\+?44[0-9]{10}$/', $cleanedPhone)) {
                    $cleanedPhone = preg_replace('/^\+?44/', '0', $cleanedPhone);
                }

                // Validate email
                $customerEmail = filter_var($record['customer_email'], FILTER_VALIDATE_EMAIL)
                    ? $record['customer_email']
                    : 'customerservice@ngnmotors.co.uk';

                // Prepare notifier payload
                $notifierData = [
                    'motorbike_id' => $motorbike->id,
                    'motorbike_reg' => $motorbike->reg_no,
                    'mot_due_date' => $compliance->mot_due_date,
                    'tax_due_date' => $compliance->tax_due_date,
                    'mot_status' => $daysToMot < 0
                        ? 'Expired'
                        : ($daysToMot <= 30 ? 'Due in '.$daysToMot.' days' : 'MOT valid'),
                    'updated_at' => now(),
                ];

                // Insert or Update notifier
                $existing = NgnMotNotifier::where('motorbike_id', $motorbike->id)->first();

                if ($existing) {
                    // Detect if compliance data has changed
                    $complianceChanged = (
                        $existing->mot_due_date != $compliance->mot_due_date ||
                        $existing->tax_due_date != $compliance->tax_due_date ||
                        $existing->mot_status != (
                            $daysToMot < 0
                                ? 'Expired'
                                : ($daysToMot <= 30 ? 'Due in '.$daysToMot.' days' : 'MOT valid')
                        )
                    );

                    if ($complianceChanged) {
                        $existing->update(array_merge(
                            $notifierData,
                            [
                                'mot_is_notified_30' => $existing->mot_is_notified_30,
                                'mot_is_notified_10' => $existing->mot_is_notified_10,
                                'mot_email_sent_30' => $existing->mot_email_sent_30,
                                'mot_email_sent_10' => $existing->mot_email_sent_10,
                                'mot_email_sent_expired' => $existing->mot_email_sent_expired,
                                'mot_last_email_notification_date' => $existing->mot_last_email_notification_date,
                                'customer_name' => $record['customer_name'],
                                'customer_contact' => $cleanedPhone,
                                'customer_email' => $customerEmail,
                            ]
                        ));
                        Log::info("Updated notifier (compliance change): {$motorbike->reg_no}");
                    } else {
                        Log::info("No compliance changes for: {$motorbike->reg_no}");
                    }
                } else {
                    if ($daysToMot > 30) {
                        continue;
                    }
                    NgnMotNotifier::create(array_merge(
                        $notifierData,
                        [
                            'customer_name' => $record['customer_name'],
                            'customer_contact' => $cleanedPhone,
                            'customer_email' => $customerEmail,
                            'mot_notify_email' => true,
                            'mot_notify_phone' => false,
                            'mot_is_notified_30' => false,
                            'mot_is_notified_10' => false,
                            'mot_email_sent_30' => false,
                            'mot_email_sent_10' => false,
                            'mot_email_sent_expired' => false,
                            'mot_last_email_notification_date' => null,
                            'created_at' => now(),
                        ]
                    ));
                    Log::info("Inserted notifier: {$motorbike->reg_no}");
                }
            }

            Log::info('Populate MOT Notifier: Completed.');
        } catch (\Exception $e) {
            Log::error('MOT Notifier Error: '.$e->getMessage());
        }
    }
}
