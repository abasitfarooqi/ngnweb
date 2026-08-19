<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
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

        $actor = $this->customerActor($request);

        return response()->json([
            'data' => $this->mapProfile($profile, $actor),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        if ($this->resolveProfile($request) === null) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'message' => 'Your profile is read-only. Contact NGN if you need any details updated.',
        ], 422);
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
                ->forCustomerUpload()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'is_mandatory', 'required_for', 'validation_rules']),
        ]);
    }

    public function cancelOrder(Request $request, int $id): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $order = EcOrder::query()
            ->where('customer_id', $actor->id)
            ->findOrFail($id);

        if (in_array($order->order_status, ['cancelled', 'delivered'], true)) {
            return response()->json(['message' => 'This order cannot be cancelled.'], 422);
        }

        $order->update([
            'order_status' => 'cancelled',
            'shipping_status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'data' => [
                'id' => $order->id,
                'order_status' => 'cancelled',
            ],
        ]);
    }

    public function resendVerification(Request $request): JsonResponse
    {
        $actor = $this->customerActor($request);
        if (! $actor) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($actor->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        $actor->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.']);
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

    private function mapProfile(Customer $profile, ?CustomerAuth $actor = null): array
    {
        return [
            'full_name' => trim(($profile->first_name ?? '').' '.($profile->last_name ?? '')),
            'email' => (string) ($actor?->email ?? $profile->email ?? ''),
            'phone' => (string) ($profile->phone ?? ''),
            'whatsapp' => (string) ($profile->whatsapp ?? ''),
        ];
    }

    private function paymentMethodCacheKey(int $customerAuthId): string
    {
        return 'mobile.portal.payment_method.'.$customerAuthId;
    }
}
