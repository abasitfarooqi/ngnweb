<?php

namespace App\Support;

use PDO;
use PDOException;

class ProdToLocalTableSync
{
    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     */
    public static function connectSource(array $config): PDO
    {
        return self::pdo($config);
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     */
    public static function connectTarget(array $config): PDO
    {
        return self::pdo($config);
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     */
    protected static function pdo(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $config['database']
        );

        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
        ]);

        return $pdo;
    }

    public static function qid(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }

    /**
     * Spatie permission package tables (Laravel default names).
     *
     * @return list<string>
     */
    public static function permissionRelatedTables(): array
    {
        return [
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
        ];
    }

    /**
     * Replace destination table structure and copy all rows from production.
     *
     * @return array{rows:int, inserted:int, schema_only_columns:int}
     */
    public static function syncTable(PDO $src, PDO $dst, string $srcSchema, string $table): array
    {
        return self::syncTableWithSchemaFrom($src, $srcSchema, $src, $srcSchema, $dst, $table);
    }

    /**
     * Create table from canonical schema DB (e.g. ngn_clean), copy row data from production for shared columns only.
     *
     * @return array{rows:int, inserted:int, schema_only_columns:int}
     */
    public static function syncTableWithSchemaFrom(
        PDO $dataSrc,
        string $dataSchema,
        PDO $schemaSrc,
        string $schemaSchema,
        PDO $dst,
        string $table
    ): array {
        $q = self::qid($table);

        $row = $schemaSrc->query('SHOW CREATE TABLE '.$q)->fetch(PDO::FETCH_ASSOC);
        if (! $row || empty($row['Create Table'])) {
            throw new PDOException('SHOW CREATE TABLE failed for '.$table.' in schema '.$schemaSchema);
        }
        $createSql = self::normalizeCreateTableSql($row['Create Table']);

        $hasDataTable = self::tableExists($dataSrc, $dataSchema, $table);
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
        } catch (PDOException $e) {
            // Some engines or temp tables may not support convert; data copy will surface real issues.
        }

        $schemaColumns = self::tableColumns($schemaSrc, $schemaSchema, $table);
        $dataColumns = $hasDataTable ? self::tableColumns($dataSrc, $dataSchema, $table) : [];
        $copyColumns = array_values(array_intersect($schemaColumns, $dataColumns));
        $schemaOnlyColumns = count($schemaColumns) - count($copyColumns);

        if ($count === 0 || $copyColumns === []) {
            $dst->exec('SET UNIQUE_CHECKS=1');
            $dst->exec('SET FOREIGN_KEY_CHECKS=1');

            return ['rows' => $count, 'inserted' => 0, 'schema_only_columns' => max(0, $schemaOnlyColumns)];
        }

        $colList = implode(',', array_map([self::class, 'qid'], $copyColumns));
        $placeholders = implode(',', array_fill(0, count($copyColumns), '?'));
        $insertSql = 'INSERT INTO '.$q.' ('.$colList.') VALUES ('.$placeholders.')';
        $insert = $dst->prepare($insertSql);

        $chunk = 500;
        $inserted = 0;
        $offset = 0;

        $dst->exec("SET SESSION sql_mode = ''");

        while ($offset < $count) {
            $select = 'SELECT '.$colList.' FROM '.$q.' LIMIT '.(int) $chunk.' OFFSET '.(int) $offset;
            $rows = $dataSrc->query($select)->fetchAll(PDO::FETCH_NUM);
            foreach ($rows as $r) {
                foreach ($r as $i => $v) {
                    if (is_string($v)) {
                        $r[$i] = self::sanitizeUtf8ForMysql($v);
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

    /**
     * @return list<string>
     */
    public static function tableColumns(PDO $pdo, string $schema, string $table): array
    {
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
             ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([$schema, $table]);
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return is_array($columns) ? array_values($columns) : [];
    }

    public static function tableExists(PDO $pdo, string $schema, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
        );
        $stmt->execute([$schema, $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Production may run MySQL 8.0.13+ with DEFAULT (CURDATE()) etc.; local MySQL 5.7 or strict
     * modes often reject that syntax. Normalise so structure can be created, then data still copies.
     */
    public static function normalizeCreateTableSql(string $sql): string
    {
        // MySQL 8 functional default for DATE
        $sql = preg_replace(
            '/\bDEFAULT\s*\(\s*CURDATE\s*\(\s*\)\s*\)/i',
            "DEFAULT '2000-01-01'",
            $sql
        ) ?? $sql;

        $sql = preg_replace(
            '/\bDEFAULT\s*\(\s*CURRENT_DATE\s*\(\s*\)\s*\)/i',
            "DEFAULT '2000-01-01'",
            $sql
        ) ?? $sql;

        // Rare: unparenthesised CURDATE() as default
        $sql = preg_replace(
            '/\bDEFAULT\s+CURDATE\s*\(\s*\)/i',
            "DEFAULT '2000-01-01'",
            $sql
        ) ?? $sql;

        // TEXT/BLOB/JSON: non-NULL DEFAULT is invalid on many MySQL builds (prod may use 8.0.13+ features).
        $sql = preg_replace(
            '/(`[^`]+`\s+(?:tinytext|text|mediumtext|longtext|blob|tinyblob|mediumblob|longblob|json)(?:\s+[^,\n]*?)?)\s+DEFAULT\s+(?!NULL\b)([^,\n)]+)/i',
            '$1',
            $sql
        ) ?? $sql;

        // Zero dates / invalid DATE/DATETIME defaults (NO_ZERO_DATE, strict SQL modes).
        $sql = preg_replace(
            "/DEFAULT\\s+'0000-00-00(?:\\s+00:00:00)?'/i",
            'DEFAULT NULL',
            $sql
        ) ?? $sql;

        // VARCHAR/CHAR with DEFAULT current_timestamp() — invalid on string columns; prod SHOW CREATE may expose it.
        $sql = preg_replace(
            '/((?:varchar|char)\([^)]+\)\s+NOT\s+NULL)\s+DEFAULT\s+\(?\s*current_timestamp\s*\(\s*\)\s*\)?/i',
            "$1 DEFAULT ''",
            $sql
        ) ?? $sql;

        $sql = preg_replace(
            '/((?:varchar|char)\([^)]+\))\s+DEFAULT\s+\(?\s*current_timestamp\s*\(\s*\)\s*\)?/i',
            '$1 DEFAULT NULL',
            $sql
        ) ?? $sql;

        return $sql;
    }

    /**
     * Strip invalid UTF-8 (e.g. broken surrogate pairs) so inserts into utf8mb4 columns succeed.
     */
    public static function sanitizeUtf8ForMysql(string $value): string
    {
        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $value);

        return $clean !== false ? $clean : '';
    }
}
