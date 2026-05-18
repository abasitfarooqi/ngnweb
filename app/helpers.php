<?php

if (! function_exists('ngn_asset')) {
    /**
     * Versioned URL for compiled static assets in public/assets/ngn/.
     */
    function ngn_asset(string $file): string
    {
        $relative = 'assets/ngn/'.ltrim($file, '/');
        $path = public_path($relative);

        if (! is_file($path)) {
            return asset($relative);
        }

        return asset($relative).'?v='.filemtime($path);
    }
}
