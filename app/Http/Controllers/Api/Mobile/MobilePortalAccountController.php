<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Models\DocumentType;
use App\Models\Ecommerce\EcOrder;
use App\Models\Ecommerce\EcPaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class MobilePortalAccountController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $profile = $this->resolveProfile($request);
        if (! $profile) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'data' => $this->mapProfile($profile),
            'branches' => Branch::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $profile = $this->resolveProfile($request);
        if (! $profile) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'preferred_branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'dob' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'license_issuance_authority' => ['nullable', 'string', 'max:100'],
            'license_issuance_date' => ['nullable', 'date'],
            'license_expiry_date' => ['nullable', 'date'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (! empty($payload['license_expiry_date']) && ! empty($payload['license_issuance_date'])
            && (string) $payload['license_expiry_date'] <= (string) $payload['license_issuance_date']) {
            return response()->json(['message' => 'Licence expiry date must be after licence issuance date.'], 422);
        }

        $profile->update([
            'first_name' => $payload['first_name'] ?? $profile->first_name,
            'last_name' => $payload['last_name'] ?? $profile->last_name,
            'phone' => $payload['phone'],
            'whatsapp' => $payload['whatsapp'] ?? '',
            'postcode' => $payload['postcode'] ?? '',
            'city' => $payload['city'] ?? '',
            'country' => $payload['country'] ?? 'United Kingdom',
            'preferred_branch_id' => $payload['preferred_branch_id'] ?? null,
            'dob' => $payload['dob'] ?? $profile->dob,
            'nationality' => $payload['nationality'] ?? '',
            'address' => $payload['address'] ?? '',
            'license_number' => $payload['license_number'] ?? '',
            'license_issuance_authority' => $payload['license_issuance_authority'] ?? '',
            'license_issuance_date' => $payload['license_issuance_date'] ?? null,
            'license_expiry_date' => $payload['license_expiry_date'] ?? null,
            'emergency_contact' => $payload['emergency_contact_name'] ?? '',
        ]);

        return response()->json([
            'message' => 'Profile updated.',
            'data' => $this->mapProfile($profile->fresh()),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (! (bool) $actor->customer?->is_register) {
            return response()->json(['message' => 'Password changes are only available for registered club members.'], 422);
        }

        $payload = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check((string) $payload['current_password'], (string) $actor->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $actor->password = Hash::make((string) $payload['password']);
        $actor->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function paymentMethods(Request $request): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'available' => EcPaymentMethod::query()->active()->get(['id', 'title', 'slug', 'logo', 'instructions']),
            'selected_payment_method_id' => Cache::get($this->paymentMethodCacheKey($actor->id)),
        ]);
    }

    public function selectPaymentMethod(Request $request): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $payload = $request->validate([
            'payment_method_id' => ['required', 'integer', 'exists:ec_payment_methods,id'],
        ]);

        Cache::put($this->paymentMethodCacheKey($actor->id), (int) $payload['payment_method_id'], now()->addDays(30));

        return response()->json(['message' => 'Payment method selected for mobile checkout.']);
    }

    public function clearPaymentMethod(Request $request): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        Cache::forget($this->paymentMethodCacheKey($actor->id));

        return response()->json(['message' => 'Selected payment method cleared.']);
    }

    public function orderDetail(Request $request, int $id): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $order = EcOrder::query()
            ->with(['items.product', 'shippingMethod', 'paymentMethod', 'customerAddress', 'branch'])
            ->where('customer_id', $actor->id)
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $order->id,
                'order_date' => optional($order->order_date)->toDateTimeString(),
                'order_status' => $order->order_status,
                'payment_status' => $order->payment_status,
                'shipping_status' => $order->shipping_status,
                'total_amount' => (float) ($order->total_amount ?? 0),
                'discount' => (float) ($order->discount ?? 0),
                'tax' => (float) ($order->tax ?? 0),
                'shipping_cost' => (float) ($order->shipping_cost ?? 0),
                'grand_total' => (float) ($order->grand_total ?? 0),
                'shipping_method' => $order->shippingMethod?->name,
                'payment_method' => $order->paymentMethod?->title,
                'branch' => $order->branch ? ['id' => $order->branch->id, 'name' => $order->branch->name] : null,
                'address' => $order->customerAddress ? [
                    'street_address' => $order->customerAddress->street_address,
                    'city' => $order->customerAddress->city,
                    'postcode' => $order->customerAddress->postcode,
                ] : null,
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'name' => $item->product_name ?: $item->product?->name,
                    'sku' => $item->sku ?: $item->product?->sku,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) ($item->unit_price ?? 0),
                    'line_total' => (float) ($item->line_total ?? 0),
                    'image_url' => $item->product?->image_url,
                ])->values(),
            ],
        ]);
    }

    public function documentTypes(): JsonResponse
    {
        return response()->json([
            'data' => DocumentType::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'is_mandatory', 'required_for', 'validation_rules']),
        ]);
    }

    private function customerActor(Request $request): ?CustomerAuth
    {
        $actor = $request->user('customer') ?: $request->user('sanctum');

        return $actor instanceof CustomerAuth ? $actor : null;
    }

    private function resolveProfile(Request $request): ?Customer
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return null;
        }

        if ($actor->customer instanceof Customer) {
            return $actor->customer;
        }

        $created = Customer::query()->create([
            'first_name' => 'Customer',
            'last_name' => (string) ($actor->email ?? 'Account'),
            'username' => 'customer_'.$actor->id,
            'email' => (string) ($actor->email ?? ''),
            'country' => 'United Kingdom',
            'verification_status' => 'draft',
        ]);
        $actor->customer_id = $created->id;
        $actor->save();

        return $created;
    }

    private function mapProfile(Customer $profile): array
    {
        $ec = $profile->emergency_contact;
        $emergencyContact = is_array($ec) ? ($ec['name'] ?? '') : (string) $ec;

        return [
            'id' => $profile->id,
            'first_name' => (string) ($profile->first_name ?? ''),
            'last_name' => (string) ($profile->last_name ?? ''),
            'phone' => (string) ($profile->phone ?? ''),
            'whatsapp' => (string) ($profile->whatsapp ?? ''),
            'postcode' => (string) ($profile->postcode ?? ''),
            'city' => (string) ($profile->city ?? ''),
            'country' => (string) ($profile->country ?? 'United Kingdom'),
            'preferred_branch_id' => $profile->preferred_branch_id,
            'dob' => $profile->dob ? $profile->dob->format('Y-m-d') : null,
            'nationality' => (string) ($profile->nationality ?? ''),
            'address' => (string) ($profile->address ?? ''),
            'license_number' => (string) ($profile->license_number ?? ''),
            'license_issuance_authority' => (string) ($profile->license_issuance_authority ?? ''),
            'license_issuance_date' => $profile->license_issuance_date ? $profile->license_issuance_date->format('Y-m-d') : null,
            'license_expiry_date' => $profile->license_expiry_date ? $profile->license_expiry_date->format('Y-m-d') : null,
            'emergency_contact_name' => $emergencyContact,
        ];
    }

    private function paymentMethodCacheKey(int $customerAuthId): string
    {
        return 'mobile.portal.payment_method.'.$customerAuthId;
    }
}
