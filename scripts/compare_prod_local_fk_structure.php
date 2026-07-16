<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Support\CloudwaysToDigitalOceanDataMigrator;
use Illuminate\Support\Facades\DB;
use PDO;

function fetchFks(PDO $pdo, string $schema): array
{
    $stmt = $pdo->prepare(
        'SELECT k.CONSTRAINT_NAME, k.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME, r.DELETE_RULE, r.UPDATE_RULE
         FROM information_schema.KEY_COLUMN_USAGE k
         JOIN information_schema.REFERENTIAL_CONSTRAINTS r
           ON r.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
         WHERE k.TABLE_SCHEMA = ? AND k.REFERENCED_TABLE_NAME IS NOT NULL
         ORDER BY k.TABLE_NAME, k.COLUMN_NAME'
    );
    $stmt->execute([$schema]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $bySig = [];
    $byName = [];
    foreach ($rows as $row) {
        $sig = $row['TABLE_NAME'].'|'.$row['COLUMN_NAME'].'|'.$row['REFERENCED_TABLE_NAME'].'|'.$row['REFERENCED_COLUMN_NAME'];
        $bySig[$sig] = $row;
        $byName[$row['CONSTRAINT_NAME']] = $row;
    }

    return ['bySig' => $bySig, 'byName' => $byName, 'count' => count($rows)];
}

$targetSchema = DB::connection()->getDatabaseName();
$sourceCfg = CloudwaysToDigitalOceanDataMigrator::connectionConfigFromLaravel('sync_prod');
$sourceDsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $sourceCfg['host'], $sourceCfg['port'], $sourceCfg['database']);
$source = new PDO($sourceDsn, $sourceCfg['username'], $sourceCfg['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$target = DB::connection()->getPdo();

$prod = fetchFks($source, $sourceCfg['database']);
$local = fetchFks($target, $targetSchema);

$missingSig = array_diff_key($prod['bySig'], $local['bySig']);
$extraSig = array_diff_key($local['bySig'], $prod['bySig']);
$sharedSig = array_intersect_key($prod['bySig'], $local['bySig']);

$ruleDiff = [];
$nameDiff = [];
foreach ($sharedSig as $sig => $p) {
    $l = $local['bySig'][$sig];
    if ($p['CONSTRAINT_NAME'] !== $l['CONSTRAINT_NAME']) {
        $nameDiff[$sig] = ['prod' => $p['CONSTRAINT_NAME'], 'local' => $l['CONSTRAINT_NAME']];
    }
    if ($p['DELETE_RULE'] !== $l['DELETE_RULE'] || $p['UPDATE_RULE'] !== $l['UPDATE_RULE']) {
        $ruleDiff[$sig] = [
            'prod' => $p,
            'local' => $l,
        ];
    }
}

echo "Structural FK diff\n";
echo "Missing relationship on local: ".count($missingSig)."\n";
echo "Extra relationship on local: ".count($extraSig)."\n";
echo "Same relationship, different constraint name: ".count($nameDiff)."\n";
echo "Same relationship, different ON DELETE/UPDATE: ".count($ruleDiff)."\n\n";

if ($missingSig) {
    echo "--- Missing relationships ---\n";
    foreach ($missingSig as $sig => $r) {
        echo "$sig | prod_name={$r['CONSTRAINT_NAME']} | DELETE {$r['DELETE_RULE']}\n";
    }
    echo "\n";
}

if ($extraSig) {
    echo "--- Extra relationships ---\n";
    foreach ($extraSig as $sig => $r) {
        echo "$sig | local_name={$r['CONSTRAINT_NAME']} | DELETE {$r['DELETE_RULE']}\n";
    }
    echo "\n";
}

if ($nameDiff) {
    echo "--- Name mismatches (first 30) ---\n";
    $i = 0;
    foreach ($nameDiff as $sig => $d) {
        echo "$sig | prod={$d['prod']} local={$d['local']}\n";
        if (++$i >= 30) break;
    }
    echo "\n";
}

if ($ruleDiff) {
    echo "--- Rule mismatches (first 15) ---\n";
    $i = 0;
    foreach ($ruleDiff as $sig => $d) {
        echo "$sig\n";
        echo "  prod:  DELETE {$d['prod']['DELETE_RULE']} UPDATE {$d['prod']['UPDATE_RULE']}\n";
        echo "  local: DELETE {$d['local']['DELETE_RULE']} UPDATE {$d['local']['UPDATE_RULE']}\n";
        if (++$i >= 15) break;
    }
}

// ngn_digital_invoice_items detail
echo "\n--- ngn_digital_invoice_items FKs ---\n";
foreach (['prod' => $prod, 'local' => $local] as $label => $set) {
    echo strtoupper($label).":\n";
    foreach ($set['bySig'] as $sig => $r) {
        if (str_starts_with($sig, 'ngn_digital_invoice_items|')) {
            echo "  {$r['CONSTRAINT_NAME']} | $sig | DELETE {$r['DELETE_RULE']}\n";
        }
    }
}
