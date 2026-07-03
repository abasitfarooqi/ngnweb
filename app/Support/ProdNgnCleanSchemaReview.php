<?php

namespace App\Support;

use PDO;
use RuntimeException;

/**
 * Compare production schema vs ngn_clean (canonical). No row data.
 */
class ProdNgnCleanSchemaReview
{
    /**
     * @return array{
     *     production_database:string,
     *     canonical_database:string,
     *     generated_at:string,
     *     summary: array{tables_only_ngn_clean:int, tables_only_production:int, tables_shared:int, tables_with_column_gaps:int},
     *     tables_only_ngn_clean: list<string>,
     *     tables_only_production: list<string>,
     *     tables: array<string, array{
     *         status: string,
     *         columns_ngn_clean: list<string>,
     *         columns_production: list<string>,
     *         missing_on_production: list<string>,
     *         extra_on_production: list<string>,
     *         column_types_ngn_clean: array<string, array<string, mixed>>
     *     }>
     * }
     */
    public static function compare(PDO $prod, string $prodDb, PDO $ngn, string $ngnDb): array
    {
        $prodTables = self::baseTables($prod, $prodDb);
        $ngnTables = self::baseTables($ngn, $ngnDb);

        $onlyNgn = array_values(array_diff($ngnTables, $prodTables));
        $onlyProd = array_values(array_diff($prodTables, $ngnTables));
        $shared = array_values(array_intersect($prodTables, $ngnTables));
        sort($onlyNgn);
        sort($onlyProd);
        sort($shared);

        $ngnColumns = self::columnsByTable($ngn, $ngnDb);
        $prodColumns = self::columnsByTable($prod, $prodDb);
        $ngnColumnMeta = self::columnMetaByTable($ngn, $ngnDb);

        $tables = [];
        $withGaps = 0;

        foreach ($onlyNgn as $table) {
            $tables[$table] = [
                'status' => 'only_ngn_clean',
                'columns_ngn_clean' => $ngnColumns[$table] ?? [],
                'columns_production' => [],
                'missing_on_production' => $ngnColumns[$table] ?? [],
                'extra_on_production' => [],
                'column_types_ngn_clean' => $ngnColumnMeta[$table] ?? [],
            ];
            $withGaps++;
        }

        foreach ($onlyProd as $table) {
            $tables[$table] = [
                'status' => 'only_production',
                'columns_ngn_clean' => [],
                'columns_production' => $prodColumns[$table] ?? [],
                'missing_on_production' => [],
                'extra_on_production' => $prodColumns[$table] ?? [],
                'column_types_ngn_clean' => [],
            ];
        }

        foreach ($shared as $table) {
            $ngnCols = $ngnColumns[$table] ?? [];
            $prodCols = $prodColumns[$table] ?? [];
            $missing = array_values(array_diff($ngnCols, $prodCols));
            $extra = array_values(array_diff($prodCols, $ngnCols));
            $status = 'match';
            if ($missing !== [] || $extra !== []) {
                $status = $missing !== [] ? 'missing_columns_on_production' : 'extra_columns_on_production';
                if ($missing !== [] && $extra !== []) {
                    $status = 'column_mismatch';
                }
                if ($missing !== []) {
                    $withGaps++;
                }
            }

            $tables[$table] = [
                'status' => $status,
                'columns_ngn_clean' => $ngnCols,
                'columns_production' => $prodCols,
                'missing_on_production' => $missing,
                'extra_on_production' => $extra,
                'column_types_ngn_clean' => $ngnColumnMeta[$table] ?? [],
            ];
        }

        return [
            'production_database' => $prodDb,
            'canonical_database' => $ngnDb,
            'generated_at' => now()->toIso8601String(),
            'summary' => [
                'tables_only_ngn_clean' => count($onlyNgn),
                'tables_only_production' => count($onlyProd),
                'tables_shared' => count($shared),
                'tables_with_column_gaps' => $withGaps,
            ],
            'tables_only_ngn_clean' => $onlyNgn,
            'tables_only_production' => $onlyProd,
            'tables' => $tables,
        ];
    }

