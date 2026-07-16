<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductionForeignKeyRestorer
{
    /**
     * @return array{
     *     added:list<string>,
     *     skipped_existing:list<string>,
     *     skipped_orphans:list<string>,
     *     skipped_missing_table:list<string>,
     *     failed:list<array{name:string,message:string}>
     * }
     */
    public static function restoreMissing(): array
    {
        $result = self::emptyResult();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach (ProductionForeignKeyCatalog::definitions() as $name => $def) {
                if (self::constraintExists($name)) {
                    $result['skipped_existing'][] = $name;

                    continue;
                }

                if (! Schema::hasTable($def['table']) || ! Schema::hasTable($def['ref_table'])) {
                    $result['skipped_missing_table'][] = $name;

                    continue;
                }

                if (self::orphanCount($def) > 0) {
                    $result['skipped_orphans'][] = $name;

                    continue;
                }

                try {
                    DB::statement(self::addConstraintSql($name, $def));
                    $result['added'][] = $name;
                } catch (\Throwable $e) {
                    $result['failed'][] = [
                        'name' => $name,
                        'message' => $e->getMessage(),
                    ];
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return $result;
    }

    /**
     * Drop and recreate FK constraints so names and ON DELETE/UPDATE match production exactly.
     *
     * @return array{
     *     realigned:list<string>,
     *     skipped_ok:list<string>,
     *     skipped_orphans:list<string>,
     *     skipped_missing_table:list<string>,
     *     failed:list<array{name:string,message:string}>
     * }
     */
    public static function realignWithProduction(): array
    {
        $result = [
            'realigned' => [],
            'skipped_ok' => [],
            'skipped_orphans' => [],
            'skipped_missing_table' => [],
            'failed' => [],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            foreach (ProductionForeignKeyCatalog::definitions() as $prodName => $def) {
                if (! Schema::hasTable($def['table']) || ! Schema::hasTable($def['ref_table'])) {
                    $result['skipped_missing_table'][] = $prodName;

                    continue;
                }

                if (self::orphanCount($def) > 0) {
                    $result['skipped_orphans'][] = $prodName;

                    continue;
                }

                $local = self::findLocalBySignature($def);

                if ($local === null) {
                    try {
                        DB::statement(self::addConstraintSql($prodName, $def));
                        $result['realigned'][] = $prodName;
                    } catch (\Throwable $e) {
                        $result['failed'][] = ['name' => $prodName, 'message' => $e->getMessage()];
                    }

                    continue;
                }

                if (
                    self::constraintNamesMatch($local->CONSTRAINT_NAME, $prodName)
                    && self::rulesMatch($def['on_delete'], $def['on_update'], $local->DELETE_RULE, $local->UPDATE_RULE)
                ) {
                    $result['skipped_ok'][] = $prodName;

                    continue;
                }

                try {
                    self::dropForeignKeysOnColumn($def['table'], $def['column']);
                    self::dropConstraintIfExists($def['table'], $prodName);
                    DB::statement(self::addConstraintSql($prodName, $def));
                    $result['realigned'][] = $prodName;
                } catch (\Throwable $e) {
                    $result['failed'][] = ['name' => $prodName, 'message' => $e->getMessage()];
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        return $result;
    }

    /**
     * @return list<array{
     *     constraint:string,
     *     table:string,
     *     column:string,
     *     ref_table:string,
     *     ref_column:string,
     *     prod_on_delete:string,
     *     local_on_delete:string,
     *     prod_on_update:string,
     *     local_on_update:string
     * }>
     */
    public static function ruleMismatchReport(): array
    {
        $report = [];

        foreach (ProductionForeignKeyCatalog::definitions() as $prodName => $def) {
            if (! Schema::hasTable($def['table']) || ! Schema::hasTable($def['ref_table'])) {
                continue;
            }

            $local = self::findLocalBySignature($def);
            if ($local === null) {
                continue;
            }

            if (self::rulesMatch($def['on_delete'], $def['on_update'], $local->DELETE_RULE, $local->UPDATE_RULE)
                && self::constraintNamesMatch($local->CONSTRAINT_NAME, $prodName)) {
                continue;
            }

            $report[] = [
                'constraint' => $prodName,
                'local_constraint' => $local->CONSTRAINT_NAME,
                'table' => $def['table'],
                'column' => $def['column'],
                'ref_table' => $def['ref_table'],
                'ref_column' => $def['ref_column'],
                'prod_on_delete' => $def['on_delete'],
                'local_on_delete' => $local->DELETE_RULE,
                'prod_on_update' => $def['on_update'],
                'local_on_update' => $local->UPDATE_RULE,
            ];
        }

        return $report;
    }

    /**
     * @param  array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}  $def
     */
    public static function orphanCount(array $def): int
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS c
             FROM `'.$def['table'].'` t
             LEFT JOIN `'.$def['ref_table'].'` r ON t.`'.$def['column'].'` = r.`'.$def['ref_column'].'`
             WHERE t.`'.$def['column'].'` IS NOT NULL
               AND r.`'.$def['ref_column'].'` IS NULL'
        );

        return (int) ($row->c ?? 0);
    }

    /**
     * @return list<array{constraint:string,table:string,column:string,ref_table:string,ref_column:string,orphans:int}>
     */
    public static function orphanReport(): array
    {
        $report = [];

        foreach (ProductionForeignKeyCatalog::definitions() as $name => $def) {
            if (! Schema::hasTable($def['table']) || ! Schema::hasTable($def['ref_table'])) {
                continue;
            }

            $orphans = self::orphanCount($def);
            if ($orphans === 0) {
                continue;
            }

            $report[] = [
                'constraint' => $name,
                'table' => $def['table'],
                'column' => $def['column'],
                'ref_table' => $def['ref_table'],
                'ref_column' => $def['ref_column'],
                'orphans' => $orphans,
            ];
        }

        return $report;
    }

    /**
     * @param  array{table:string,column:string,ref_table:string,ref_column:string}  $def
     */
    private static function findLocalBySignature(array $def): ?object
    {
        return DB::selectOne(
            'SELECT k.CONSTRAINT_NAME, r.DELETE_RULE, r.UPDATE_RULE
             FROM information_schema.KEY_COLUMN_USAGE k
             JOIN information_schema.REFERENTIAL_CONSTRAINTS r
               ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
              AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
             WHERE k.TABLE_SCHEMA = DATABASE()
               AND k.TABLE_NAME = ?
               AND k.COLUMN_NAME = ?
               AND k.REFERENCED_TABLE_NAME = ?
               AND k.REFERENCED_COLUMN_NAME = ?
             LIMIT 1',
            [$def['table'], $def['column'], $def['ref_table'], $def['ref_column']]
        );
    }

    private static function dropConstraintIfExists(string $table, string $name): void
    {
        if (! self::constraintExists($name)) {
            return;
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
            $table,
            $name
        ));
    }

    private static function dropForeignKeysOnColumn(string $table, string $column): void
    {
        $rows = DB::select(
            'SELECT k.CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE k
             WHERE k.TABLE_SCHEMA = DATABASE()
               AND k.TABLE_NAME = ?
               AND k.COLUMN_NAME = ?
               AND k.REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        foreach ($rows as $row) {
            self::dropConstraintIfExists($table, ProductionForeignKeyCatalog::constraintName($row->CONSTRAINT_NAME));
        }
    }

    private static function constraintNamesMatch(mixed $left, mixed $right): bool
    {
        return ProductionForeignKeyCatalog::constraintName($left)
            === ProductionForeignKeyCatalog::constraintName($right);
    }

    private static function constraintExists(string $name): bool
    {
        $name = ProductionForeignKeyCatalog::constraintName($name);
        $row = DB::selectOne(
            'SELECT 1 AS ok
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND CONSTRAINT_NAME = ?
               AND CONSTRAINT_TYPE = ?
             LIMIT 1',
            [$name, 'FOREIGN KEY']
        );

        return $row !== null;
    }

    private static function rulesMatch(
        string $prodDelete,
        string $prodUpdate,
        string $localDelete,
        string $localUpdate
    ): bool {
        return strtoupper(trim($prodDelete)) === strtoupper(trim($localDelete))
            && strtoupper(trim($prodUpdate)) === strtoupper(trim($localUpdate));
    }

    /**
     * @param  array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}  $def
     */
    private static function addConstraintSql(string $name, array $def): string
    {
        $name = ProductionForeignKeyCatalog::constraintName($name);
        $deleteRule = self::sqlRule($def['on_delete']);
        $updateRule = self::sqlRule($def['on_update']);

        return sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s',
            $def['table'],
            $name,
            $def['column'],
            $def['ref_table'],
            $def['ref_column'],
            $deleteRule,
            $updateRule
        );
    }

    private static function sqlRule(string $rule): string
    {
        $rule = strtoupper(trim($rule));

        return match ($rule) {
            'CASCADE' => 'CASCADE',
            'SET NULL' => 'SET NULL',
            'NO ACTION' => 'NO ACTION',
            default => 'RESTRICT',
        };
    }

    /** @return array{added:list<string>,skipped_existing:list<string>,skipped_orphans:list<string>,skipped_missing_table:list<string>,failed:list<array{name:string,message:string}>} */
    private static function emptyResult(): array
    {
        return [
            'added' => [],
            'skipped_existing' => [],
            'skipped_orphans' => [],
            'skipped_missing_table' => [],
            'failed' => [],
        ];
    }
}
