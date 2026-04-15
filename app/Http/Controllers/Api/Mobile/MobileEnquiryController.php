<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\SupportConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileEnquiryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = max(1, min(50, (int) $request->integer('per_page', 20)));

        $rows = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->when($request->filled('enquiry_type'), fn ($q) => $q->where('enquiry_type', (string) $request->string('enquiry_type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', (string) $request->string('status')))
            ->with('conversation:id,uuid,status,service_booking_id')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json($rows);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $row = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->with('conversation:id,uuid,status,service_booking_id')
            ->findOrFail($id);

        return response()->json([
            'data' => $row,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $customerAuth = $request->user('sanctum') ?: Auth::guard('customer')->user();
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'module' => ['required', 'string', 'in:bike_new,bike_used,ebike,rental,mot,repairs,service,recovery,finance,shop,spareparts,general'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4000'],
            'phone' => ['required', 'string', 'max:40'],
            /** When the portal account has no email, the app sends the address typed in the form. */
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'reg_no' => ['nullable', 'string', 'max:40'],
            'reference_type' => ['nullable', 'string', 'max:60'],
            'reference_id' => ['nullable', 'integer', 'min:1'],
            'booking_date' => ['nullable', 'date'],
            'booking_time' => ['nullable', 'date_format:H:i'],
            'metadata' => ['nullable', 'array'],
        ]);

        $profile = $customerAuth->customer;
        $name = trim((string) (($profile?->first_name ?? '').' '.($profile?->last_name ?? '')));
        if ($name === '') {
            $name = 'Portal customer';
        }

        $serviceType = $this->serviceTypeFromModule($data['module']);
        $enquiryType = $this->enquiryTypeFromModule($data['module']);

        $resolvedEmail = trim((string) ($data['email'] ?? ''));
        if ($resolvedEmail === '') {
            $resolvedEmail = trim((string) ($customerAuth->email ?? ''));
        }

        $subject = trim((string) ($data['subject'] ?? ''));
        if ($subject === '') {
            $subject = $serviceType;
        }

        $lines = [
            'Source: mobile.api.v1',
            'Module: '.$data['module'],
            isset($data['reference_type']) ? 'Reference type: '.$data['reference_type'] : null,
            isset($data['reference_id']) ? 'Reference id: '.(int) $data['reference_id'] : null,
            'Message: '.trim((string) $data['message']),
            ! empty($data['metadata']) ? 'Metadata: '.json_encode($data['metadata']) : null,
        ];

        $booking = null;
        $conversation = null;

        DB::transaction(function () use (&$booking, &$conversation, $customerAuth, $name, $serviceType, $enquiryType, $subject, $lines, $data, $resolvedEmail): void {
            $booking = ServiceBooking::query()->create([
                'customer_id' => $customerAuth->customer_id,
                'customer_auth_id' => $customerAuth->id,
                'submission_context' => 'authenticated_customer',
                'enquiry_type' => $enquiryType,
                'service_type' => $serviceType,
                'subject' => $subject,
                'description' => implode(' | ', array_values(array_filter($lines))),
                'requires_schedule' => in_array($data['module'], ['mot', 'service', 'repairs', 'recovery'], true),
                'booking_date' => $data['booking_date'] ?? null,
                'booking_time' => $data['booking_time'] ?? null,
                'status' => 'Pending',
                'fullname' => $name,
                'phone' => (string) $data['phone'],
                'reg_no' => (string) ($data['reg_no'] ?? 'N/A'),
                'email' => $resolvedEmail,
            ]);

            $conversation = SupportConversation::query()->create([
                'customer_auth_id' => $customerAuth->id,
                'service_booking_id' => $booking->id,
                'title' => $serviceType,
                'topic' => $subject,
                'status' => 'open',
            ]);

            $booking->forceFill(['conversation_id' => $conversation->id])->save();
        });

        return response()->json([
            'message' => 'Enquiry created successfully.',
            'service_booking_id' => $booking?->id,
            'conversation_id' => $conversation?->id,
            'conversation_uuid' => $conversation?->uuid,
        ], 201);
    }

    private function serviceTypeFromModule(string $module): string
    {
        return match ($module) {
            'bike_new' => 'New bike enquiry',
            'bike_used' => 'Used bike enquiry',
            'ebike' => 'E-bike enquiry',
            'rental' => 'Motorcycle rental enquiry',
            'mot' => 'MOT enquiry',
            'repairs' => 'Repair enquiry',
            'service' => 'Service enquiry',
            'recovery' => 'Delivery and recovery enquiry',
            'finance' => 'Motorcycle Finance Enquiry',
            'shop' => 'Shop stock enquiry',
            'spareparts' => 'Spare parts enquiry',
            default => 'General enquiry',
        };
    }

    private function enquiryTypeFromModule(string $module): string
    {
        return match ($module) {
            'bike_new' => 'new_bike',
            'bike_used' => 'used_bike',
            'ebike' => 'e_bike',
            'rental' => 'rental',
            'mot' => 'mot',
            'repairs', 'service' => 'service',
            'recovery' => 'recovery_delivery',
            'finance' => 'finance',
            'shop' => 'shop',
            'spareparts' => 'shop',
            default => 'general',
        };
    }
}
