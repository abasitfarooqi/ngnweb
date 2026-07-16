<?php

namespace App\Support;

use App\Support\CloudwaysToDigitalOceanDataMigrator;
use Illuminate\Support\Facades\File;
use PDO;

class ProductionForeignKeyCatalog
{
    private const SNAPSHOT_PATH = 'database/schema/production_foreign_keys.json';

    /** @var array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>|null */
    private static ?array $cache = null;

    public static function clearCache(): void
    {
        self::$cache = null;
    }

    /**
     * Canonical FK map from older production (nqfkhvtysa / sync_prod).
     * Priority: live sync_prod → committed JSON snapshot → bootstrap SQL → legacy migrations.
     *
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    public static function definitions(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $defs = self::loadFromBootstrap();
        $defs = self::mergeDefinitions($defs, self::loadFromLegacyMigrations());

        $snapshot = self::loadFromSnapshot();
        if ($snapshot !== null) {
            $defs = self::mergeDefinitions($defs, $snapshot);
        }

        $live = self::loadFromSyncProd();
        if ($live !== null) {
            $defs = self::mergeDefinitions($defs, $live);
        }

        $defs = self::normaliseDefinitionKeys($defs);

        ksort($defs, SORT_STRING);
        self::$cache = $defs;

        return $defs;
    }

    public static function constraintName(string|int $name): string
    {
        return (string) $name;
    }

    public static function snapshotPath(): string
    {
        return base_path(self::SNAPSHOT_PATH);
    }

    /**
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>|null
     */
    private static function loadFromSyncProd(): ?array
    {
        $config = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
        if ($config === null) {
            return null;
        }

        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['database']
            );
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            return self::fetchFromInformationSchema($pdo, $config['database']);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>|null
     */
    private static function loadFromSnapshot(): ?array
    {
        $path = self::snapshotPath();
        if (! is_file($path)) {
            return null;
        }

        $payload = json_decode(File::get($path), true);
        if (! is_array($payload) || ! isset($payload['foreign_keys']) || ! is_array($payload['foreign_keys'])) {
            return null;
        }

        return self::normaliseMap($payload['foreign_keys']);
    }

