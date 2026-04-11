<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Jobs\MoveCustomerDocumentToSpacesJob;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerAgreement;
use App\Models\CustomerAppointments;
use App\Models\CustomerAuth;
use App\Models\CustomerContract;
use App\Models\CustomerDocument;
use App\Models\DeliveryVehicleType;
use App\Models\DocumentType;
use App\Models\DsOrder;
use App\Models\FinanceApplication;
use App\Models\JudopayOnboarding;
use App\Models\JudopaySubscription;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use App\Models\NgnMitQueue;
use App\Models\RentingBooking;
use App\Models\SystemCountry;
use App\Support\CustomerDocumentStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MobilePortalExperienceController extends Controller
{
    private function customerAuthFrom(Request $request): ?CustomerAuth
    {
        $actor = $request->user('customer') ?: $request->user('sanctum') ?: Auth::guard('customer')->user();

        return $actor instanceof CustomerAuth ? $actor : null;
    }

    private function portalCustomerId(CustomerAuth $customerAuth): ?int
    {
        return $customerAuth->customer_id ?: $customerAuth->customer?->id;
    }

    public function pageBlueprint(): JsonResponse
    {
        return response()->json([
            'module' => 'portal_experience_phase_2',
            'endpoints' => [
                'addresses' => [
                    'list' => '/api/v1/mobile/portal/addresses',
                    'countries' => '/api/v1/mobile/portal/addresses/countries',
                    'create' => '/api/v1/mobile/portal/addresses',
                    'update' => '/api/v1/mobile/portal/addresses/{id}',
                    'delete' => '/api/v1/mobile/portal/addresses/{id}',
                    'set_default' => '/api/v1/mobile/portal/addresses/{id}/default',
                ],
                'documents' => [
                    'overview' => '/api/v1/mobile/portal/documents',
                    'upload' => '/api/v1/mobile/portal/documents/upload',
                ],
                'recurring_payments' => [
                    'list' => '/api/v1/mobile/portal/payments/recurring?service=all|rental|finance',
                ],
                'repairs_appointment' => [
                    'options' => '/api/v1/mobile/portal/repairs/appointment/options',
                    'create' => '/api/v1/mobile/portal/repairs/appointments',
                ],
                'rentals_create_flow' => [
                    'browse_options' => '/api/v1/mobile/portal/rentals/browse/options',
                    'available' => '/api/v1/mobile/portal/rentals/available?branch_id=&filter=all|scooter|motorbike&search=',
                    'create_blueprint' => '/api/v1/mobile/portal/rentals/create/{motorbikeId}/blueprint',
                    'create_request' => '/api/v1/mobile/portal/rentals/create/{motorbikeId}',
                ],
                'recovery' => [
                    'options' => '/api/v1/mobile/portal/recovery/options',
                    'quote' => '/api/v1/mobile/portal/recovery/quote',
                    'create_request' => '/api/v1/mobile/portal/recovery/requests',
                ],
            ],
        ]);
    }

    public function addresses(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['data' => []]);
        }

        $rows = CustomerAddress::query()
            ->where('customer_id', $customerId)
            ->with('customer')
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get()
            ->map(fn (CustomerAddress $row) => [
                'id' => $row->id,
                'type' => $row->type,
                'is_default' => (bool) $row->is_default,
                'first_name' => $row->first_name,
                'last_name' => $row->last_name,
                'company_name' => $row->company_name,
                'street_address' => $row->street_address,
                'street_address_plus' => $row->street_address_plus,
                'postcode' => $row->postcode,
                'city' => $row->city,
                'phone_number' => $row->phone_number,
                'country_id' => $row->country_id,
            ]);

        return response()->json(['data' => $rows]);
    }

    public function addressCountries(): JsonResponse
    {
        return response()->json([
            'data' => SystemCountry::query()
                ->orderBy('name')
                ->get(['id', 'name', 'cca2', 'cca3', 'flag']),
        ]);
    }

    public function createAddress(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['message' => 'Customer profile missing'], 422);
        }

        $payload = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'],
            'street_address_plus' => ['nullable', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30'],
            'country_id' => ['required', 'integer', 'exists:system_countries,id'],
            'type' => ['required', 'in:shipping,billing'],
        ]);

        $hasDefault = CustomerAddress::query()->where('customer_id', $customerId)->exists();

        $row = CustomerAddress::query()->create([
            ...$payload,
            'customer_id' => $customerId,
            'company_name' => $payload['company_name'] ?? '-',
            'street_address_plus' => $payload['street_address_plus'] ?? '-',
            'is_default' => ! $hasDefault,
        ]);

        return response()->json([
            'message' => 'Address added.',
            'data' => $row,
        ], 201);
    }

    public function updateAddress(Request $request, int $id): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['message' => 'Customer profile missing'], 422);
        }

        $payload = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'street_address' => ['required', 'string', 'max:255'],
            'street_address_plus' => ['nullable', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30'],
            'country_id' => ['required', 'integer', 'exists:system_countries,id'],
            'type' => ['required', 'in:shipping,billing'],
        ]);

        $row = CustomerAddress::query()
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        $row->update([
            ...$payload,
            'company_name' => $payload['company_name'] ?? '-',
            'street_address_plus' => $payload['street_address_plus'] ?? '-',
        ]);

        return response()->json([
            'message' => 'Address updated.',
            'data' => $row->fresh(),
        ]);
    }

    public function deleteAddress(Request $request, int $id): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['message' => 'Customer profile missing'], 422);
        }

        $row = CustomerAddress::query()
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        $wasDefault = (bool) $row->is_default;
        $row->delete();

        if ($wasDefault) {
            $replacement = CustomerAddress::query()
                ->where('customer_id', $customerId)
                ->latest('id')
                ->first();
            if ($replacement) {
                $replacement->is_default = true;
                $replacement->save();
            }
        }

        return response()->json(['message' => 'Address removed.']);
    }

    public function setAddressDefault(Request $request, int $id): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['message' => 'Customer profile missing'], 422);
        }

        CustomerAddress::query()
            ->where('customer_id', $customerId)
            ->update(['is_default' => false]);

        CustomerAddress::query()
            ->where('customer_id', $customerId)
            ->findOrFail($id)
            ->update(['is_default' => true]);

        return response()->json(['message' => 'Default address updated.']);
    }

    public function documents(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['message' => 'Customer profile missing'], 422);
        }

        $rentalDocs = DocumentType::query()->forRental()->orderBy('sort_order')->orderBy('name')->get();
        $financeDocs = DocumentType::query()->forFinance()->orderBy('sort_order')->orderBy('name')->get();
        $rentalDocIds = $rentalDocs->pluck('id');
        $financeDocIds = $financeDocs->pluck('id');

        $otherDocs = DocumentType::query()
            ->whereNotIn('id', $rentalDocIds->merge($financeDocIds)->unique()->values())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $uploadedDocuments = CustomerDocument::query()
            ->where('customer_id', $customerId)
            ->with('documentType')
            ->latest('id')
            ->get()
            ->map(fn (CustomerDocument $doc) => [
                'id' => $doc->id,
                'document_type_id' => $doc->document_type_id,
                'document_type_name' => $doc->documentType?->name,
                'file_name' => $doc->file_name,
                'file_format' => $doc->file_format,
                'document_number' => $doc->document_number,
                'valid_until' => optional($doc->valid_until)->toDateString(),
                'status' => $doc->status,
                'sent_private' => (bool) ($doc->sent_private ?? false),
                'file_url' => $this->resolveStoredFileUrl($doc->file_path, (bool) ($doc->sent_private ?? false)),
                'created_at' => optional($doc->created_at)->toDateTimeString(),
            ]);

        $uploadedByType = $uploadedDocuments->keyBy('document_type_id');
        $rentalMandatoryIds = $rentalDocs->where('is_mandatory', true)->pluck('id');
        $financeMandatoryIds = $financeDocs->where('is_mandatory', true)->pluck('id');

        $rentalAgreements = CustomerAgreement::query()
            ->where('customer_id', $customerId)
            ->latest('id')
            ->get()
            ->map(fn (CustomerAgreement $agreement) => [
                'id' => $agreement->id,
                'file_name' => $agreement->file_name,
                'sent_private' => (bool) ($agreement->sent_private ?? false),
                'file_url' => $this->resolveStoredFileUrl($agreement->file_path, (bool) ($agreement->sent_private ?? false)),
            ]);

        $financeContracts = CustomerContract::query()
            ->where('customer_id', $customerId)
            ->latest('id')
            ->get()
            ->map(fn (CustomerContract $contract) => [
                'id' => $contract->id,
                'file_name' => $contract->file_name,
                'sent_private' => (bool) ($contract->sent_private ?? false),
                'file_url' => $this->resolveStoredFileUrl($contract->file_path, (bool) ($contract->sent_private ?? false)),
            ]);

        return response()->json([
            'requirements' => [
                'rental' => $rentalDocs->values(),
                'finance' => $financeDocs->values(),
                'other' => $otherDocs->values(),
                'missing_mandatory_rental' => $rentalMandatoryIds->filter(fn ($id) => ! $uploadedByType->has($id))->values(),
                'missing_mandatory_finance' => $financeMandatoryIds->filter(fn ($id) => ! $uploadedByType->has($id))->values(),
            ],
            'uploaded' => $uploadedDocuments->values(),
            'agreements' => $rentalAgreements,
            'contracts' => $financeContracts,
        ]);
    }

    public function uploadDocument(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $customerId = $this->portalCustomerId($customerAuth);
        if (! $customerId) {
            return response()->json(['message' => 'Customer profile missing'], 422);
        }

        $payload = $request->validate([
            'document_type_id' => ['required', 'integer', 'exists:document_types,id'],
            'file' => ['required', 'file', 'max:10240'],
            'document_number' => ['nullable', 'string', 'max:255'],
            'valid_until' => ['nullable', 'date'],
        ]);

        $file = $request->file('file');
        $path = 'customer-documents/'.Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        CustomerDocumentStorage::put($path, $file->get());

        $existing = CustomerDocument::query()->where([
            'customer_id' => $customerId,
            'document_type_id' => (int) $payload['document_type_id'],
        ])->first();

        $oldPath = $existing?->file_path;

        $attributes = [
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_format' => $file->getClientOriginalExtension(),
            'document_number' => (string) ($payload['document_number'] ?? ''),
            'valid_until' => $payload['valid_until'] ?? null,
        ];
        if (Schema::hasColumn('customer_documents', 'status')) {
            $attributes['status'] = 'pending_review';
        }

        $row = CustomerDocument::query()->updateOrCreate([
            'customer_id' => $customerId,
            'document_type_id' => (int) $payload['document_type_id'],
        ], $attributes);

        if ($oldPath && $oldPath !== $path) {
            CustomerDocumentStorage::delete($oldPath);
        }

        MoveCustomerDocumentToSpacesJob::dispatch($row->id, $path)
            ->delay(now()->addMinutes(10));

        return response()->json([
            'message' => 'Document uploaded successfully. File is staged on site storage and queued for DigitalOcean sync.',
            'data' => [
                'id' => $row->id,
                'file_name' => $row->file_name,
                'status' => $row->status,
                'file_url' => $this->resolveStoredFileUrl($row->file_path, false),
                'sync_status' => CustomerDocumentStorage::spacesConfigured() ? 'queued_to_spaces' : 'spaces_not_configured',
            ],
        ], 201);
    }

    public function recurringPayments(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $serviceFilter = strtolower(trim((string) $request->string('service', 'all')));
        if (! in_array($serviceFilter, ['all', 'rental', 'finance'], true)) {
            $serviceFilter = 'all';
        }

        $customer = $customerAuth->customer;
        if (! $customer instanceof Customer) {
            return response()->json(['data' => []]);
        }

        $onboardingIds = JudopayOnboarding::query()
            ->where('onboardable_type', Customer::class)
            ->where('onboardable_id', $customer->id)
            ->pluck('id');

        $rentalIds = RentingBooking::query()
            ->where('customer_id', $customer->id)
            ->pluck('id');

        $financeIds = FinanceApplication::query()
            ->where('customer_id', $customer->id)
            ->pluck('id');

        $subscriptions = JudopaySubscription::query()
            ->with([
                'subscribable' => function (MorphTo $morphTo): void {
                    $morphTo->morphWith([
                        RentingBooking::class => ['rentingBookingItems.motorbike'],
                        FinanceApplication::class => ['application_items.motorbike'],
                    ]);
                },
                'mitPaymentSessions',
                'citPaymentSessions',
            ])
            ->where(function (Builder $query) use ($onboardingIds, $rentalIds, $financeIds): void {
                if ($onboardingIds->isNotEmpty()) {
                    $query->orWhereIn('judopay_onboarding_id', $onboardingIds);
                }
                if ($rentalIds->isNotEmpty()) {
                    $query->orWhere(function (Builder $nested) use ($rentalIds): void {
                        $nested
                            ->where('subscribable_type', RentingBooking::class)
                            ->whereIn('subscribable_id', $rentalIds);
                    });
                }
                if ($financeIds->isNotEmpty()) {
                    $query->orWhere(function (Builder $nested) use ($financeIds): void {
                        $nested
                            ->where('subscribable_type', FinanceApplication::class)
                            ->whereIn('subscribable_id', $financeIds);
                    });
                }
            })
            ->latest('id')
            ->get()
            ->map(function (JudopaySubscription $subscription) {
                $mitSessions = $subscription->mitPaymentSessions->sortByDesc('created_at')->values();
                $successful = $mitSessions->where('status', 'success');
                $failed = $mitSessions->filter(fn ($row) => in_array($row->status, ['declined', 'error', 'cancelled'], true));
                $queued = NgnMitQueue::query()
                    ->where('subscribable_id', $subscription->id)
                    ->where('cleared', false)
                    ->orderBy('mit_fire_date')
                    ->get();

                $serviceType = $subscription->subscribable_type === RentingBooking::class ? 'rental' : 'finance';
                $vehicleLabel = 'N/A';
                if ($serviceType === 'rental') {
                    $bike = $subscription->subscribable?->rentingBookingItems?->first()?->motorbike;
                    if ($bike) {
                        $vehicleLabel = trim(($bike->make ?? '').' '.($bike->model ?? '').' '.($bike->reg_no ?? ''));
                    }
                } else {
                    $bike = $subscription->subscribable?->application_items?->first()?->motorbike;
                    if ($bike) {
                        $vehicleLabel = trim(($bike->make ?? '').' '.($bike->model ?? '').' '.($bike->reg_no ?? ''));
                    }
                }

                return [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'amount' => (float) ($subscription->amount ?? 0),
                    'service_type' => $serviceType,
                    'vehicle_label' => $vehicleLabel,
                    'paid_total' => (float) $successful->sum('amount'),
                    'paid_count' => $successful->count(),
                    'failed_count' => $failed->count(),
                    'queued_count' => $queued->count(),
                    'next_due_at' => optional($queued->first()?->mit_fire_date)->toDateTimeString(),
                    'last_paid_at' => optional($successful->first()?->payment_completed_at ?? $successful->first()?->created_at)->toDateTimeString(),
                    'created_at' => optional($subscription->created_at)->toDateTimeString(),
                ];
            })
            ->filter(fn (array $row) => $serviceFilter === 'all' ? true : $row['service_type'] === $serviceFilter)
            ->values();

        return response()->json([
            'service_filter' => $serviceFilter,
            'data' => $subscriptions,
        ]);
    }

    public function rentalBrowseOptions(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name', 'address', 'city']),
            'default_branch_id' => $customerAuth->customer?->preferred_branch_id,
            'filters' => [
                ['key' => 'all', 'label' => 'All'],
                ['key' => 'scooter', 'label' => 'Scooter (<=125cc)'],
                ['key' => 'motorbike', 'label' => 'Motorbike (>125cc)'],
            ],
        ]);
    }

    public function rentalAvailable(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $branchId = (int) $request->integer('branch_id', 0);
        $filterType = strtolower(trim((string) $request->string('filter', 'all')));
        if (! in_array($filterType, ['all', 'scooter', 'motorbike'], true)) {
            $filterType = 'all';
        }
        $search = trim((string) $request->string('search', ''));

        $query = DB::table('motorbikes as MB')
            ->leftJoin('branches as BR', 'MB.branch_id', '=', 'BR.id')
            ->join('motorbike_registrations as MR', 'MB.id', '=', 'MR.motorbike_id')
            ->leftJoin('motorbike_annual_compliance as MC', 'MC.motorbike_id', '=', 'MB.id')
            ->rightJoin('renting_pricings as RP', 'RP.motorbike_id', '=', 'MB.id')
            ->select(
                'MB.id',
                'MB.make',
                'MB.model',
                'MB.year',
                'MB.engine',
                'MB.color',
                'MB.is_ebike',
                'MB.branch_id',
                'MR.registration_number as reg_no',
                'BR.name as branch_name',
                'RP.weekly_price',
                DB::raw('COALESCE(RP.minimum_deposit, RP.deposit, 0) as deposit'),
                DB::raw("CONCAT(COALESCE(MC.mot_status,''), IFNULL(CONCAT(' ', MC.mot_due_date), '')) as mot_status"),
                DB::raw("CONCAT(COALESCE(MC.road_tax_status,''), IFNULL(CONCAT(' ', MC.tax_due_date), '')) as road_tax_status")
            )
            ->where('MB.vehicle_profile_id', 1)
            ->where('RP.iscurrent', true)
            ->whereNotExists(function ($nested): void {
                $nested->select(DB::raw(1))
                    ->from('renting_booking_items')
                    ->whereColumn('renting_booking_items.motorbike_id', 'MB.id')
                    ->where('renting_booking_items.is_posted', true)
                    ->whereNull('renting_booking_items.end_date');
            })
            ->where(function ($q): void {
                $q->where('MB.is_ebike', true)
                    ->orWhere(function ($q2): void {
                        $q2->where('MB.is_ebike', false)
                            ->where('MC.road_tax_status', 'Taxed')
                            ->where(function ($q3): void {
                                $q3->where('MC.mot_status', 'Valid')
                                    ->orWhere('MC.mot_status', 'No details held by DVLA');
                            });
                    });
            });

        if ($branchId > 0) {
            $query->where('MB.branch_id', $branchId);
        }
        if ($filterType === 'scooter') {
            $query->where('MB.engine', '<=', 125);
        } elseif ($filterType === 'motorbike') {
            $query->where('MB.engine', '>', 125);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('MB.make', 'like', '%'.$search.'%')
                    ->orWhere('MB.model', 'like', '%'.$search.'%')
                    ->orWhere('MR.registration_number', 'like', '%'.$search.'%');
            });
        }

        $data = $query
            ->orderBy('MB.make')
            ->orderBy('MB.model')
            ->limit(200)
            ->get()
            ->map(fn ($row) => [
                'motorbike_id' => (int) $row->id,
                'name' => trim((string) $row->make.' '.$row->model),
                'make' => $row->make,
                'model' => $row->model,
                'year' => $row->year,
                'engine' => $row->engine,
                'is_ebike' => (bool) $row->is_ebike,
                'reg_no' => $row->reg_no,
                'branch_id' => $row->branch_id,
                'branch_name' => $row->branch_name,
                'weekly_price' => (float) ($row->weekly_price ?? 0),
                'deposit' => (float) ($row->deposit ?? 0),
                'mot_status' => $row->mot_status,
                'road_tax_status' => $row->road_tax_status,
            ])
            ->values();

        return response()->json([
            'filter' => [
                'branch_id' => $branchId > 0 ? $branchId : null,
                'type' => $filterType,
                'search' => $search,
            ],
            'data' => $data,
        ]);
    }

    public function rentalCreateBlueprint(Request $request, int $motorbikeId): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $motorbike = \App\Models\Motorbike::query()
            ->with(['images', 'currentRentingPricing', 'branch'])
            ->findOrFail($motorbikeId);

        $pricing = $motorbike->currentRentingPricing;
        $weeklyRent = $pricing ? (float) ($pricing->weekly_price ?? 80) : 80.0;
        $deposit = $pricing ? (float) ($pricing->deposit ?? 200) : 200.0;

        return response()->json([
            'motorbike' => [
                'id' => $motorbike->id,
                'name' => trim((string) $motorbike->make.' '.$motorbike->model),
                'make' => $motorbike->make,
                'model' => $motorbike->model,
                'year' => $motorbike->year,
                'engine' => $motorbike->engine,
                'reg_no' => $motorbike->reg_no,
                'colour' => $motorbike->color,
                'branch' => $motorbike->branch ? [
                    'id' => $motorbike->branch->id,
                    'name' => $motorbike->branch->name,
                ] : null,
                'image_url' => optional($motorbike->images->firstWhere('is_primary', true) ?: $motorbike->images->first())->file_path
                    ? Storage::url((string) (optional($motorbike->images->firstWhere('is_primary', true) ?: $motorbike->images->first())->file_path))
                    : null,
            ],
            'pricing' => [
                'weekly_rent' => $weeklyRent,
                'deposit' => $deposit,
                'initial_payment' => $weeklyRent + $deposit,
            ],
            'allowed_period_weeks' => [1, 2, 4, 8, 12, 26, 52],
            'fields' => [
                ['name' => 'start_date', 'required' => true, 'type' => 'date', 'min' => now()->toDateString()],
                ['name' => 'rental_period', 'required' => true, 'type' => 'integer', 'allowed' => [1, 2, 4, 8, 12, 26, 52]],
                ['name' => 'notes', 'required' => false, 'type' => 'string', 'max' => 3000],
                ['name' => 'agree_terms', 'required' => true, 'type' => 'boolean', 'must_be_true' => true],
            ],
        ]);
    }

    public function rentalCreateRequest(Request $request, int $motorbikeId): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $motorbike = \App\Models\Motorbike::query()
            ->with(['currentRentingPricing', 'branch'])
            ->findOrFail($motorbikeId);

        $payload = $request->validate([
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'rental_period' => ['required', 'integer', 'in:1,2,4,8,12,26,52'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'agree_terms' => ['accepted'],
        ]);

        $pricing = $motorbike->currentRentingPricing;
        $weeklyRent = $pricing ? (float) ($pricing->weekly_price ?? 0) : 0.0;
        $deposit = $pricing ? (float) ($pricing->deposit ?? 0) : 0.0;

        $subject = 'Rental booking request: '.trim((string) $motorbike->make.' '.$motorbike->model);
        $description = implode("\n", array_filter([
            'Source: api.v1.mobile.portal.rentals.create',
            'Motorbike ID: '.$motorbike->id,
            'Registration: '.((string) $motorbike->reg_no ?: 'N/A'),
            'Start date: '.$payload['start_date'],
            'Rental period (weeks): '.$payload['rental_period'],
            'Weekly rent: '.$weeklyRent,
            'Deposit: '.$deposit,
            'Branch: '.($motorbike->branch?->name ?: 'N/A'),
            ! empty($payload['notes']) ? 'Customer notes: '.trim((string) $payload['notes']) : null,
        ]));

        $booking = ServiceBooking::query()->create([
            'customer_id' => $customerAuth->customer_id ?: $customerAuth->customer?->id,
            'customer_auth_id' => $customerAuth->id,
            'submission_context' => 'portal.rentals.create',
            'enquiry_type' => 'rental',
            'service_type' => 'Rental',
            'subject' => $subject,
            'description' => $description,
            'requires_schedule' => true,
            'booking_date' => $payload['start_date'],
            'status' => 'Pending',
            'fullname' => trim((string) ($customerAuth->customer?->full_name ?: $customerAuth->name ?: 'Portal customer')),
            'phone' => (string) ($customerAuth->customer?->phone ?? ''),
            'reg_no' => (string) ($motorbike->reg_no ?? ''),
            'email' => (string) ($customerAuth->email ?? ''),
            'is_dealt' => false,
            'notes' => trim((string) ($payload['notes'] ?? '')),
        ]);

        return response()->json([
            'message' => 'Rental booking request submitted. We will contact you to confirm.',
            'data' => [
                'enquiry_id' => $booking->id,
                'status' => $booking->status,
                'start_date' => $payload['start_date'],
                'rental_period' => (int) $payload['rental_period'],
                'motorbike_id' => $motorbike->id,
            ],
        ], 201);
    }

    public function repairsAppointmentOptions(): JsonResponse
    {
        return response()->json([
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name', 'address', 'city']),
            'time_slots' => [
                '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00',
                '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
            ],
            'service_type_examples' => ['Basic service', 'Full service', 'Diagnostics', 'Other'],
        ]);
    }

    public function createRepairsAppointment(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'service_type' => ['required', 'string', 'max:255'],
            'bike_reg_no' => ['required', 'string', 'min:2', 'max:12'],
            'bike_make' => ['nullable', 'string', 'max:100'],
            'bike_model' => ['nullable', 'string', 'max:100'],
            'mileage' => ['nullable', 'string', 'max:50'],
            'issue_description' => ['nullable', 'string', 'max:3000'],
            'date_requested' => ['required', 'date', 'after:today'],
            'time_slot' => ['required', 'string', 'max:10'],
            'branch_id' => ['required', 'exists:branches,id'],
            'repair_authorisation_limit' => ['nullable', 'string', 'max:30'],
        ]);

        $customerProfile = $customerAuth->customer;
        $customerName = trim((string) ($customerProfile?->first_name.' '.$customerProfile?->last_name));
        if ($customerName === '') {
            $customerName = (string) ($customerAuth->name ?? 'Portal customer');
        }

        $branch = Branch::query()->find((int) $payload['branch_id']);
        $appointmentAt = $payload['date_requested'].' '.$payload['time_slot'].':00';

        $bookingReason = implode("\n", array_filter([
            'Repairs workshop appointment',
            'Source: api.v1.mobile.portal.repairs.appointments',
            'Service type: '.trim($payload['service_type']),
            'Branch: '.($branch?->name ?? ('Branch #'.$payload['branch_id'])),
            ! empty($payload['bike_make']) ? 'Make: '.trim((string) $payload['bike_make']) : null,
            ! empty($payload['bike_model']) ? 'Model: '.trim((string) $payload['bike_model']) : null,
            ! empty($payload['mileage']) ? 'Mileage: '.trim((string) $payload['mileage']) : null,
            ! empty($payload['issue_description']) ? 'Issue: '.trim((string) $payload['issue_description']) : null,
            'Repair authorisation limit: '.trim((string) ($payload['repair_authorisation_limit'] ?? '0')),
        ]));

        $appointment = CustomerAppointments::query()->create([
            'appointment_date' => $appointmentAt,
            'customer_name' => $customerName,
            'registration_number' => strtoupper(trim((string) $payload['bike_reg_no'])),
            'contact_number' => trim((string) ($customerProfile?->phone ?? '')) ?: null,
            'email' => trim((string) ($customerAuth->email ?? '')) ?: null,
            'is_resolved' => false,
            'booking_reason' => $bookingReason,
        ]);

        return response()->json([
            'message' => 'Repairs appointment request recorded.',
            'data' => [
                'id' => $appointment->id,
                'appointment_date' => optional($appointment->appointment_date)->toDateTimeString(),
                'registration_number' => $appointment->registration_number,
                'is_resolved' => (bool) $appointment->is_resolved,
            ],
        ], 201);
    }

    public function recoveryOptions(): JsonResponse
    {
        return response()->json([
            'vehicle_types' => DeliveryVehicleType::query()
                ->orderBy('id')
                ->get(['id', 'name', 'cc_range', 'additional_fee']),
            'pricing_formula' => [
                'base_fee' => 25.0,
                'distance_fee' => 'max(0, miles - 3) * 3',
                'moveable_false_fee' => 15.0,
                'time_surcharge_peak' => '15% for 07:00-08:59 and 17:00-19:59',
                'time_surcharge_night' => '25% for 21:00-06:59',
            ],
        ]);
    }

    public function recoveryQuote(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'distance_miles' => ['required', 'numeric', 'min:0'],
            'vehicle_type_id' => ['required', 'integer', 'exists:delivery_vehicle_types,id'],
            'moveable' => ['required', 'boolean'],
            'pick_up_datetime' => ['required', 'date'],
        ]);

        $quote = $this->calculateTotalCost(
            (float) $payload['distance_miles'],
            (int) $payload['vehicle_type_id'],
            (bool) $payload['moveable'],
            (string) $payload['pick_up_datetime']
        );

        return response()->json([
            'distance_miles' => (float) $payload['distance_miles'],
            'vehicle_type_id' => (int) $payload['vehicle_type_id'],
            'moveable' => (bool) $payload['moveable'],
            'pick_up_datetime' => $payload['pick_up_datetime'],
            'total_cost' => $quote,
        ]);
    }

    public function createRecoveryRequest(Request $request): JsonResponse
    {
        $customerAuth = $this->customerAuthFrom($request);
        if (! $customerAuth) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

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
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ]);

        $distance = (float) $payload['distance_miles'];
        $vehicleTypeId = (int) $payload['vehicle_type_id'];
        $totalCost = $this->calculateTotalCost($distance, $vehicleTypeId, (bool) $payload['moveable'], (string) $payload['pick_up_datetime']);

        $order = DsOrder::query()->create([
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
            'pickup_lat' => 0,
            'pickup_lon' => 0,
            'dropoff_lat' => 0,
            'dropoff_lon' => 0,
        ]);

        $vehicleTypeName = $this->formatVehicleTypeLabel($vehicleTypeId);
        $branchId = (int) ($payload['branch_id'] ?? 1);
        $branch = Branch::query()->find($branchId);

        $enquiry = MotorbikeDeliveryOrderEnquiries::query()->create([
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
            'vehicle_type' => $vehicleTypeName,
            'vehicle_type_id' => $vehicleTypeId,
            'branch_name' => $branch?->name ?? 'Catford',
            'branch_id' => $branchId,
            'is_dealt' => false,
            'notes' => 'Order enquiry created from mobile API.',
        ]);

        return response()->json([
            'message' => 'Recovery order submitted.',
            'data' => [
                'order_id' => $order->id,
                'enquiry_id' => $enquiry->id,
                'total_cost' => $totalCost,
                'vehicle_type' => $vehicleTypeName,
            ],
        ], 201);
    }

    private function calculateTotalCost(float $distance, int $vehicleTypeId, bool $moveable, string $serviceTime): float
    {
        $baseFee = 25.0;
        $distanceFee = max(0, $distance - 3) * 3;
        $motorcycleTypeFee = match ($vehicleTypeId) {
            2 => 5.0,
            3 => 10.0,
            default => 0.0,
        };
        $hour = (int) date('H', strtotime($serviceTime));
        $timeSurcharge = 0.0;
        if (($hour >= 7 && $hour < 9) || ($hour >= 17 && $hour < 20)) {
            $timeSurcharge = 0.15;
        } elseif ($hour >= 21 || $hour < 7) {
            $timeSurcharge = 0.25;
        }
        $additionalFees = $moveable ? 0.0 : 15.0;

        return round((($baseFee + $distanceFee + $motorcycleTypeFee) * (1 + $timeSurcharge)) + $additionalFees, 2);
    }

    private function formatVehicleTypeLabel(int $vehicleTypeId): string
    {
        return match ($vehicleTypeId) {
            1 => 'Standard (0-125cc CC)',
            2 => 'Mid-Range (126-600cc CC)',
            3 => 'Heavy/Dual (601cc+ CC)',
            default => 'Motorcycle',
        };
    }

    private function resolveStoredFileUrl(?string $path, bool $isPrivate = false): ?string
    {
        if (! $path || $isPrivate) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $normalised = ltrim(str_replace(['storage/', 'public/'], '', $path), '/');
        if ($normalised === '') {
            return null;
        }

        try {
            if (str_starts_with($normalised, 'customer-documents/')) {
                return CustomerDocumentStorage::urlForPath($path) ?? url('/storage/'.$normalised);
            }

            if (str_starts_with($normalised, 'customers/')) {
                return Storage::disk('public')->url($normalised);
            }

            return Storage::disk('public')->url($normalised);
        } catch (\Throwable) {
            return str_starts_with($path, '/storage/')
                ? url($path)
                : url('/storage/'.$normalised);
        }
    }
}
