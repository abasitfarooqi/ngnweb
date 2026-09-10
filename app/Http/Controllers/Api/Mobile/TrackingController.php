<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Models\LocationSharingEvent;
use App\Models\TrackingLocation;
use App\Models\TrackingSource;
use App\Services\Tracking\TrackingAgreementService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackingController
{
    public function __construct(private readonly TrackingAgreementService $agreements) {}

    public function config(): JsonResponse
    {
        return response()->json([
            'enabled' => (bool) config('tracking.enabled'),
            'normal_interval_minutes' => config('tracking.normal_interval_minutes'),
            'recovery_interval_minutes' => config('tracking.recovery_phone_interval_minutes'),
        ]);
    }

    public function agreements(Request $request): JsonResponse
    {
        $this->ensureEnabled();
        $customer = $this->customer($request);
        $items = collect($this->agreements->activeFor($customer))->map(function (array $context) use ($customer): array {
            $model = $context['model'];
            $source = TrackingSource::firstOrCreate([
                'customer_id' => $customer->id,
                'vehicle_id' => $context['vehicle_id'],
                'source_type' => 'customer_phone',
            ], ['status' => 'inactive', 'sharing_enabled' => false]);
            return ['type' => $model instanceof \App\Models\RentingBooking ? 'rental' : 'finance', 'id' => $model->id, 'vehicle_id' => $context['vehicle_id'], 'sharing_enabled' => (bool) $source->sharing_enabled, 'status' => $this->status($source)];
        });
        return response()->json(['data' => $items->values()]);
    }

    public function sharing(Request $request, string $type, int $id): JsonResponse
    {
        $this->ensureEnabled();
        $request->validate(['enabled' => ['required', 'boolean']]);
        $customer = $this->customer($request);
        $context = $this->agreements->findOwnedActive($customer, $type, $id);
        abort_unless($context, 404);
        $source = TrackingSource::firstOrCreate(['customer_id' => $customer->id, 'vehicle_id' => $context['vehicle_id'], 'source_type' => 'customer_phone'], ['status' => 'inactive']);
        DB::transaction(function () use ($source, $request, $context, $customer): void {
            $enabled = (bool) $request->boolean('enabled');
            $source->update(['sharing_enabled' => $enabled, 'status' => $enabled ? 'unavailable' : 'inactive']);
            LocationSharingEvent::create(['customer_id' => $customer->id, 'trackable_type' => $context['model']->getMorphClass(), 'trackable_id' => $context['model']->id, 'tracking_source_id' => $source->id, 'action' => $enabled ? 'enabled' : 'disabled', 'recorded_at' => now()]);
        });
        return response()->json(['sharing_enabled' => $source->fresh()->sharing_enabled]);
    }

    public function location(Request $request, string $type, int $id): JsonResponse
    {
        $this->ensureEnabled();
        $data = $request->validate(['latitude' => ['required', 'numeric', 'between:-90,90'], 'longitude' => ['required', 'numeric', 'between:-180,180'], 'accuracy_metres' => ['nullable', 'numeric', 'min:0'], 'speed' => ['nullable', 'numeric', 'min:0'], 'heading' => ['nullable', 'numeric', 'between:0,360'], 'altitude' => ['nullable', 'numeric'], 'recorded_at' => ['required', 'date']]);
        $customer = $this->customer($request);
        $context = $this->agreements->findOwnedActive($customer, $type, $id);
        abort_unless($context, 404);
        $source = TrackingSource::query()->where(['customer_id' => $customer->id, 'vehicle_id' => $context['vehicle_id'], 'source_type' => 'customer_phone'])->first();
        abort_unless($source?->sharing_enabled, 403, 'Location sharing is disabled.');
        $recordedAt = Carbon::parse($data['recorded_at']);
        abort_if($recordedAt->lt(now()->subDay()) || $recordedAt->gt(now()->addMinutes(5)), 422, 'Invalid location timestamp.');
        $latest = $source->locations()->where('trackable_type', $context['model']->getMorphClass())->where('trackable_id', $context['model']->id)->latest('recorded_at')->first();
        abort_if($latest && $latest->recorded_at->diffInMinutes($recordedAt) < max(1, (int) config('tracking.normal_interval_minutes')), 429, 'Location update is too frequent.');
        $location = $source->locations()->create($data + ['trackable_type' => $context['model']->getMorphClass(), 'trackable_id' => $context['model']->id, 'vehicle_id' => $context['vehicle_id'], 'customer_id' => $customer->id, 'recorded_at' => $recordedAt, 'received_at' => now(), 'test_data' => (bool) config('tracking.test_mode')]);
        $source->update(['status' => 'active', 'last_seen_at' => now()]);
        return response()->json(['data' => $location], 201);
    }

    private function ensureEnabled(): void { abort_unless((bool) config('tracking.enabled'), 404); }
    private function status(TrackingSource $source): string { if (! $source->sharing_enabled) return 'disabled'; if (! $source->last_seen_at) return 'unavailable'; return $source->last_seen_at->lt(now()->subMinutes((int) config('tracking.stale_after_minutes'))) ? 'stale' : 'sharing'; }
    private function customer(Request $request): \App\Models\Customer
    {
        $actor = $request->user();
        return $actor->customer ?? $actor;
    }
}
