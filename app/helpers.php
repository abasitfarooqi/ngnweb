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

if (! function_exists('judopay_using_flux')) {
    /**
     * True when Judopay admin screens are being served under Flux Admin.
     */
    function judopay_using_flux(): bool
    {
        if (request()->routeIs('flux-admin.judopay.*')) {
            session(['judopay_ui' => 'flux']);

            return true;
        }

        // Direct Backpack GETs must never stay stuck on the Flux shell.
        if (request()->routeIs('page.judopay.*') && request()->isMethod('GET')) {
            session(['judopay_ui' => 'backpack']);

            return false;
        }

        $previous = (string) url()->previous();

        if (str_contains($previous, '/flux-admin/judopay')) {
            session(['judopay_ui' => 'flux']);

            return true;
        }

        return session('judopay_ui') === 'flux';
    }
}

if (! function_exists('judopay_route')) {
    /**
     * Named Judopay admin URL — Flux or Backpack — without rewriting POST action endpoints.
     *
     * @param  mixed  $parameters
     */
    function judopay_route(string $name, $parameters = [], bool $absolute = true): string
    {
        if (judopay_using_flux()) {
            $fluxName = 'flux-admin.judopay.'.$name;
            if (\Illuminate\Support\Facades\Route::has($fluxName)) {
                return route($fluxName, $parameters, $absolute);
            }
        }

        return route('page.judopay.'.$name, $parameters, $absolute);
    }
}
