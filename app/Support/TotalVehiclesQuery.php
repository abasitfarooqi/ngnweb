<?php

namespace App\Support;

use App\Models\ApplicationItem;
use App\Models\CompanyVehicle;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\MotorbikesSale;
use App\Models\RentingBookingItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * NGN fleet overview. "All" uses ngn_vehicle flag; category tabs query motorbikes
 * by role (finance, rental, etc.) regardless of vehicle_profile_id or ngn_vehicle.
 */
final class TotalVehiclesQuery
{
    public static function base(): Builder
    {
        return Motorbike::query()->from('motorbikes');
    }

    /** Bikes flagged as internal NGN stock (All tab + dashboard total). */
    public static function internalFleetBase(): Builder
    {
        return self::base()->where('motorbikes.ngn_vehicle', true);
    }

    public static function count(): int
    {
        return (int) self::internalFleetBase()->toBase()->getCountForPagination();
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

    /** Used-sale bikes moved into internal rental stock (not sold, hidden from website). */
    public static function saleRentalMotorbikeIdsSubquery(): Builder
    {
        return MotorbikesSale::query()
            ->select('motorbike_id')
            ->whereNotNull('motorbike_id')
            ->where('is_rented', true)
            ->where(function ($q) {
                $q->where('is_sold', false)->orWhereNull('is_sold');
            });
    }

    /** @deprecated Use saleRentalMotorbikeIdsSubquery() — kept for any legacy callers. */
    public static function availableForSaleMotorbikeIdsSubquery(): Builder
    {
        return self::saleRentalMotorbikeIdsSubquery();
    }

    /**
     * Active payment plans: one row per motorbike (latest application item),
     * contract not cancelled, logbook not transferred, log book not sent.
     */
    public static function activePaymentPlanMotorbikeCount(): int
    {
        $row = DB::selectOne('
            SELECT COUNT(*) AS aggregate FROM (
                SELECT latest.motorbike_id
                FROM (
                    SELECT
                        m.id AS motorbike_id,
                        fa.is_cancelled,
                        fa.logbook_transfer_date,
                        fa.log_book_sent,
                        ROW_NUMBER() OVER (
                            PARTITION BY m.id
                            ORDER BY ai.created_at DESC, ai.id DESC
                        ) AS rn
                    FROM application_items ai
                    INNER JOIN motorbikes m ON m.id = ai.motorbike_id
                    INNER JOIN finance_applications fa ON fa.id = COALESCE(ai.application_id, ai.app_id)
                    WHERE m.deleted_at IS NULL
                ) latest
                WHERE latest.rn = 1
                  AND COALESCE(latest.is_cancelled, 0) = 0
                  AND latest.logbook_transfer_date IS NULL
                  AND COALESCE(latest.log_book_sent, 0) = 0
            ) counted
        ');

        return (int) ($row->aggregate ?? 0);
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

    /** @return array{rental:int,finance_new:int,finance_used:int,company:int,sale_rental:int,total:int} */
    public static function categoryCounts(): array
    {
        $roleCount = fn ($sub) => (int) self::base()
            ->whereIn('motorbikes.id', $sub)
            ->toBase()
            ->getCountForPagination();

        return [
            'rental' => $roleCount(self::activeRentalMotorbikeIdsSubquery()),
            'finance_new' => $roleCount(self::activeFinanceNewMotorbikeIdsSubquery()),
            'finance_used' => $roleCount(self::activeFinanceUsedMotorbikeIdsSubquery()),
            'company' => $roleCount(CompanyVehicle::query()->select('motorbike_id')),
            'sale_rental' => $roleCount(self::saleRentalMotorbikeIdsSubquery()),
            'for_sale' => $roleCount(self::saleRentalMotorbikeIdsSubquery()),
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

        $forSale = self::saleRentalMotorbikeIdsSubquery()
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
                $roles[] = 'Sale rental';
            }
            if ($roles === []) {
                $roles[] = 'Internal fleet';
            }
            $map[$id] = $roles;
        }

        return $map;
    }
}
