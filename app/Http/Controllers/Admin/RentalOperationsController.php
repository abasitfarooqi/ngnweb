<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\RentingController as LegacyRentingController;
use App\Models\BookingClosing;
use App\Models\BookingIssuanceItem;
use App\Models\CustomerAddress;
use App\Models\CustomerAuth;
use App\Models\CustomerDocument;
use App\Models\CustomerProfile;
use App\Models\DocumentType;
use App\Models\PaymentMethod;
use App\Models\RentingBooking;
use App\Models\RentingOtherCharge;
use App\Models\RentingOtherChargesTransaction;
use App\Models\RentingServiceVideo;
use App\Models\RentingTransaction;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;

class RentalOperationsController extends Controller
{
    public function index()
    {
        return view('admin.rentals.operations', [
            'title' => 'Rental Operations',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'Rental Operations' => false,
            ],
        ]);
    }

    public function newBooking()
    {
        Log::info('New booking page requested (Backpack rental operations).');

        try {
            $data = app(LegacyRentingController::class)->bookingNewPageData();
        } catch (Throwable $e) {
            Log::error('New booking data failed: '.$e->getMessage());

            abort(500, 'Could not load new booking data.');
        }

        return view('admin.rentals.booking-new', array_merge($data, [
            'title' => 'New Booking',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'Rental Operations' => route('page.rental_operations.index'),
                'New Booking' => false,
            ],
        ]));
    }

    public function bookingsManagement()
    {
        return app(LegacyRentingController::class)->renting_bookings();
    }

    public function inactiveBookings()
    {
        return app(LegacyRentingController::class)->inactive_renting_bookings();
    }

    public function allBookings(Request $request)
    {
        return app(LegacyRentingController::class)->all_renting_bookings($request);
    }

    public function bookingInvoiceDates(Request $request)
    {
        return app(LegacyRentingController::class)->invoiceDatesAllView($request);
    }

    public function changeBookingStartDate()
    {
        return app(LegacyRentingController::class)->showUpdateStartDateForm();
    }

    public function bookingDetails(int $bookingId)
    {
        $booking = RentingBooking::with([
            'customer',
            'rentingBookingItems.motorbike',
            'bookingInvoices' => fn ($query) => $query->orderByDesc('invoice_date'),
        ])->findOrFail($bookingId);

        $customerContext = $this->syncCustomerContext($booking->customer);
        $uploadedDocuments = CustomerDocument::query()
            ->with('documentType')
            ->where('customer_id', $booking->customer_id)
            ->where(function ($query) {
                $query->whereNull('id_deleted')
                    ->orWhere('id_deleted', false);
            })
            ->latest('id')
            ->get()
            ->unique('document_type_id')
            ->keyBy('document_type_id');

        $bookingItemIds = $booking->rentingBookingItems->pluck('id');
        $otherCharges = RentingOtherCharge::query()
            ->where('booking_id', $booking->id)
            ->latest()
            ->get();

        return view('admin.rentals.booking-details', [
            'title' => 'Booking Details',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'Rental Operations' => route('page.rental_operations.index'),
                'Booking Details' => false,
            ],
            'booking' => $booking,
            'documents' => $uploadedDocuments->values(),
            'uploadedDocumentsByType' => $uploadedDocuments,
            'rentalDocumentTypes' => DocumentType::query()->forRental()->orderBy('sort_order')->orderBy('name')->get(),
            'financeDocumentTypes' => DocumentType::query()->forFinance()->orderBy('sort_order')->orderBy('name')->get(),
            'customerAuth' => $customerContext['auth'],
            'customerProfile' => $customerContext['profile'],
            'customerAddresses' => $customerContext['addresses'],
            'transactions' => RentingTransaction::query()
                ->with(['paymentMethod', 'user'])
                ->where('booking_id', $booking->id)
                ->latest('transaction_date')
                ->get(),
            'issuances' => BookingIssuanceItem::query()
                ->with(['issuedBy', 'bookingItem.motorbike'])
                ->whereIn('booking_item_id', $bookingItemIds)
                ->latest()
                ->get(),
            'otherCharges' => $otherCharges,
            'otherChargeTransactions' => RentingOtherChargesTransaction::query()
                ->with(['paymentMethod', 'user', 'charges'])
                ->whereIn('charges_id', $otherCharges->pluck('id'))
                ->latest('transaction_date')
                ->get(),
            'summary' => json_decode(app(LegacyRentingController::class)->getBookingSummary($bookingId)->getContent(), true),
            'paymentMethods' => PaymentMethod::enabled()->orderBy('title')->get(),
            'closingStatus' => BookingClosing::query()->where('booking_id', $booking->id)->first(),
            'serviceVideos' => RentingServiceVideo::query()->where('booking_id', $booking->id)->latest()->get(),
        ]);
    }

    private function syncCustomerContext($customer): array
    {
        if (! $customer) {
            return [
                'auth' => null,
                'profile' => null,
                'addresses' => collect(),
            ];
        }

        $customerAuth = CustomerAuth::query()
            ->where('customer_id', $customer->id)
            ->first();

        $customerProfile = null;

        if ($customerAuth) {
            $customerProfile = CustomerProfile::query()->firstOrCreate(
                ['customer_auth_id' => $customerAuth->id],
                [
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'phone' => $customer->phone,
                    'whatsapp' => $customer->whatsapp,
                    'dob' => $customer->dob,
                    'nationality' => $customer->nationality,
                    'license_number' => $customer->license_number,
                    'license_expiry_date' => $customer->license_expiry_date,
                    'license_issuance_authority' => $customer->license_issuance_authority,
                    'license_issuance_date' => $customer->license_issuance_date,
                    'address' => $customer->address,
                    'postcode' => $customer->postcode,
                    'city' => $customer->city,
                    'country' => $customer->country,
                    'reputation_note' => $customer->reputation_note,
                    'rating' => $customer->rating ?? 0,
                    'is_register' => (bool) $customer->is_register,
                    'verification_status' => 'draft',
                ]
            );
        }

        $addresses = CustomerAddress::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        if ($addresses->isEmpty() && ($customer->address || $customer->postcode || $customer->city || $customer->phone)) {
            CustomerAddress::query()->create([
                'customer_id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'street_address' => $customer->address ?: 'Address pending',
                'postcode' => $customer->postcode,
                'city' => $customer->city ?: 'London',
                'phone_number' => $customer->phone,
                'is_default' => true,
                'type' => 'billing',
                'country_id' => CustomerAddress::DEFAULT_COUNTRY_ID,
            ]);

            $addresses = CustomerAddress::query()
                ->where('customer_id', $customer->id)
                ->orderByDesc('is_default')
                ->latest('id')
                ->get();
        }

        return [
            'auth' => $customerAuth,
            'profile' => $customerProfile,
            'addresses' => $addresses,
        ];
    }
}
