<?php

namespace App\Support;

use PDO;
use RuntimeException;

/**
 * Align production schema toward a reference database (local / new migrations).
 * ADD ONLY: CREATE TABLE + ADD COLUMN. Never touches row data.
 */
final class ProductionSchemaAligner
{
    /** @var list<array<string, mixed>> */
    private array $log = [];

    private PDO $production;

    private PDO $reference;

    private string $productionSchema;

    private string $referenceSchema;

    /**
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $productionConfig
     * @param  array{host:string,port:int,database:string,username:string,password:string}  $referenceConfig
     */
    public function __construct(array $productionConfig, array $referenceConfig)
    {
        $this->productionSchema = $productionConfig['database'];
        $this->referenceSchema = $referenceConfig['database'];
        $this->production = ProdToLocalTableSync::connectTarget($productionConfig);
        $this->reference = ProdToLocalTableSync::connectSource($referenceConfig);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildPlan(): array
    {
        $report = ProdNgnCleanSchemaReview::compare(
            $this->production,
            $this->productionSchema,
            $this->reference,
            $this->referenceSchema
        );

        $actions = [];

        foreach ($report['tables_only_ngn_clean'] as $table) {
            $actions[] = [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'planned',
            ];
        }

        foreach ($report['tables'] as $table => $meta) {
            $missing = $meta['missing_on_production'] ?? [];
            if ($missing === [] || ($meta['status'] ?? '') === 'only_production') {
                continue;
            }

            foreach ($missing as $column) {
                $colMeta = $meta['column_types_ngn_clean'][$column] ?? null;
                if ($colMeta === null) {
                    $actions[] = [
                        'type' => 'add_column',
                        'table' => $table,
                        'column' => $column,
                        'status' => 'skipped',
                        'phase' => 'missing_column_meta',
                        'message' => 'Column metadata not found on reference database.',
                    ];

                    continue;
                }

                $rowCount = $this->tableRowCount($this->production, $table);
                $risk = self::assessAddColumnRisk($colMeta, $rowCount);
                if ($risk !== null) {
                    $actions[] = [
                        'type' => 'add_column',
                        'table' => $table,
                        'column' => $column,
                        'status' => 'blocked',
                        'phase' => 'unsafe_not_null',
                        'message' => $risk,
                        'production_rows' => $rowCount,
                    ];

                    continue;
                }

                $sql = ProdNgnCleanSchemaReview::buildAddColumnStatement(
                    $table,
                    $column,
                    $colMeta,
                    $meta['columns_ngn_clean'] ?? []
                );

                $actions[] = [
                    'type' => 'add_column',
                    'table' => $table,
                    'column' => $column,
                    'status' => 'planned',
                    'sql' => $sql,
                    'production_rows' => $rowCount,
                ];
            }
        }

        return [
            'production_database' => $this->productionSchema,
            'reference_database' => $this->referenceSchema,
            'generated_at' => now()->toIso8601String(),
            'summary' => $report['summary'],
            'tables_only_on_reference' => $report['tables_only_ngn_clean'],
            'tables_only_on_production' => $report['tables_only_production'],
            'actions' => $actions,
            'schema_review' => $report,
        ];
    }

    /**
     * @param  callable(string, array<string, mixed>): void|null  $onAction
     * @return array<string, mixed>
     */
    public function apply(bool $dryRun, ?callable $onAction = null): array
    {
        $plan = $this->buildPlan();
        $this->log = [];

        $created = 0;
        $columnsAdded = 0;
        $skipped = 0;
        $blocked = 0;
        $failed = 0;

        if (! $dryRun) {
            $this->production->exec('SET FOREIGN_KEY_CHECKS=0');
        }

        try {
            foreach ($plan['actions'] as $action) {
                $type = (string) ($action['type'] ?? '');
                $status = (string) ($action['status'] ?? '');

                if ($status === 'blocked' || $status === 'skipped') {
                    $this->record($action, $status, (string) ($action['message'] ?? $status));
                    $status === 'blocked' ? $blocked++ : $skipped++;
                    $onAction !== null && $onAction($type, $action);

                    continue;
                }

                if ($type === 'create_table') {
                    $result = $this->applyCreateTable((string) $action['table'], $dryRun);
                    $this->record($result, $result['status'], (string) ($result['message'] ?? ''));
                    match ($result['status']) {
                        'ok', 'dry_run', 'skipped' => $created += $result['status'] === 'skipped' ? 0 : 1,
                        default => $failed++,
                    };
                    $onAction !== null && $onAction($type, $result);

                    continue;
                }

                if ($type === 'add_column') {
                    $result = $this->applyAddColumn($action, $dryRun);
                    $this->record($result, $result['status'], (string) ($result['message'] ?? ''));
                    match ($result['status']) {
                        'ok' => $columnsAdded++,
                        'dry_run' => $columnsAdded++,
                        'skipped' => $skipped++,
                        'blocked' => $blocked++,
                        default => $failed++,
                    };
                    $onAction !== null && $onAction($type, $result);
                }
            }
        } finally {
            if (! $dryRun) {
                $this->production->exec('SET FOREIGN_KEY_CHECKS=1');
            }
        }

        $payload = [
            'mode' => $dryRun ? 'dry_run' : 'execute',
            'production_database' => $this->productionSchema,
            'reference_database' => $this->referenceSchema,
            'finished_at' => now()->toIso8601String(),
            'summary' => [
                'tables_created' => $created,
                'columns_added' => $columnsAdded,
                'skipped' => $skipped,
                'blocked' => $blocked,
                'failed' => $failed,
                'actions_total' => count($plan['actions']),
            ],
            'plan_summary' => $plan['summary'],
            'tables_only_on_reference' => $plan['tables_only_on_reference'],
            'tables_only_on_production' => $plan['tables_only_on_production'],
            'entries' => $this->log,
        ];

        $payload['report_path'] = $this->writeReport($payload);

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private function applyCreateTable(string $table, bool $dryRun): array
    {
        if (ProdToLocalTableSync::tableExists($this->production, $this->productionSchema, $table)) {
            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'skipped',
                'phase' => 'already_exists',
                'message' => 'Table already exists on production.',
            ];
        }

        if (! ProdToLocalTableSync::tableExists($this->reference, $this->referenceSchema, $table)) {
            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'failed',
                'phase' => 'missing_on_reference',
                'message' => 'Table not found on reference database.',
            ];
        }

        $row = $this->reference->query('SHOW CREATE TABLE '.ProdToLocalTableSync::qid($table))->fetch(PDO::FETCH_ASSOC);
        if (! is_array($row) || empty($row['Create Table'])) {
            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'failed',
                'phase' => 'show_create',
                'message' => 'SHOW CREATE TABLE failed on reference.',
            ];
        }

