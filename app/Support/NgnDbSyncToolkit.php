<?php

namespace App\Support;

use PDO;
use RuntimeException;

class NgnDbSyncToolkit
{
    /**
     * @return array{
     *     database:string,
     *     tables:array<string, array{
     *         name:string,
     *         create_sql:string,
     *         columns:list<string>,
     *         column_meta:array<string, array{
     *             column_type:string,
     *             is_nullable:string,
     *             column_default:mixed,
     *             extra:string,
     *             ordinal_position:int
     *         }>,
     *         primary_key:list<string>,
     *         dependencies:list<string>,
     *         row_count:int|null
     *     }>
     * }
     */
    public static function inspectSchema(PDO $pdo, string $database, bool $withRowCounts = false): array
    {
        $tables = self::baseTables($pdo, $database);
        $columnMeta = self::columnMetaByTable($pdo, $database);
        $primaryKeys = self::primaryKeys($pdo, $database);
        $dependencies = self::tableDependencies($pdo, $database, $tables);

        $result = [];
        foreach ($tables as $table) {
            $row = $pdo->query('SHOW CREATE TABLE '.ProdToLocalTableSync::qid($table))->fetch(PDO::FETCH_ASSOC);
            if (! is_array($row) || ! isset($row['Create Table'])) {
                throw new RuntimeException('Unable to read CREATE TABLE for '.$table);
            }

            $meta = $columnMeta[$table] ?? [];
            uasort($meta, static fn (array $a, array $b): int => (int) $a['ordinal_position'] <=> (int) $b['ordinal_position']);

            $result[$table] = [
                'name' => $table,
                'create_sql' => rtrim((string) $row['Create Table'], " \r\n\t;").';',
                'columns' => array_keys($meta),
                'column_meta' => $meta,
                'primary_key' => $primaryKeys[$table] ?? [],
                'dependencies' => $dependencies[$table] ?? [],
                'row_count' => $withRowCounts ? self::tableRowCount($pdo, $table) : null,
            ];
        }

        return [
            'database' => $database,
            'tables' => $result,
        ];
    }

    /**
     * @param  array{database:string,tables:array<string,mixed>}  $production
     * @param  array{database:string,tables:array<string,mixed>}  $local
     * @return array<string, mixed>
     */
    public static function compareSchemas(array $production, array $local): array
    {
        $prodTables = array_keys($production['tables']);
        $localTables = array_keys($local['tables']);

        $prodTableMap = self::namesByLower($prodTables);
        $localTableMap = self::namesByLower($localTables);
        $logicalKeys = array_values(array_unique(array_merge(array_keys($prodTableMap), array_keys($localTableMap))));
        sort($logicalKeys);

        $summary = [
            'tables_only_production' => 0,
            'tables_only_local' => 0,
            'tables_shared' => 0,
            'table_case_conflicts' => 0,
            'column_case_conflicts' => 0,
            'tables_with_local_only_columns' => 0,
            'tables_with_production_only_columns' => 0,
            'tables_with_definition_mismatches' => 0,
            'sync_blocker_tables' => 0,
        ];

        $tables = [];
        foreach ($logicalKeys as $logicalKey) {
            $prodNames = $prodTableMap[$logicalKey] ?? [];
            $localNames = $localTableMap[$logicalKey] ?? [];

            $productionTable = count($prodNames) === 1 ? $prodNames[0] : null;
            $localTable = count($localNames) === 1 ? $localNames[0] : null;

            $tableCaseConflict = $productionTable !== null
                && $localTable !== null
                && $productionTable !== $localTable;

            $status = 'shared';
            if ($productionTable === null) {
                $status = 'only_local';
                $summary['tables_only_local']++;
            } elseif ($localTable === null) {
                $status = 'only_production';
                $summary['tables_only_production']++;
            } else {
                $summary['tables_shared']++;
            }

            if ($tableCaseConflict) {
                $summary['table_case_conflicts']++;
            }

            $prodMeta = $productionTable !== null ? $production['tables'][$productionTable] : null;
            $localMeta = $localTable !== null ? $local['tables'][$localTable] : null;

            $columnComparison = self::compareColumns(
                is_array($prodMeta) ? $prodMeta : null,
                is_array($localMeta) ? $localMeta : null
            );

            if ($columnComparison['local_only'] !== []) {
                $summary['tables_with_local_only_columns']++;
            }
            if ($columnComparison['production_only'] !== []) {
                $summary['tables_with_production_only_columns']++;
            }
            if ($columnComparison['definition_mismatches'] !== []) {
                $summary['tables_with_definition_mismatches']++;
            }
            if ($columnComparison['case_conflicts'] !== []) {
                $summary['column_case_conflicts'] += count($columnComparison['case_conflicts']);
            }
            if ($columnComparison['production_sync_blockers'] !== []) {
                $summary['sync_blocker_tables']++;
            }

            $tables[$logicalKey] = [
                'logical_name' => $logicalKey,
                'status' => $status,
                'production_table' => $productionTable,
                'local_table' => $localTable,
                'table_case_conflict' => $tableCaseConflict,
                'production_columns' => $prodMeta['columns'] ?? [],
                'local_columns' => $localMeta['columns'] ?? [],
                'production_primary_key' => $prodMeta['primary_key'] ?? [],
                'local_primary_key' => $localMeta['primary_key'] ?? [],
                'production_dependencies' => $prodMeta['dependencies'] ?? [],
                'local_dependencies' => $localMeta['dependencies'] ?? [],
                'production_row_count' => $prodMeta['row_count'] ?? null,
                'local_row_count' => $localMeta['row_count'] ?? null,
                'column_case_conflicts' => $columnComparison['case_conflicts'],
                'columns_only_in_production' => $columnComparison['production_only'],
                'columns_only_in_local' => $columnComparison['local_only'],
                'definition_mismatches' => $columnComparison['definition_mismatches'],
                'production_sync_blockers' => $columnComparison['production_sync_blockers'],
            ];
        }

        return [
            'generated_at' => now()->toIso8601String(),
            'production_database' => $production['database'],
            'local_database' => $local['database'],
            'summary' => $summary,
            'tables' => $tables,
        ];
    }

