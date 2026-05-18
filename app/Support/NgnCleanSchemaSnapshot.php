<?php

namespace App\Support;

use PDO;
use RuntimeException;

/**
 * Portable copy of the ngn_clean MySQL schema (CREATE TABLE + column names).
 * Committed under database/schema/ngn-clean so production does not need an ngn_clean database.
 */
class NgnCleanSchemaSnapshot
{
    public static function defaultPath(): string
    {
        $env = env('NGN_CLEAN_SCHEMA_PATH');

        return $env !== null && $env !== ''
            ? (str_starts_with($env, '/') ? $env : base_path($env))
            : base_path('database/schema/ngn-clean');
    }

    public static function manifestPath(string $root): string
    {
        return rtrim($root, '/').'/manifest.json';
    }

    public static function tablesPath(string $root): string
    {
        return rtrim($root, '/').'/tables';
    }

    /**
     * @return array{
     *     generated_at:string,
     *     source_database:string,
     *     tables: array<string, array{columns:list<string>, create_sql:string}>
     * }
     */
    public static function load(string $root): array
    {
        $manifestFile = self::manifestPath($root);
        if (! is_file($manifestFile)) {
            throw new RuntimeException('Schema snapshot missing: '.$manifestFile.'. Run: php artisan db:export-ngnclean-schema');
        }

        $decoded = json_decode((string) file_get_contents($manifestFile), true);
        if (! is_array($decoded) || ! isset($decoded['tables']) || ! is_array($decoded['tables'])) {
            throw new RuntimeException('Invalid schema manifest: '.$manifestFile);
        }

        $tables = [];
        foreach ($decoded['tables'] as $table => $meta) {
            if (! is_array($meta)) {
                continue;
            }
            $sqlFile = (string) ($meta['sql_file'] ?? '');
            $createSql = (string) ($meta['create_sql'] ?? '');
            if ($createSql === '' && $sqlFile !== '') {
                $path = rtrim($root, '/').'/'.ltrim($sqlFile, '/');
                if (! is_file($path)) {
                    throw new RuntimeException('Missing schema SQL for table '.$table.': '.$path);
                }
                $createSql = trim((string) file_get_contents($path));
            }
            $columns = $meta['columns'] ?? [];
            if (! is_array($columns)) {
                $columns = [];
            }
            $tables[(string) $table] = [
                'columns' => array_values($columns),
                'create_sql' => $createSql,
            ];
        }

        return [
            'generated_at' => (string) ($decoded['generated_at'] ?? ''),
            'source_database' => (string) ($decoded['source_database'] ?? 'ngn_clean'),
            'tables' => $tables,
        ];
    }

    /**
     * @return list<string>
     */
    public static function tableNames(string $root): array
    {
        $manifest = self::load($root);
        $names = array_keys($manifest['tables']);
        sort($names);

        return $names;
    }

    /**
     * Export all base tables from a live ngn_clean database into the snapshot folder.
     *
     * @return array{tables:int, path:string}
     */
    public static function exportFromDatabase(PDO $pdo, string $database, string $root): array
    {
        $tablesDir = self::tablesPath($root);
        if (! is_dir($tablesDir) && ! mkdir($tablesDir, 0755, true) && ! is_dir($tablesDir)) {
            throw new RuntimeException('Cannot create: '.$tablesDir);
        }

        foreach (glob($tablesDir.'/*.sql') ?: [] as $old) {
            @unlink($old);
        }

        $stmt = $pdo->prepare(
            "SELECT TABLE_NAME
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
             ORDER BY TABLE_NAME"
        );
        $stmt->execute([$database]);
        $tableNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $tableNames = is_array($tableNames) ? array_values($tableNames) : [];

        $manifestTables = [];
        foreach ($tableNames as $table) {
            $table = (string) $table;
            $row = $pdo->query('SHOW CREATE TABLE '.ProdToLocalTableSync::qid($table))->fetch(PDO::FETCH_ASSOC);
            if (! is_array($row) || empty($row['Create Table'])) {
                continue;
            }
            $createSql = ProdToLocalTableSync::normalizeCreateTableSql((string) $row['Create Table']);
            $sqlFile = 'tables/'.$table.'.sql';
            file_put_contents(rtrim($root, '/').'/'.$sqlFile, $createSql."\n");

            $manifestTables[$table] = [
                'columns' => ProdToLocalTableSync::tableColumns($pdo, $database, $table),
                'sql_file' => $sqlFile,
            ];
        }

        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'source_database' => $database,
            'tables' => $manifestTables,
        ];

