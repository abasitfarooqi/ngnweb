<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PDO;
use RuntimeException;

class ProductionRelationRepair
{
    /**
     * Insert missing parent rows referenced by broken FK data.
     * Uses sync_prod when configured; motorbikes fall back to registration metadata.
     *
     * @return array{
     *     repaired_tables:list<string>,
     *     inserted:int,
     *     skipped_no_source:list<string>,
     *     failed:list<array{table:string,message:string}>
     * }
     */
    public static function repairMissingParents(): array
    {
        $result = [
            'repaired_tables' => [],
            'inserted' => 0,
            'skipped_no_source' => [],
            'failed' => [],
        ];

        $orphans = ProductionForeignKeyRestorer::orphanReport();
        if ($orphans === []) {
            return $result;
        }

        $byParent = [];
        foreach ($orphans as $row) {
            $byParent[$row['ref_table']][] = $row;
        }

        $parentOrder = self::parentTableOrder(array_keys($byParent));

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach ($parentOrder as $parentTable) {
                if (! isset($byParent[$parentTable])) {
                    continue;
                }

                $missingIds = self::missingParentIds($byParent[$parentTable]);
                if ($missingIds === []) {
                    continue;
                }

                try {
                    if ($parentTable === 'motorbikes') {
                        $inserted = self::repairMotorbikes($missingIds);
                    } else {
                        $inserted = self::repairGenericParent($parentTable, $missingIds);
                    }

                    if ($inserted > 0) {
                        $result['repaired_tables'][] = $parentTable;
                        $result['inserted'] += $inserted;
                    } elseif ($inserted === 0) {
                        $result['skipped_no_source'][] = $parentTable;
                    }
                } catch (\Throwable $e) {
                    $result['failed'][] = [
                        'table' => $parentTable,
                        'message' => $e->getMessage(),
                    ];
                    Log::warning('Production relation repair failed for '.$parentTable, [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return $result;
    }

    /**
     * @param  list<array{constraint:string,table:string,column:string,ref_table:string,ref_column:string,orphans:int}>  $rows
     * @return list<int|string>
     */
    private static function missingParentIds(array $rows): array
    {
        $ids = [];

        foreach ($rows as $row) {
            $values = DB::table($row['table'])
                ->whereNotNull($row['column'])
                ->whereNotIn($row['column'], function ($query) use ($row) {
                    $query->select($row['ref_column'])->from($row['ref_table']);
                })
                ->distinct()
                ->pluck($row['column']);

            foreach ($values as $value) {
                $ids[(string) $value] = $value;
            }
        }

        return array_values($ids);
    }

    /**
     * @param  list<int|string>  $missingIds
     */
    private static function repairGenericParent(string $parentTable, array $missingIds): int
    {
        $source = self::sourcePdo();
        if ($source === null) {
            return 0;
        }

        $targetColumns = Schema::getColumnListing($parentTable);
        $sourceColumns = self::tableColumns($source, $parentTable);
        $copyColumns = array_values(array_intersect($targetColumns, $sourceColumns));

        if ($copyColumns === []) {
            throw new RuntimeException('No shared columns for '.$parentTable);
        }

        $inserted = 0;
        foreach (array_chunk($missingIds, 200) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $pk = self::primaryKeyColumn($parentTable) ?? 'id';
            $selectCols = implode(',', array_map(static fn (string $c): string => '`'.$c.'`', $copyColumns));
            $stmt = $source->prepare(
                'SELECT '.$selectCols.' FROM `'.$parentTable.'` WHERE `'.$pk.'` IN ('.$placeholders.')'
            );
            $stmt->execute(array_values($chunk));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                if (DB::table($parentTable)->where($pk, $row[$pk])->exists()) {
                    continue;
                }

                DB::table($parentTable)->insert($row);
                $inserted++;
            }

            $foundIds = array_map(static fn (array $row): int => (int) $row[$pk], $rows);
            $stillMissing = array_values(array_diff(array_map('intval', $chunk), $foundIds));
            $inserted += self::insertStubParents($parentTable, $stillMissing);
        }

        return $inserted;
    }

    /**
     * @param  list<int>  $missingIds
     */
    private static function insertStubParents(string $parentTable, array $missingIds): int
    {
        if ($missingIds === []) {
            return 0;
        }

        $inserted = 0;

        foreach ($missingIds as $id) {
            if (DB::table($parentTable)->where('id', $id)->exists()) {
                continue;
            }

            $payload = match ($parentTable) {
                'system_countries' => [
                    'id' => $id,
                    'name' => 'Unknown',
                    'name_official' => 'Unknown',
                    'cca2' => 'XX',
                    'cca3' => 'XXX',
                    'flag' => '',
                    'latitude' => 0,
                    'longitude' => 0,
                    'currencies' => '{}',
                ],
                default => null,
            };

            if ($payload === null) {
                continue;
            }

            DB::table($parentTable)->insert($payload);
            $inserted++;
        }

        return $inserted;
    }

    /**
     * @param  list<int|string>  $missingIds
     */
    private static function repairMotorbikes(array $missingIds): int
    {
        $inserted = 0;
        $source = self::sourcePdo();

        foreach (array_chunk($missingIds, 100) as $chunk) {
            $rows = [];

            if ($source !== null) {
                $placeholders = implode(',', array_fill(0, count($chunk), '?'));
                $stmt = $source->prepare('SELECT * FROM `motorbikes` WHERE `id` IN ('.$placeholders.')');
                $stmt->execute(array_values($chunk));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $foundIds = array_map(static fn (array $row): int => (int) $row['id'], $rows);
            $stillMissing = array_values(array_diff(array_map('intval', $chunk), $foundIds));

            foreach ($rows as $row) {
                if (DB::table('motorbikes')->where('id', $row['id'])->exists()) {
                    continue;
                }

                DB::table('motorbikes')->insert(self::filterExistingColumns('motorbikes', $row));
                $inserted++;
            }

            foreach ($stillMissing as $id) {
                if (DB::table('motorbikes')->where('id', $id)->exists()) {
                    continue;
                }

                $reg = DB::table('motorbike_registrations')
                    ->where('motorbike_id', $id)
                    ->orderByDesc('id')
                    ->value('registration_number');

                $reg = $reg ? strtoupper(str_replace(' ', '', (string) $reg)) : 'UNKNOWN-'.$id;
                $vin = 'repair-'.$id.'-'.substr(sha1($reg), 0, 12);

                while (DB::table('motorbikes')->where('vin_number', $vin)->exists()) {
                    $vin = 'repair-'.$id.'-'.substr(sha1($vin.microtime(true)), 0, 12);
                }

                DB::table('motorbikes')->insert([
                    'id' => $id,
                    'vehicle_profile_id' => 1,
                    'is_ebike' => 0,
                    'vin_number' => $vin,
                    'make' => 'UNKNOWN',
                    'model' => 'UNKNOWN',
                    'year' => (int) date('Y'),
                    'engine' => 'UNKNOWN',
                    'color' => 'UNKNOWN',
                    'reg_no' => $reg,
                    'marked_for_export' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        }

        return $inserted;
    }

    /**
     * @param  list<string>  $tables
     * @return list<string>
     */
    private static function parentTableOrder(array $tables): array
    {
        $deps = [];
        foreach (ProductionForeignKeyCatalog::definitions() as $def) {
            if (! in_array($def['table'], $tables, true)) {
                continue;
            }
            $deps[$def['table']][] = $def['ref_table'];
        }

        $ordered = [];
        $visited = [];

        $visit = static function (string $table) use (&$visit, &$ordered, &$visited, $deps, $tables): void {
            if (isset($visited[$table])) {
                return;
            }
            $visited[$table] = true;
            foreach ($deps[$table] ?? [] as $dep) {
                if (in_array($dep, $tables, true)) {
                    $visit($dep);
                }
            }
            $ordered[] = $table;
        };

        sort($tables);
        foreach ($tables as $table) {
            $visit($table);
        }

        return array_values(array_unique($ordered));
    }

    private static function sourcePdo(): ?PDO
    {
        $config = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
        if ($config === null) {
            return null;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        );

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    /** @return list<string> */
    private static function tableColumns(PDO $pdo, string $table): array
    {
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
             ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([$table]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return is_array($rows) ? array_values($rows) : [];
    }

    private static function primaryKeyColumn(string $table): ?string
    {
        $row = DB::selectOne(
            'SELECT COLUMN_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND CONSTRAINT_NAME = "PRIMARY"
             LIMIT 1',
            [$table]
        );

        return $row->COLUMN_NAME ?? null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private static function filterExistingColumns(string $table, array $row): array
    {
        $columns = array_flip(Schema::getColumnListing($table));
        $filtered = [];
        foreach ($row as $key => $value) {
            if (isset($columns[$key])) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}