    /**
     * @param  array<string, mixed>  $comparison
     * @return array{json:string,markdown:string}
     */
    public static function writeComparisonReport(array $comparison, string $folder): array
    {
        self::ensureDirectory($folder);

        $jsonPath = rtrim($folder, '/').'/comparison.json';
        $mdPath = rtrim($folder, '/').'/comparison.md';

        file_put_contents(
            $jsonPath,
            json_encode($comparison, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
        );

        $summary = $comparison['summary'];
        $lines = [
            '# NGN DB sync comparison',
            '',
            'Generated: '.($comparison['generated_at'] ?? ''),
            'Production DB: `'.($comparison['production_database'] ?? '').'`',
            'Local DB: `'.($comparison['local_database'] ?? '').'`',
            '',
            '## Summary',
            '',
            '| Metric | Count |',
            '|--------|------:|',
            '| Tables only in production | '.($summary['tables_only_production'] ?? 0).' |',
            '| Tables only in local | '.($summary['tables_only_local'] ?? 0).' |',
            '| Shared tables | '.($summary['tables_shared'] ?? 0).' |',
            '| Table case conflicts | '.($summary['table_case_conflicts'] ?? 0).' |',
            '| Column case conflicts | '.($summary['column_case_conflicts'] ?? 0).' |',
            '| Tables with local-only columns | '.($summary['tables_with_local_only_columns'] ?? 0).' |',
            '| Tables with production-only columns | '.($summary['tables_with_production_only_columns'] ?? 0).' |',
            '| Tables with definition mismatches | '.($summary['tables_with_definition_mismatches'] ?? 0).' |',
            '| Production sync blocker tables | '.($summary['sync_blocker_tables'] ?? 0).' |',
            '',
        ];

        foreach ($comparison['tables'] as $table) {
            $needsDetail = ($table['table_case_conflict'] ?? false)
                || ($table['column_case_conflicts'] ?? []) !== []
                || ($table['columns_only_in_local'] ?? []) !== []
                || ($table['columns_only_in_production'] ?? []) !== []
                || ($table['definition_mismatches'] ?? []) !== []
                || ($table['production_sync_blockers'] ?? []) !== [];

            if (! $needsDetail) {
                continue;
            }

            $name = $table['local_table'] ?? $table['production_table'] ?? $table['logical_name'] ?? 'unknown';
            $lines[] = '## `'.$name.'`';
            $lines[] = '';
            $lines[] = 'Status: `'.($table['status'] ?? 'unknown').'`';
            $lines[] = '';

            if (($table['table_case_conflict'] ?? false) === true) {
                $lines[] = 'Table case conflict: production `'.$table['production_table'].'`, local `'.$table['local_table'].'`';
                $lines[] = '';
            }

            if (($table['column_case_conflicts'] ?? []) !== []) {
                $lines[] = 'Column case conflicts:';
                foreach ($table['column_case_conflicts'] as $conflict) {
                    $lines[] = '- production `'.$conflict['production'].'`, local `'.$conflict['local'].'`';
                }
                $lines[] = '';
            }

            if (($table['columns_only_in_local'] ?? []) !== []) {
                $lines[] = 'Local-only columns: `'.implode('`, `', $table['columns_only_in_local']).'`';
                $lines[] = '';
            }

            if (($table['columns_only_in_production'] ?? []) !== []) {
                $lines[] = 'Production-only columns: `'.implode('`, `', $table['columns_only_in_production']).'`';
                $lines[] = '';
            }

            if (($table['definition_mismatches'] ?? []) !== []) {
                $lines[] = 'Definition mismatches:';
                foreach ($table['definition_mismatches'] as $mismatch) {
                    $lines[] = '- `'.$mismatch['production'].'` vs `'.$mismatch['local'].'`: '.$mismatch['difference'];
                }
                $lines[] = '';
            }

            if (($table['production_sync_blockers'] ?? []) !== []) {
                $lines[] = 'Production sync blockers:';
                foreach ($table['production_sync_blockers'] as $blocker) {
                    $lines[] = '- `'.$blocker['column'].'`: '.$blocker['reason'];
                }
                $lines[] = '';
            }
        }

        file_put_contents($mdPath, implode("\n", $lines)."\n");

        return ['json' => $jsonPath, 'markdown' => $mdPath];
    }

    /**
     * @param  array{database:string,tables:array<string,mixed>}  $production
     * @param  array{database:string,tables:array<string,mixed>}  $local
     * @param  array<string, mixed>  $comparison
     * @return array{
     *     table_case_map:array<string,string>,
     *     tables:array<string, array{
     *         logical_name:string,
     *         resolved_name:string,
     *         status:string,
     *         create_sql:string,
     *         columns:list<string>,
     *         source:string,
     *         production_table:string|null,
     *         local_table:string|null,
     *         production_columns:list<string>,
     *         dependencies:list<string>
     *     }>
     * }
     */
    public static function buildUnifiedPlan(array $production, array $local, array $comparison, ?string $preferCase): array
    {
        self::assertCasePreferenceIfNeeded($comparison, $preferCase);

        $tableCaseMap = self::buildTableCaseMap($comparison, $preferCase);
        $tables = [];

        foreach ($comparison['tables'] as $logicalName => $table) {
            $status = (string) ($table['status'] ?? 'shared');
            $productionTable = $table['production_table'];
            $localTable = $table['local_table'];

            if ($status === 'only_production') {
                $meta = $production['tables'][$productionTable];
                $resolvedName = $tableCaseMap[$productionTable] ?? $productionTable;
                $sql = ProdToLocalTableSync::normalizeCreateTableSql(
                    self::applyIdentifierCaseMap($meta['create_sql'], $tableCaseMap)
                );
                $tables[$logicalName] = [
                    'logical_name' => $logicalName,
                    'resolved_name' => $resolvedName,
                    'status' => $status,
                    'create_sql' => $sql,
                    'columns' => $meta['columns'],
                    'source' => 'production',
                    'production_table' => $productionTable,
                    'local_table' => null,
                    'production_columns' => $meta['columns'],
                    'dependencies' => self::mapDependencies($meta['dependencies'], $tableCaseMap),
                ];
                continue;
            }

            if ($status === 'only_local') {
                $meta = $local['tables'][$localTable];
                $resolvedName = $tableCaseMap[$localTable] ?? $localTable;
                $sql = ProdToLocalTableSync::normalizeCreateTableSql(
                    self::applyIdentifierCaseMap($meta['create_sql'], $tableCaseMap)
                );
                $tables[$logicalName] = [
                    'logical_name' => $logicalName,
                    'resolved_name' => $resolvedName,
                    'status' => $status,
                    'create_sql' => $sql,
                    'columns' => $meta['columns'],
                    'source' => 'local',
                    'production_table' => null,
                    'local_table' => $localTable,
                    'production_columns' => [],
                    'dependencies' => self::mapDependencies($meta['dependencies'], $tableCaseMap),
                ];
                continue;
            }

            $prodMeta = $production['tables'][$productionTable];
            $localMeta = $local['tables'][$localTable];
            $columnCaseMap = self::buildColumnCaseMap($prodMeta['columns'], $localMeta['columns'], $preferCase);

            $merged = self::mergeSharedCreateSql(
                $prodMeta['create_sql'],
                $localMeta['create_sql'],
                $prodMeta['columns'],
                $localMeta['columns']
            );

            $sql = ProdToLocalTableSync::normalizeCreateTableSql(
                self::applyIdentifierCaseMap($merged['sql'], array_merge($tableCaseMap, $columnCaseMap))
            );

            $resolvedColumns = [];
            foreach ($merged['columns'] as $column) {
                $resolvedColumns[] = $columnCaseMap[$column] ?? $column;
            }

            $resolvedName = $tableCaseMap[$localTable] ?? $tableCaseMap[$productionTable] ?? $localTable ?? $productionTable;
            $dependencies = array_values(array_unique(array_merge(
                self::mapDependencies($prodMeta['dependencies'], $tableCaseMap),
                self::mapDependencies($localMeta['dependencies'], $tableCaseMap)
            )));
            sort($dependencies);

            $tables[$logicalName] = [
                'logical_name' => $logicalName,
                'resolved_name' => $resolvedName,
                'status' => $status,
                'create_sql' => $sql,
                'columns' => $resolvedColumns,
                'source' => 'merged',
                'production_table' => $productionTable,
                'local_table' => $localTable,
                'production_columns' => $prodMeta['columns'],
                'dependencies' => $dependencies,
            ];
        }

        return [
            'table_case_map' => $tableCaseMap,
            'tables' => $tables,
        ];
    }

    /**
     * @param  array<string, mixed>  $comparison
     * @return array{bootstrap:list<string>,alignment:list<string>}
     */
    public static function writeMigrationArtifacts(array $comparison, array $plan, array $local, string $bootstrapFolder, string $alignmentFolder): array
    {
        self::ensureDirectory($bootstrapFolder);
        self::ensureDirectory($alignmentFolder);
        self::clearPhpAndJsonFiles($bootstrapFolder);
        self::clearPhpAndJsonFiles($alignmentFolder);

        $ordered = self::sortPlanTables($plan['tables']);
        $bootstrapFiles = [];
        $sequence = 1;
        foreach ($ordered as $table) {
            $safe = self::safeName($table['resolved_name']);
            $file = sprintf(
                '%s/%s_%04d_create_%s_table.php',
                rtrim($bootstrapFolder, '/'),
                now()->format('Y_m_d_His'),
                $sequence++,
                $safe
            );
            file_put_contents($file, self::bootstrapMigrationTemplate($table['resolved_name'], $table['create_sql']));
            $bootstrapFiles[] = $file;
        }

        $alignmentFiles = [];
        $alignSequence = 1;

        foreach ($comparison['tables'] as $logicalName => $table) {
            $status = (string) ($table['status'] ?? 'shared');
            if ($status === 'only_local') {
                $resolved = $plan['tables'][$logicalName] ?? null;
                if (! is_array($resolved)) {
                    continue;
                }
                $safe = self::safeName($resolved['resolved_name']);
                $file = sprintf(
                    '%s/%s_%04d_create_%s_table.php',
                    rtrim($alignmentFolder, '/'),
                    now()->format('Y_m_d_His'),
                    $alignSequence++,
                    $safe
                );
                file_put_contents($file, self::alignmentCreateMigrationTemplate($resolved['resolved_name'], $resolved['create_sql']));
                $alignmentFiles[] = $file;
                continue;
            }

            if ($status !== 'shared' || ($table['columns_only_in_local'] ?? []) === []) {
                continue;
            }

            $localTable = $table['local_table'];
            if (! is_string($localTable) || ! isset($local['tables'][$localTable])) {
                continue;
            }

            $localParsed = self::parseCreateTable($local['tables'][$localTable]['create_sql']);
            $columnMap = self::buildColumnCaseMap(
                $table['production_columns'] ?? [],
                $table['local_columns'] ?? [],
                'local'
            );

            $statements = [];
            foreach ($table['columns_only_in_local'] as $column) {
                $line = $localParsed['column_lines'][$column] ?? null;
                if ($line === null) {
                    continue;
                }

                $resolvedTable = $plan['tables'][$logicalName]['resolved_name'] ?? $localTable;
                $resolvedColumn = $columnMap[$column] ?? $column;
                $previous = self::previousColumn($table['local_columns'] ?? [], $column);
                $after = '';
                if ($previous !== null) {
                    $after = ' AFTER '.ProdToLocalTableSync::qid($columnMap[$previous] ?? $previous);
                }

                $clean = preg_replace('/^\s*`[^`]+`\s+/', ProdToLocalTableSync::qid($resolvedColumn).' ', trim($line)) ?? trim($line);
                $clean = rtrim($clean, ',');
                $statements[] = [
                    'column' => $resolvedColumn,
                    'sql' => 'ALTER TABLE '.ProdToLocalTableSync::qid($resolvedTable).' ADD COLUMN '.$clean.$after,
                ];
            }

            if ($statements === []) {
                continue;
            }

            $safe = self::safeName($plan['tables'][$logicalName]['resolved_name'] ?? $logicalName);
            $file = sprintf(
                '%s/%s_%04d_align_%s_columns.php',
                rtrim($alignmentFolder, '/'),
                now()->format('Y_m_d_His'),
                $alignSequence++,
                $safe
            );
            file_put_contents($file, self::alignmentAlterMigrationTemplate(
                $plan['tables'][$logicalName]['resolved_name'] ?? $logicalName,
                $statements
            ));
            $alignmentFiles[] = $file;
        }

        file_put_contents(
            rtrim($bootstrapFolder, '/').'/manifest.json',
            json_encode([
                'generated_at' => now()->toIso8601String(),
                'tables' => array_map(static fn (array $table): string => $table['resolved_name'], $ordered),
                'files' => $bootstrapFiles,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        );

        file_put_contents(
            rtrim($alignmentFolder, '/').'/manifest.json',
            json_encode([
                'generated_at' => now()->toIso8601String(),
                'files' => $alignmentFiles,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        );

        return [
            'bootstrap' => $bootstrapFiles,
            'alignment' => $alignmentFiles,
        ];
    }

    /**
     * @param  array{tables:array<string,mixed>}  $plan
     * @param  array<string, mixed>  $comparison
     * @return array{tables:int,rows:int}
     */
    public static function syncProductionIntoTarget(PDO $production, PDO $target, array $plan, array $comparison): array
    {
        $tables = self::sortPlanTables($plan['tables']);
        $syncedTables = 0;
        $syncedRows = 0;

        $target->exec('SET FOREIGN_KEY_CHECKS=0');
        $target->exec('SET UNIQUE_CHECKS=0');
        $target->exec("SET SESSION sql_mode = ''");

        try {
            foreach ($tables as $table) {
                $resolvedName = $table['resolved_name'];
                $target->exec('DROP TABLE IF EXISTS '.ProdToLocalTableSync::qid($resolvedName));
                $target->exec(rtrim($table['create_sql'], ';'));

                if (($table['production_table'] ?? null) === null) {
                    continue;
                }

                $comparisonTable = $comparison['tables'][$table['logical_name']] ?? [];
                $blockers = $comparisonTable['production_sync_blockers'] ?? [];
                if ($blockers !== []) {
                    throw new RuntimeException('Sync blocked for table '.$resolvedName.' because local-only required columns have no default.');
                }

                $map = self::sharedColumnMapForCopy(
                    $table['production_columns'],
                    $table['columns']
                );

                if ($map === []) {
                    $syncedTables++;
                    continue;
                }

                $sourceColumns = array_keys($map);
                $targetColumns = array_values($map);
                $rowCount = self::copyTableRows(
                    $production,
                    (string) $table['production_table'],
                    $sourceColumns,
                    $target,
                    $resolvedName,
                    $targetColumns
                );

                $syncedTables++;
                $syncedRows += $rowCount;
            }
        } finally {
            $target->exec('SET UNIQUE_CHECKS=1');
            $target->exec('SET FOREIGN_KEY_CHECKS=1');
        }

        return ['tables' => $syncedTables, 'rows' => $syncedRows];
    }

    /**
     * @return array{tables:int,rows:int}
     */
    public static function exportSnapshot(PDO $source, string $database, string $folder): array
    {
        self::ensureDirectory($folder);
        self::clearJsonFiles($folder);
        foreach (glob(rtrim($folder, '/').'/*.jsonl') ?: [] as $file) {
            @unlink($file);
        }

        $schema = self::inspectSchema($source, $database, false);
        $ordered = self::sortSchemaTables($schema['tables']);
        $manifest = [
            'generated_at' => now()->toIso8601String(),
            'database' => $database,
            'tables' => [],
        ];

        $rowsTotal = 0;
        foreach ($ordered as $table) {
            $name = $table['name'];
            $columns = $table['columns'];
            $path = rtrim($folder, '/').'/'.$name.'.jsonl';
            $handle = fopen($path, 'wb');
            if ($handle === false) {
                throw new RuntimeException('Cannot open snapshot file for writing: '.$path);
            }

            $rowCount = 0;
            $supportsUnbuffered = defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY');
            if ($supportsUnbuffered) {
                $source->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            }

            try {
                $statement = $source->query('SELECT * FROM '.ProdToLocalTableSync::qid($name));
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    fwrite($handle, json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");
                    $rowCount++;
                }
            } finally {
                if ($supportsUnbuffered) {
                    $source->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                }
            }
            fclose($handle);

            $rowsTotal += $rowCount;

            $manifest['tables'][] = [
                'name' => $name,
                'file' => basename($path),
                'columns' => $columns,
                'rows' => $rowCount,
            ];
        }

        file_put_contents(
            rtrim($folder, '/').'/manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n"
        );

        return ['tables' => count($ordered), 'rows' => $rowsTotal];
    }

    /**
     * @return array{tables:int,rows:int}
     */
    public static function applySnapshot(PDO $target, string $folder): array
    {
        $manifestPath = rtrim($folder, '/').'/manifest.json';
        if (! is_file($manifestPath)) {
            throw new RuntimeException('Snapshot manifest not found: '.$manifestPath);
        }

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $tables = is_array($manifest['tables'] ?? null) ? $manifest['tables'] : [];

        $appliedTables = 0;
        $appliedRows = 0;

        $target->exec('SET FOREIGN_KEY_CHECKS=0');
        $target->exec('SET UNIQUE_CHECKS=0');
        $target->exec("SET SESSION sql_mode = ''");

        try {
            foreach ($tables as $tableMeta) {
                $table = (string) ($tableMeta['name'] ?? '');
                if ($table === '') {
                    continue;
                }

                $fileName = (string) ($tableMeta['file'] ?? ($table.'.jsonl'));
                $file = rtrim($folder, '/').'/'.$fileName;
                if (! is_file($file)) {
                    continue;
                }

                $columns = is_array($tableMeta['columns'] ?? null) ? array_values($tableMeta['columns']) : [];

                $target->exec('TRUNCATE TABLE '.ProdToLocalTableSync::qid($table));
                if ($columns !== []) {
                    $colList = implode(',', array_map([ProdToLocalTableSync::class, 'qid'], $columns));
                    $placeholders = implode(',', array_fill(0, count($columns), '?'));
                    $statement = $target->prepare(
                        'INSERT INTO '.ProdToLocalTableSync::qid($table).' ('.$colList.') VALUES ('.$placeholders.')'
                    );

                    $handle = fopen($file, 'rb');
                    if ($handle === false) {
                        throw new RuntimeException('Cannot open snapshot file for reading: '.$file);
                    }

                    while (($line = fgets($handle)) !== false) {
                        $row = json_decode($line, true);
                        if (! is_array($row)) {
                            continue;
                        }
                        $values = [];
                        foreach ($columns as $column) {
                            $value = $row[$column] ?? null;
                            $values[] = is_string($value) ? ProdToLocalTableSync::sanitizeUtf8ForMysql($value) : $value;
                        }
                        $statement->execute($values);
                        $appliedRows++;
                    }
                    fclose($handle);
                }

                $appliedTables++;
            }
        } finally {
            $target->exec('SET UNIQUE_CHECKS=1');
            $target->exec('SET FOREIGN_KEY_CHECKS=1');
        }

        return ['tables' => $appliedTables, 'rows' => $appliedRows];
    }

    /**
     * @param  array<string, mixed>|null  $production
     * @param  array<string, mixed>|null  $local
     * @return array{
     *     case_conflicts:list<array{production:string,local:string}>,
     *     production_only:list<string>,
     *     local_only:list<string>,
     *     definition_mismatches:list<array{production:string,local:string,difference:string}>,
     *     production_sync_blockers:list<array{column:string,reason:string}>
     * }
     */
    protected static function compareColumns(?array $production, ?array $local): array
    {
        $prodColumns = $production['columns'] ?? [];
        $localColumns = $local['columns'] ?? [];
        $prodMap = self::namesByLower($prodColumns);
        $localMap = self::namesByLower($localColumns);
        $logicalKeys = array_values(array_unique(array_merge(array_keys($prodMap), array_keys($localMap))));
        sort($logicalKeys);

        $caseConflicts = [];
        $productionOnly = [];
        $localOnly = [];
        $definitionMismatches = [];
        $syncBlockers = [];

        foreach ($logicalKeys as $logicalKey) {
            $prodNames = $prodMap[$logicalKey] ?? [];
            $localNames = $localMap[$logicalKey] ?? [];
            $prodName = count($prodNames) === 1 ? $prodNames[0] : null;
            $localName = count($localNames) === 1 ? $localNames[0] : null;

            if ($prodName === null) {
                if ($localName !== null) {
                    $localOnly[] = $localName;
                    $meta = $local['column_meta'][$localName] ?? null;
                    if (is_array($meta) && self::isRequiredWithoutDefault($meta)) {
                        $syncBlockers[] = [
                            'column' => $localName,
                            'reason' => 'column is local-only, NOT NULL, and has no default for production row replay',
                        ];
                    }
                }

                continue;
            }

            if ($localName === null) {
                $productionOnly[] = $prodName;

                continue;
            }

            if ($prodName !== $localName) {
                $caseConflicts[] = [
                    'production' => $prodName,
                    'local' => $localName,
                ];
            }

            $prodMeta = $production['column_meta'][$prodName] ?? [];
            $localMeta = $local['column_meta'][$localName] ?? [];
            $difference = self::columnDifference($prodMeta, $localMeta);
            if ($difference !== null) {
                $definitionMismatches[] = [
                    'production' => $prodName,
                    'local' => $localName,
                    'difference' => $difference,
                ];
            }
        }

        return [
            'case_conflicts' => $caseConflicts,
            'production_only' => $productionOnly,
            'local_only' => $localOnly,
            'definition_mismatches' => $definitionMismatches,
            'production_sync_blockers' => $syncBlockers,
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    protected static function namesByLower(array $names): array
    {
        $result = [];
        foreach ($names as $name) {
            $key = strtolower((string) $name);
            $result[$key] ??= [];
            $result[$key][] = (string) $name;
        }

        foreach ($result as $key => $values) {
            $values = array_values(array_unique($values));
            sort($values);
            $result[$key] = $values;
        }

        return $result;
    }

    protected static function columnDifference(array $production, array $local): ?string
    {
        $checks = [
            'column_type' => 'type',
            'is_nullable' => 'nullable',
            'column_default' => 'default',
            'extra' => 'extra',
        ];

        foreach ($checks as $key => $label) {
            $prod = $production[$key] ?? null;
            $loc = $local[$key] ?? null;
            if ($prod !== $loc) {
                return $label.' differs (production='.var_export($prod, true).', local='.var_export($loc, true).')';
            }
        }

        return null;
    }

    protected static function isRequiredWithoutDefault(array $meta): bool
    {
        $nullable = strtoupper((string) ($meta['is_nullable'] ?? 'YES')) === 'YES';
        $default = $meta['column_default'] ?? null;
        $extra = strtolower((string) ($meta['extra'] ?? ''));

        return ! $nullable && $default === null && ! str_contains($extra, 'auto_increment');
    }

    /**
     * @param  array<string, mixed>  $comparison
     */
    protected static function assertCasePreferenceIfNeeded(array $comparison, ?string $preferCase): void
    {
        $hasConflict = false;
        foreach ($comparison['tables'] as $table) {
            if (($table['table_case_conflict'] ?? false) === true || ($table['column_case_conflicts'] ?? []) !== []) {
                $hasConflict = true;
                break;
            }
        }

        if ($hasConflict && ! in_array($preferCase, ['local', 'production'], true)) {
            throw new RuntimeException('Case-only conflicts exist. Re-run with --prefer-case=local or --prefer-case=production after reviewing the generated comparison report.');
        }
    }

    /**
     * @param  array<string, mixed>  $comparison
     * @return array<string,string>
     */
    protected static function buildTableCaseMap(array $comparison, ?string $preferCase): array
    {
        $map = [];
        foreach ($comparison['tables'] as $table) {
            $productionTable = $table['production_table'];
            $localTable = $table['local_table'];

            if (! is_string($productionTable) && ! is_string($localTable)) {
                continue;
            }

            if (is_string($productionTable) && ! is_string($localTable)) {
                $map[$productionTable] = $productionTable;
                continue;
            }

            if (! is_string($productionTable) && is_string($localTable)) {
                $map[$localTable] = $localTable;
                continue;
            }

            $resolved = $preferCase === 'production' ? $productionTable : $localTable;
            $map[$productionTable] = $resolved;
            $map[$localTable] = $resolved;
        }

        return $map;
    }

    /**
     * @param  list<string>  $productionColumns
     * @param  list<string>  $localColumns
     * @return array<string,string>
     */
    protected static function buildColumnCaseMap(array $productionColumns, array $localColumns, ?string $preferCase): array
    {
        $map = [];
        $prodLower = self::namesByLower($productionColumns);
        $localLower = self::namesByLower($localColumns);
        $logicalKeys = array_values(array_unique(array_merge(array_keys($prodLower), array_keys($localLower))));

        foreach ($logicalKeys as $logicalKey) {
            $prod = count($prodLower[$logicalKey] ?? []) === 1 ? $prodLower[$logicalKey][0] : null;
            $local = count($localLower[$logicalKey] ?? []) === 1 ? $localLower[$logicalKey][0] : null;
            if ($prod === null && $local === null) {
                continue;
            }

            if ($prod === null) {
                $map[$local] = $local;
                continue;
            }

            if ($local === null) {
                $map[$prod] = $prod;
                continue;
            }

            $resolved = $preferCase === 'production' ? $prod : $local;
            $map[$prod] = $resolved;
            $map[$local] = $resolved;
        }

        return $map;
    }

    /**
     * @param  list<string>  $productionColumns
     * @param  list<string>  $localColumns
     * @return array{sql:string,columns:list<string>}
     */
    protected static function mergeSharedCreateSql(
        string $productionCreateSql,
        string $localCreateSql,
        array $productionColumns,
        array $localColumns
    ): array {
        $productionParsed = self::parseCreateTable($productionCreateSql);
        $localParsed = self::parseCreateTable($localCreateSql);

        $prodMap = self::namesByLower($productionColumns);
        $localMap = self::namesByLower($localColumns);

        $mergedOrder = $localColumns;
        foreach ($productionColumns as $prodColumn) {
            $logical = strtolower($prodColumn);
            if (isset($localMap[$logical])) {
                continue;
            }

            $inserted = false;
            $prodIndex = array_search($prodColumn, $productionColumns, true);
            if ($prodIndex !== false) {
                for ($i = $prodIndex + 1, $max = count($productionColumns); $i < $max; $i++) {
                    $next = $productionColumns[$i];
                    if (! isset($localMap[strtolower($next)])) {
                        continue;
                    }
                    $resolvedNext = $localMap[strtolower($next)][0];
                    $position = array_search($resolvedNext, $mergedOrder, true);
                    if ($position !== false) {
                        array_splice($mergedOrder, $position, 0, [$prodColumn]);
                        $inserted = true;
                        break;
                    }
                }
            }

            if (! $inserted) {
                $mergedOrder[] = $prodColumn;
            }
        }

        $mergedLines = [];
        foreach ($mergedOrder as $column) {
            if (isset($localParsed['column_lines'][$column])) {
                $mergedLines[] = $localParsed['column_lines'][$column];
                continue;
            }

            if (isset($productionParsed['column_lines'][$column])) {
                $mergedLines[] = $productionParsed['column_lines'][$column];
            }
        }

        $mergedKeys = $localParsed['key_lines'];
        $localKeySet = array_map(static fn (string $line): string => strtolower(trim($line)), $localParsed['key_lines']);
        $missingProductionColumns = array_values(array_filter(
            $productionColumns,
            static fn (string $column): bool => ! isset($localMap[strtolower($column)])
        ));

        foreach ($productionParsed['key_lines'] as $line) {
            $normalized = strtolower(trim($line));
            if (in_array($normalized, $localKeySet, true)) {
                continue;
            }

            $referencedColumns = self::columnsReferencedByKeyLine($line);
            if ($referencedColumns === []) {
                continue;
            }

            $needsKey = false;
            foreach ($referencedColumns as $referencedColumn) {
                foreach ($missingProductionColumns as $missingColumn) {
                    if (strtolower($referencedColumn) === strtolower($missingColumn)) {
                        $needsKey = true;
                        break 2;
                    }
                }
            }

            if ($needsKey) {
                $mergedKeys[] = $line;
            }
        }

        $bodyLines = array_merge($mergedLines, $mergedKeys);
        $bodyLines = array_values(array_filter($bodyLines, static fn (?string $line): bool => $line !== null && trim($line) !== ''));

        $rebuilt = $localParsed['header']."\n";
        $lastIndex = count($bodyLines) - 1;
        foreach ($bodyLines as $index => $line) {
            $rebuilt .= rtrim($line, ',').($index === $lastIndex ? '' : ',')."\n";
        }
        $rebuilt .= $localParsed['tail'];
        if (! str_ends_with($rebuilt, ';')) {
            $rebuilt .= ';';
        }

        return [
            'sql' => $rebuilt,
            'columns' => $mergedOrder,
        ];
    }

    /**
     * @return array{header:string,tail:string,column_lines:array<string,string>,key_lines:list<string>}
     */
    protected static function parseCreateTable(string $createSql): array
    {
        $sql = rtrim(trim($createSql), ';');
        $lines = preg_split("/\r\n|\n|\r/", $sql);
        if (! is_array($lines) || count($lines) < 2) {
            throw new RuntimeException('Unexpected CREATE TABLE format.');
        }

        $header = array_shift($lines);
        $tail = array_pop($lines);
        if ($header === null || $tail === null) {
            throw new RuntimeException('Unexpected CREATE TABLE structure.');
        }

        $columnLines = [];
        $keyLines = [];
        foreach ($lines as $line) {
            $trimmed = ltrim(trim($line), ',');
            if (preg_match('/^`([^`]+)`\s+/u', $trimmed, $matches) === 1) {
                $columnLines[$matches[1]] = $trimmed;
                continue;
            }

            $keyLines[] = $trimmed;
        }

        return [
            'header' => $header,
            'tail' => $tail,
            'column_lines' => $columnLines,
            'key_lines' => $keyLines,
        ];
    }

    /**
     * @return list<string>
     */
    protected static function columnsReferencedByKeyLine(string $line): array
    {
        if (preg_match('/\(([^)]+)\)/', $line, $matches) !== 1) {
            return [];
        }

        preg_match_all('/`([^`]+)`/', $matches[1], $inner);

        return is_array($inner[1] ?? null) ? array_values($inner[1]) : [];
    }

    /**
     * @param  array<string,string>  $map
     */
    protected static function applyIdentifierCaseMap(string $sql, array $map): string
    {
        return preg_replace_callback('/`([^`]+)`/u', static function (array $matches) use ($map): string {
            $name = $matches[1];

            return '`'.($map[$name] ?? $name).'`';
        }, $sql) ?? $sql;
    }

    /**
     * @param  list<string>  $dependencies
     * @param  array<string,string>  $tableCaseMap
     * @return list<string>
     */
    protected static function mapDependencies(array $dependencies, array $tableCaseMap): array
    {
        $mapped = [];
        foreach ($dependencies as $dependency) {
            $mapped[] = $tableCaseMap[$dependency] ?? $dependency;
        }

        $mapped = array_values(array_unique($mapped));
        sort($mapped);

        return $mapped;
    }

    /**
     * @param  array<string, array<string, mixed>>  $tables
     * @return list<array<string, mixed>>
     */
    protected static function sortPlanTables(array $tables): array
    {
        $byResolved = [];
        foreach ($tables as $table) {
            $byResolved[$table['resolved_name']] = $table;
        }

        $dependencies = [];
        foreach ($byResolved as $resolved => $table) {
            $dependencies[$resolved] = array_values(array_filter(
                $table['dependencies'] ?? [],
                static fn (string $dependency): bool => isset($byResolved[$dependency]) && $dependency !== ''
            ));
        }

        $sortedNames = self::topologicalSort(array_keys($byResolved), $dependencies);

        $ordered = [];
        foreach ($sortedNames as $name) {
            $ordered[] = $byResolved[$name];
        }

        return $ordered;
    }

    /**
     * @param  array<string, array<string, mixed>>  $tables
     * @return list<array<string, mixed>>
     */
    protected static function sortSchemaTables(array $tables): array
    {
        $dependencies = [];
        foreach ($tables as $name => $table) {
            $dependencies[$name] = array_values(array_filter(
                $table['dependencies'] ?? [],
                static fn (string $dependency): bool => isset($tables[$dependency]) && $dependency !== ''
            ));
        }

        $sortedNames = self::topologicalSort(array_keys($tables), $dependencies);

        $ordered = [];
        foreach ($sortedNames as $name) {
            $ordered[] = $tables[$name];
        }

        return $ordered;
    }

    /**
     * @param  list<string>  $names
     * @param  array<string,list<string>>  $dependencies
     * @return list<string>
     */
    protected static function topologicalSort(array $names, array $dependencies): array
    {
        $names = array_values(array_unique($names));
        sort($names);

        $indegree = [];
        $adjacency = [];
        foreach ($names as $name) {
            $indegree[$name] = 0;
            $adjacency[$name] = [];
        }

        foreach ($dependencies as $name => $deps) {
            foreach ($deps as $dependency) {
                if (! isset($indegree[$name], $indegree[$dependency])) {
                    continue;
                }
                $indegree[$name]++;
                $adjacency[$dependency][] = $name;
            }
        }

        $queue = [];
        foreach ($indegree as $name => $count) {
            if ($count === 0) {
                $queue[] = $name;
            }
        }
        sort($queue);

        $ordered = [];
        while ($queue !== []) {
            $current = array_shift($queue);
            $ordered[] = $current;

            $children = $adjacency[$current] ?? [];
            sort($children);
            foreach ($children as $child) {
                $indegree[$child]--;
                if ($indegree[$child] === 0) {
                    $queue[] = $child;
                    sort($queue);
                }
            }
        }

        if (count($ordered) < count($names)) {
            foreach (array_values(array_diff($names, $ordered)) as $name) {
                $ordered[] = $name;
            }
        }

        return $ordered;
    }

    protected static function bootstrapMigrationTemplate(string $table, string $createSql): string
    {
        $tableExport = var_export($table, true);
        $sql = rtrim($createSql);
        if (! str_ends_with($sql, ';')) {
            $sql .= ';';
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('DROP TABLE IF EXISTS '.\$this->qid({$tableExport}));
        DB::unprepared(<<<'SQL'
{$sql}
SQL
        );
        DB::statement('SET UNIQUE_CHECKS=1');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists({$tableExport});
    }

    protected function qid(string \$name): string
    {
        return '`'.str_replace('`', '``', \$name).'`';
    }
};

PHP;
    }

    protected static function alignmentCreateMigrationTemplate(string $table, string $createSql): string
    {
        $tableExport = var_export($table, true);
        $sql = rtrim($createSql);
        if (! str_ends_with($sql, ';')) {
            $sql .= ';';
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable({$tableExport})) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::unprepared(<<<'SQL'
{$sql}
SQL
        );
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        Schema::dropIfExists({$tableExport});
    }
};

PHP;
    }

    /**
     * @param  list<array{column:string,sql:string}>  $statements
     */
    protected static function alignmentAlterMigrationTemplate(string $table, array $statements): string
    {
        $tableExport = var_export($table, true);
        $blocks = '';
        foreach ($statements as $statement) {
            $columnExport = var_export($statement['column'], true);
            $sqlExport = var_export($statement['sql'], true);
            $blocks .= <<<PHP

        if (! Schema::hasColumn({$tableExport}, {$columnExport})) {
            DB::statement({$sqlExport});
        }
PHP;
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // Manual rollback only.
    }
};

PHP;
    }

    /**
     * @param  list<string>  $productionColumns
     * @param  list<string>  $targetColumns
     * @return array<string,string>
     */
    protected static function sharedColumnMapForCopy(array $productionColumns, array $targetColumns): array
    {
        $targetMap = self::namesByLower($targetColumns);
        $map = [];
        foreach ($productionColumns as $productionColumn) {
            $logical = strtolower($productionColumn);
            if (! isset($targetMap[$logical][0])) {
                continue;
            }

            $map[$productionColumn] = $targetMap[$logical][0];
        }

        return $map;
    }

    /**
     * @param  list<string>  $sourceColumns
     * @param  list<string>  $targetColumns
     */
    protected static function copyTableRows(
        PDO $source,
        string $sourceTable,
        array $sourceColumns,
        PDO $target,
        string $targetTable,
        array $targetColumns
    ): int {
        $sourceList = implode(',', array_map([ProdToLocalTableSync::class, 'qid'], $sourceColumns));
        $targetList = implode(',', array_map([ProdToLocalTableSync::class, 'qid'], $targetColumns));
        $placeholders = implode(',', array_fill(0, count($targetColumns), '?'));
        $insert = $target->prepare(
            'INSERT INTO '.ProdToLocalTableSync::qid($targetTable).' ('.$targetList.') VALUES ('.$placeholders.')'
        );

        $count = 0;
        $statement = $source->query('SELECT '.$sourceList.' FROM '.ProdToLocalTableSync::qid($sourceTable));
        while ($row = $statement->fetch(PDO::FETCH_NUM)) {
            foreach ($row as $index => $value) {
                if (is_string($value)) {
                    $row[$index] = ProdToLocalTableSync::sanitizeUtf8ForMysql($value);
                }
            }
            $insert->execute($row);
            $count++;
        }

        return $count;
    }

    protected static function previousColumn(array $columns, string $column): ?string
    {
        $index = array_search($column, $columns, true);
        if ($index === false || $index === 0) {
            return null;
        }

        return $columns[$index - 1] ?? null;
    }

    /**
     * @return list<string>
     */
    protected static function baseTables(PDO $pdo, string $schema): array
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

        $result = [];
        foreach ($rows as $row) {
            $table = (string) ($row['TABLE_NAME'] ?? '');
            $column = (string) ($row['COLUMN_NAME'] ?? '');
            if ($table === '' || $column === '') {
                continue;
            }

            $result[$table][$column] = [
                'column_type' => (string) ($row['COLUMN_TYPE'] ?? ''),
                'is_nullable' => (string) ($row['IS_NULLABLE'] ?? 'YES'),
                'column_default' => $row['COLUMN_DEFAULT'] ?? null,
                'extra' => (string) ($row['EXTRA'] ?? ''),
                'ordinal_position' => (int) ($row['ORDINAL_POSITION'] ?? 0),
            ];
        }

        return $result;
    }

    /**
     * @return array<string, list<string>>
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
        foreach ($rows as $row) {
            $table = (string) ($row['TABLE_NAME'] ?? '');
            $column = (string) ($row['COLUMN_NAME'] ?? '');
            if ($table === '' || $column === '') {
                continue;
            }
            $result[$table] ??= [];
            $result[$table][] = $column;
        }

        return $result;
    }

    /**
     * @param  list<string>  $tables
     * @return array<string, list<string>>
     */
    protected static function tableDependencies(PDO $pdo, string $schema, array $tables): array
    {
        $allowed = array_fill_keys($tables, true);
        $result = [];
        foreach ($tables as $table) {
            $result[$table] = [];
        }

        $stmt = $pdo->prepare(
            "SELECT TABLE_NAME, REFERENCED_TABLE_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL"
        );
        $stmt->execute([$schema]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $table = (string) ($row['TABLE_NAME'] ?? '');
            $referenced = (string) ($row['REFERENCED_TABLE_NAME'] ?? '');
            if (! isset($allowed[$table], $allowed[$referenced])) {
                continue;
            }
            $result[$table][] = $referenced;
        }

        foreach ($result as $table => $dependencies) {
            $dependencies = array_values(array_unique($dependencies));
            sort($dependencies);
            $result[$table] = $dependencies;
        }

        return $result;
    }

    protected static function tableRowCount(PDO $pdo, string $table): int
    {
        return (int) $pdo->query('SELECT COUNT(*) FROM '.ProdToLocalTableSync::qid($table))->fetchColumn();
    }

    protected static function safeName(string $name): string
    {
        $safe = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', $name) ?? $name);
        $safe = trim($safe, '_');

        return $safe !== '' ? $safe : 'table';
    }

    protected static function ensureDirectory(string $folder): void
    {
        if (is_dir($folder)) {
            return;
        }

        if (! mkdir($folder, 0755, true) && ! is_dir($folder)) {
            throw new RuntimeException('Cannot create directory: '.$folder);
        }
    }

    protected static function clearPhpAndJsonFiles(string $folder): void
    {
        self::clearJsonFiles($folder);
        foreach (glob(rtrim($folder, '/').'/*.php') ?: [] as $file) {
            @unlink($file);
        }
    }

    protected static function clearJsonFiles(string $folder): void
    {
        foreach (glob(rtrim($folder, '/').'/*.json') ?: [] as $file) {
            @unlink($file);
        }
    }
}
