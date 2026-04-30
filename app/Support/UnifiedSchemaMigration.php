<?php

namespace App\Support;

use PDO;
use RuntimeException;

class UnifiedSchemaMigration
{
    /**
     * @return array{
     *     migration_files:list<string>,
     *     report_file:string,
     *     totals:array{production_tables:int,target_tables:int,unified_tables:int,target_only_tables:int}
     * }
     */
    public static function generate(PDO $src, string $srcSchema, PDO $dst, string $dstSchema, string $folder): array
    {
        self::ensureDirectory($folder);
        self::clearGeneratedFolder($folder);

        $prodTables = self::tableNames($src, $srcSchema);
        $targetTables = self::tableNames($dst, $dstSchema);

        $targetOnly = array_values(array_diff($targetTables, $prodTables));
        sort($targetOnly);

        $unifiedSql = [];
        $origins = [];
        foreach ($prodTables as $table) {
            $unifiedSql[$table] = self::createTableSql($src, $table, true);
            $origins[$table] = 'production';
        }
        foreach ($targetOnly as $table) {
            $unifiedSql[$table] = self::createTableSql($dst, $table, false);
            $origins[$table] = 'target_only';
        }
        $allTables = array_keys($unifiedSql);
        $dependencies = self::tableDependencies($src, $srcSchema, $allTables);
        $dstDependencies = self::tableDependencies($dst, $dstSchema, $allTables);
        foreach ($dstDependencies as $table => $deps) {
            $dependencies[$table] = array_values(array_unique(array_merge($dependencies[$table] ?? [], $deps)));
            sort($dependencies[$table]);
        }
        $orderedTables = self::sortTablesByDependencies(array_keys($unifiedSql), $dependencies);
        $orderedSql = [];
        foreach ($orderedTables as $table) {
            $orderedSql[$table] = $unifiedSql[$table];
        }

        $migrationFiles = self::writePerTableMigrations($folder, $orderedSql, $origins);
        $reportFile = rtrim($folder, '/').'/unified_schema_report.json';

        file_put_contents(
            $reportFile,
            self::reportJson($src, $srcSchema, $dst, $dstSchema, $prodTables, $targetTables, $targetOnly, array_keys($unifiedSql), $migrationFiles, $origins)
        );

        return [
            'migration_files' => $migrationFiles,
            'report_file' => $reportFile,
            'totals' => [
                'production_tables' => count($prodTables),
                'target_tables' => count($targetTables),
                'unified_tables' => count($unifiedSql),
                'target_only_tables' => count($targetOnly),
            ],
        ];
    }

    /**
     * @return list<string>
     */
    protected static function tableNames(PDO $pdo, string $schema): array
    {
        $stmt = $pdo->prepare(
            "SELECT TABLE_NAME
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
             ORDER BY TABLE_NAME"
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return is_array($rows) ? array_values($rows) : [];
    }

    protected static function createTableSql(PDO $pdo, string $table, bool $normalise): string
    {
        $row = $pdo->query('SHOW CREATE TABLE '.ProdToLocalTableSync::qid($table))->fetch(PDO::FETCH_ASSOC);
        if (! is_array($row) || ! isset($row['Create Table'])) {
            throw new RuntimeException('Unable to read CREATE TABLE for '.$table);
        }

        $sql = (string) $row['Create Table'];

        return $normalise ? ProdToLocalTableSync::normalizeCreateTableSql($sql) : $sql;
    }

    /**
     * @param  array<string,string>  $tableSqlByName
     * @param  array<string,string>  $originsByTable
     * @return list<string>
     */
    protected static function writePerTableMigrations(string $folder, array $tableSqlByName, array $originsByTable): array
    {
        $files = [];
        $sequence = 2;
        foreach ($tableSqlByName as $table => $sql) {
            $safe = self::safeTableForFilename($table);
            $filename = sprintf(
                '%s/2024_01_01_%06d_create_%s_table.php',
                rtrim($folder, '/'),
                $sequence,
                $safe
            );

            $sanitizedSql = self::stripForeignKeyConstraints($sql);
            file_put_contents($filename, self::singleTableMigrationTemplate($table, $sanitizedSql, $originsByTable[$table] ?? 'unknown'));
            $files[] = $filename;
            $sequence++;
        }

        return $files;
    }

    protected static function singleTableMigrationTemplate(string $table, string $createSql, string $origin): string
    {
        $tableExport = var_export($table, true);
        $comment = $origin === 'production'
            ? 'Schema source: production database'
            : 'Schema source: connected target-only table';
        $sql = rtrim($createSql);
        if (! str_ends_with($sql, ';')) {
            $sql .= ';';
        }

        $template = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // __COMMENT__
        DB::statement('DROP TABLE IF EXISTS '.$this->qid(__TABLE__));
        DB::unprepared(<<<'SQL'
__CREATE_SQL__
SQL
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(__TABLE__);
    }

    protected function qid(string $name): string
    {
        return '`'.str_replace('`', '``', $name).'`';
    }
};

PHP;

        return str_replace(
            ['__COMMENT__', '__TABLE__', '__CREATE_SQL__'],
            [$comment, $tableExport, $sql],
            $template
        );
    }