    /**
     * @param  array<string, mixed>  $report
     * @return list<string> migration file paths
     */
    public static function writeAlignmentMigrations(array $report, PDO $ngn, string $ngnDb, string $folder): array
    {
        self::ensureDir($folder);
        foreach (glob($folder.'/*.php') ?: [] as $old) {
            @unlink($old);
        }

        $files = [];
        $sequence = 1;

        foreach ($report['tables_only_ngn_clean'] as $table) {
            $row = $ngn->query('SHOW CREATE TABLE '.ProdToLocalTableSync::qid($table))->fetch(PDO::FETCH_ASSOC);
            if (! is_array($row) || empty($row['Create Table'])) {
                continue;
            }
            $sql = ProdToLocalTableSync::normalizeCreateTableSql((string) $row['Create Table']);
            $files[] = self::writeCreateMigration($folder, $sequence++, $table, $sql);
        }

        foreach ($report['tables'] as $table => $meta) {
            $status = (string) ($meta['status'] ?? '');
            if ($status === 'only_ngn_clean' || $status === 'only_production') {
                continue;
            }
            $missing = $meta['missing_on_production'] ?? [];
            if ($missing === []) {
                continue;
            }
            $alterLines = [];
            $columnsToAdd = [];
            foreach ($missing as $column) {
                $colMeta = $meta['column_types_ngn_clean'][$column] ?? null;
                if ($colMeta === null) {
                    continue;
                }
                $columnsToAdd[] = $column;
                $alterLines[] = self::addColumnSql($table, $column, $colMeta, $meta['columns_ngn_clean'] ?? []);
            }
            if ($alterLines === []) {
                continue;
            }
            $files[] = self::writeAlterMigration($folder, $sequence++, $table, $alterLines, $columnsToAdd);
        }

        return $files;
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public static function writeMarkdownSummary(array $report, string $path): void
    {
        $lines = [
            '# Production vs ngn_clean schema review',
            '',
            'Generated: '.($report['generated_at'] ?? ''),
            'Production DB: `'.($report['production_database'] ?? '').'`',
            'Canonical DB: `'.($report['canonical_database'] ?? '').'`',
            '',
            '## Summary',
            '',
            '| Metric | Count |',
            '|--------|------:|',
            '| Tables only in ngn_clean | '.($report['summary']['tables_only_ngn_clean'] ?? 0).' |',
            '| Tables only in production | '.($report['summary']['tables_only_production'] ?? 0).' |',
            '| Shared tables | '.($report['summary']['tables_shared'] ?? 0).' |',
            '| Tables production must align | '.($report['summary']['tables_with_column_gaps'] ?? 0).' |',
            '',
        ];

        if (($report['tables_only_ngn_clean'] ?? []) !== []) {
            $lines[] = '## Tables only in ngn_clean (CREATE migration generated)';
            $lines[] = '';
            foreach ($report['tables_only_ngn_clean'] as $table) {
                $lines[] = '- `'.$table.'`';
            }
            $lines[] = '';
        }

        $lines[] = '## Column gaps on shared tables';
        $lines[] = '';

        foreach ($report['tables'] as $table => $meta) {
            if (($meta['status'] ?? '') === 'only_ngn_clean') {
                continue;
            }
            $missing = $meta['missing_on_production'] ?? [];
            if ($missing === []) {
                continue;
            }
            $lines[] = '### `'.$table.'`';
            $lines[] = '';
            $lines[] = 'Missing on production: `'.implode('`, `', $missing).'`';
            $lines[] = '';
        }

        file_put_contents($path, implode("\n", $lines));
    }

    /**
     * @return list<string>
     */
    protected static function baseTables(PDO $pdo, string $schema): array
    {
        $stmt = $pdo->prepare(
            "SELECT TABLE_NAME FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
             ORDER BY TABLE_NAME"
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return is_array($rows) ? array_values($rows) : [];
    }

    /**
     * @return array<string, list<string>>
     */
    protected static function columnsByTable(PDO $pdo, string $schema): array
    {
        $stmt = $pdo->prepare(
            'SELECT TABLE_NAME, COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ?
             ORDER BY TABLE_NAME, ORDINAL_POSITION'
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $row) {
            $t = (string) ($row['TABLE_NAME'] ?? '');
            $c = (string) ($row['COLUMN_NAME'] ?? '');
            if ($t === '' || $c === '') {
                continue;
            }
            $out[$t] ??= [];
            $out[$t][] = $c;
        }

        return $out;
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    protected static function columnMetaByTable(PDO $pdo, string $schema): array
    {
        $stmt = $pdo->prepare(
            'SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, ORDINAL_POSITION
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ?
             ORDER BY TABLE_NAME, ORDINAL_POSITION'
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $row) {
            $t = (string) ($row['TABLE_NAME'] ?? '');
            $c = (string) ($row['COLUMN_NAME'] ?? '');
            if ($t === '' || $c === '') {
                continue;
            }
            $out[$t][$c] = [
                'column_type' => (string) ($row['COLUMN_TYPE'] ?? ''),
                'is_nullable' => (string) ($row['IS_NULLABLE'] ?? 'YES'),
                'column_default' => $row['COLUMN_DEFAULT'],
                'extra' => (string) ($row['EXTRA'] ?? ''),
                'ordinal_position' => (int) ($row['ORDINAL_POSITION'] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $meta
     * @param  list<string>  $orderedColumns
     */
    public static function buildAddColumnStatement(string $table, string $column, array $meta, array $orderedColumns): string
    {
        return self::addColumnSql($table, $column, $meta, $orderedColumns);
    }

    /**
     * @param  array<string, mixed>  $meta
     * @param  list<string>  $orderedColumns
     */
    protected static function addColumnSql(string $table, string $column, array $meta, array $orderedColumns): string
    {
        $qTable = ProdToLocalTableSync::qid($table);
        $qCol = ProdToLocalTableSync::qid($column);
        $type = (string) ($meta['column_type'] ?? 'varchar(255)');
        $nullable = strtoupper((string) ($meta['is_nullable'] ?? 'YES')) === 'YES';
        $extra = strtolower((string) ($meta['extra'] ?? ''));

        $def = $qCol.' '.$type;
        $def .= $nullable ? ' NULL' : ' NOT NULL';

        $default = $meta['column_default'] ?? null;
        if ($default !== null && ! str_contains(strtolower($extra), 'auto_increment')) {
            if (is_numeric($default)) {
                $def .= ' DEFAULT '.$default;
            } else {
                $def .= " DEFAULT '".str_replace("'", "''", (string) $default)."'";
            }
        } elseif ($nullable) {
            $def .= ' DEFAULT NULL';
        }

        if (str_contains($extra, 'auto_increment')) {
            $def .= ' AUTO_INCREMENT';
        }

        $after = '';
        $pos = array_search($column, $orderedColumns, true);
        if ($pos !== false && $pos > 0) {
            $prev = $orderedColumns[$pos - 1];
            $after = ' AFTER '.ProdToLocalTableSync::qid($prev);
        }

        return 'ALTER TABLE '.$qTable.' ADD COLUMN '.$def.$after;
    }

    protected static function writeCreateMigration(string $folder, int $seq, string $table, string $createSql): string
    {
        $safe = preg_replace('/[^a-z0-9_]+/', '_', strtolower($table)) ?: 'table';
        $class = 'create_'.$safe.'_table';
        $file = sprintf('%s/%s_%s.php', rtrim($folder, '/'), now()->format('Y_m_d_His').'_'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT), $class);
        $sql = rtrim($createSql);
        if (! str_ends_with($sql, ';')) {
            $sql .= ';';
        }
        $tableExport = var_export($table, true);

        $body = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Align production: table exists in ngn_clean only. */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable({$tableExport})) {
            return;
        }

        DB::unprepared(<<<'SQL'
{$sql}
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists({$tableExport});
    }
};

PHP;

        file_put_contents($file, $body);

        return $file;
    }

    /**
     * @param  list<string>  $alterLines
     * @param  list<string>  $columns
     */
    protected static function writeAlterMigration(string $folder, int $seq, string $table, array $alterLines, array $columns): string
    {
        $safe = preg_replace('/[^a-z0-9_]+/', '_', strtolower($table)) ?: 'table';
        $prefix = now()->format('Y_m_d_His').'_'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
        $file = rtrim($folder, '/').'/'.$prefix.'_align_'.$safe.'_columns.php';
        $tableExport = var_export($table, true);

        $blocks = '';
        foreach ($columns as $i => $col) {
            $colExport = var_export($col, true);
            $sql = $alterLines[$i] ?? '';
            if ($sql === '') {
                continue;
            }
            $blocks .= "\n        if (! Schema::hasColumn({$tableExport}, {$colExport})) {\n";
            $blocks .= "            DB::statement(".var_export($sql, true).");\n";
            $blocks .= "        }\n";
        }

        $body = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Align production columns to ngn_clean canonical schema. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable({$tableExport})) {
            return;
        }
{$blocks}
    }

    public function down(): void
    {
        // Manual rollback if required.
    }
};

PHP;

        file_put_contents($file, $body);

        return $file;
    }

    protected static function ensureDir(string $folder): void
    {
        if (! is_dir($folder) && ! mkdir($folder, 0755, true) && ! is_dir($folder)) {
            throw new RuntimeException('Cannot create folder: '.$folder);
        }
    }
}
