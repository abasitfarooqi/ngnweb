<?php

namespace App\Support;

use PDO;
use RuntimeException;

class CloudwaysToDigitalOceanDataMigrator
{
    private const CHUNK_SIZE = 500;

    private PDO $source;

    private PDO $target;

    private string $sourceSchema;

    private string $targetSchema;

    /** @var list<array{table:string,status:string,phase?:string,message?:string,rows:int}> */
    private array $log = [];

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $sourceConfig
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $targetConfig
     */
    public function __construct(array $sourceConfig, array $targetConfig)
    {
        $this->sourceSchema = $sourceConfig['database'];
        $this->targetSchema = $targetConfig['database'];
        $this->source = $this->connect($sourceConfig, $this->sourceSchema);
        $this->target = $this->connect($targetConfig, $this->targetSchema);
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     */
    public static function ensureDatabaseExists(array $config): void
    {
        $pdo = self::connectWithoutDatabase($config);
        $db = self::qid($config['database']);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS {$db} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * @return list<string>
     */
    public function listProductionTables(): array
    {
        $stmt = $this->source->prepare(
            'SELECT TABLE_NAME FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ?
             AND TABLE_NAME <> ?
             ORDER BY TABLE_NAME'
        );
        $stmt->execute([$this->sourceSchema, 'BASE TABLE', 'migrations']);
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return is_array($tables) ? array_values($tables) : [];
    }

    /**
     * @param  callable(string, array<string, mixed>): void|null  $onTable
     * @return array{
     *     tables_total:int,
     *     tables_ok:int,
     *     tables_failed:int,
     *     tables_skipped:int,
     *     rows_copied:int,
     *     errors:list<array{table:string,status:string,phase?:string,message?:string,rows:int}>,
     *     report_path:string
     * }
     */
    public function syncAll(?callable $onTable = null): array
    {
        return $this->syncTables($this->listProductionTables(), $onTable);
    }

    /**
     * Overwrite only the given tables (truncate + insert from production).
     *
     * @param  list<string>  $tables
     * @param  callable(string, array<string, mixed>): void|null  $onTable
     * @return array{
     *     tables_total:int,
     *     tables_ok:int,
     *     tables_failed:int,
     *     tables_skipped:int,
     *     rows_copied:int,
     *     errors:list<array{table:string,status:string,phase?:string,message?:string,rows:int}>,
     *     report_path:string
     * }
     */
    public function syncTables(array $tables, ?callable $onTable = null): array
    {
        $this->log = [];
        $tables = array_values(array_unique(array_filter(array_map('strval', $tables))));
        $rowsCopied = 0;
        $ok = 0;
        $failed = 0;
        $skipped = 0;

        $this->target->exec('SET FOREIGN_KEY_CHECKS=0');
        $this->target->exec('SET UNIQUE_CHECKS=0');
        $this->target->exec("SET SESSION sql_mode = ''");

        try {
            foreach ($tables as $table) {
                try {
                    $result = $this->overwriteTable($table);
                    $entry = [
                        'table' => $table,
                        'status' => $result['status'],
                        'phase' => $result['phase'] ?? null,
                        'message' => $result['message'] ?? null,
                        'rows' => $result['rows'],
                    ];
                    $this->log[] = $entry;

                    if ($result['status'] === 'ok') {
                        $ok++;
                        $rowsCopied += $result['rows'];
                    } elseif ($result['status'] === 'skipped') {
                        $skipped++;
                    } else {
                        $failed++;
                    }

                    if ($onTable !== null) {
                        $onTable($table, $entry);
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    $entry = [
                        'table' => $table,
                        'status' => 'failed',
                        'phase' => 'sync',
                        'message' => $e->getMessage(),
                        'rows' => 0,
                    ];
                    $this->log[] = $entry;

                    if ($onTable !== null) {
                        $onTable($table, $entry);
                    }
                }
            }
        } finally {
            $this->target->exec('SET UNIQUE_CHECKS=1');
            $this->target->exec('SET FOREIGN_KEY_CHECKS=1');
        }

        $reportPath = $this->writeReport([
            'finished_at' => date('c'),
            'source' => $this->sourceSchema,
            'target' => $this->targetSchema,
            'tables_total' => count($tables),
            'tables_ok' => $ok,
            'tables_failed' => $failed,
            'tables_skipped' => $skipped,
            'rows_copied' => $rowsCopied,
            'tables' => $this->log,
        ]);

        return [
            'tables_total' => count($tables),
            'tables_ok' => $ok,
            'tables_failed' => $failed,
            'tables_skipped' => $skipped,
            'rows_copied' => $rowsCopied,
            'errors' => array_values(array_filter($this->log, static fn (array $r): bool => ($r['status'] ?? '') !== 'ok')),
            'report_path' => $reportPath,
        ];
    }

    /**
     * @return array{status:string,phase?:string,message?:string,rows:int}
     */
    public function overwriteTable(string $table): array
    {
        if (! $this->tableExists($this->target, $this->targetSchema, $table)) {
            return [
                'status' => 'skipped',
                'phase' => 'missing_target_table',
                'message' => 'Table does not exist on target (run migrations first).',
                'rows' => 0,
            ];
        }

        if (! $this->tableExists($this->source, $this->sourceSchema, $table)) {
            return [
                'status' => 'skipped',
                'phase' => 'missing_source_table',
                'message' => 'Table does not exist on production.',
                'rows' => 0,
            ];
        }

        $q = self::qid($table);
        $sourceColumns = $this->tableColumns($this->source, $this->sourceSchema, $table);
        $targetColumns = $this->tableColumns($this->target, $this->targetSchema, $table);
        $copyColumns = array_values(array_intersect($sourceColumns, $targetColumns));

        if ($copyColumns === []) {
            return [
                'status' => 'failed',
                'phase' => 'no_shared_columns',
                'message' => 'No shared columns between production and target.',
                'rows' => 0,
            ];
        }

        $rowCount = (int) $this->source->query('SELECT COUNT(*) FROM '.$q)->fetchColumn();

        $this->target->exec('TRUNCATE TABLE '.$q);

        if ($rowCount === 0) {
            return ['status' => 'ok', 'rows' => 0];
        }

        $colList = implode(',', array_map([self::class, 'qid'], $copyColumns));
        $placeholders = implode(',', array_fill(0, count($copyColumns), '?'));
        $insert = $this->target->prepare(
            'INSERT INTO '.$q.' ('.$colList.') VALUES ('.$placeholders.')'
        );

        $inserted = 0;
        $offset = 0;

        while ($offset < $rowCount) {
            $select = 'SELECT '.$colList.' FROM '.$q.' LIMIT '.(int) self::CHUNK_SIZE.' OFFSET '.(int) $offset;
            $rows = $this->source->query($select)->fetchAll(PDO::FETCH_NUM);

            foreach ($rows as $row) {
                foreach ($row as $i => $value) {
                    if (is_string($value)) {
                        $row[$i] = self::sanitizeUtf8ForMysql($value);
                    }
                }
                $insert->execute($row);
                $inserted++;
            }

            $offset += self::CHUNK_SIZE;
        }

        return ['status' => 'ok', 'rows' => $inserted];
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    public static function connectionConfigFromLaravel(string $connection): ?array
    {
        $cfg = config("database.connections.{$connection}");
        if (! is_array($cfg)) {
            return null;
        }

        $host = (string) ($cfg['host'] ?? '');
        $database = (string) ($cfg['database'] ?? '');
        $username = (string) ($cfg['username'] ?? '');

        if ($host === '' || $database === '' || $username === '') {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) ($cfg['port'] ?? 3306),
            'database' => $database,
            'username' => $username,
            'password' => (string) ($cfg['password'] ?? ''),
        ];
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $a
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $b
     */
    public static function isSameDatabase(array $a, array $b): bool
    {
        return strtolower($a['host']) === strtolower($b['host'])
            && (int) $a['port'] === (int) $b['port']
            && strtolower($a['database']) === strtolower($b['database']);
    }

    public static function qid(string $identifier): string
    {
        return '`'.str_replace('`', '``', $identifier).'`';
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     */
    private static function connect(array $config, string $database): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            $config['port'],
            $database
        );

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
        ]);
    }

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     */
    private static function connectWithoutDatabase(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;charset=utf8mb4',
            $config['host'],
            $config['port']
        );

        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private function tableExists(PDO $pdo, string $schema, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
        );
        $stmt->execute([$schema, $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @return list<string>
     */
    private function tableColumns(PDO $pdo, string $schema, string $table): array
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

    /**
     * @param  array<string, mixed>  $payload
     */
    private function writeReport(array $payload): string
    {
        $dir = storage_path('logs');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir.'/cloudways-do-data-migrate-'.date('Y-m-d_His').'.json';
        $encoded = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            throw new RuntimeException('Unable to encode sync report JSON.');
        }

        file_put_contents($path, $encoded);

        return $path;
    }

    private static function sanitizeUtf8ForMysql(string $value): string
    {
        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $value);

        return $clean !== false ? $clean : '';
    }
}
