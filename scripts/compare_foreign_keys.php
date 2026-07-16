<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$expected = [];
$indexOnly = [];

foreach (glob(database_path('migrations/LatestMigrationFiles/bootstrap/*.php')) as $f) {
    $content = file_get_contents($f);
    if (! preg_match('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $content, $block)) {
        continue;
    }
    $table = $block[1];
    $body = $block[2];

    $fkCols = [];
    if (preg_match_all(
        '/CONSTRAINT `([^`]+)` FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)` \(`([^`]+)`\)([^,\n]*)/',
        $body,
        $m,
        PREG_SET_ORDER
    )) {
        foreach ($m as $x) {
            $expected[$x[1]] = [
                'table' => $table,
                'column' => $x[2],
                'ref_table' => $x[3],
                'ref_column' => $x[4],
                'rules' => trim($x[5]),
            ];
            $fkCols[$x[2]] = true;
        }
    }

    if (preg_match_all('/KEY `([^`]+)` \(`([^`]+)`\)/', $body, $m, PREG_SET_ORDER)) {
        foreach ($m as $x) {
            if (str_ends_with($x[1], '_foreign') && ! isset($fkCols[$x[2]])) {
                $indexOnly[] = [
                    'table' => $table,
                    'index' => $x[1],
                    'column' => $x[2],
                ];
            }
        }
    }
}

// Legacy FK migrations
$legacyDir = base_path('database___/database copy 2/migrations_0');
if (is_dir($legacyDir)) {
    foreach (glob($legacyDir.'/2026_02_12_222707_add_foreign_keys_to_*_table.php') as $f) {
        $content = file_get_contents($f);
        if (! preg_match("/Schema::table\('([^']+)'/", $content, $tm)) {
            continue;
        }
        $table = $tm[1];
        if (preg_match_all(
            '/->foreign\(\[\s*\'([^\']+)\'\s*\]\)->references\(\[\s*\'([^\']+)\'\s*\]\)->on\(\'([^\']+)\'\)(.*?);/s',
            $content,
            $m,
            PREG_SET_ORDER
        )) {
            foreach ($m as $x) {
                $name = $table.'_'.$x[1].'_foreign';
                if (! isset($expected[$name])) {
                    $expected[$name] = [
                        'table' => $table,
                        'column' => $x[1],
                        'ref_table' => $x[3],
                        'ref_column' => $x[2],
                        'rules' => trim(preg_replace('/\s+/', ' ', $x[4] ?? '')),
                        'source' => 'legacy',
                    ];
                }
            }
        }
    }
}

$presentRows = DB::select(
    'SELECT k.CONSTRAINT_NAME
     FROM information_schema.KEY_COLUMN_USAGE k
     WHERE k.TABLE_SCHEMA = DATABASE()
       AND k.REFERENCED_TABLE_NAME IS NOT NULL'
);
$present = array_flip(array_column($presentRows, 'CONSTRAINT_NAME'));

$missing = [];
foreach ($expected as $name => $meta) {
    if (! isset($present[$name])) {
        $missing[$name] = $meta;
    }
}

echo 'Bootstrap+legacy expected FKs: '.count($expected).PHP_EOL;
echo 'Present in DB: '.count($present).PHP_EOL;
echo 'Missing FK constraints: '.count($missing).PHP_EOL.PHP_EOL;

foreach ($missing as $name => $e) {
    $orphans = null;
    if (schemaHasTable($e['table']) && schemaHasTable($e['ref_table'])) {
        $row = DB::selectOne(
            "SELECT COUNT(*) AS c FROM `{$e['table']}` t
             LEFT JOIN `{$e['ref_table']}` r ON t.`{$e['column']}` = r.`{$e['ref_column']}`
             WHERE t.`{$e['column']}` IS NOT NULL AND r.`{$e['ref_column']}` IS NULL"
        );
        $orphans = (int) ($row->c ?? 0);
    }
    $src = $e['source'] ?? 'bootstrap';
    echo "{$name} | {$e['table']}.{$e['column']} -> {$e['ref_table']}.{$e['ref_column']} | orphans={$orphans} | {$src} | {$e['rules']}".PHP_EOL;
}

echo PHP_EOL.'Index-only (named _foreign but no CONSTRAINT in bootstrap): '.count($indexOnly).PHP_EOL;
foreach ($indexOnly as $i) {
    echo "  {$i['table']}.{$i['column']} (index {$i['index']})".PHP_EOL;
}

function schemaHasTable(string $table): bool
{
    return (bool) DB::selectOne(
        'SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1',
        [$table]
    );
}
