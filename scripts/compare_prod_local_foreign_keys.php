<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\CloudwaysToDigitalOceanDataMigrator;
use Illuminate\Support\Facades\DB;
use PDO;

function fkCatalog(PDO $pdo, string $schema): array
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
         ORDER BY k.TABLE_NAME, k.CONSTRAINT_NAME'
    );
    $stmt->execute([$schema]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $row) {
        $key = $row['CONSTRAINT_NAME'];
        $out[$key] = [
            'table' => $row['TABLE_NAME'],
            'column' => $row['COLUMN_NAME'],
            'ref_table' => $row['REFERENCED_TABLE_NAME'],
            'ref_column' => $row['REFERENCED_COLUMN_NAME'],
            'on_delete' => $row['DELETE_RULE'],
            'on_update' => $row['UPDATE_RULE'],
        ];
    }

    return $out;
}

function orphanCount(PDO $pdo, array $def): int
{
    $sql = sprintf(
        'SELECT COUNT(*) FROM `%s` t
         LEFT JOIN `%s` r ON t.`%s` = r.`%s`
         WHERE t.`%s` IS NOT NULL AND r.`%s` IS NULL',
        $def['table'],
        $def['ref_table'],
        $def['column'],
        $def['ref_column'],
        $def['column'],
        $def['ref_column']
    );

    return (int) $pdo->query($sql)->fetchColumn();
}

$targetSchema = DB::connection()->getDatabaseName();
$sourceCfg = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
if ($sourceCfg === null) {
    fwrite(STDERR, "sync_prod not configured\n");
    exit(1);
}

$sourceDsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $sourceCfg['host'],
    $sourceCfg['port'],
    $sourceCfg['database']
);
$source = new PDO($sourceDsn, $sourceCfg['username'], $sourceCfg['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$target = DB::connection()->getPdo();

$prodFks = fkCatalog($source, $sourceCfg['database']);
$localFks = fkCatalog($target, $targetSchema);

$missingOnLocal = array_diff_key($prodFks, $localFks);
$extraOnLocal = array_diff_key($localFks, $prodFks);
$shared = array_intersect_key($prodFks, $localFks);

$ruleDiff = [];
foreach ($shared as $name => $prod) {
    $local = $localFks[$name];
    if ($prod['on_delete'] !== $local['on_delete'] || $prod['on_update'] !== $local['on_update']) {
        $ruleDiff[$name] = ['prod' => $prod, 'local' => $local];
    }
}

echo "=== FK comparison: production ({$sourceCfg['database']}) vs local ({$targetSchema}) ===\n\n";
echo 'Production FK count: '.count($prodFks)."\n";
echo 'Local FK count: '.count($localFks)."\n";
echo 'Missing on local: '.count($missingOnLocal)."\n";
echo 'Extra on local (not on prod): '.count($extraOnLocal)."\n";
echo 'Rule differences: '.count($ruleDiff)."\n\n";

if ($missingOnLocal !== []) {
    echo "--- Missing on local ---\n";
    foreach ($missingOnLocal as $name => $def) {
        $localOrphans = orphanCount($target, $def);
        $prodOrphans = orphanCount($source, $def);
        echo sprintf(
            "%s | %s.%s -> %s.%s | DELETE %s | local_orphans=%d prod_orphans=%d\n",
            $name,
            $def['table'],
            $def['column'],
            $def['ref_table'],
            $def['ref_column'],
            $def['on_delete'],
            $localOrphans,
            $prodOrphans
        );
    }
    echo "\n";
}

if ($extraOnLocal !== []) {
    echo "--- Extra on local (first 20) ---\n";
    $i = 0;
    foreach ($extraOnLocal as $name => $def) {
        echo "$name | {$def['table']}.{$def['column']} -> {$def['ref_table']}.{$def['ref_column']}\n";
        if (++$i >= 20) {
            echo '... and '.(count($extraOnLocal) - 20)." more\n";
            break;
        }
    }
    echo "\n";
}

// Local orphans for ALL prod FKs (even if constraint exists locally)
echo "--- Local orphan rows (prod FK set) ---\n";
$orphanProblems = [];
foreach ($prodFks as $name => $def) {
    $tableExists = (bool) $target->query(
        "SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$targetSchema}' AND TABLE_NAME = '{$def['table']}' LIMIT 1"
    )->fetchColumn();
    $refExists = (bool) $target->query(
        "SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = '{$targetSchema}' AND TABLE_NAME = '{$def['ref_table']}' LIMIT 1"
    )->fetchColumn();
    if (! $tableExists || ! $refExists) {
        continue;
    }
    $c = orphanCount($target, $def);
    if ($c > 0) {
        $orphanProblems[$name] = ['def' => $def, 'count' => $c];
        echo "$c | $name | {$def['table']}.{$def['column']} -> {$def['ref_table']}.{$def['ref_column']}\n";
    }
}
if ($orphanProblems === []) {
    echo "(none)\n";
}

file_put_contents(
    storage_path('logs/fk-diff-production-vs-local.json'),
    json_encode([
        'generated_at' => date('c'),
        'production' => $sourceCfg['database'],
        'local' => $targetSchema,
        'missing_on_local' => $missingOnLocal,
        'extra_on_local' => $extraOnLocal,
        'rule_diff' => $ruleDiff,
        'local_orphans' => $orphanProblems,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
);

echo "\nReport: ".storage_path('logs/fk-diff-production-vs-local.json')."\n";
