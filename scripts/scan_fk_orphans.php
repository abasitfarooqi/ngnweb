<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$fks = DB::select(
    'SELECT k.CONSTRAINT_NAME, k.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME
     FROM information_schema.KEY_COLUMN_USAGE k
     WHERE k.TABLE_SCHEMA = DATABASE()
       AND k.REFERENCED_TABLE_NAME IS NOT NULL
     ORDER BY k.TABLE_NAME, k.CONSTRAINT_NAME'
);

$problems = [];
foreach ($fks as $fk) {
    $row = DB::selectOne(
        "SELECT COUNT(*) AS c FROM `{$fk->TABLE_NAME}` t
         LEFT JOIN `{$fk->REFERENCED_TABLE_NAME}` r ON t.`{$fk->COLUMN_NAME}` = r.`{$fk->REFERENCED_COLUMN_NAME}`
         WHERE t.`{$fk->COLUMN_NAME}` IS NOT NULL AND r.`{$fk->REFERENCED_COLUMN_NAME}` IS NULL"
    );
    $c = (int) ($row->c ?? 0);
    if ($c > 0) {
        $problems[] = [
            'constraint' => $fk->CONSTRAINT_NAME,
            'from' => "{$fk->TABLE_NAME}.{$fk->COLUMN_NAME}",
            'to' => "{$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}",
            'orphans' => $c,
        ];
    }
}

echo 'FK orphan scan: '.count($problems).' constraints with broken references'.PHP_EOL.PHP_EOL;
foreach ($problems as $p) {
    echo "{$p['orphans']} | {$p['from']} -> {$p['to']} ({$p['constraint']})".PHP_EOL;
}
