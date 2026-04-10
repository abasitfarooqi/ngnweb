<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\EcOrder;
use App\Models\MOTBooking;
use App\Models\RentingBooking;
use App\Models\ServiceBooking;
use App\Models\SupportConversation;
use App\Models\VehicleDeliveryOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobilePortalController extends Controller
{
    private function customerAuthFrom(Request $request)
    {
        return $request->user('sanctum') ?: Auth::guard('customer')->user();
    }

    public function overview(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $customerAuth->customer_id;
        $email = trim((string) ($customerAuth->email ?? ''));

        $activeRental = null;
        if ($customerId) {
            $activeRental = RentingBooking::query()
                ->where('customer_id', $customerId)
                ->active()
                ->with(['activeItems.motorbike:id,make,model,reg_no'])
                ->latest('id')
                ->first();
        }

        $upcomingMot = null;
        if ($email !== '') {
            $upcomingMot = MOTBooking::query()
                ->whereRaw('LOWER(customer_email) = ?', [strtolower($email)])
                ->where('date_of_appointment', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->orderBy('date_of_appointment')
                ->first();
        }

        $upcomingDelivery = null;
        if ($email !== '') {
            $upcomingDelivery = VehicleDeliveryOrder::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($email)])
                ->where('pickup_date', '>=', now())
                ->orderBy('pickup_date')
                ->first();
        }

        $enquiryQuery = ServiceBooking::query()->forPortalCustomer($customerAuth);

        return response()->json([
            'customer' => [
                'customer_auth_id' => $customerAuth->id,
                'customer_id' => $customerId,
                'email' => $customerAuth->email,
            ],
            'counts' => [
                'enquiries_total' => (clone $enquiryQuery)->count(),
                'enquiries_open' => (clone $enquiryQuery)->whereIn('status', ['Pending', 'Open'])->count(),
                'support_open' => SupportConversation::query()
                    ->where('customer_auth_id', $customerAuth->id)
                    ->whereNotIn('status', ['resolved', 'closed'])
                    ->count(),
                'orders_total' => EcOrder::query()->where('customer_id', $customerAuth->id)->count(),
            ],
            'upcoming' => [
                'active_rental' => $activeRental ? [
                    'id' => $activeRental->id,
                    'state' => $activeRental->state,
                    'start_date' => optional($activeRental->start_date)->toDateTimeString(),
                    'due_date' => optional($activeRental->due_date)->toDateTimeString(),
                    'items' => $activeRental->activeItems->map(fn ($item) => [
                        'motorbike_id' => $item->motorbike_id,
                        'name' => trim((string) ($item->motorbike->make ?? '').' '.($item->motorbike->model ?? '')),
                        'reg_no' => $item->motorbike->reg_no ?? null,
                    ])->values(),
                ] : null,
                'mot' => $upcomingMot ? [
                    'id' => $upcomingMot->id,
                    'vehicle_registration' => $upcomingMot->vehicle_registration,
                    'date_of_appointment' => optional($upcomingMot->date_of_appointment)->toDateTimeString(),
                    'status' => $upcomingMot->status,
                ] : null,
                'delivery' => $upcomingDelivery ? [
                    'id' => $upcomingDelivery->id,
                    'pickup_date' => optional($upcomingDelivery->pickup_date)->toDateTimeString(),
                    'vrm' => $upcomingDelivery->vrm,
                    'status_hint' => 'scheduled',
                ] : null,
            ],
            'links' => [
                'support_inbox_api' => '/api/v1/customer/support/conversations',
                'shop_orders_api' => '/api/v1/shop/orders',
                'enquiries_api' => '/api/v1/mobile/enquiries',
            ],
        ]);
    }

    public function myOrders(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = max(1, min(50, (int) $request->integer('per_page', 20)));

        $orders = EcOrder::query()
            ->where('customer_id', $customerAuth->id)
            ->withCount('orderItems')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (EcOrder $order) => [
                'id' => $order->id,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'shipping_status' => $order->shipping_status,
                'grand_total' => (float) ($order->grand_total ?? 0),
                'currency' => $order->currency,
                'items_count' => (int) ($order->order_items_count ?? 0),
                'created_at' => optional($order->created_at)->toDateTimeString(),
            ]);

        return response()->json($orders);
    }

    public function myRentals(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = max(1, min(50, (int) $request->integer('per_page', 20)));
        $customerId = $customerAuth->customer_id;

        if (! $customerId) {
            return response()->json([
                'data' => [],
                'message' => 'No customer profile linked.',
            ]);
        }

        $rentals = RentingBooking::query()
            ->where('customer_id', $customerId)
            ->with(['bookingItems.motorbike:id,make,model,reg_no'])
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(function (RentingBooking $booking) {
                return [
                    'id' => $booking->id,
                    'state' => $booking->state,
                    'deposit' => (float) ($booking->deposit ?? 0),
                    'start_date' => optional($booking->start_date)->toDateTimeString(),
                    'due_date' => optional($booking->due_date)->toDateTimeString(),
                    'items' => $booking->bookingItems->map(fn ($item) => [
                        'motorbike_id' => $item->motorbike_id,
                        'name' => trim((string) ($item->motorbike->make ?? '').' '.($item->motorbike->model ?? '')),
                        'reg_no' => $item->motorbike->reg_no ?? null,
                        'weekly_rent' => (float) ($item->weekly_rent ?? 0),
                    ])->values(),
                ];
            });

        return response()->json($rentals);
    }

    public function myMotBookings(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = max(1, min(50, (int) $request->integer('per_page', 20)));
        $email = trim((string) ($customerAuth->email ?? ''));

        $query = MOTBooking::query()->orderByDesc('date_of_appointment');
        if ($email !== '') {
            $query->whereRaw('LOWER(customer_email) = ?', [strtolower($email)]);
        } else {
            $query->whereRaw('1 = 0');
        }

        $bookings = $query
            ->paginate($perPage)
            ->through(fn (MOTBooking $booking) => [
                'id' => $booking->id,
                'vehicle_registration' => $booking->vehicle_registration,
                'date_of_appointment' => optional($booking->date_of_appointment)->toDateTimeString(),
                'status' => $booking->status,
                'branch_id' => $booking->branch_id,
            ]);

        return response()->json($bookings);
    }

    public function myRecoveryRequests(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $perPage = max(1, min(50, (int) $request->integer('per_page', 20)));
        $email = trim((string) ($customerAuth->email ?? ''));

        $query = VehicleDeliveryOrder::query()->orderByDesc('pickup_date');
        if ($email !== '') {
            $query->whereRaw('LOWER(email) = ?', [strtolower($email)]);
        } else {
            $query->whereRaw('1 = 0');
        }

        $requests = $query
            ->paginate($perPage)
            ->through(fn (VehicleDeliveryOrder $row) => [
                'id' => $row->id,
                'pickup_date' => optional($row->pickup_date)->toDateTimeString(),
                'vrm' => $row->vrm,
                'full_name' => $row->full_name,
                'phone_number' => $row->phone_number,
                'total_distance' => (float) ($row->total_distance ?? 0),
                'surcharge' => (float) ($row->surcharge ?? 0),
                'branch_id' => $row->branch_id,
            ]);

        return response()->json($requests);
    }
}
