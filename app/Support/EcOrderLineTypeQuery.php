<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

final class EcOrderLineTypeQuery
{
    public static function apply(Builder $query, ?string $lineType): Builder
    {
        return match ($lineType) {
            'catalogue' => $query->whereDoesntHave('orderItems', function (Builder $q): void {
                $q->where('item_type', 'sparepart');
            }),
            'sparepart' => $query
                ->whereHas('orderItems', fn (Builder $q) => $q->where('item_type', 'sparepart'))
                ->whereDoesntHave('orderItems', fn (Builder $q) => $q->where('item_type', 'catalogue')),
            'mixed' => $query
                ->whereHas('orderItems', fn (Builder $q) => $q->where('item_type', 'sparepart'))
                ->whereHas('orderItems', fn (Builder $q) => $q->where('item_type', 'catalogue')),
            default => $query,
        };
    }
}