        $sql = ProdToLocalTableSync::normalizeCreateTableSql((string) $row['Create Table']);
        if (! self::isSafeCreateTableSql($sql)) {
            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'failed',
                'phase' => 'unsafe_sql',
                'message' => 'CREATE TABLE SQL failed safety check.',
                'sql' => $sql,
            ];
        }

        if ($dryRun) {
            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'dry_run',
                'message' => 'Would create table on production.',
                'sql' => $sql,
            ];
        }

        try {
            $this->production->exec($sql);

            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'ok',
                'message' => 'Table created on production.',
                'sql' => $sql,
            ];
        } catch (\Throwable $e) {
            return [
                'type' => 'create_table',
                'table' => $table,
                'status' => 'failed',
                'phase' => 'execute',
                'message' => $e->getMessage(),
                'sql' => $sql,
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $action
     * @return array<string, mixed>
     */
    private function applyAddColumn(array $action, bool $dryRun): array
    {
        $table = (string) ($action['table'] ?? '');
        $column = (string) ($action['column'] ?? '');
        $sql = (string) ($action['sql'] ?? '');

        if ($table === '' || $column === '') {
            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'failed',
                'phase' => 'invalid_action',
                'message' => 'Missing table or column.',
            ];
        }

        if (! ProdToLocalTableSync::tableExists($this->production, $this->productionSchema, $table)) {
            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'failed',
                'phase' => 'missing_table',
                'message' => 'Production table does not exist (create table first).',
            ];
        }

        $existing = ProdToLocalTableSync::tableColumns($this->production, $this->productionSchema, $table);
        if (in_array($column, $existing, true)) {
            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'skipped',
                'phase' => 'already_exists',
                'message' => 'Column already exists on production.',
            ];
        }

        if ($sql === '' || ! self::isSafeAddColumnSql($sql)) {
            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'failed',
                'phase' => 'unsafe_sql',
                'message' => 'ADD COLUMN SQL failed safety check.',
                'sql' => $sql,
            ];
        }

        if ($dryRun) {
            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'dry_run',
                'message' => 'Would add column on production.',
                'sql' => $sql,
                'production_rows' => $action['production_rows'] ?? null,
            ];
        }

        try {
            $this->production->exec($sql);

            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'ok',
                'message' => 'Column added on production.',
                'sql' => $sql,
                'production_rows' => $action['production_rows'] ?? null,
            ];
        } catch (\Throwable $e) {
            return [
                'type' => 'add_column',
                'table' => $table,
                'column' => $column,
                'status' => 'failed',
                'phase' => 'execute',
                'message' => $e->getMessage(),
                'sql' => $sql,
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private static function assessAddColumnRisk(array $meta, int $rowCount): ?string
    {
        if ($rowCount === 0) {
            return null;
        }

        $nullable = strtoupper((string) ($meta['is_nullable'] ?? 'YES')) === 'YES';
        $extra = strtolower((string) ($meta['extra'] ?? ''));
        $default = $meta['column_default'] ?? null;

        if (str_contains($extra, 'auto_increment')) {
            return null;
        }

        if (! $nullable && $default === null) {
            return 'NOT NULL column without default on a table with existing rows — skipped to protect production data.';
        }

        return null;
    }

    private function tableRowCount(PDO $pdo, string $table): int
    {
        if (! ProdToLocalTableSync::tableExists($pdo, $this->productionSchema, $table)) {
            return 0;
        }

        try {
            return (int) $pdo->query('SELECT COUNT(*) FROM '.ProdToLocalTableSync::qid($table))->fetchColumn();
        } catch (\Throwable) {
            return 0;
        }
    }

    private static function isSafeCreateTableSql(string $sql): bool
    {
        $upper = strtoupper(trim($sql));

        if (! str_starts_with($upper, 'CREATE TABLE')) {
            return false;
        }

        return ! self::containsForbiddenDdl($sql);
    }

    private static function isSafeAddColumnSql(string $sql): bool
    {
        $upper = strtoupper(trim($sql));

        if (! str_starts_with($upper, 'ALTER TABLE')) {
            return false;
        }

        if (! str_contains($upper, ' ADD COLUMN ') && ! str_contains($upper, ' ADD ')) {
            return false;
        }

        if (preg_match('/\b(DROP|MODIFY|CHANGE|RENAME|TRUNCATE|DELETE|UPDATE|INSERT)\b/i', $sql)) {
            return false;
        }

        return true;
    }

    private static function containsForbiddenDdl(string $sql): bool
    {
        return (bool) preg_match('/\b(DROP|TRUNCATE|DELETE|UPDATE|INSERT|REPLACE)\b/i', $sql);
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function record(array $entry, string $status, string $message): void
    {
        $this->log[] = array_merge($entry, [
            'status' => $status,
            'message' => $message,
            'logged_at' => now()->toIso8601String(),
        ]);
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

        $path = $dir.'/production-schema-align-'.date('Y-m-d_His').'.json';
        $encoded = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($encoded === false) {
            throw new RuntimeException('Unable to encode schema align report JSON.');
        }

        file_put_contents($path, $encoded);

        return $path;
    }
}
