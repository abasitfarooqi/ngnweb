<?php

namespace App\Support;

use App\Models\FinanceApplication;
use Illuminate\Http\Request;

/** Preserve finance index filters in URLs when opening edit/show and returning after save. */
final class FluxAdminFinanceListQuery
{
    /** @var list<string> */
    public const KEYS = [
        'q',
        'sort',
        'dir',
        'pp',
        'page',
        'contractType',
        'status',
        'filterLogbook',
        'filterPosted',
        'contractDateFrom',
        'contractDateTo',
    ];

    public static function paramsFromRequest(?Request $request = null): array
    {
        $request ??= request();

        return collect(self::KEYS)
            ->mapWithKeys(fn (string $key) => [$key => $request->query($key)])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->all();
    }

    public static function queryString(?Request $request = null): string
    {
        return http_build_query(self::paramsFromRequest($request));
    }

    public static function indexUrl(?Request $request = null): string
    {
        $query = self::queryString($request);

        return route('flux-admin.finance.index').($query !== '' ? '?'.$query : '');
    }

    public static function editUrl(FinanceApplication $application, ?Request $request = null): string
    {
        $query = self::queryString($request);
        $base = route('flux-admin.finance.edit', $application);

        return $query !== '' ? $base.'?'.$query : $base;
    }

    public static function showUrl(FinanceApplication $application, ?Request $request = null): string
    {
        $query = self::queryString($request);
        $base = route('flux-admin.finance.show', $application);

        return $query !== '' ? $base.'?'.$query : $base;
    }
}
