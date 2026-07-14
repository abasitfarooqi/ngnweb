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
        if (request()->routeIs('flux-admin.judopay.*')
            || request()->routeIs('flux-admin.judopay-*')
            || request()->routeIs('flux-admin.ngn-mit-queue.*')) {
            session(['judopay_ui' => 'flux']);

            return true;
        }

        // Direct Backpack GETs must never stay stuck on the Flux shell.
        if ((request()->routeIs('page.judopay.*')
            || request()->routeIs('dev-judopay-*')
            || request()->routeIs('dev-ngn-mit-queue.*')) && request()->isMethod('GET')) {
            session(['judopay_ui' => 'backpack']);

            return false;
        }

        $previous = (string) url()->previous();

        if (str_contains($previous, '/flux-admin/judopay')
            || str_contains($previous, '/flux-admin/ngn-mit-queue')
            || str_contains($previous, '/flux-admin/judopay-')) {
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

if (! function_exists('judopay_counterpart_url')) {
    /**
     * Equivalent page in the other Judopay admin UI (Flux ↔ Backpack).
     * Staff can switch skins without losing the same ops/CRUD surface.
     */
    function judopay_counterpart_url(): string
    {
        $name = (string) (request()->route()?->getName() ?? '');
        $params = request()->route()?->parameters() ?? [];

        $pairs = [
            'flux-admin.judopay.index' => 'page.judopay.index',
            'page.judopay.index' => 'flux-admin.judopay.index',
            'flux-admin.judopay.mit-dashboard' => 'page.judopay.mit-dashboard',
            'page.judopay.mit-dashboard' => 'flux-admin.judopay.mit-dashboard',
            'flux-admin.judopay.weekly-mit-queue' => 'page.judopay.weekly-mit-queue',
            'page.judopay.weekly-mit-queue' => 'flux-admin.judopay.weekly-mit-queue',
            'flux-admin.judopay.subscribe' => 'page.judopay.subscribe',
            'page.judopay.subscribe' => 'flux-admin.judopay.subscribe',
            'flux-admin.judopay-subscriptions.index' => 'dev-judopay-subscription.index',
            'dev-judopay-subscription.index' => 'flux-admin.judopay-subscriptions.index',
            'flux-admin.judopay-subscriptions.create' => 'dev-judopay-subscription.index',
            'flux-admin.judopay-subscriptions.edit' => 'dev-judopay-subscription.edit',
            'dev-judopay-subscription.edit' => 'flux-admin.judopay-subscriptions.edit',
            'flux-admin.judopay-mit-queue.index' => 'dev-judopay-mit-queue.index',
            'dev-judopay-mit-queue.index' => 'flux-admin.judopay-mit-queue.index',
            'flux-admin.judopay-mit-queue.create' => 'dev-judopay-mit-queue.index',
            'flux-admin.judopay-mit-queue.edit' => 'dev-judopay-mit-queue.edit',
            'dev-judopay-mit-queue.edit' => 'flux-admin.judopay-mit-queue.edit',
            'flux-admin.ngn-mit-queue.index' => 'dev-ngn-mit-queue.index',
            'dev-ngn-mit-queue.index' => 'flux-admin.ngn-mit-queue.index',
            'flux-admin.ngn-mit-queue.create' => 'dev-ngn-mit-queue.index',
            'flux-admin.ngn-mit-queue.edit' => 'dev-ngn-mit-queue.edit',
            'dev-ngn-mit-queue.edit' => 'flux-admin.ngn-mit-queue.edit',
        ];

        $target = $pairs[$name] ?? null;

        if ($target && \Illuminate\Support\Facades\Route::has($target)) {
            $forward = [];
            if (str_contains($target, 'subscribe') && isset($params['id'])) {
                $forward['id'] = $params['id'];
            }
            if (str_ends_with($target, '.edit') && isset($params['id'])) {
                $forward['id'] = $params['id'];
            }

            return route($target, $forward);
        }

        // Fallbacks when mid-form or unmatched route.
        return judopay_using_flux()
            ? route('page.judopay.index')
            : route('flux-admin.judopay.index');
    }
}

if (! function_exists('judopay_switch_label')) {
    /**
     * Label for the staff UI switch control.
     */
    function judopay_switch_label(): string
    {
        return judopay_using_flux() ? 'Switch to Backpack' : 'Switch to Flux';
    }
}