    /**
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    private static function loadFromBootstrap(): array
    {
        $defs = [];
        $bootstrapDir = database_path('migrations/LatestMigrationFiles/bootstrap');
        if (! is_dir($bootstrapDir)) {
            return $defs;
        }

        foreach (File::glob($bootstrapDir.'/*.php') as $file) {
            $content = File::get($file);
            if (! preg_match('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $content, $block)) {
                continue;
            }

            $body = $block[2];
            if (! preg_match_all(
                '/CONSTRAINT `([^`]+)` FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)` \(`([^`]+)`\)([^,\n]*)/',
                $body,
                $matches,
                PREG_SET_ORDER
            )) {
                continue;
            }

            foreach ($matches as $match) {
                $defs[self::constraintName($match[1])] = self::normaliseRow([
                    'table' => $block[1],
                    'column' => $match[2],
                    'ref_table' => $match[3],
                    'ref_column' => $match[4],
                    'rules' => trim($match[5]),
                ]);
            }
        }

        return $defs;
    }

    /**
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    private static function loadFromLegacyMigrations(): array
    {
        $defs = [];
        $legacyDir = base_path('database___/database copy 2/migrations_0');
        if (! is_dir($legacyDir)) {
            return $defs;
        }

        foreach (File::glob($legacyDir.'/2026_02_12_222707_add_foreign_keys_to_*_table.php') as $file) {
            $content = File::get($file);
            if (! preg_match("/Schema::table\('([^']+)'/", $content, $tableMatch)) {
                continue;
            }

            $table = $tableMatch[1];
            if (! preg_match_all(
                '/->foreign\(\[\s*\'([^\']+)\'\s*\]\)->references\(\[\s*\'([^\']+)\'\s*\]\)->on\(\'([^\']+)\'\)(.*?);/s',
                $content,
                $matches,
                PREG_SET_ORDER
            )) {
                continue;
            }

            foreach ($matches as $match) {
                $name = self::constraintName($table.'_'.$match[1].'_foreign');
                if (isset($defs[$name])) {
                    continue;
                }

                $defs[$name] = self::normaliseRow([
                    'table' => $table,
                    'column' => $match[1],
                    'ref_table' => $match[3],
                    'ref_column' => $match[2],
                    'rules' => trim(preg_replace('/\s+/', ' ', $match[4] ?? '')),
                ]);
            }
        }

        return $defs;
    }

    /**
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    private static function fetchFromInformationSchema(PDO $pdo, string $schema): array
    {
        $stmt = $pdo->prepare(
            'SELECT
                k.CONSTRAINT_NAME,
                k.TABLE_NAME,
                k.COLUMN_NAME,
                k.REFERENCED_TABLE_NAME,
                k.REFERENCED_COLUMN_NAME,
                r.DELETE_RULE,
                r.UPDATE_RULE
             FROM information_schema.KEY_COLUMN_USAGE k
             JOIN information_schema.REFERENTIAL_CONSTRAINTS r
               ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA
              AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
             WHERE k.TABLE_SCHEMA = ?
               AND k.REFERENCED_TABLE_NAME IS NOT NULL
             ORDER BY k.CONSTRAINT_NAME'
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];

        foreach ($rows as $row) {
            $map[self::constraintName($row['CONSTRAINT_NAME'])] = [
                'table' => $row['TABLE_NAME'],
                'column' => $row['COLUMN_NAME'],
                'ref_table' => $row['REFERENCED_TABLE_NAME'],
                'ref_column' => $row['REFERENCED_COLUMN_NAME'],
                'on_delete' => $row['DELETE_RULE'],
                'on_update' => $row['UPDATE_RULE'],
            ];
        }

        return $map;
    }

    /**
     * @param  array<string, array<string, string>>  $map
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    private static function normaliseMap(array $map): array
    {
        $out = [];
        foreach ($map as $name => $row) {
            $out[self::constraintName($name)] = self::normaliseRow(array_merge($row, ['rules' => '']));
        }

        return $out;
    }

    /**
     * @param  array{table:string,column:string,ref_table:string,ref_column:string,rules?:string,on_delete?:string,on_update?:string}  $row
     * @return array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}
     */
    private static function normaliseRow(array $row): array
    {
        $rules = strtoupper($row['rules'] ?? '');

        return [
            'table' => $row['table'],
            'column' => $row['column'],
            'ref_table' => $row['ref_table'],
            'ref_column' => $row['ref_column'],
            'on_delete' => $row['on_delete'] ?? self::ruleValue($rules, 'ON DELETE', 'RESTRICT'),
            'on_update' => $row['on_update'] ?? self::ruleValue($rules, 'ON UPDATE', 'RESTRICT'),
        ];
    }

    /**
     * Merge FK maps; incoming layer replaces any existing row with the same relationship signature.
     *
     * @param  array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>  $base
     * @param  array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>  $incoming
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    private static function mergeDefinitions(array $base, array $incoming): array
    {
        if ($incoming === []) {
            return $base;
        }

        $sigToName = [];
        foreach ($base as $name => $def) {
            $sigToName[self::relationshipSignature($def)] = self::constraintName($name);
        }

        foreach ($incoming as $name => $def) {
            $name = self::constraintName($name);
            $sig = self::relationshipSignature($def);
            if (isset($sigToName[$sig])) {
                unset($base[$sigToName[$sig]]);
            }
            $base[$name] = $def;
            $sigToName[$sig] = $name;
        }

        return $base;
    }

    /**
     * @param  array{table:string,column:string,ref_table:string,ref_column:string}  $def
     */
    private static function relationshipSignature(array $def): string
    {
        return $def['table'].'|'.$def['column'].'|'.$def['ref_table'].'|'.$def['ref_column'];
    }

    /**
     * @param  array<string|int, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>  $defs
     * @return array<string, array{table:string,column:string,ref_table:string,ref_column:string,on_delete:string,on_update:string}>
     */
    private static function normaliseDefinitionKeys(array $defs): array
    {
        $out = [];
        foreach ($defs as $name => $def) {
            $out[self::constraintName($name)] = $def;
        }

        return $out;
    }

    private static function ruleValue(string $rules, string $prefix, string $default): string
    {
        if (preg_match('/'.preg_quote($prefix, '/').'\s+([A-Z ]+)/', $rules, $match)) {
            return trim($match[1]);
        }

        return $default;
    }
}
