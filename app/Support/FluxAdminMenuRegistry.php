<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/** Sidebar + quick-link pages for Flux Admin global search. */
final class FluxAdminMenuRegistry
{
    /**
     * @return list<array{label: string, group: string, url: string, keywords: string, search_text: string}>
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

                if (! FluxAdminSearchGate::allowsMenuRoute($route)) {
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

        if ($when === 'full_club_admin') {
            return FluxAdminAccess::canFullClubAdmin($user);
        }

        if ($when === 'limited_club') {
            return ! FluxAdminAccess::canFullClubAdmin($user);
        }

        if ($when === 'club_commons_role') {
            return method_exists($user, 'hasRole')
                && $user->hasRole('see-menu-commons')
                && ! FluxAdminAccess::canFullClubAdmin($user);
        }

        if (! empty($entry['role'])) {
            return method_exists($user, 'hasRole') && $user->hasRole((string) $entry['role']);
        }

        if (! empty($entry['canany']) && is_array($entry['canany'])) {
            foreach ($entry['canany'] as $permission) {
                if (self::userCan($user, (string) $permission)) {
                    return true;
                }
            }

            return false;
        }

        $permission = $entry['permission'] ?? null;

        if ($permission !== null && $permission !== '') {
            return self::userCan($user, (string) $permission);
        }

        return true;
    }

    protected static function userCan(Authenticatable $user, string $permission): bool
    {
        if (method_exists($user, 'can') && $user->can($permission)) {
            return true;
        }

        return FluxAdminAccess::isSuperAdmin($user) || FluxAdminAccess::isAdmin($user);
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
