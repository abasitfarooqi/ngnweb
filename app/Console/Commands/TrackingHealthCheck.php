<?php

namespace App\Console\Commands;

use App\Models\TrackingAlert;
use App\Models\TrackingSource;
use Illuminate\Console\Command;

class TrackingHealthCheck extends Command
{
    protected $signature = 'tracking:health-check';
    protected $description = 'Derive dormant tracking health and create deduplicated stale alerts';

    public function handle(): int
    {
        if (! config('tracking.enabled')) return self::SUCCESS;
        $threshold = now()->subMinutes((int) config('tracking.stale_after_minutes'));
        TrackingSource::query()->where('sharing_enabled', true)->each(function (TrackingSource $source) use ($threshold): void {
            $latest = $source->locations()->latest('recorded_at')->first();
            if (! $latest || $latest->recorded_at->lt($threshold)) {
                TrackingAlert::firstOrCreate([
                    'trackable_type' => $latest?->trackable_type ?? TrackingSource::class,
                    'trackable_id' => $latest?->trackable_id ?? $source->id,
                    'tracking_source_id' => $source->id,
                    'alert_type' => 'NO_LOCATION_RECEIVED',
                    'resolved_at' => null,
                ], [
                    'vehicle_id' => $source->vehicle_id,
                    'severity' => 'warning',
                    'title' => 'Location update missing',
                    'message' => 'No recent customer phone location has been received within the configured threshold.',
                    'triggered_at' => now(),
                ]);
            }
        });
        return self::SUCCESS;
    }
}
