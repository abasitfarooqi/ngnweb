<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Shared Geoapify geocode + driving-distance lookup, extracted from the
 * duplicated logic in Site\Recovery\Delivery and Portal\Recovery\Request so
 * the mobile app (and any future caller) has one place to get postcode ->
 * miles for recovery/delivery quoting.
 */
class GeoapifyDistanceService
{
    /**
     * @return array{lat:float,lon:float,formatted:string,postcode:string}|null
     */
    public function geocode(string $postcode): ?array
    {
        $postcode = trim($postcode);
        if ($postcode === '') {
            return null;
        }

        $cacheKey = 'geoapify_geocode_'.md5(strtolower($postcode));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($postcode) {
            $apiKey = config('services.geoapify.key');
            $baseUrl = rtrim((string) config('services.geoapify.url'), '/');
            if (! $apiKey || ! $baseUrl) {
                return null;
            }

            $this->throttle('geoapify_geocode_last_request');

            try {
                $response = Http::timeout(10)->get($baseUrl.'/geocode/search', [
                    'text' => $postcode,
                    'apiKey' => $apiKey,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Geoapify geocode request failed: '.$e->getMessage());

                return null;
            }

            if (! $response->successful()) {
                return null;
            }

            $features = $response->json('features', []);
            if (! is_array($features) || $features === []) {
                return null;
            }

            $feature = $features[0];
            $coordinates = $feature['geometry']['coordinates'] ?? null;
            if (! is_array($coordinates) || count($coordinates) < 2) {
                return null;
            }

            return [
                'lon' => (float) $coordinates[0],
                'lat' => (float) $coordinates[1],
                'formatted' => (string) ($feature['properties']['formatted'] ?? ''),
                'postcode' => (string) ($feature['properties']['postcode'] ?? ''),
            ];
        });
    }

    /**
     * @param  array{lat:float,lon:float}  $from
     * @param  array{lat:float,lon:float}  $to
     * @return array{distance_miles:float,duration_minutes:float}|null
     */
    public function drivingDistance(array $from, array $to): ?array
    {
        if (! isset($from['lat'], $from['lon'], $to['lat'], $to['lon'])) {
            return null;
        }

        $cacheKey = 'geoapify_route_'.md5($from['lat'].','.$from['lon'].'|'.$to['lat'].','.$to['lon']);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($from, $to) {
            $apiKey = config('services.geoapify.key');
            $baseUrl = rtrim((string) config('services.geoapify.url'), '/');
            if (! $apiKey || ! $baseUrl) {
                return null;
            }

            $this->throttle('geoapify_routing_last_request');

            try {
                $response = Http::timeout(10)->get($baseUrl.'/routing', [
                    'waypoints' => $from['lat'].','.$from['lon'].'|'.$to['lat'].','.$to['lon'],
                    'mode' => 'drive',
                    'apiKey' => $apiKey,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Geoapify routing request failed: '.$e->getMessage());

                return null;
            }

            if (! $response->successful()) {
                return null;
            }

            $features = $response->json('features', []);
            if (! is_array($features) || $features === []) {
                return null;
            }

            $properties = $features[0]['properties'] ?? [];
            $distance = (float) ($properties['distance'] ?? 0);
            $units = (string) ($properties['distance_units'] ?? 'meters');
            $miles = $units === 'meters' ? round($distance / 1609.34, 2) : round($distance, 2);

            return [
                'distance_miles' => $miles,
                'duration_minutes' => round((float) ($properties['time'] ?? 0) / 60, 1),
            ];
        });
    }

    /**
     * Convenience: postcode -> postcode driving distance in one call.
     *
     * @return array{
     *     distance_miles:float,
     *     duration_minutes:float,
     *     pickup:array{lat:float,lon:float,formatted:string},
     *     dropoff:array{lat:float,lon:float,formatted:string}
     * }|null
     */
    public function distanceBetweenPostcodes(string $pickupPostcode, string $dropoffPostcode): ?array
    {
        $pickup = $this->geocode($pickupPostcode);
        $dropoff = $this->geocode($dropoffPostcode);

        if ($pickup === null || $dropoff === null) {
            return null;
        }

        $route = $this->drivingDistance($pickup, $dropoff);
        if ($route === null) {
            return null;
        }

        return [
            'distance_miles' => $route['distance_miles'],
            'duration_minutes' => $route['duration_minutes'],
            'pickup' => $pickup,
            'dropoff' => $dropoff,
        ];
    }

    private function throttle(string $key): void
    {
        $last = Cache::get($key);
        $now = microtime(true);
        if ($last) {
            $elapsed = $now - $last;
            if ($elapsed < 1) {
                usleep((int) ((1 - $elapsed) * 1_000_000));
            }
        }
        Cache::put($key, microtime(true), 60);
    }
}