        file_put_contents(
            self::manifestPath($root),
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n"
        );

        return ['tables' => count($manifestTables), 'path' => $root];
    }

    /**
     * @param  array{columns:list<string>, create_sql:string}  $tableSchema
     * @return array{rows:int, inserted:int, schema_only_columns:int}
     */
    public static function applyTableToTarget(
        PDO $dataSrc,
        string $dataSchema,
        PDO $dst,
        string $table,
        array $tableSchema
    ): array {
        $q = ProdToLocalTableSync::qid($table);
        $createSql = trim($tableSchema['create_sql']);
        if ($createSql === '') {
            throw new RuntimeException('Empty CREATE TABLE for '.$table.' in schema snapshot');
        }

        $schemaColumns = $tableSchema['columns'];
        $hasDataTable = ProdToLocalTableSync::tableExists($dataSrc, $dataSchema, $table);
        $count = 0;
        if ($hasDataTable) {
            $count = (int) $dataSrc->query('SELECT COUNT(*) FROM '.$q)->fetchColumn();
        }

        $dst->exec('SET FOREIGN_KEY_CHECKS=0');
        $dst->exec('SET UNIQUE_CHECKS=0');
        $dst->exec('DROP TABLE IF EXISTS '.$q);
        $dst->exec($createSql);

        try {
            $dst->exec('ALTER TABLE '.$q.' CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        } catch (\PDOException $e) {
        }

        $dataColumns = $hasDataTable ? ProdToLocalTableSync::tableColumns($dataSrc, $dataSchema, $table) : [];
        $copyColumns = array_values(array_intersect($schemaColumns, $dataColumns));
        $schemaOnlyColumns = count($schemaColumns) - count($copyColumns);

        if ($count === 0 || $copyColumns === []) {
            $dst->exec('SET UNIQUE_CHECKS=1');
            $dst->exec('SET FOREIGN_KEY_CHECKS=1');

            return ['rows' => $count, 'inserted' => 0, 'schema_only_columns' => max(0, $schemaOnlyColumns)];
        }

        $colList = implode(',', array_map([ProdToLocalTableSync::class, 'qid'], $copyColumns));
        $placeholders = implode(',', array_fill(0, count($copyColumns), '?'));
        $insert = $dst->prepare('INSERT INTO '.$q.' ('.$colList.') VALUES ('.$placeholders.')');

        $dst->exec("SET SESSION sql_mode = ''");
        $inserted = 0;
        $offset = 0;
        $chunk = 500;

        while ($offset < $count) {
            $select = 'SELECT '.$colList.' FROM '.$q.' LIMIT '.(int) $chunk.' OFFSET '.(int) $offset;
            $rows = $dataSrc->query($select)->fetchAll(PDO::FETCH_NUM);
            foreach ($rows as $r) {
                foreach ($r as $i => $v) {
                    if (is_string($v)) {
                        $r[$i] = ProdToLocalTableSync::sanitizeUtf8ForMysql($v);
                    }
                }
                $insert->execute($r);
                $inserted++;
            }
            $offset += $chunk;
        }

        $dst->exec('SET UNIQUE_CHECKS=1');
        $dst->exec('SET FOREIGN_KEY_CHECKS=1');

        return ['rows' => $count, 'inserted' => $inserted, 'schema_only_columns' => max(0, $schemaOnlyColumns)];
    }
}
