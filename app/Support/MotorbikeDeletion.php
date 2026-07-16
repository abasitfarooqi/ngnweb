<?php

namespace App\Support;

use App\Models\Motorbike;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MotorbikeDeletion
{
    /** @return list<array{count: int, what: string, fix: string}> */
    public static function blockerDetails(int $motorbikeId): array
    {
        $blockers = [];

        foreach (self::hardBlockerDefinitions() as $definition) {
            $count = (int) DB::table($definition['table'])->where('motorbike_id', $motorbikeId)->count();
            if ($count > 0) {
                $blockers[] = [
                    'count' => $count,
                    'what' => $definition['what'],
                    'fix' => $definition['fix'],
                ];
            }
        }

        return $blockers;
    }

    public static function failureReason(Motorbike $motorbike): ?string
    {
        $blockers = self::blockerDetails((int) $motorbike->id);
        if ($blockers === []) {
            return null;
        }

        $lines = [
            'Cannot delete '.self::label($motorbike).'. This motorbike is still linked to other records:',
        ];

        foreach ($blockers as $blocker) {
            $lines[] = sprintf(
                '• %d %s%s — %s',
                $blocker['count'],
                $blocker['what'],
                $blocker['count'] === 1 ? '' : 's',
                $blocker['fix']
            );
        }

        return implode("\n", $lines);
    }

    public static function delete(Motorbike $motorbike): void
    {
        $label = self::label($motorbike);

        $reason = self::failureReason($motorbike);
        if ($reason !== null) {
            throw new RuntimeException($reason);
        }

        try {
            DB::transaction(function () use ($motorbike) {
                self::purgeDependents((int) $motorbike->id);
                $motorbike->delete();
            });
        } catch (QueryException $e) {
            if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
                throw new RuntimeException(
                    self::friendlyConstraintMessage($label, $e),
                    previous: $e
                );
            }

            throw $e;
        }
    }

    /** @return list<array{table: string, what: string, fix: string}> */
    private static function hardBlockerDefinitions(): array
    {
        return [
            [
                'table' => 'renting_booking_items',
                'what' => 'rental booking item',
                'fix' => 'close or reassign it under Rentals → Bookings',
            ],
            [
                'table' => 'pcn_cases',
                'what' => 'PCN case',
                'fix' => 'reassign or delete it under PCN → Cases',
            ],
            [
                'table' => 'application_items',
                'what' => 'finance application item',
                'fix' => 'remove it from Finance → Applications',
            ],
            [
                'table' => 'company_vehicles',
                'what' => 'company vehicle link',
                'fix' => 'unlink it under Vehicles → Company vehicles',
            ],
            [
                'table' => 'claim_motorbikes',
                'what' => 'insurance claim',
                'fix' => 'remove it under Vehicles → Claims',
            ],
            [
                'table' => 'recovered_motorbikes',
                'what' => 'recovered motorbike record',
                'fix' => 'delete it under Vehicles → Recovered motorbikes',
            ],
            [
                'table' => 'vehicle_issuances',
                'what' => 'vehicle issuance',
                'fix' => 'return or delete it under Vehicles → Vehicle issuances',
            ],
        ];
    }

    private static function purgeDependents(int $motorbikeId): void
    {
        $saleIds = DB::table('motorbikes_sale')->where('motorbike_id', $motorbikeId)->pluck('id');
        if ($saleIds->isNotEmpty()) {
            DB::table('motorbike_sale_logs')->whereIn('motorbikes_sale_id', $saleIds)->delete();
            DB::table('motorbikes_sale')->where('motorbike_id', $motorbikeId)->delete();
        }

        $repairIds = DB::table('motorbikes_repair')->where('motorbike_id', $motorbikeId)->pluck('id');
        if ($repairIds->isNotEmpty()) {
            $updateIds = DB::table('motorbike_repair_updates')->whereIn('motorbike_repair_id', $repairIds)->pluck('id');
            if ($updateIds->isNotEmpty()) {
                DB::table('repair_update_service')->whereIn('update_id', $updateIds)->delete();
                DB::table('motorbike_repair_updates')->whereIn('id', $updateIds)->delete();
            }
            DB::table('motorbike_repair_observations')->whereIn('motorbike_repair_id', $repairIds)->delete();
            DB::table('motorbikes_repair')->whereIn('id', $repairIds)->delete();
        }

        DB::table('motorbike_annual_compliance')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('motorbike_registrations')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('motorbike_maintenance_logs')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('motorbikes_cat_b')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('ngn_mot_notifier')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('renting_pricings')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('pcn_email_jobs')->where('motorbike_id', $motorbikeId)->delete();
        DB::table('motorbike_images')->where('motorbike_id', $motorbikeId)->delete();

        DB::table('customer_documents')->where('motorbike_id', $motorbikeId)->update(['motorbike_id' => null]);
        DB::table('ngn_digital_invoices')->where('motorbike_id', $motorbikeId)->update(['motorbike_id' => null]);
    }

    private static function label(Motorbike $motorbike): string
    {
        $reg = trim((string) ($motorbike->reg_no ?? ''));

        return $reg !== '' ? $reg : 'motorbike #'.$motorbike->id;
    }

    private static function friendlyConstraintMessage(string $label, QueryException $e): string
    {
        if (preg_match('/CONSTRAINT `([^`]+)` FOREIGN KEY/', $e->getMessage(), $matches)) {
            $constraint = $matches[1];
            $hint = self::constraintHints()[$constraint] ?? null;
            if ($hint !== null) {
                return "Cannot delete {$label}.\n• Cause: {$hint['cause']}.\n• Fix: {$hint['fix']}.";
            }
        }

        if (preg_match('/`([^`]+)`\.`([^`]+)`/', $e->getMessage(), $matches)) {
            $table = str_replace('_', ' ', $matches[2]);

            return "Cannot delete {$label}.\n• Cause: linked {$table} records still exist.\n• Fix: remove those records first, then try again.";
        }

        return "Cannot delete {$label}.\n• Cause: it is still referenced by other records.\n• Fix: remove the linked records first, then try again.";
    }

    /** @return array<string, array{cause: string, fix: string}> */
    private static function constraintHints(): array
    {
        return [
            'motorbike_annual_compliance_motorbike_id_foreign' => [
                'cause' => 'MOT / tax compliance history exists',
                'fix' => 'this should clear automatically — refresh and retry; contact IT if it persists',
            ],
            'motorbikes_sale_motorbike_id_foreign' => [
                'cause' => 'a used-bike sale listing exists',
                'fix' => 'delete the sale listing under Motorbike sales first',
            ],
            'renting_booking_items_motorbike_id_foreign' => [
                'cause' => 'rental booking history exists',
                'fix' => 'close or reassign rentals under Rentals → Bookings',
            ],
            'pcn_cases_motorbike_id_foreign' => [
                'cause' => 'PCN cases are linked to this bike',
                'fix' => 'reassign or delete them under PCN → Cases',
            ],
        ];
    }
}
