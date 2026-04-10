<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\BookingIssuanceItem;
use App\Models\CustomerAddress;
use App\Models\CustomerAgreement;
use App\Models\CustomerAuth;
use App\Models\CustomerContract;
use App\Models\CustomerDocument;
use App\Models\Ecommerce\EcOrder;
use App\Models\MOTBooking;
use App\Models\MotorbikeMaintenanceLog;
use App\Models\MotorbikeRepair;
use App\Models\RentingBooking;
use App\Models\RentingTransaction;
use App\Models\ServiceBooking;
use App\Models\SupportConversation;
use App\Models\VehicleDeliveryOrder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobilePortalController extends Controller
{
    private function customerAuthFrom(Request $request)
    {
        $actor = $request->user('customer') ?: $request->user('sanctum') ?: Auth::guard('customer')->user();

        return $actor instanceof CustomerAuth ? $actor : null;
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

    public function createMotBooking(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'date_of_appointment' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', 'string', 'max:10'],
            'motorbike_reg_no' => ['required', 'string', 'min:2', 'max:10'],
            'motorbike_make' => ['required', 'string', 'min:2', 'max:50'],
            'motorbike_model' => ['required', 'string', 'min:2', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $customerProfile = $customerAuth->customer;
        $customerName = trim((string) ($customerProfile?->first_name.' '.$customerProfile?->last_name));
        $customerName = $customerName !== '' ? $customerName : (string) ($customerAuth?->name ?? 'Portal customer');
        $customerPhone = trim((string) ($customerProfile?->phone ?? $customerAuth?->phone ?? ''));
        $customerEmail = trim((string) ($customerAuth?->email ?? ''));
        $appointmentStart = trim($payload['date_of_appointment'].' '.$payload['time_slot']);
        $regNo = strtoupper(trim((string) $payload['motorbike_reg_no']));

        $motBooking = MOTBooking::withoutEvents(function () use ($payload, $appointmentStart, $customerName, $customerPhone, $customerEmail, $regNo) {
            return MOTBooking::query()->create([
                'branch_id' => (int) $payload['branch_id'],
                'vehicle_registration' => $regNo,
                'vehicle_chassis' => null,
                'vehicle_color' => null,
                'date_of_appointment' => $payload['date_of_appointment'],
                'start' => $appointmentStart,
                'end' => $appointmentStart,
                'customer_name' => $customerName,
                'customer_contact' => $customerPhone,
                'customer_email' => $customerEmail !== '' ? $customerEmail : null,
                'status' => 'pending',
                'title' => 'pending MOT '.$regNo.' '.$customerName,
                'notes' => implode("\n", array_filter([
                    'Make: '.trim((string) $payload['motorbike_make']),
                    'Model: '.trim((string) $payload['motorbike_model']),
                    ! empty($payload['notes']) ? 'Notes: '.trim((string) $payload['notes']) : null,
                    'Source: api.v1.mobile.portal.mot.book',
                ])),
                'all_day' => false,
                'is_validate' => true,
                'payment_method' => null,
                'payment_notes' => null,
                'user_id' => null,
            ]);
        });

        $serviceBooking = ServiceBooking::query()->create([
            'customer_id' => $customerAuth?->customer_id,
            'customer_auth_id' => $customerAuth?->id,
            'submission_context' => 'authenticated_customer',
            'enquiry_type' => 'mot',
            'service_type' => 'MOT portal booking',
            'subject' => 'MOT booking request',
            'description' => implode(' | ', array_filter([
                'Reg: '.$regNo,
                'Make: '.trim((string) $payload['motorbike_make']),
                'Model: '.trim((string) $payload['motorbike_model']),
                'Date: '.$payload['date_of_appointment'],
                'Time: '.$payload['time_slot'],
                ! empty($payload['notes']) ? 'Notes: '.trim((string) $payload['notes']) : null,
                'Source: api.v1.mobile.portal.mot.book',
            ])),
            'requires_schedule' => true,
            'booking_date' => $payload['date_of_appointment'],
            'booking_time' => $payload['time_slot'],
            'status' => 'Pending',
            'fullname' => $customerName,
            'phone' => $customerPhone,
            'reg_no' => $regNo,
            'email' => $customerEmail !== '' ? $customerEmail : null,
        ]);

        return response()->json([
            'message' => 'MOT booking submitted.',
            'data' => [
                'mot_booking_id' => $motBooking->id,
                'service_booking_id' => $serviceBooking->id,
                'vehicle_registration' => $motBooking->vehicle_registration,
                'date_of_appointment' => optional($motBooking->date_of_appointment)->toDateTimeString(),
                'status' => $motBooking->status,
            ],
        ], 201);
    }

    public function rentalDetail(Request $request, int $id): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $customerAuth->customer_id;
        if (! $customerId) {
            return response()->json(['message' => 'No customer profile linked.'], 422);
        }

        $booking = RentingBooking::query()
            ->where('customer_id', $customerId)
            ->with(['bookingItems.motorbike', 'activeItems.motorbike'])
            ->findOrFail($id);

        $invoiceRows = BookingInvoice::query()
            ->where('booking_id', $booking->id)
            ->where('is_posted', true)
            ->where('amount', '>', 0)
            ->with([
                'transactions' => fn ($q) => $q->orderByDesc('created_at')->with([
                    'user:id,first_name,last_name',
                    'paymentMethod:id,title',
                ]),
            ])
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();

        $isEnded = $booking->bookingItems->isNotEmpty() && $booking->bookingItems->every(fn ($item) => ! empty($item->end_date));
        $currentWeekEndDay = Carbon::now()->startOfWeek(Carbon::MONDAY)->addDays(6)->startOfDay();
        $rentalEndDay = $isEnded
            ? $booking->bookingItems
                ->map(fn ($item) => $item->end_date ? Carbon::parse($item->end_date)->startOfDay() : null)
                ->filter()
                ->max()
            : null;

        $visibleInvoices = $invoiceRows->filter(function (BookingInvoice $invoice) use ($isEnded, $currentWeekEndDay) {
            if ($isEnded) {
                return true;
            }
            if (! $invoice->invoice_date) {
                return true;
            }

            return ! $invoice->invoice_date->startOfDay()->gt($currentWeekEndDay);
        })->values();

        $invoiceMeta = [];
        foreach ($visibleInvoices as $invoice) {
            $postRental = $isEnded && $rentalEndDay && $invoice->invoice_date
                && $invoice->invoice_date->startOfDay()->gt($rentalEndDay);
            $paidFromTransactions = (float) $invoice->transactions->sum('amount');
            $paidDisplay = $invoice->is_paid ? (float) $invoice->amount : $paidFromTransactions;
            $leftToPay = max(0.0, (float) $invoice->amount - $paidDisplay);
            $invoiceMeta[$invoice->id] = [
                'post_rental' => $postRental,
                'display_left_to_pay' => $postRental ? 0.0 : $leftToPay,
            ];
        }

        $agreements = CustomerAgreement::query()
            ->where('customer_id', $customerId)
            ->where('booking_id', $booking->id)
            ->orderByDesc('id')
            ->get();

        $otherCharges = DB::table('renting_other_charges')
            ->where('booking_id', $booking->id)
            ->orderBy('id')
            ->get();

        $closing = BookingClosing::query()->where('booking_id', $booking->id)->first();
        $bookingItemIds = $booking->bookingItems->pluck('id')->filter()->values();

        $issuance = BookingIssuanceItem::query()
            ->whereIn('booking_item_id', $bookingItemIds)
            ->with(['issuedBy:id,first_name,last_name', 'bookingItem.motorbike:id,reg_no'])
            ->orderByDesc('created_at')
            ->get();

        $maintenance = MotorbikeMaintenanceLog::query()
            ->where('booking_id', $booking->id)
            ->with(['user:id,first_name,last_name', 'motorbike:id,reg_no'])
            ->orderByDesc('serviced_at')
            ->get();

        $paymentHistory = RentingTransaction::query()
            ->where('booking_id', $booking->id)
            ->with(['transactionType', 'paymentMethod'])
            ->orderByDesc('created_at')
            ->get();

        $pcnSummary = DB::table('renting_booking_items as rbi')
            ->join('renting_bookings as rb', 'rb.id', '=', 'rbi.booking_id')
            ->join('motorbikes as m', 'm.id', '=', 'rbi.motorbike_id')
            ->leftJoin('pcn_cases as pc', function ($join): void {
                $join->on('pc.motorbike_id', '=', 'rbi.motorbike_id')
                    ->whereRaw('DATE(pc.date_of_contravention) >= DATE(COALESCE(rbi.start_date, rb.start_date))')
                    ->whereRaw('DATE(pc.date_of_contravention) <= DATE(COALESCE(rbi.end_date, CURDATE()))');
            })
            ->where('rbi.booking_id', $booking->id)
            ->whereNotNull('pc.id')
            ->selectRaw('
                COUNT(pc.id) as total_count,
                SUM(COALESCE(pc.reduced_amount, pc.full_amount, 0)) as total_amount,
                SUM(CASE WHEN pc.isClosed = 0 THEN 1 ELSE 0 END) as open_count
            ')
            ->first();

        $repairReports = collect();
        $customerEmail = trim((string) ($customerAuth->email ?? ''));
        if ($customerEmail !== '') {
            $regs = $booking->bookingItems
                ->map(fn ($item) => strtoupper(trim((string) ($item->motorbike?->reg_no ?? ''))))
                ->filter()
                ->values();
            if ($regs->isNotEmpty()) {
                $repairReports = MotorbikeRepair::query()
                    ->with(['motorbike'])
                    ->whereRaw('LOWER(email) = ?', [strtolower($customerEmail)])
                    ->latest('id')
                    ->get()
                    ->filter(fn ($repair) => $regs->contains(strtoupper(trim((string) ($repair->motorbike?->reg_no ?? '')))))
                    ->values();
            }
        }

        return response()->json([
            'data' => [
                'booking' => [
                    'id' => $booking->id,
                    'state' => $booking->state,
                    'deposit' => (float) ($booking->deposit ?? 0),
                    'start_date' => optional($booking->start_date)->toDateTimeString(),
                    'due_date' => optional($booking->due_date)->toDateTimeString(),
                    'items' => $booking->bookingItems->map(fn ($item) => [
                        'id' => $item->id,
                        'motorbike_id' => $item->motorbike_id,
                        'name' => trim((string) ($item->motorbike->make ?? '').' '.($item->motorbike->model ?? '')),
                        'reg_no' => $item->motorbike->reg_no ?? null,
                        'weekly_rent' => (float) ($item->weekly_rent ?? 0),
                        'start_date' => optional($item->start_date)->toDateString(),
                        'due_date' => optional($item->due_date)->toDateString(),
                        'end_date' => optional($item->end_date)->toDateString(),
                    ])->values(),
                ],
                'invoices' => $visibleInvoices->map(fn ($invoice) => [
                    'id' => $invoice->id,
                    'invoice_date' => optional($invoice->invoice_date)->toDateString(),
                    'amount' => (float) ($invoice->amount ?? 0),
                    'is_paid' => (bool) $invoice->is_paid,
                    'state' => $invoice->state,
                    'meta' => $invoiceMeta[$invoice->id] ?? null,
                    'transactions' => $invoice->transactions->map(fn ($tx) => [
                        'id' => $tx->id,
                        'amount' => (float) ($tx->amount ?? 0),
                        'notes' => $tx->notes,
                        'date' => optional($tx->transaction_date)->toDateTimeString(),
                        'payment_method' => $tx->paymentMethod?->title,
                        'handled_by' => trim((string) (($tx->user->first_name ?? '').' '.($tx->user->last_name ?? ''))),
                    ])->values(),
                ])->values(),
                'agreements' => $agreements->map(fn ($agreement) => [
                    'id' => $agreement->id,
                    'file_name' => $agreement->file_name,
                    'file_path' => $agreement->file_path,
                    'sent_private' => (bool) ($agreement->sent_private ?? false),
                ])->values(),
                'other_charges' => $otherCharges,
                'closing' => $closing,
                'issuance' => $issuance,
                'maintenance' => $maintenance,
                'payment_history' => $paymentHistory,
                'pcn_summary' => [
                    'total_count' => (int) ($pcnSummary->total_count ?? 0),
                    'open_count' => (int) ($pcnSummary->open_count ?? 0),
                    'total_amount' => (float) ($pcnSummary->total_amount ?? 0),
                ],
                'repair_reports' => $repairReports->map(fn ($repair) => [
                    'id' => $repair->id,
                    'reg_no' => $repair->motorbike?->reg_no,
                    'status' => $repair->status ?? null,
                    'created_at' => optional($repair->created_at)->toDateTimeString(),
                ])->values(),
            ],
        ]);
    }

    public function fullState(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $customerAuth->customer_id;
        $email = strtolower(trim((string) ($customerAuth->email ?? '')));

        $orders = EcOrder::query()
            ->where('customer_id', $customerAuth->id)
            ->withCount('orderItems')
            ->latest('id')
            ->limit(10)
            ->get();

        $enquiries = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->latest('id')
            ->limit(10)
            ->get();

        $rentals = collect();
        if ($customerId) {
            $rentals = RentingBooking::query()
                ->where('customer_id', $customerId)
                ->with(['bookingItems.motorbike:id,make,model,reg_no'])
                ->latest('id')
                ->limit(10)
                ->get();
        }

        $mot = collect();
        $deliveries = collect();
        if ($email !== '') {
            $mot = MOTBooking::query()
                ->whereRaw('LOWER(customer_email) = ?', [$email])
                ->latest('date_of_appointment')
                ->limit(10)
                ->get();

            $deliveries = VehicleDeliveryOrder::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->latest('pickup_date')
                ->limit(10)
                ->get();
        }

        $addressesCount = $customerId
            ? CustomerAddress::query()->where('customer_id', $customerId)->count()
            : 0;
        $documentsCount = $customerId
            ? CustomerDocument::query()->where('customer_id', $customerId)->count()
            : 0;
        $agreementsCount = $customerId
            ? CustomerAgreement::query()->where('customer_id', $customerId)->count()
            : 0;
        $contractsCount = $customerId
            ? CustomerContract::query()->where('customer_id', $customerId)->count()
            : 0;

        $support = SupportConversation::query()
            ->where('customer_auth_id', $customerAuth->id)
            ->latest('id')
            ->limit(20)
            ->get(['id', 'uuid', 'subject', 'status', 'service_booking_id', 'assigned_backpack_user_id', 'updated_at']);

        return response()->json([
            'customer' => [
                'customer_auth_id' => $customerAuth->id,
                'customer_id' => $customerId,
                'email' => $customerAuth->email,
                'name' => $customerAuth->customer?->full_name ?? $customerAuth->name,
                'phone' => $customerAuth->customer?->phone ?? null,
            ],
            'counts' => [
                'orders' => $orders->count(),
                'enquiries' => $enquiries->count(),
                'rentals' => $rentals->count(),
                'mot_bookings' => $mot->count(),
                'recovery_requests' => $deliveries->count(),
                'support_threads' => $support->count(),
                'addresses' => $addressesCount,
                'documents' => $documentsCount,
                'agreements' => $agreementsCount,
                'contracts' => $contractsCount,
            ],
            'orders' => $orders->map(fn (EcOrder $order) => [
                'id' => $order->id,
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'shipping_status' => $order->shipping_status,
                'grand_total' => (float) ($order->grand_total ?? 0),
                'currency' => $order->currency,
                'items_count' => (int) ($order->order_items_count ?? 0),
                'created_at' => optional($order->created_at)->toDateTimeString(),
            ])->values(),
            'enquiries' => $enquiries->map(fn (ServiceBooking $row) => [
                'id' => $row->id,
                'enquiry_type' => $row->enquiry_type,
                'subject' => $row->subject,
                'status' => $row->status,
                'booking_date' => optional($row->booking_date)->toDateString(),
                'created_at' => optional($row->created_at)->toDateTimeString(),
            ])->values(),
            'rentals' => $rentals->map(fn (RentingBooking $row) => [
                'id' => $row->id,
                'state' => $row->state,
                'start_date' => optional($row->start_date)->toDateTimeString(),
                'due_date' => optional($row->due_date)->toDateTimeString(),
                'items' => $row->bookingItems->map(fn ($item) => [
                    'motorbike_id' => $item->motorbike_id,
                    'name' => trim((string) ($item->motorbike->make ?? '').' '.($item->motorbike->model ?? '')),
                    'reg_no' => $item->motorbike->reg_no ?? null,
                ])->values(),
            ])->values(),
            'mot_bookings' => $mot->map(fn (MOTBooking $row) => [
                'id' => $row->id,
                'vehicle_registration' => $row->vehicle_registration,
                'date_of_appointment' => optional($row->date_of_appointment)->toDateTimeString(),
                'status' => $row->status,
                'branch_id' => $row->branch_id,
            ])->values(),
            'recovery_requests' => $deliveries->map(fn (VehicleDeliveryOrder $row) => [
                'id' => $row->id,
                'pickup_date' => optional($row->pickup_date)->toDateTimeString(),
                'vrm' => $row->vrm,
                'full_name' => $row->full_name,
                'phone_number' => $row->phone_number,
                'branch_id' => $row->branch_id,
            ])->values(),
            'support_threads' => $support,
            'links' => [
                'support_customer_api' => '/api/v1/customer/support/conversations',
                'support_staff_api' => '/api/v1/staff/support/conversations',
                'portal_page_blueprint' => '/api/v1/mobile/portal/page-blueprint',
            ],
        ]);
    }

    public function bookingsUnified(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $customerAuth->customer_id;
        $email = trim((string) ($customerAuth->email ?? ''));

        $rentalRows = collect();
        if ($customerId) {
            $rentalRows = RentingBooking::query()
                ->where('customer_id', $customerId)
                ->latest('id')
                ->limit(100)
                ->get()
                ->map(fn (RentingBooking $row) => [
                    'type' => 'rental',
                    'id' => $row->id,
                    'title' => 'Rental booking #'.$row->id,
                    'status' => $row->state,
                    'date' => optional($row->start_date)->toDateTimeString(),
                ]);
        }

        $motRows = collect();
        if ($email !== '') {
            $motRows = MOTBooking::query()
                ->whereRaw('LOWER(customer_email) = ?', [strtolower($email)])
                ->latest('date_of_appointment')
                ->limit(100)
                ->get()
                ->map(fn (MOTBooking $row) => [
                    'type' => 'mot',
                    'id' => $row->id,
                    'title' => 'MOT '.$row->vehicle_registration,
                    'status' => $row->status,
                    'date' => optional($row->date_of_appointment)->toDateTimeString(),
                ]);
        }

        $recoveryRows = collect();
        if ($email !== '') {
            $recoveryRows = VehicleDeliveryOrder::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($email)])
                ->latest('pickup_date')
                ->limit(100)
                ->get()
                ->map(fn (VehicleDeliveryOrder $row) => [
                    'type' => 'recovery',
                    'id' => $row->id,
                    'title' => 'Recovery '.$row->vrm,
                    'status' => 'scheduled',
                    'date' => optional($row->pickup_date)->toDateTimeString(),
                ]);
        }

        return response()->json([
            'data' => $rentalRows
                ->concat($motRows)
                ->concat($recoveryRows)
                ->sortByDesc(fn (array $row) => $row['date'] ?? '')
                ->values(),
        ]);
    }
}