    /**
     * @param  list<string>  $prodTables
     * @param  list<string>  $targetTables
     * @param  list<string>  $targetOnly
     * @param  list<string>  $unifiedTables
     * @param  list<string>  $migrationFiles
     * @param  array<string,string>  $originsByTable
     */
    protected static function reportJson(
        PDO $src,
        string $srcSchema,
        PDO $dst,
        string $dstSchema,
        array $prodTables,
        array $targetTables,
        array $targetOnly,
        array $unifiedTables,
        array $migrationFiles,
        array $originsByTable
    ): string {
        $report = [
            'production_schema' => $srcSchema,
            'target_schema' => $dstSchema,
            'generated_at' => date(DATE_ATOM),
            'tables' => [
                'production_count' => count($prodTables),
                'target_count' => count($targetTables),
                'target_only' => $targetOnly,
                'unified_count' => count($unifiedTables),
            ],
            'table_origins' => $originsByTable,
            'migration_files' => $migrationFiles,
            'primary_keys' => [
                'production' => self::primaryKeys($src, $srcSchema),
                'target' => self::primaryKeys($dst, $dstSchema),
            ],
            'columns_exact_case' => [
                'production' => self::columnsByTable($src, $srcSchema),
                'target' => self::columnsByTable($dst, $dstSchema),
            ],
        ];

        return json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '{}';
    }

    /**
     * @return array<string,list<string>>
     */
    protected static function primaryKeys(PDO $pdo, string $schema): array
    {
        $stmt = $pdo->prepare(
            "SELECT TABLE_NAME, COLUMN_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND CONSTRAINT_NAME = 'PRIMARY'
             ORDER BY TABLE_NAME, ORDINAL_POSITION"
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $table = (string) ($row['TABLE_NAME'] ?? '');
                $column = (string) ($row['COLUMN_NAME'] ?? '');
                if ($table === '' || $column === '') {
                    continue;
                }
                $result[$table] ??= [];
                $result[$table][] = $column;
            }
        }

