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
}
