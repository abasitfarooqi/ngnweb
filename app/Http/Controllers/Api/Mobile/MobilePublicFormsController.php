<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MotChecker;
use App\Models\MotTaxAlertSubscription;
use App\Models\ServiceBooking;
use App\Services\DvlaVehicleEnquiryService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobilePublicFormsController extends Controller
{
    public function serviceContent(string $slug): JsonResponse
    {
        $payload = match ($slug) {
            'mot' => [
                'title' => 'MOT',
                'cta' => ['label' => 'Book MOT', 'enquiry_module' => 'mot'],
                'blocks' => [
                    ['type' => 'intro', 'title' => 'MOT checker and booking', 'body' => 'Run MOT check, subscribe to reminders, then submit booking enquiry.'],
                ],
            ],
            'repairs/basic' => [
                'title' => 'Basic service',
                'cta' => ['label' => 'Book basic service', 'enquiry_module' => 'basic_service'],
                'blocks' => [['type' => 'package', 'title' => 'Basic package', 'body' => 'Basic maintenance checks and oil/filter service.']],
            ],
            'repairs/full' => [
                'title' => 'Full service',
                'cta' => ['label' => 'Book full service', 'enquiry_module' => 'full_service'],
                'blocks' => [['type' => 'package', 'title' => 'Full package', 'body' => 'Comprehensive service and detailed inspection coverage.']],
            ],
            'repairs/comparison' => [
                'title' => 'Service comparison',
                'cta' => ['label' => 'Send repair enquiry', 'enquiry_module' => 'repairs'],
                'blocks' => [['type' => 'comparison', 'title' => 'Basic vs full', 'body' => 'Use table layout in app for side-by-side package differences.']],
            ],
            'recovery' => [
                'title' => 'Recovery and delivery',
                'cta' => ['label' => 'Get quote', 'mobile_endpoint' => '/api/v1/mobile/portal/recovery/quote'],
                'blocks' => [['type' => 'flow', 'title' => 'Quote then request', 'body' => 'Collect bike and deliver bike with distance-based pricing.']],
            ],
            'rentals' => [
                'title' => 'Rentals',
                'cta' => ['label' => 'Browse rentals', 'mobile_endpoint' => '/api/v1/mobile/rentals'],
                'blocks' => [['type' => 'flow', 'title' => 'Browse then enquire', 'body' => 'Card-led browse with weekly price and enquiry CTA.']],
            ],
            default => null,
        };

        if (! $payload) {
            return response()->json(['message' => 'Service content not found'], 404);
        }

        return response()->json(['data' => $payload]);
    }

    public function motCheck(Request $request, DvlaVehicleEnquiryService $dvla): JsonResponse
    {
        $payload = $request->validate([
            'reg_no' => ['required', 'string', 'min:2', 'max:10'],
            'notify_email' => ['nullable', 'email'],
        ]);

        $reg = strtoupper(str_replace(' ', '', trim((string) $payload['reg_no'])));
        $result = $dvla->lookup($reg);
        if (! ($result['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'Unable to check MOT right now.',
            ], 422);
        }

        $body = $result['body'] ?? [];
        $motExpiry = $body['motExpiryDate'] ?? null;
        $motDueDate = $motExpiry ? Carbon::parse($motExpiry)->format('Y-m-d') : null;

        if (! empty($payload['notify_email'])) {
            MotChecker::query()->updateOrCreate(
                ['vehicle_registration' => $reg, 'email' => strtolower(trim((string) $payload['notify_email']))],
                ['mot_due_date' => $motDueDate]
            );
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'registration' => $reg,
                'mot_status' => $body['motStatus'] ?? 'No details held by DVLA',
                'mot_expiry' => $motExpiry ? Carbon::parse($motExpiry)->format('j F Y') : null,
                'tax_status' => $body['taxStatus'] ?? 'No details held by DVLA',
                'tax_due' => ! empty($body['taxDueDate']) ? Carbon::parse((string) $body['taxDueDate'])->format('j F Y') : null,
                'make' => $body['make'] ?? null,
                'model' => $body['model'] ?? null,
                'year' => $body['yearOfManufacture'] ?? null,
            ],
        ]);
    }

    public function motAlerts(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'first_name' => ['required', 'string', 'min:2'],
            'last_name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'reg_no' => ['required', 'string', 'min:2', 'max:10'],
            'notify_email' => ['nullable', 'boolean'],
            'notify_phone' => ['nullable', 'boolean'],
            'enable_deals' => ['nullable', 'boolean'],
        ]);

        $row = MotTaxAlertSubscription::query()->create([
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'email' => strtolower(trim((string) $payload['email'])),
            'phone' => trim((string) $payload['phone']),
            'vehicle_registration' => strtoupper(str_replace(' ', '', trim((string) $payload['reg_no']))),
            'notify_email' => (bool) ($payload['notify_email'] ?? true),
            'notify_sms' => (bool) ($payload['notify_phone'] ?? false),
            'enable_deals' => (bool) ($payload['enable_deals'] ?? false),
        ]);

        return response()->json([
            'message' => 'Alert subscription created.',
            'data' => [
                'id' => $row->id,
                'vehicle_registration' => $row->vehicle_registration,
            ],
        ], 201);
    }

    public function financeContent(): JsonResponse
    {
        return response()->json([
            'data' => [
                'calculator' => [
                    'defaults' => ['loan_amount' => 3000, 'deposit' => 500, 'term' => 12, 'rate' => 0],
                    'allowed_terms' => [6, 12],
                    'formula' => '((loan_amount - deposit) / term) where rate is 0 on current public page',
                ],
                'application_fields' => [
                    'first_name', 'last_name', 'email', 'phone', 'employment_status',
                    'bike_interest', 'notes', 'consent', 'loan_amount', 'deposit', 'term',
                ],
            ],
        ]);
    }

    public function financeCalculate(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'loan_amount' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'term' => ['required', 'integer', 'in:6,12'],
            'rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $principal = max(0, (float) $payload['loan_amount'] - (float) $payload['deposit']);
        $rate = max(0, (float) ($payload['rate'] ?? 0));
        $term = (int) $payload['term'];
        $monthlyRate = ($rate / 100) / 12;

        $monthly = $monthlyRate > 0
            ? round($principal * ($monthlyRate * pow(1 + $monthlyRate, $term)) / (pow(1 + $monthlyRate, $term) - 1), 2)
            : round($principal / $term, 2);

        return response()->json([
            'data' => [
                'monthly_payment' => $monthly,
                'principal' => round($principal, 2),
                'term' => $term,
                'rate' => $rate,
            ],
        ]);
    }

    public function financeApply(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'first_name' => ['required', 'string', 'min:2'],
            'last_name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'employment_status' => ['required', 'string'],
            'bike_interest' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'consent' => ['accepted'],
            'loan_amount' => ['required', 'numeric', 'min:0'],
            'deposit' => ['required', 'numeric', 'min:0'],
            'term' => ['required', 'integer', 'in:6,12'],
        ]);

        $booking = ServiceBooking::query()->create([
            'customer_id' => null,
            'customer_auth_id' => null,
            'submission_context' => 'guest',
            'enquiry_type' => 'finance',
            'service_type' => 'Finance enquiry',
            'subject' => 'Finance enquiry',
            'description' => implode(' | ', array_filter([
                'Bike: '.trim((string) ($payload['bike_interest'] ?? 'N/A')),
                'Finance amount GBP: '.number_format((float) $payload['loan_amount'], 2, '.', ''),
                'Deposit GBP: '.number_format((float) $payload['deposit'], 2, '.', ''),
                'Term months: '.(int) $payload['term'],
                'Employment: '.trim((string) $payload['employment_status']),
                ! empty($payload['notes']) ? 'Notes: '.trim((string) $payload['notes']) : null,
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim($payload['first_name'].' '.$payload['last_name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => 'Finance enquiry',
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Finance enquiry received.',
            'data' => ['enquiry_id' => $booking->id, 'status' => $booking->status],
        ], 201);
    }

    public function contactCallback(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'phone' => ['required', 'string', 'min:10'],
            'preferred_time' => ['required', 'string'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => 'contact',
            'service_type' => 'Call back request',
            'subject' => 'Call back request',
            'description' => implode(' | ', array_filter([
                'Preferred time: '.trim((string) $payload['preferred_time']),
                ! empty($payload['message']) ? 'Message: '.trim((string) $payload['message']) : null,
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => 'N/A',
            'email' => null,
        ]);

        return response()->json([
            'message' => 'Call back request received.',
            'data' => ['enquiry_id' => $booking->id],
        ], 201);
    }

    public function contactTradeAccount(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'company_name' => ['required', 'string', 'min:2'],
            'contact_name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'address' => ['required', 'string', 'min:5'],
            'vat_number' => ['nullable', 'string', 'max:32'],
            'message' => ['required', 'string', 'min:10'],
        ]);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => 'trade',
            'service_type' => 'Trade account',
            'subject' => 'Trade account',
            'description' => implode(' | ', array_filter([
                'Company: '.trim((string) $payload['company_name']),
                'Address: '.trim((string) $payload['address']),
                ! empty($payload['vat_number']) ? 'VAT: '.trim((string) $payload['vat_number']) : null,
                'Message: '.trim((string) $payload['message']),
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['contact_name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => 'N/A',
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Trade account enquiry received.',
            'data' => ['enquiry_id' => $booking->id],
        ], 201);
    }

    public function contactServiceBooking(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'selected_branch' => ['nullable', 'integer', 'exists:branches,id'],
            'service_type' => ['required', 'string', 'max:255'],
            'reg_no' => ['nullable', 'string', 'max:30'],
            'make' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'preferred_date' => ['nullable', 'date'],
            'preferred_time' => ['nullable', 'date_format:H:i'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => ServiceBooking::inferEnquiryType((string) $payload['service_type'], (string) ($payload['message'] ?? '')),
            'service_type' => trim((string) $payload['service_type']),
            'subject' => trim((string) $payload['service_type']),
            'description' => implode(' | ', array_filter([
                ! empty($payload['selected_branch']) ? 'Branch ID: '.(int) $payload['selected_branch'] : null,
                ! empty($payload['make']) ? 'Make: '.trim((string) $payload['make']) : null,
                ! empty($payload['model']) ? 'Model: '.trim((string) $payload['model']) : null,
                ! empty($payload['message']) ? 'Message: '.trim((string) $payload['message']) : null,
            ])),
            'requires_schedule' => ! empty($payload['preferred_date']) || ! empty($payload['preferred_time']),
            'booking_date' => $payload['preferred_date'] ?? null,
            'booking_time' => $payload['preferred_time'] ?? null,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => ! empty($payload['reg_no']) ? trim((string) $payload['reg_no']) : 'N/A',
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Service booking enquiry received.',
            'data' => ['enquiry_id' => $booking->id, 'status' => $booking->status],
        ], 201);
    }

    public function salesEnquiry(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => 'sales',
            'service_type' => 'Sales enquiry',
            'subject' => 'Sales enquiry',
            'description' => ! empty($payload['message']) ? trim((string) $payload['message']) : 'General sales enquiry from mobile app.',
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => 'N/A',
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Sales enquiry received.',
            'data' => ['enquiry_id' => $booking->id, 'status' => $booking->status],
        ], 201);
    }

    public function rentalEnquiry(Request $request, int $id): JsonResponse
    {
        $bike = \App\Models\Motorbike::query()
            ->with('currentRentingPricing')
            ->findOrFail($id);

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'period' => ['nullable', 'string', 'max:50'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $pricing = $bike->currentRentingPricing;
        $bikeName = trim((string) $bike->make.' '.$bike->model);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => 'rental',
            'service_type' => 'Rental enquiry',
            'subject' => 'Rental enquiry: '.$bikeName,
            'description' => implode(' | ', array_filter([
                'Bike: '.$bikeName.' (ID: '.$bike->id.')',
                $pricing ? 'Weekly price: £'.number_format((float) ($pricing->weekly_price ?? 0), 2) : null,
                ! empty($payload['period']) ? 'Preferred period: '.trim((string) $payload['period']) : null,
                ! empty($payload['message']) ? 'Message: '.trim((string) $payload['message']) : null,
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => (string) ($bike->reg_no ?? 'N/A'),
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Rental enquiry received.',
            'data' => ['enquiry_id' => $booking->id, 'status' => $booking->status],
        ], 201);
    }

    public function bikeEnquiry(Request $request, string $type, int $id): JsonResponse
    {
        if ($type === 'new') {
            $bike = \App\Models\Motorcycle::query()->findOrFail($id);
            $bikeName = trim((string) $bike->make.' '.$bike->model);
            $price = (float) ($bike->sale_new_price ?? 0);
        } else {
            $bike = \App\Models\Motorbike::query()
                ->join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')
                ->select('motorbikes.*', 'motorbikes_sale.price')
                ->where('motorbikes.id', $id)
                ->firstOrFail();
            $bikeName = trim((string) $bike->make.' '.$bike->model);
            $price = (float) ($bike->price ?? 0);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'min:10'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => 'sales',
            'service_type' => 'Bike enquiry',
            'subject' => ucfirst($type).' bike enquiry: '.$bikeName,
            'description' => implode(' | ', array_filter([
                'Bike: '.$bikeName.' (ID: '.$id.', Type: '.$type.')',
                'Price: £'.number_format($price, 2),
                ! empty($payload['message']) ? 'Message: '.trim((string) $payload['message']) : null,
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['name']),
            'phone' => trim((string) $payload['phone']),
            'reg_no' => property_exists($bike, 'reg_no') ? ((string) ($bike->reg_no ?? 'N/A')) : 'N/A',
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Bike enquiry received.',
            'data' => ['enquiry_id' => $booking->id, 'status' => $booking->status],
        ], 201);
    }

    public function contactGeneral(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'min:10'],
            'topic' => ['nullable', 'string', 'max:100'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $booking = ServiceBooking::query()->create([
            'enquiry_type' => 'contact',
            'service_type' => 'General contact',
            'subject' => ! empty($payload['topic']) ? trim((string) $payload['topic']) : 'General contact',
            'description' => implode(' | ', array_filter([
                ! empty($payload['branch_id']) ? 'Branch ID: '.(int) $payload['branch_id'] : null,
                ! empty($payload['topic']) ? 'Topic: '.trim((string) $payload['topic']) : null,
                'Message: '.trim((string) $payload['message']),
            ])),
            'requires_schedule' => false,
            'status' => 'Pending',
            'fullname' => trim((string) $payload['name']),
            'phone' => trim((string) ($payload['phone'] ?? '')),
            'reg_no' => 'N/A',
            'email' => trim((string) $payload['email']),
        ]);

        return response()->json([
            'message' => 'Contact message received.',
            'data' => ['enquiry_id' => $booking->id, 'status' => $booking->status],
        ], 201);
    }

    public function motBook(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'date_of_appointment' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', 'string', 'max:10'],
            'motorbike_reg_no' => ['required', 'string', 'min:2', 'max:10'],
            'motorbike_make' => ['required', 'string', 'min:2', 'max:50'],
            'motorbike_model' => ['required', 'string', 'min:2', 'max:50'],
            'customer_name' => ['required', 'string', 'min:2'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['required', 'string', 'min:10'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $regNo = strtoupper(trim((string) $payload['motorbike_reg_no']));
        $appointmentStart = trim($payload['date_of_appointment'].' '.$payload['time_slot']);

        $motBooking = \App\Models\MOTBooking::withoutEvents(fn () => \App\Models\MOTBooking::query()->create([
            'branch_id' => (int) $payload['branch_id'],
            'vehicle_registration' => $regNo,
            'date_of_appointment' => $payload['date_of_appointment'],
            'start' => $appointmentStart,
            'end' => $appointmentStart,
            'customer_name' => trim((string) $payload['customer_name']),
            'customer_contact' => trim((string) $payload['customer_phone']),
            'customer_email' => trim((string) $payload['customer_email']),
            'status' => 'pending',
            'title' => 'pending MOT '.$regNo.' '.trim((string) $payload['customer_name']),
            'notes' => implode("\n", array_filter([
                'Make: '.trim((string) $payload['motorbike_make']),
                'Model: '.trim((string) $payload['motorbike_model']),
                ! empty($payload['notes']) ? 'Notes: '.trim((string) $payload['notes']) : null,
                'Source: api.v1.mobile.public.mot.book',
            ])),
            'all_day' => false,
            'is_validate' => true,
        ]));

        return response()->json([
            'message' => 'MOT booking submitted.',
            'data' => [
                'mot_booking_id' => $motBooking->id,
                'vehicle_registration' => $motBooking->vehicle_registration,
                'date_of_appointment' => optional($motBooking->date_of_appointment)->toDateTimeString(),
                'status' => $motBooking->status,
            ],
        ], 201);
    }

    public function recoveryVehicleTypes(): JsonResponse
    {
        return response()->json([
            'vehicle_types' => \App\Models\DeliveryVehicleType::query()
                ->orderBy('id')
                ->get(['id', 'name', 'cc_range', 'additional_fee']),
        ]);
    }

    public function recoveryQuotePublic(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'distance_miles' => ['required', 'numeric', 'min:0'],
            'vehicle_type_id' => ['required', 'integer', 'exists:delivery_vehicle_types,id'],
            'moveable' => ['required', 'boolean'],
            'pick_up_datetime' => ['required', 'date'],
        ]);

        $distance = (float) $payload['distance_miles'];
        $vehicleTypeId = (int) $payload['vehicle_type_id'];
        $baseFee = 25.0;
        $distanceFee = max(0, $distance - 3) * 3;
        $motorcycleTypeFee = match ($vehicleTypeId) { 2 => 5.0, 3 => 10.0, default => 0.0 };
        $hour = (int) date('H', strtotime((string) $payload['pick_up_datetime']));
        $timeSurcharge = (($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 20)) ? 0.15 : (($hour >= 21 || $hour < 7) ? 0.25 : 0.0);
        $additionalFees = ((bool) $payload['moveable']) ? 0.0 : 15.0;
        $totalCost = round((($baseFee + $distanceFee + $motorcycleTypeFee) * (1 + $timeSurcharge)) + $additionalFees, 2);

        return response()->json([
            'distance_miles' => $distance,
            'vehicle_type_id' => $vehicleTypeId,
            'total_cost' => $totalCost,
        ]);
    }

    public function recoveryRequestPublic(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'pickup_postcode' => ['required', 'string', 'max:20'],
            'dropoff_postcode' => ['required', 'string', 'max:20'],
            'pickup_address' => ['required', 'string', 'max:255'],
            'dropoff_address' => ['required', 'string', 'max:255'],
            'pick_up_datetime' => ['required', 'date', 'after_or_equal:now'],
            'vrm' => ['required', 'string', 'max:20'],
            'vehicle_type_id' => ['required', 'exists:delivery_vehicle_types,id'],
            'moveable' => ['required', 'boolean'],
            'documents' => ['required', 'boolean'],
            'keys' => ['required', 'boolean'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'customer_address' => ['required', 'string', 'max:255'],
            'distance_miles' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:3000'],
        ]);

        $distance = (float) $payload['distance_miles'];
        $vehicleTypeId = (int) $payload['vehicle_type_id'];
        $baseFee = 25.0;
        $distanceFee = max(0, $distance - 3) * 3;
        $motorcycleTypeFee = match ($vehicleTypeId) { 2 => 5.0, 3 => 10.0, default => 0.0 };
        $hour = (int) date('H', strtotime((string) $payload['pick_up_datetime']));
        $timeSurcharge = (($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 20)) ? 0.15 : (($hour >= 21 || $hour < 7) ? 0.25 : 0.0);
        $additionalFees = ((bool) $payload['moveable']) ? 0.0 : 15.0;
        $totalCost = round((($baseFee + $distanceFee + $motorcycleTypeFee) * (1 + $timeSurcharge)) + $additionalFees, 2);

        $order = \App\Models\DsOrder::query()->create([
            'pick_up_datetime' => $payload['pick_up_datetime'],
            'full_name' => $payload['full_name'],
            'phone' => $payload['phone'],
            'address' => $payload['customer_address'],
            'postcode' => strtoupper(trim((string) $payload['pickup_postcode'])),
            'note' => trim((string) ($payload['note'] ?? '')) ?: null,
            'proceed' => false,
        ]);

        $order->dsOrderItems()->create([
            'pickup_address' => $payload['pickup_address'],
            'pickup_postcode' => strtoupper(trim((string) $payload['pickup_postcode'])),
            'dropoff_address' => $payload['dropoff_address'],
            'dropoff_postcode' => strtoupper(trim((string) $payload['dropoff_postcode'])),
            'vrm' => strtoupper(trim((string) $payload['vrm'])),
            'moveable' => (bool) $payload['moveable'],
            'documents' => (bool) $payload['documents'],
            'keys' => (bool) $payload['keys'],
            'note' => trim((string) ($payload['note'] ?? '')) ?: null,
            'distance' => $distance,
            'pickup_lat' => 0, 'pickup_lon' => 0,
            'dropoff_lat' => 0, 'dropoff_lon' => 0,
        ]);

        $enquiry = \App\Models\MotorbikeDeliveryOrderEnquiries::query()->create([
            'order_id' => (string) $order->id,
            'pickup_address' => $payload['pickup_address'],
            'dropoff_address' => $payload['dropoff_address'],
            'pickup_postcode' => strtoupper(trim((string) $payload['pickup_postcode'])),
            'dropoff_postcode' => strtoupper(trim((string) $payload['dropoff_postcode'])),
            'vrm' => strtoupper(trim((string) $payload['vrm'])),
            'moveable' => (bool) $payload['moveable'],
            'documents' => (bool) $payload['documents'],
            'keys' => (bool) $payload['keys'],
            'pick_up_datetime' => $payload['pick_up_datetime'],
            'distance' => $distance,
            'note' => trim((string) ($payload['note'] ?? '')),
            'full_name' => $payload['full_name'],
            'phone' => $payload['phone'],
            'email' => $payload['email'],
            'customer_address' => $payload['customer_address'],
            'customer_postcode' => strtoupper(trim((string) $payload['pickup_postcode'])),
            'total_cost' => $totalCost,
            'vehicle_type' => match ($vehicleTypeId) { 1 => 'Standard (0-125cc CC)', 2 => 'Mid-Range (126-600cc CC)', 3 => 'Heavy/Dual (601cc+ CC)', default => 'Motorcycle' },
            'vehicle_type_id' => $vehicleTypeId,
            'branch_name' => 'Catford',
            'branch_id' => 1,
            'is_dealt' => false,
            'notes' => 'Recovery enquiry from mobile API (public).',
        ]);

        return response()->json([
            'message' => 'Recovery request submitted.',
            'data' => [
                'order_id' => $order->id,
                'enquiry_id' => $enquiry->id,
                'total_cost' => $totalCost,
            ],
        ], 201);
    }
}
