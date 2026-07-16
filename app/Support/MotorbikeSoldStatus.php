<?php

namespace App\Support;

use App\Models\ApplicationItem;
use App\Models\FinanceApplication;
use App\Models\MotorbikesSale;
use Illuminate\Database\Eloquent\Builder;

final class MotorbikeSoldStatus
{
    /** Finance contracts with log book transferred — treated as sold. */
    public static function financeSoldMotorbikeIdsSubquery(): Builder
    {
        return ApplicationItem::query()
            ->select('motorbike_id')
            ->whereNotNull('motorbike_id')
            ->whereIn(
                'application_id',
                FinanceApplication::query()
                    ->where('log_book_sent', true)
                    ->select('id')
            );
    }

    public static function isSold(int $motorbikeId): bool
    {
        if (MotorbikesSale::query()
            ->where('motorbike_id', $motorbikeId)
            ->where('is_sold', true)
            ->exists()) {
            return true;
        }

        return ApplicationItem::query()
            ->where('motorbike_id', $motorbikeId)
            ->whereIn('application_id', FinanceApplication::query()
                ->where('log_book_sent', true)
                ->select('id'))
            ->exists();
    }
}
