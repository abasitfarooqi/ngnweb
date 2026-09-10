<?php

return [
    'enabled' => (bool) env('LOCATION_TRACKING_ENABLED', false),
    'normal_interval_minutes' => (int) env('TRACKING_NORMAL_INTERVAL_MINUTES', 60),
    'stale_after_minutes' => (int) env('TRACKING_STALE_AFTER_MINUTES', 120),
    'recovery_phone_interval_minutes' => (int) env('TRACKING_RECOVERY_PHONE_INTERVAL_MINUTES', 5),
    'location_retention_days' => env('TRACKING_LOCATION_RETENTION_DAYS'),
    'map_tile_url' => env('TRACKING_MAP_TILE_URL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
    'map_attribution' => env('TRACKING_MAP_ATTRIBUTION', '&copy; OpenStreetMap contributors'),
    'test_mode' => (bool) env('TRACKING_TEST_MODE', false),
];
