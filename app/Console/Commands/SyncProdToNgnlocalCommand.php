<?php

namespace App\Console\Commands;

use App\Support\ProdToLocalTableSync;
use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;

class SyncProdToNgnlocalCommand extends Command
{
    protected $signature = 'db:sync-prod-to-ngnlocal
                            {--table= : Sync only this table name}
                            {--permissions-only : Sync only Spatie/permission-related tables}
                            {--skip-views : Do not recreate views from production}
                            {--force : Allow target database name other than ngn_clean}';

    protected $description = 'Copy structure + data from production MySQL to local DB (default: ngn_clean). Set SYNC_PROD_* in .env. Includes permissions/roles tables.';

    public function handle(): int
    {
        $prod = $this->prodConfig();
        if ($prod === null) {
            $this->error('Missing production credentials. Add to .env:');
            $this->line('SYNC_PROD_DB_HOST=');
            $this->line('SYNC_PROD_DB_PORT=3306');
            $this->line('SYNC_PROD_DB_DATABASE=');
            $this->line('SYNC_PROD_DB_USERNAME=');
            $this->line('SYNC_PROD_DB_PASSWORD=');

            return 1;
        }

        $local = [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port', 3306),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
        ];

        if (($local['database'] ?? '') !== 'ngn_clean' && ! $this->option('force')) {
            $this->error('Local DB_DATABASE is not "ngn_clean". Refusing to overwrite. Use --force to override.');

            return 1;
        }

        try {
            $src = ProdToLocalTableSync::connectSource($prod);
            $dst = ProdToLocalTableSync::connectTarget($local);
        } catch (\Throwable $e) {
            $this->error('Connection failed: '.$e->getMessage());

            return 1;
        }

        $srcSchema = $prod['database'];

        if ($this->option('table')) {
            $table = $this->option('table');
            $this->info("Syncing {$table}...");
            $r = ProdToLocalTableSync::syncTable($src, $dst, $srcSchema, $table);
            $this->info("Done. rows={$r['rows']}, inserted={$r['inserted']}");
            $this->flushPermissionCache();

            return 0;
        }

        if ($this->option('permissions-only')) {
            $tables = ProdToLocalTableSync::permissionRelatedTables();
            $this->info('Syncing permission-related tables: '.implode(', ', $tables));
            foreach ($tables as $table) {
                if (! $this->tableExists($src, $srcSchema, $table)) {
                    $this->warn("Skip (not in prod): {$table}");

                    continue;
                }
                $this->line("Syncing {$table}...");
                $r = ProdToLocalTableSync::syncTable($src, $dst, $srcSchema, $table);
                $this->info("  rows={$r['rows']}");
            }
            $this->flushPermissionCache();

            return 0;
        }

        $tables = $src->query(
            'SELECT table_name FROM information_schema.tables
             WHERE table_schema = '.$src->quote($srcSchema)." AND table_type = 'BASE TABLE'
             ORDER BY table_name"
        )->fetchAll(\PDO::FETCH_COLUMN);

        $this->info('Base tables to sync: '.count($tables));
        $bar = $this->output->createProgressBar(count($tables));
        $bar->start();

        foreach ($tables as $i => $table) {
            try {
                ProdToLocalTableSync::syncTable($src, $dst, $srcSchema, $table);
            } catch (\Throwable $e) {
                $bar->finish();
                $this->newLine();
                $this->error("Failed on {$table}: ".$e->getMessage());

                return 1;
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine(2);

        if (! $this->option('skip-views')) {
            $views = $src->query(
                'SELECT table_name FROM information_schema.tables
                 WHERE table_schema = '.$src->quote($srcSchema)." AND table_type = 'VIEW'
                 ORDER BY table_name"
            )->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($views as $view) {
                try {
                    $row = $src->query('SHOW CREATE VIEW '.ProdToLocalTableSync::qid($view))->fetch(\PDO::FETCH_ASSOC);
                    if (! $row || ! isset($row['Create View'])) {
                        continue;
                    }
                    $sql = $row['Create View'];
                    $sql = preg_replace('/\bDEFINER=`[^`]+`@`[^`]+`\s+/i', '', $sql) ?? $sql;
                    $dst->exec('DROP VIEW IF EXISTS '.ProdToLocalTableSync::qid($view));
                    $dst->exec($sql);
                    $this->line("View recreated: {$view}");
                } catch (\Throwable $e) {
                    $this->warn("View {$view} skipped: ".$e->getMessage());
                }
            }
        }

        $this->info('Full sync finished.');
        $this->flushPermissionCache();

        return 0;
    }

    protected function flushPermissionCache(): void
    {
        try {
            if (class_exists(PermissionRegistrar::class)) {
                app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
                $this->info('Spatie permission cache cleared.');
            }
        } catch (\Throwable $e) {
            $this->warn('Could not clear Spatie permission cache: '.$e->getMessage());
        }
    }

    protected function tableExists(\PDO $pdo, string $schema, string $table): bool
    {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
        );
        $stmt->execute([$schema, $table]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function prodConfig(): ?array
    {
        $host = env('SYNC_PROD_DB_HOST');
        $database = env('SYNC_PROD_DB_DATABASE');
        $user = env('SYNC_PROD_DB_USERNAME');
        $pass = env('SYNC_PROD_DB_PASSWORD');

        if (! $host || ! $database || $user === null || $pass === null) {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) (env('SYNC_PROD_DB_PORT', 3306)),
            'database' => $database,
            'username' => $user,
            'password' => $pass,
        ];
    }
}
