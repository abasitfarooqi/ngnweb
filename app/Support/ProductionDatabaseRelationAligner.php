<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductionDatabaseRelationAligner
{
    /**
     * FK constraints only — matches older production ERD. No inserts, updates, or deletes on business rows.
     *
     * @return array{
     *     catalog_source:string,
     *     catalog_count:int,
     *     foreign_keys:array<string, mixed>,
     *     realign:array<string, mixed>,
     *     local_orphans_after:int,
     *     rule_mismatches_after:int,
     *     skipped_orphans:list<string>
     * }
     */
    public static function alignRelationsOnly(): array
    {
        $catalog = ProductionForeignKeyCatalog::definitions();
        $source = self::catalogSourceLabel();

        $foreignKeys = ProductionForeignKeyRestorer::restoreMissing();
        $realign = ProductionForeignKeyRestorer::realignWithProduction();

        if ($foreignKeys['failed'] !== [] || $realign['failed'] !== []) {
            $messages = array_map(
                static fn (array $row): string => $row['name'].': '.$row['message'],
                array_merge($foreignKeys['failed'], $realign['failed'])
            );

            throw new \RuntimeException(
                'Foreign key alignment failed: '.implode(' | ', $messages)
            );
        }

        return [
            'catalog_source' => $source,
            'catalog_count' => count($catalog),
            'foreign_keys' => $foreignKeys,
            'realign' => $realign,
            'local_orphans_after' => count(ProductionForeignKeyRestorer::orphanReport()),
            'rule_mismatches_after' => count(ProductionForeignKeyRestorer::ruleMismatchReport()),
            'skipped_orphans' => array_values(array_unique(array_merge(
                $foreignKeys['skipped_orphans'],
                $realign['skipped_orphans']
            ))),
        ];
    }

    /**
     * Align connected DB relations to older production (sync_prod / JSON snapshot).
     * Insert-only parent repair, then add missing FK constraints. Never deletes business rows.
     *
     * @return array{
     *     catalog_source:string,
     *     catalog_count:int,
     *     repair:array<string, mixed>,
     *     foreign_keys:array<string, mixed>,
     *     local_orphans_after:int
     * }
     */
    public static function align(): array
    {
        $catalog = ProductionForeignKeyCatalog::definitions();
        $source = self::catalogSourceLabel();

        $repair = ProductionRelationRepair::repairMissingParents();
        $foreignKeys = ProductionForeignKeyRestorer::restoreMissing();
        $realign = ProductionForeignKeyRestorer::realignWithProduction();

        if ($foreignKeys['failed'] !== [] || $realign['failed'] !== []) {
            $messages = array_map(
                static fn (array $row): string => $row['name'].': '.$row['message'],
                array_merge($foreignKeys['failed'], $realign['failed'])
            );

            throw new \RuntimeException(
                'Foreign key alignment failed: '.implode(' | ', $messages)
            );
        }

        return [
            'catalog_source' => $source,
            'catalog_count' => count($catalog),
            'repair' => $repair,
            'foreign_keys' => $foreignKeys,
            'realign' => $realign,
            'local_orphans_after' => count(ProductionForeignKeyRestorer::orphanReport()),
            'rule_mismatches_after' => count(ProductionForeignKeyRestorer::ruleMismatchReport()),
        ];
    }

    /**
     * @return array{
     *     connected_database:string,
     *     production_database:?string,
     *     production_fk_count:int,
     *     connected_fk_count:int,
     *     missing_on_connected:list<string>,
     *     extra_on_connected:list<string>,
     *     orphan_constraints:list<array{constraint:string,orphans:int}>,
     *     rule_mismatches:int
     * }
     */
    public static function compareConnectedToProduction(): array
    {
        $connected = DB::connection()->getDatabaseName();
        $connectedFks = array_map(
            static fn (string $name): string => ProductionForeignKeyCatalog::constraintName($name),
            self::fetchLocalFkNames()
        );
        $catalog = ProductionForeignKeyCatalog::definitions();
        $prodNames = array_keys($catalog);

        $missing = array_values(array_diff($prodNames, $connectedFks));
        $extra = array_values(array_diff($connectedFks, $prodNames));

        $orphans = [];
        foreach (ProductionForeignKeyRestorer::orphanReport() as $row) {
            $orphans[] = [
                'constraint' => $row['constraint'],
                'orphans' => $row['orphans'],
            ];
        }

        $prodDb = null;
        $cfg = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
        if ($cfg !== null) {
            $prodDb = $cfg['database'];
        }

        return [
            'connected_database' => $connected,
            'production_database' => $prodDb,
            'production_fk_count' => count($catalog),
            'connected_fk_count' => count($connectedFks),
            'missing_on_connected' => $missing,
            'extra_on_connected' => $extra,
            'orphan_constraints' => $orphans,
            'rule_mismatches' => count(ProductionForeignKeyRestorer::ruleMismatchReport()),
        ];
    }

    /** @return list<string> */
    private static function fetchLocalFkNames(): array
    {
        $rows = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
               AND CONSTRAINT_TYPE = ?
             ORDER BY CONSTRAINT_NAME',
            ['FOREIGN KEY']
        );

        return array_map(static fn ($row): string => $row->CONSTRAINT_NAME, $rows);
    }

    private static function catalogSourceLabel(): string
    {
        if (CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod') !== null) {
            try {
                $cfg = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
                if ($cfg !== null) {
                    return 'sync_prod:'.$cfg['database'];
                }
            } catch (\Throwable) {
                //
            }
        }

        if (is_file(ProductionForeignKeyCatalog::snapshotPath())) {
            return 'snapshot:'.ProductionForeignKeyCatalog::snapshotPath();
        }

        return 'bootstrap';
    }
}