        return $result;
    }

    /**
     * @return array<string,list<string>>
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

        $result = [];
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $table = (string) ($row['TABLE_NAME'] ?? '');
                $column = (string) ($row['COLUMN_NAME'] ?? '');
                if ($table === '' || $column === '') {
                    continue;
                }
                $result[$table] ??= [];
                $result[$table][] = $column;
            }
        }

        return $result;
    }

    protected static function safeTableForFilename(string $table): string
    {
        $safe = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', $table) ?? $table);
        $safe = trim($safe, '_');

        return $safe === '' ? 'table' : $safe;
    }

    /**
     * @param  list<string>  $tables
     * @return array<string,list<string>>
     */
    protected static function tableDependencies(PDO $pdo, string $schema, array $tables): array
    {
        $tableSet = array_fill_keys($tables, true);
        $deps = [];
        foreach ($tables as $table) {
            $deps[$table] = [];
        }

        $stmt = $pdo->prepare(
            "SELECT TABLE_NAME, REFERENCED_TABLE_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL"
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $table = (string) ($row['TABLE_NAME'] ?? '');
                $ref = (string) ($row['REFERENCED_TABLE_NAME'] ?? '');
                if (! isset($tableSet[$table]) || ! isset($tableSet[$ref])) {
                    continue;
                }
                $deps[$table][] = $ref;
            }
        }

        foreach ($deps as $table => $list) {
            $list = array_values(array_unique($list));
            sort($list);
            $deps[$table] = $list;
        }

        return $deps;
    }

    /**
     * @param  list<string>  $tables
     * @param  array<string,list<string>>  $dependencies
     * @return list<string>
     */
    protected static function sortTablesByDependencies(array $tables, array $dependencies): array
    {
        $tables = array_values(array_unique($tables));
        sort($tables);
        $tableSet = array_fill_keys($tables, true);

        $indegree = [];
        $adj = [];
        foreach ($tables as $t) {
            $indegree[$t] = 0;
            $adj[$t] = [];
        }

        foreach ($dependencies as $table => $deps) {
            if (! isset($tableSet[$table])) {
                continue;
            }
            foreach ($deps as $dep) {
                if (! isset($tableSet[$dep])) {
                    continue;
                }
                $indegree[$table]++;
                $adj[$dep][] = $table;
            }
        }

        $queue = [];
        foreach ($indegree as $table => $deg) {
            if ($deg === 0) {
                $queue[] = $table;
            }
        }
        sort($queue);

        $ordered = [];
        while ($queue !== []) {
            $current = array_shift($queue);
            $ordered[] = $current;

            $next = $adj[$current] ?? [];
            sort($next);
            foreach ($next as $child) {
                $indegree[$child]--;
                if ($indegree[$child] === 0) {
                    $queue[] = $child;
                    sort($queue);
                }
            }
        }

        if (count($ordered) < count($tables)) {
            $remaining = array_values(array_diff($tables, $ordered));
            sort($remaining);
            foreach ($remaining as $table) {
                $ordered[] = $table;
            }
        }

        return $ordered;
    }

    protected static function clearGeneratedFolder(string $folder): void
    {
        $paths = glob(rtrim($folder, '/').'/*.php');
        if (is_array($paths)) {
            foreach ($paths as $path) {
                @unlink($path);
            }
        }
        $jsonPaths = glob(rtrim($folder, '/').'/*.json');
        if (is_array($jsonPaths)) {
            foreach ($jsonPaths as $path) {
                @unlink($path);
            }
        }
    }

    /**
     * Remove foreign key constraints from generated migrations for reliable blank-DB bootstrap.
     */
    protected static function stripForeignKeyConstraints(string $sql): string
    {
        $lines = preg_split("/\r\n|\n|\r/", $sql);
        if (! is_array($lines)) {
            return $sql;
        }

        $out = [];
        foreach ($lines as $line) {
            if (preg_match('/^\s*CONSTRAINT\s+`[^`]+`\s+FOREIGN KEY\b/i', $line) === 1) {
                continue;
            }
            $out[] = $line;
        }

        $rebuilt = implode("\n", $out);

        // Fix trailing comma left before closing parenthesis.
        $rebuilt = preg_replace("/,\n\)/", "\n)", $rebuilt) ?? $rebuilt;

        return $rebuilt;
    }

    protected static function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }
        if (! mkdir($path, 0755, true) && ! is_dir($path)) {
            throw new RuntimeException('Cannot create directory: '.$path);
        }
    }
}

