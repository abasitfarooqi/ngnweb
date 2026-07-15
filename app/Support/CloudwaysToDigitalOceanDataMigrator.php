<?php

namespace App\Support;

use PDO;
use RuntimeException;

class CloudwaysToDigitalOceanDataMigrator
{
    private const CHUNK_SIZE = 500;

    private const SIDECAR_TABLE = 'sync_day_conflict_rows';

    /**
     * Parent-first order for the July-14 insert-only whitelist.
     *
     * @var list<string>
     */
    public const DAY_MERGE_TABLE_ORDER = [
        'customers',
        'motorbikes',
        'finance_applications',
        'otp_verifications',
        'personal_access_tokens',
        'sms_messages',
        'renting_pricings',
        'ngn_mot_notifier',
        'motorbike_annual_compliance',
        'motorbike_registrations',
        'motorbikes_sale',
        'club_members',
        'contract_access',
        'customer_contracts',
        'application_items',
        'booking_invoices',
        'club_member_purchases',
        'club_member_redeem',
        'club_member_spendings',
    ];

    private PDO $source;

    private PDO $target;

    private string $sourceSchema;

    private string $targetSchema;

    /** @var list<array{table:string,status:string,phase?:string,message?:string,rows:int,candidates?:int,inserted?:int,conflicts?:int}> */
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
     * Insert-only merge of rows whose created_at/updated_at fall in [dayStart, dayEndExclusive).
     * Never truncates, updates, or deletes target business rows. Conflicts go to sidecar.
     *
     * @param  list<string>  $tables
     * @param  callable(string, array<string, mixed>): void|null  $onTable
     * @return array{
     *     tables_total:int,
     *     tables_ok:int,
     *     tables_failed:int,
     *     tables_skipped:int,
     *     rows_copied:int,
     *     rows_conflicted:int,
     *     dry_run:bool,
     *     merge_batch:string,
     *     day_start:string,
     *     day_end_exclusive:string,
     *     errors:list<array{table:string,status:string,phase?:string,message?:string,rows:int}>,
     *     report_path:string
     * }
     */
    public function mergeDayWindow(
        array $tables,
        string $dayStart,
        string $dayEndExclusive,
        bool $dryRun = false,
        ?callable $onTable = null
    ): array {
        $this->log = [];
        $tables = self::orderDayMergeTables($tables);
        $mergeBatch = 'day_'.preg_replace('/\D+/', '', substr($dayStart, 0, 10)).'_'.date('YmdHis');
        $rowsCopied = 0;
        $rowsConflicted = 0;
        $ok = 0;
        $failed = 0;
        $skipped = 0;

        if (! $dryRun) {
            $this->ensureSidecarTable();
        }

        $this->target->exec('SET FOREIGN_KEY_CHECKS=0');
        $this->target->exec('SET UNIQUE_CHECKS=0');
        $this->target->exec("SET SESSION sql_mode = ''");

        try {
            foreach ($tables as $table) {
                try {
                    $result = $this->mergeTableDayWindow($table, $dayStart, $dayEndExclusive, $dryRun, $mergeBatch);
                    $entry = [
                        'table' => $table,
                        'status' => $result['status'],
                        'phase' => $result['phase'] ?? null,
                        'message' => $result['message'] ?? null,
                        'rows' => $result['inserted'],
                        'candidates' => $result['candidates'],
                        'inserted' => $result['inserted'],
                        'conflicts' => $result['conflicts'],
                    ];
                    $this->log[] = $entry;

                    if ($result['status'] === 'ok') {
                        $ok++;
                        $rowsCopied += $result['inserted'];
                        $rowsConflicted += $result['conflicts'];
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
                        'phase' => 'merge',
                        'message' => $e->getMessage(),
                        'rows' => 0,
                        'candidates' => 0,
                        'inserted' => 0,
                        'conflicts' => 0,
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
            'mode' => 'day_insert_only',
            'dry_run' => $dryRun,
            'merge_batch' => $mergeBatch,
            'day_start' => $dayStart,
            'day_end_exclusive' => $dayEndExclusive,
            'finished_at' => date('c'),
            'source' => $this->sourceSchema,
            'target' => $this->targetSchema,
            'tables_total' => count($tables),
            'tables_ok' => $ok,
            'tables_failed' => $failed,
            'tables_skipped' => $skipped,
            'rows_copied' => $rowsCopied,
            'rows_conflicted' => $rowsConflicted,
            'tables' => $this->log,
        ]);

        return [
            'tables_total' => count($tables),
            'tables_ok' => $ok,
            'tables_failed' => $failed,
            'tables_skipped' => $skipped,
            'rows_copied' => $rowsCopied,
            'rows_conflicted' => $rowsConflicted,
            'dry_run' => $dryRun,
            'merge_batch' => $mergeBatch,
            'day_start' => $dayStart,
            'day_end_exclusive' => $dayEndExclusive,
            'errors' => array_values(array_filter(
                $this->log,
                static fn (array $r): bool => ! in_array(($r['status'] ?? ''), ['ok', 'skipped'], true)
            )),
            'report_path' => $reportPath,
        ];
    }

    /**
     * Overwrite target rows from 0 through cutoff (exclusive end), leave everything
     * on or after cutoffEndExclusive untouched (no update/delete).
     *
     * Per timestamped table:
     *  - Protect target rows where created_at/updated_at >= cutoffEndExclusive
     *  - DELETE only non-protected historical target rows
     *  - INSERT matching historical rows from source; PK clash with protected → sidecar
     *
     * Tables without created_at/updated_at are skipped (cannot safely date-bound).
     *
     * @param  list<string>  $tables
     * @param  callable(string, array<string, mixed>): void|null  $onTable
     * @return array{
     *     tables_total:int,
     *     tables_ok:int,
     *     tables_failed:int,
     *     tables_skipped:int,
     *     rows_deleted:int,
     *     rows_copied:int,
     *     rows_conflicted:int,
     *     rows_protected:int,
     *     dry_run:bool,
     *     merge_batch:string,
     *     cutoff_end_exclusive:string,
     *     errors:list<array{table:string,status:string,phase?:string,message?:string,rows:int}>,
     *     report_path:string
     * }
     */
    public function overwriteThroughCutoff(
        array $tables,
        string $cutoffEndExclusive,
        bool $dryRun = false,
        ?callable $onTable = null
    ): array {
        $this->log = [];
        $tables = array_values(array_unique(array_filter(array_map('strval', $tables))));
        $mergeBatch = 'through_'.preg_replace('/\D+/', '', substr($cutoffEndExclusive, 0, 10)).'_'.date('YmdHis');
        $rowsDeleted = 0;
        $rowsCopied = 0;
        $rowsConflicted = 0;
        $rowsProtected = 0;
        $ok = 0;
        $failed = 0;
        $skipped = 0;

        if (! $dryRun) {
            $this->ensureSidecarTable();
        }

        $this->target->exec('SET FOREIGN_KEY_CHECKS=0');
        $this->target->exec('SET UNIQUE_CHECKS=0');
        $this->target->exec("SET SESSION sql_mode = ''");

        try {
            foreach ($tables as $table) {
                try {
                    $result = $this->overwriteTableThroughCutoff($table, $cutoffEndExclusive, $dryRun, $mergeBatch);
                    $entry = [
                        'table' => $table,
                        'status' => $result['status'],
                        'phase' => $result['phase'] ?? null,
                        'message' => $result['message'] ?? null,
                        'rows' => $result['inserted'],
                        'deleted' => $result['deleted'],
                        'inserted' => $result['inserted'],
                        'conflicts' => $result['conflicts'],
                        'protected' => $result['protected'],
                        'candidates' => $result['candidates'],
                    ];
                    $this->log[] = $entry;

                    if ($result['status'] === 'ok') {
                        $ok++;
                        $rowsDeleted += $result['deleted'];
                        $rowsCopied += $result['inserted'];
                        $rowsConflicted += $result['conflicts'];
                        $rowsProtected += $result['protected'];
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
                        'phase' => 'through_cutoff',
                        'message' => $e->getMessage(),
                        'rows' => 0,
                        'deleted' => 0,
                        'inserted' => 0,
                        'conflicts' => 0,
                        'protected' => 0,
                        'candidates' => 0,
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
            'mode' => 'through_cutoff_overwrite',
            'dry_run' => $dryRun,
            'merge_batch' => $mergeBatch,
            'cutoff_end_exclusive' => $cutoffEndExclusive,
            'finished_at' => date('c'),
            'source' => $this->sourceSchema,
            'target' => $this->targetSchema,
            'tables_total' => count($tables),
            'tables_ok' => $ok,
            'tables_failed' => $failed,
            'tables_skipped' => $skipped,
            'rows_deleted' => $rowsDeleted,
            'rows_copied' => $rowsCopied,
            'rows_conflicted' => $rowsConflicted,
            'rows_protected' => $rowsProtected,
            'tables' => $this->log,
        ]);

        return [
            'tables_total' => count($tables),
            'tables_ok' => $ok,
            'tables_failed' => $failed,
            'tables_skipped' => $skipped,
            'rows_deleted' => $rowsDeleted,
            'rows_copied' => $rowsCopied,
            'rows_conflicted' => $rowsConflicted,
            'rows_protected' => $rowsProtected,
            'dry_run' => $dryRun,
            'merge_batch' => $mergeBatch,
            'cutoff_end_exclusive' => $cutoffEndExclusive,
            'errors' => array_values(array_filter(
                $this->log,
                static fn (array $r): bool => ! in_array(($r['status'] ?? ''), ['ok', 'skipped'], true)
            )),
            'report_path' => $reportPath,
        ];
    }

    /**
     * @param  list<string>  $tables
     * @return list<string>
     */
    public static function orderDayMergeTables(array $tables): array
    {
        $wanted = array_fill_keys(array_map('strval', $tables), true);
        $ordered = [];

        foreach (self::DAY_MERGE_TABLE_ORDER as $table) {
            if (isset($wanted[$table])) {
                $ordered[] = $table;
                unset($wanted[$table]);
            }
        }

        foreach (array_keys($wanted) as $leftover) {
            $ordered[] = $leftover;
        }

        return $ordered;
    }

    /**
     * @return array{
     *     status:string,
     *     phase?:string,
     *     message?:string,
     *     candidates:int,
     *     deleted:int,
     *     inserted:int,
     *     conflicts:int,
     *     protected:int
     * }
     */
    private function overwriteTableThroughCutoff(
        string $table,
        string $cutoffEndExclusive,
        bool $dryRun,
        string $mergeBatch
    ): array {
        if (! $this->tableExists($this->target, $this->targetSchema, $table)) {
            return [
                'status' => 'skipped',
                'phase' => 'missing_target_table',
                'message' => 'Table does not exist on target.',
                'candidates' => 0,
                'deleted' => 0,
                'inserted' => 0,
                'conflicts' => 0,
                'protected' => 0,
            ];
        }

        if (! $this->tableExists($this->source, $this->sourceSchema, $table)) {
            return [
                'status' => 'skipped',
                'phase' => 'missing_source_table',
                'message' => 'Table does not exist on production.',
                'candidates' => 0,
                'deleted' => 0,
                'inserted' => 0,
                'conflicts' => 0,
                'protected' => 0,
            ];
        }

        $sourceColumns = $this->tableColumns($this->source, $this->sourceSchema, $table);
        $targetColumns = $this->tableColumns($this->target, $this->targetSchema, $table);
        $copyColumns = array_values(array_intersect($sourceColumns, $targetColumns));

        if ($copyColumns === []) {
            return [
                'status' => 'failed',
                'phase' => 'no_shared_columns',
                'message' => 'No shared columns between production and target.',
                'candidates' => 0,
                'deleted' => 0,
                'inserted' => 0,
                'conflicts' => 0,
                'protected' => 0,
            ];
        }

        $timeCols = array_values(array_intersect(['created_at', 'updated_at'], $copyColumns));
        if ($timeCols === []) {
            return [
                'status' => 'skipped',
                'phase' => 'no_timestamp_columns',
                'message' => 'Neither created_at nor updated_at — cannot date-bound overwrite safely.',
                'candidates' => 0,
                'deleted' => 0,
                'inserted' => 0,
                'conflicts' => 0,
                'protected' => 0,
            ];
        }

        [$protectedSql, $protectedParams] = $this->buildProtectedTimestampSql($timeCols, $cutoffEndExclusive);
        [$historicalSql, $historicalParams] = $this->buildHistoricalTimestampSql($timeCols, $cutoffEndExclusive, $protectedSql);

        $q = self::qid($table);

        $protectedCountStmt = $this->target->prepare('SELECT COUNT(*) FROM '.$q.' WHERE '.$protectedSql);
        $protectedCountStmt->execute($protectedParams);
        $protected = (int) $protectedCountStmt->fetchColumn();

        $deleteCountStmt = $this->target->prepare('SELECT COUNT(*) FROM '.$q.' WHERE '.$historicalSql);
        $deleteCountStmt->execute($historicalParams);
        $wouldDelete = (int) $deleteCountStmt->fetchColumn();

        $sourceCountStmt = $this->source->prepare('SELECT COUNT(*) FROM '.$q.' WHERE '.$historicalSql);
        $sourceCountStmt->execute($historicalParams);
        $candidates = (int) $sourceCountStmt->fetchColumn();

        if ($dryRun) {
            $pkColumns = $this->primaryKeyColumns($this->target, $this->targetSchema, $table);
            $conflicts = 0;
            if ($pkColumns !== [] && $candidates > 0) {
                $conflicts = $this->countHistoricalPkConflictsWithProtected(
                    $table,
                    $copyColumns,
                    $pkColumns,
                    $historicalSql,
                    $historicalParams,
                    $protectedSql,
                    $protectedParams
                );
            }

            return [
                'status' => 'ok',
                'candidates' => $candidates,
                'deleted' => $wouldDelete,
                'inserted' => max(0, $candidates - $conflicts),
                'conflicts' => $conflicts,
                'protected' => $protected,
            ];
        }

        $deleted = 0;
        if ($wouldDelete > 0) {
            $deleteStmt = $this->target->prepare('DELETE FROM '.$q.' WHERE '.$historicalSql);
            $deleteStmt->execute($historicalParams);
            $deleted = $deleteStmt->rowCount();
        }

        if ($candidates === 0) {
            return [
                'status' => 'ok',
                'candidates' => 0,
                'deleted' => $deleted,
                'inserted' => 0,
                'conflicts' => 0,
                'protected' => $protected,
            ];
        }

        $pkColumns = $this->primaryKeyColumns($this->target, $this->targetSchema, $table);
        $colList = implode(',', array_map([self::class, 'qid'], $copyColumns));
        $placeholders = implode(',', array_fill(0, count($copyColumns), '?'));
        $insertStmt = $this->target->prepare('INSERT INTO '.$q.' ('.$colList.') VALUES ('.$placeholders.')');

        $inserted = 0;
        $conflicts = 0;
        $usePkCursor = count($pkColumns) === 1 && in_array($pkColumns[0], $copyColumns, true);
        $pkCol = $usePkCursor ? $pkColumns[0] : null;
        $lastPk = null;
        $offset = 0;

        while (true) {
            if ($usePkCursor && $pkCol !== null) {
                $sql = 'SELECT '.$colList.' FROM '.$q.' WHERE '.$historicalSql;
                $params = $historicalParams;
                if ($lastPk !== null) {
                    $sql .= ' AND '.self::qid($pkCol).' > ?';
                    $params[] = $lastPk;
                }
                $sql .= ' ORDER BY '.self::qid($pkCol).' ASC LIMIT '.(int) self::CHUNK_SIZE;
                $stmt = $this->source->prepare($sql);
                $stmt->execute($params);
            } else {
                $sql = 'SELECT '.$colList.' FROM '.$q.' WHERE '.$historicalSql
                    .' LIMIT '.(int) self::CHUNK_SIZE.' OFFSET '.(int) $offset;
                $stmt = $this->source->prepare($sql);
                $stmt->execute($historicalParams);
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                if ($usePkCursor && $pkCol !== null) {
                    $lastPk = $row[$pkCol] ?? $lastPk;
                }

                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $row[$key] = self::sanitizeUtf8ForMysql($value);
                    }
                }

                $sourcePk = $this->formatSourcePk($row, $pkColumns);

                if ($pkColumns === []) {
                    $conflicts++;
                    $this->storeConflictRow($mergeBatch, $table, null, 'no_pk', $row);

                    continue;
                }

                if ($this->targetRowExistsByPk($table, $pkColumns, $row)) {
                    $conflicts++;
                    $this->storeConflictRow($mergeBatch, $table, $sourcePk, 'pk_protected_or_exists', $row);

                    continue;
                }

                try {
                    $values = [];
                    foreach ($copyColumns as $col) {
                        $values[] = $row[$col] ?? null;
                    }
                    $insertStmt->execute($values);
                    $inserted++;
                } catch (\Throwable $e) {
                    $conflicts++;
                    $reason = $this->conflictReasonFromException($e);
                    $this->storeConflictRow($mergeBatch, $table, $sourcePk, $reason, $row);
                }
            }

            if (! $usePkCursor) {
                $offset += count($rows);
            }

            if (count($rows) < self::CHUNK_SIZE) {
                break;
            }
        }

        return [
            'status' => 'ok',
            'candidates' => $candidates,
            'deleted' => $deleted,
            'inserted' => $inserted,
            'conflicts' => $conflicts,
            'protected' => $protected,
        ];
    }

    /**
     * Rows with any timestamp on/after cutoff — never delete or update.
     *
     * @param  list<string>  $timeCols
     * @return array{0:string,1:list<string>}
     */
    private function buildProtectedTimestampSql(array $timeCols, string $cutoffEndExclusive): array
    {
        $parts = [];
        $params = [];
        foreach ($timeCols as $col) {
            $parts[] = '('.self::qid($col).' IS NOT NULL AND '.self::qid($col).' >= ?)';
            $params[] = $cutoffEndExclusive;
        }

        return ['('.implode(' OR ', $parts).')', $params];
    }

    /**
     * Historical rows eligible for overwrite: has a timestamp before cutoff, and not protected.
     *
     * @param  list<string>  $timeCols
     * @return array{0:string,1:list<string>}
     */
    private function buildHistoricalTimestampSql(array $timeCols, string $cutoffEndExclusive, string $protectedSql): array
    {
        $beforeParts = [];
        $params = [];
        foreach ($timeCols as $col) {
            $beforeParts[] = '('.self::qid($col).' IS NOT NULL AND '.self::qid($col).' < ?)';
            $params[] = $cutoffEndExclusive;
        }

        // protected params are needed again for NOT (protected)
        foreach ($timeCols as $col) {
            $params[] = $cutoffEndExclusive;
        }

        $sql = '(('.implode(' OR ', $beforeParts).') AND NOT '.$protectedSql.')';

        return [$sql, $params];
    }

    /**
     * Dry-run helper: count source historical rows whose PK already exists on a protected target row.
     *
     * @param  list<string>  $copyColumns
     * @param  list<string>  $pkColumns
     * @param  list<string>  $historicalParams
     * @param  list<string>  $protectedParams
     */
    private function countHistoricalPkConflictsWithProtected(
        string $table,
        array $copyColumns,
        array $pkColumns,
        string $historicalSql,
        array $historicalParams,
        string $protectedSql,
        array $protectedParams
    ): int {
        if ($pkColumns === [] || array_diff($pkColumns, $copyColumns) !== []) {
            return 0;
        }

        $q = self::qid($table);
        $pkList = implode(',', array_map([self::class, 'qid'], $pkColumns));
        $conflicts = 0;
        $offset = 0;

        while (true) {
            $sql = 'SELECT '.$pkList.' FROM '.$q.' WHERE '.$historicalSql
                .' LIMIT '.(int) self::CHUNK_SIZE.' OFFSET '.(int) $offset;
            $stmt = $this->source->prepare($sql);
            $stmt->execute($historicalParams);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                if (! $this->targetRowExistsByPk($table, $pkColumns, $row)) {
                    continue;
                }

                $parts = [];
                $params = $protectedParams;
                foreach ($pkColumns as $col) {
                    $parts[] = self::qid($col).' = ?';
                    $params[] = $row[$col] ?? null;
                }
                $check = $this->target->prepare(
                    'SELECT 1 FROM '.$q.' WHERE '.$protectedSql.' AND '.implode(' AND ', $parts).' LIMIT 1'
                );
                $check->execute($params);
                if ($check->fetchColumn() !== false) {
                    $conflicts++;
                }
            }

            $offset += count($rows);
            if (count($rows) < self::CHUNK_SIZE) {
                break;
            }
        }

        return $conflicts;
    }

    /**
     * @return array{status:string,phase?:string,message?:string,candidates:int,inserted:int,conflicts:int}
     */
    private function mergeTableDayWindow(
        string $table,
        string $dayStart,
        string $dayEndExclusive,
        bool $dryRun,
        string $mergeBatch
    ): array {
        if (! $this->tableExists($this->target, $this->targetSchema, $table)) {
            return [
                'status' => 'skipped',
                'phase' => 'missing_target_table',
                'message' => 'Table does not exist on target.',
                'candidates' => 0,
                'inserted' => 0,
                'conflicts' => 0,
            ];
        }

        if (! $this->tableExists($this->source, $this->sourceSchema, $table)) {
            return [
                'status' => 'skipped',
                'phase' => 'missing_source_table',
                'message' => 'Table does not exist on production.',
                'candidates' => 0,
                'inserted' => 0,
                'conflicts' => 0,
            ];
        }

        $sourceColumns = $this->tableColumns($this->source, $this->sourceSchema, $table);
        $targetColumns = $this->tableColumns($this->target, $this->targetSchema, $table);
        $copyColumns = array_values(array_intersect($sourceColumns, $targetColumns));

        if ($copyColumns === []) {
            return [
                'status' => 'failed',
                'phase' => 'no_shared_columns',
                'message' => 'No shared columns between production and target.',
                'candidates' => 0,
                'inserted' => 0,
                'conflicts' => 0,
            ];
        }

        $timeCols = array_values(array_intersect(['created_at', 'updated_at'], $copyColumns));
        if ($timeCols === []) {
            return [
                'status' => 'skipped',
                'phase' => 'no_timestamp_columns',
                'message' => 'Neither created_at nor updated_at exists on shared columns.',
                'candidates' => 0,
                'inserted' => 0,
                'conflicts' => 0,
            ];
        }

        $pkColumns = $this->primaryKeyColumns($this->target, $this->targetSchema, $table);
        $q = self::qid($table);
        $colList = implode(',', array_map([self::class, 'qid'], $copyColumns));
        $dayClauses = [];
        $dayParams = [];
        foreach ($timeCols as $col) {
            $dayClauses[] = '('.self::qid($col).' >= ? AND '.self::qid($col).' < ?)';
            $dayParams[] = $dayStart;
            $dayParams[] = $dayEndExclusive;
        }
        $daySql = '('.implode(' OR ', $dayClauses).')';

        $countStmt = $this->source->prepare('SELECT COUNT(*) FROM '.$q.' WHERE '.$daySql);
        $countStmt->execute($dayParams);
        $candidates = (int) $countStmt->fetchColumn();

        if ($candidates === 0) {
            return [
                'status' => 'ok',
                'candidates' => 0,
                'inserted' => 0,
                'conflicts' => 0,
            ];
        }

        $inserted = 0;
        $conflicts = 0;
        $placeholders = implode(',', array_fill(0, count($copyColumns), '?'));
        $insertSql = 'INSERT INTO '.$q.' ('.$colList.') VALUES ('.$placeholders.')';
        $insertStmt = $dryRun ? null : $this->target->prepare($insertSql);

        $usePkCursor = count($pkColumns) === 1 && in_array($pkColumns[0], $copyColumns, true);
        $pkCol = $usePkCursor ? $pkColumns[0] : null;
        $lastPk = null;
        $offset = 0;

        while (true) {
            if ($usePkCursor && $pkCol !== null) {
                $sql = 'SELECT '.$colList.' FROM '.$q.' WHERE '.$daySql;
                $params = $dayParams;
                if ($lastPk !== null) {
                    $sql .= ' AND '.self::qid($pkCol).' > ?';
                    $params[] = $lastPk;
                }
                $sql .= ' ORDER BY '.self::qid($pkCol).' ASC LIMIT '.(int) self::CHUNK_SIZE;
                $stmt = $this->source->prepare($sql);
                $stmt->execute($params);
            } else {
                $sql = 'SELECT '.$colList.' FROM '.$q.' WHERE '.$daySql
                    .' LIMIT '.(int) self::CHUNK_SIZE.' OFFSET '.(int) $offset;
                $stmt = $this->source->prepare($sql);
                $stmt->execute($dayParams);
            }

            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($rows === []) {
                break;
            }

            foreach ($rows as $row) {
                if ($usePkCursor && $pkCol !== null) {
                    $lastPk = $row[$pkCol] ?? $lastPk;
                }

                foreach ($row as $key => $value) {
                    if (is_string($value)) {
                        $row[$key] = self::sanitizeUtf8ForMysql($value);
                    }
                }

                $sourcePk = $this->formatSourcePk($row, $pkColumns);

                if ($pkColumns !== [] && $this->targetRowExistsByPk($table, $pkColumns, $row)) {
                    $conflicts++;
                    if (! $dryRun) {
                        $this->storeConflictRow($mergeBatch, $table, $sourcePk, 'pk_exists', $row);
                    }

                    continue;
                }

                if ($dryRun) {
                    $inserted++;

                    continue;
                }

                if ($pkColumns === []) {
                    $conflicts++;
                    $this->storeConflictRow($mergeBatch, $table, null, 'no_pk', $row);

                    continue;
                }

                try {
                    $values = [];
                    foreach ($copyColumns as $col) {
                        $values[] = $row[$col] ?? null;
                    }
                    $insertStmt->execute($values);
                    $inserted++;
                } catch (\Throwable $e) {
                    $conflicts++;
                    $reason = $this->conflictReasonFromException($e);
                    $this->storeConflictRow($mergeBatch, $table, $sourcePk, $reason, $row);
                }
            }

            if (! $usePkCursor) {
                $offset += count($rows);
            }

            if (count($rows) < self::CHUNK_SIZE) {
                break;
            }
        }

        return [
            'status' => 'ok',
            'candidates' => $candidates,
            'inserted' => $inserted,
            'conflicts' => $conflicts,
        ];
    }

    private function ensureSidecarTable(): void
    {
        $table = self::qid(self::SIDECAR_TABLE);
        $this->target->exec(
            "CREATE TABLE IF NOT EXISTS {$table} (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `merge_batch` VARCHAR(64) NOT NULL,
                `table_name` VARCHAR(191) NOT NULL,
                `source_pk` VARCHAR(191) NULL,
                `reason` VARCHAR(64) NOT NULL,
                `row_json` LONGTEXT NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `sync_day_conflict_batch_table` (`merge_batch`, `table_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }

    /**
     * @param  list<string>  $pkColumns
     * @param  array<string, mixed>  $row
     */
    private function targetRowExistsByPk(string $table, array $pkColumns, array $row): bool
    {
        if ($pkColumns === []) {
            return false;
        }

        $parts = [];
        $params = [];
        foreach ($pkColumns as $col) {
            if (! array_key_exists($col, $row) || $row[$col] === null) {
                return false;
            }
            $parts[] = self::qid($col).' = ?';
            $params[] = $row[$col];
        }

        $sql = 'SELECT 1 FROM '.self::qid($table).' WHERE '.implode(' AND ', $parts).' LIMIT 1';
        $stmt = $this->target->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * @param  list<string>  $pkColumns
     * @param  array<string, mixed>  $row
     */
    private function formatSourcePk(array $row, array $pkColumns): ?string
    {
        if ($pkColumns === []) {
            return null;
        }

        $parts = [];
        foreach ($pkColumns as $col) {
            if (! array_key_exists($col, $row)) {
                return null;
            }
            $parts[] = $col.'='.(string) $row[$col];
        }

        $joined = implode(',', $parts);

        return strlen($joined) > 191 ? substr($joined, 0, 191) : $joined;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function storeConflictRow(
        string $mergeBatch,
        string $table,
        ?string $sourcePk,
        string $reason,
        array $row
    ): void {
        $json = json_encode($row, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            $json = '{"error":"unable to encode row"}';
        }

        $stmt = $this->target->prepare(
            'INSERT INTO '.self::qid(self::SIDECAR_TABLE)
            .' (`merge_batch`, `table_name`, `source_pk`, `reason`, `row_json`, `created_at`)
             VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$mergeBatch, $table, $sourcePk, $reason, $json]);
    }

    private function conflictReasonFromException(\Throwable $e): string
    {
        $message = strtolower($e->getMessage());

        if (str_contains($message, 'duplicate') || str_contains($message, '1062')) {
            return 'unique_conflict';
        }

        if (str_contains($message, 'foreign key') || str_contains($message, '1452')) {
            return 'fk_fail';
        }

        return 'insert_fail';
    }

    /**
     * @return list<string>
     */
    private function primaryKeyColumns(PDO $pdo, string $schema, string $table): array
    {
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
             ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([$schema, $table, 'PRIMARY']);
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return is_array($columns) ? array_values($columns) : [];
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
     * Full mysqldump (+ gzip) of a database. Used as a mandatory pre-flight
     * backup of the connected target before live through-cutoff overwrite.
     *
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $config
     * @return array{path:string,bytes:int,sha256:string}
     */
    public static function dumpDatabaseSqlGzip(array $config, ?string $label = null): array
    {
        $mysqldump = self::resolveBinary('mysqldump');
        $gzip = self::resolveBinary('gzip');

        $dir = storage_path('backups/pre-through-cutoff');
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new RuntimeException('Unable to create backup directory: '.$dir);
        }

        $safeDb = preg_replace('/[^A-Za-z0-9_\-]+/', '_', $config['database']) ?: 'database';
        $safeLabel = $label !== null && $label !== ''
            ? preg_replace('/[^A-Za-z0-9_\-]+/', '_', $label)
            : 'backup';
        $path = $dir.'/'.$safeDb.'_'.$safeLabel.'_'.date('Y-m-d_His').'.sql.gz';

        $cnf = tempnam(sys_get_temp_dir(), 'ngn_mysql_');
        if ($cnf === false) {
            throw new RuntimeException('Unable to create temporary MySQL defaults file.');
        }

        $cnfBody = "[client]\n"
            ."host=".self::iniEscape($config['host'])."\n"
            ."port=".(int) $config['port']."\n"
            ."user=".self::iniEscape($config['username'])."\n"
            ."password=".self::iniEscape($config['password'])."\n";

        try {
            if (file_put_contents($cnf, $cnfBody) === false) {
                throw new RuntimeException('Unable to write temporary MySQL defaults file.');
            }
            chmod($cnf, 0600);

            $cmd = sprintf(
                'bash -c %s',
                escapeshellarg(
                    'set -o pipefail; '
                    .escapeshellarg($mysqldump)
                    .' --defaults-extra-file='.escapeshellarg($cnf)
                    .' --single-transaction --routines --triggers'
                    .' --default-character-set=utf8mb4 --databases '
                    .escapeshellarg($config['database'])
                    .' | '.escapeshellarg($gzip).' -c > '
                    .escapeshellarg($path)
                )
            );

            $output = [];
            $exitCode = 0;
            exec($cmd.' 2>&1', $output, $exitCode);

            if ($exitCode !== 0) {
                @unlink($path);
                throw new RuntimeException(
                    'mysqldump failed (exit '.$exitCode.'): '.implode("\n", $output)
                );
            }

            if (! is_file($path) || filesize($path) < 64) {
                @unlink($path);
                throw new RuntimeException('Backup file missing or empty after mysqldump: '.$path);
            }

            $bytes = (int) filesize($path);
            $hash = hash_file('sha256', $path);
            if ($hash === false) {
                throw new RuntimeException('Unable to hash backup file: '.$path);
            }

            return [
                'path' => $path,
                'bytes' => $bytes,
                'sha256' => $hash,
            ];
        } finally {
            @unlink($cnf);
        }
    }

    private static function resolveBinary(string $name): string
    {
        $output = [];
        $exitCode = 0;
        exec('command -v '.escapeshellarg($name).' 2>/dev/null', $output, $exitCode);
        $path = trim((string) ($output[0] ?? ''));
        if ($exitCode !== 0 || $path === '' || ! is_executable($path)) {
            throw new RuntimeException(
                $name.' not found on PATH. Install it before running live --through (required for SQL backup).'
            );
        }

        return $path;
    }

    private static function iniEscape(string $value): string
    {
        return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
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
