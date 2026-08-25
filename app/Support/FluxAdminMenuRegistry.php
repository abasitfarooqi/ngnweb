<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/** Sidebar + quick-link pages for Flux Admin global search. */
final class FluxAdminMenuRegistry
{
    /**
     * @return list<array{label: string, group: string, url: string, route: ?string, keywords: string, search_text: string}>
     */
    public static function items(): array
    {
        $out = [];

        foreach (config('flux-admin-menu.entries', []) as $entry) {
            if (! self::isVisible($entry)) {
                continue;
            }

            $route = $entry['route'] ?? null;
            $url = $entry['url'] ?? null;

            if ($url === null) {
                if (! is_string($route) || $route === '' || ! Route::has($route)) {
                    continue;
                }

                if (! isset($entry['when']) && ! FluxAdminSearchGate::allowsMenuRoute($route, ignoreHidden: true)) {
                    continue;
                }

                try {
                    $url = route($route, $entry['params'] ?? []);
                } catch (\Throwable) {
                    continue;
                }
            }

            $group = (string) ($entry['group'] ?? 'Menu');
            $label = (string) ($entry['label'] ?? '');
            $keywords = Str::lower(trim($label.' '.$group.' '.($entry['keywords'] ?? '')));

            $out[] = [
                'label' => $label,
                'group' => $group,
                'url' => $url,
                'route' => is_string($route) ? $route : null,
                'keywords' => $keywords,
                'search_text' => $keywords,
            ];
        }

        return $out;
    }

    /** @param array<string, mixed> $entry */
    protected static function isVisible(array $entry): bool
    {
        $user = FluxAdminAccess::user();

        if ($user === null) {
            return false;
        }

        $when = $entry['when'] ?? null;

        if ($when === 'communications_log') {
            return FluxAdminAccess::canViewCommunicationsLog($user);
        }

        if ($when === 'communications') {
            return FluxAdminAccess::canAccessCommunications($user);
        }

        if ($when === 'full_club_admin' || $when === 'super_admin' || ! empty($entry['super_admin'])) {
            return FluxAdminAccess::isSuperAdmin($user);
        }

        $requirement = [];

        if (! empty($entry['role'])) {
            $requirement['role'] = (string) $entry['role'];
        }

        if (! empty($entry['canany']) && is_array($entry['canany'])) {
            $requirement['any'] = $entry['canany'];
        }

        $permission = $entry['permission'] ?? null;

        if ($permission !== null && $permission !== '') {
            $requirement['permission'] = (string) $permission;
        }

        if ($requirement !== []) {
            return FluxAdminPageAccess::allowsRequirement($user, $requirement);
        }

        $route = $entry['route'] ?? null;

        return is_string($route) && $route !== ''
            ? FluxAdminPageAccess::allows($user, $route)
            : true;
    }

    public static function matches(string $query, array $item): bool
    {
        $term = Str::lower(trim($query));

        if ($term === '' || mb_strlen($term) < 2) {
            return false;
        }

        $haystack = $item['search_text'] ?? '';

        if (Str::contains($haystack, $term)) {
            return true;
        }

        $words = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return false;
        }

        foreach ($words as $word) {
            if (! Str::contains($haystack, $word)) {
                return false;
            }
        }

        return true;
    }

    public static function matchScore(string $query, array $item): int
    {
        $term = Str::lower(trim($query));
        $label = Str::lower($item['label'] ?? '');
        $group = Str::lower($item['group'] ?? '');

        if ($label === $term) {
            return 1000;
        }

        if ($group === $term) {
            return 900;
        }

        if (Str::startsWith($label, $term)) {
            return 800;
        }

        if (Str::startsWith($group, $term)) {
            return 700;
        }

        if (Str::contains($label, $term)) {
            return 600;
        }

        if (Str::contains($group, $term)) {
            return 500;
        }

        return self::matches($query, $item) ? 100 : 0;
    }
}
