<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/** Shared query for Backpack / Flux vehicle database (motorbike_annual_compliance). */
final class VehicleDatabaseQuery
{
    public static function associationStatusSelectSql(): string
    {
        return <<<'SQL'
            CASE
                WHEN company_vehicles.motorbike_id IS NOT NULL THEN "COMPANY VEHICLE"
                WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN CONCAT("INSTALLMENT TRANSFERRED: ", app_customers.first_name, " ", app_customers.last_name)
                WHEN application_items.motorbike_id IS NOT NULL THEN CONCAT("INSTALLMENT: ", app_customers.first_name, " ", app_customers.last_name)
                WHEN renting_booking_items.motorbike_id IS NOT NULL THEN CONCAT("RENTAL: ", rent_customers.first_name, " ", rent_customers.last_name)
                WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN "SOLD"
                WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN "SALE"
                ELSE "Unassociated"
            END
        SQL;
    }

    public static function associationStatusFilterSql(): string
    {
        return <<<'SQL'
            CASE
                WHEN company_vehicles.motorbike_id IS NOT NULL THEN "COMPANY VEHICLE"
                WHEN application_items.motorbike_id IS NOT NULL AND finance_applications.log_book_sent = true THEN "INSTALLMENT TRANSFERRED"
                WHEN application_items.motorbike_id IS NOT NULL THEN "INSTALLMENT"
                WHEN renting_booking_items.motorbike_id IS NOT NULL THEN "RENTAL"
                WHEN motorbikes_sale.motorbike_id IS NOT NULL AND motorbikes_sale.is_sold = true THEN "SOLD"
                WHEN motorbikes_sale.motorbike_id IS NOT NULL THEN "SALE"
                ELSE "Unassociated"
            END
        SQL;
    }

    /** @return list<string> */
    public static function associationFilterOptions(): array
    {
        return [
            'Unassociated',
            'MOT',
            'SUBSCRIBER',
            'INSTALLMENT TRANSFERRED',
            'INSTALLMENT',
            'RENTAL',
            'SALE',
            'SOLD',
            'COMPANY VEHICLE',
        ];
    }

    public static function applyJoins(Builder $query): Builder
    {
        return $query
            ->leftJoin('application_items', 'motorbike_annual_compliance.motorbike_id', '=', 'application_items.motorbike_id')
            ->leftJoin('finance_applications', 'application_items.application_id', '=', 'finance_applications.id')
            ->leftJoin('customers as app_customers', 'finance_applications.customer_id', '=', 'app_customers.id')
            ->leftJoin('renting_booking_items', 'motorbike_annual_compliance.motorbike_id', '=', 'renting_booking_items.motorbike_id')
            ->leftJoin('renting_bookings', 'renting_booking_items.booking_id', '=', 'renting_bookings.id')
            ->leftJoin('customers as rent_customers', 'renting_bookings.customer_id', '=', 'rent_customers.id')
            ->leftJoin('motorbikes_sale', 'motorbike_annual_compliance.motorbike_id', '=', 'motorbikes_sale.motorbike_id')
            ->leftJoin('company_vehicles', 'motorbike_annual_compliance.motorbike_id', '=', 'company_vehicles.motorbike_id');
    }

    public static function applySelect(Builder $query): Builder
    {
        return $query->select(
            'motorbike_annual_compliance.*',
            'motorbikes_sale.is_sold',
            DB::raw(self::associationStatusSelectSql().' as association_status')
        );
    }

    /** One row per motorbike — latest updated_at (then highest id). */
    public static function applyCurrentOnly(Builder $query): Builder
    {
        return $query->whereIn('motorbike_annual_compliance.id', function ($sub): void {
            $sub->selectRaw('MAX(mac.id)')
                ->from('motorbike_annual_compliance as mac')
                ->whereRaw('mac.updated_at = (
                    SELECT MAX(mac2.updated_at)
                    FROM motorbike_annual_compliance mac2
                    WHERE mac2.motorbike_id = mac.motorbike_id
                )')
                ->groupBy('mac.motorbike_id');
        });
    }
}
