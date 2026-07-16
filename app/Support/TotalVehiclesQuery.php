<?php

namespace App\Support;

use App\Models\ApplicationItem;
use App\Models\CompanyVehicle;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\MotorbikesSale;
use App\Models\RentingBookingItem;
use Illuminate\Database\Eloquent\Builder;

/**
 * Internal (profile 1) vehicles on active rentals, active finance, company list, and/or available used sales.
 */
final class TotalVehiclesQuery
{
    public static function base(): Builder
    {
        return Motorbike::query()
            ->from('motorbikes')
            ->where('motorbikes.vehicle_profile_id', 1)
            ->where(function (Builder $q) {
                $q->whereIn('motorbikes.id', self::activeRentalMotorbikeIdsSubquery())
                    ->orWhereIn('motorbikes.id', self::activeFinanceMotorbikeIdsSubquery())
                    ->orWhereIn('motorbikes.id', CompanyVehicle::query()->select('motorbike_id'))
                    ->orWhereIn('motorbikes.id', self::availableForSaleMotorbikeIdsSubquery());
            });
    }

    public static function count(): int
    {
        return (int) self::base()->toBase()->getCountForPagination();
    }

    public static function activeRentalMotorbikeIdsSubquery(): Builder
    {
        return RentingBookingItem::query()
            ->select('motorbike_id')
            ->where('is_posted', true)
            ->whereNull('end_date')
            ->whereNotNull('motorbike_id')
            ->whereHas('booking', fn ($q) => $q->where('is_posted', true));
    }

    /** Available used listings from /flux-admin/motorbike-sales (not sold). */
    public static function availableForSaleMotorbikeIdsSubquery(): Builder
    {
        return MotorbikesSale::query()
            ->select('motorbike_id')
            ->whereNotNull('motorbike_id')
            ->where(function ($q) {
                $q->where('is_sold', false)->orWhereNull('is_sold');
            });
    }

    /**
     * Active payment plans still in progress (current types in use):
     * posted, not cancelled, log book not sent,
     * and is_new_latest / is_used_latest / is_subscription.
     */
    public static function activeFinanceApplicationsQuery(): Builder
    {
        return FinanceApplication::query()
            ->where('is_posted', true)
            ->where(function ($q) {
                $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
            })
            ->where(function ($q) {
                $q->where('log_book_sent', false)->orWhereNull('log_book_sent');
            })
            ->where(function ($q) {
                $q->where('is_new_latest', true)
                    ->orWhere('is_used_latest', true)
                    ->orWhere('is_subscription', true);
            });
    }

    public static function activeFinanceMotorbikeIdsSubquery(): Builder
    {
        return ApplicationItem::query()
            ->select('motorbike_id')
            ->whereNotNull('motorbike_id')
            ->whereIn('application_id', self::activeFinanceApplicationsQuery()->select('id'));
    }

    public static function activeFinanceNewMotorbikeIdsSubquery(): Builder
    {
        return ApplicationItem::query()
            ->select('motorbike_id')
            ->whereNotNull('motorbike_id')
            ->whereIn(
                'application_id',
                self::activeFinanceApplicationsQuery()->where('is_new_latest', true)->select('id')
            );
    }

    public static function activeFinanceUsedMotorbikeIdsSubquery(): Builder
    {
        return ApplicationItem::query()
            ->select('motorbike_id')
            ->whereNotNull('motorbike_id')
            ->whereIn(
                'application_id',
                self::activeFinanceApplicationsQuery()
                    ->where(function ($q) {
                        $q->where('is_used_latest', true)
                            ->orWhere(function ($inner) {
                                $inner->where('is_subscription', true)
                                    ->where(function ($flags) {
                                        $flags->where('is_new_latest', false)->orWhereNull('is_new_latest');
                                    });
                            });
                    })
                    ->select('id')
            );
    }

    /** @return array{rental:int,finance_new:int,finance_used:int,company:int,for_sale:int,total:int} */
    public static function categoryCounts(): array
    {
        $internalCount = function ($sub) {
            return Motorbike::query()
                ->where('vehicle_profile_id', 1)
                ->whereIn('id', $sub)
                ->count();
        };

        return [
            'rental' => $internalCount(self::activeRentalMotorbikeIdsSubquery()),
            'finance_new' => $internalCount(self::activeFinanceNewMotorbikeIdsSubquery()),
            'finance_used' => $internalCount(self::activeFinanceUsedMotorbikeIdsSubquery()),
            'company' => $internalCount(CompanyVehicle::query()->select('motorbike_id')),
            'for_sale' => $internalCount(self::availableForSaleMotorbikeIdsSubquery()),
            'total' => self::count(),
        ];
    }

    /** Efficient roles map for a page of IDs. */
    public static function rolesMapForIds(iterable $motorbikeIds): array
    {
        $ids = collect($motorbikeIds)->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        $rental = self::activeRentalMotorbikeIdsSubquery()
            ->whereIn('motorbike_id', $ids)
            ->pluck('motorbike_id')
            ->unique()
            ->flip();

        $financeNew = self::activeFinanceNewMotorbikeIdsSubquery()
            ->whereIn('motorbike_id', $ids)
            ->pluck('motorbike_id')
            ->unique()
            ->flip();

        $financeUsed = self::activeFinanceUsedMotorbikeIdsSubquery()
            ->whereIn('motorbike_id', $ids)
            ->pluck('motorbike_id')
            ->unique()
            ->flip();

        $company = CompanyVehicle::query()
            ->whereIn('motorbike_id', $ids)
            ->pluck('motorbike_id')
            ->unique()
            ->flip();

        $forSale = self::availableForSaleMotorbikeIdsSubquery()
            ->whereIn('motorbike_id', $ids)
            ->pluck('motorbike_id')
            ->unique()
            ->flip();

        $map = [];
        foreach ($ids as $id) {
            $roles = [];
            if (isset($rental[$id])) {
                $roles[] = 'Active rental';
            }
            if (isset($financeNew[$id])) {
                $roles[] = 'Finance new';
            }
            if (isset($financeUsed[$id])) {
                $roles[] = 'Finance used';
            }
            if (isset($company[$id])) {
                $roles[] = 'Company';
            }
            if (isset($forSale[$id])) {
                $roles[] = 'For sale';
            }
            $map[$id] = $roles;
        }

        return $map;
    }
}
