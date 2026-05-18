<?php

namespace App\Console\Commands;

use App\Support\ProdNgnCleanSchemaReview;
use App\Support\ProdToLocalTableSync;
use Illuminate\Console\Command;

class ReviewProdNgnCleanSchemaCommand extends Command
{
    protected $signature = 'db:review-prod-ngnclean-schema
                            {--connection= : Local connection with ngn_clean (default: database.default)}
                            {--source-db= : Canonical DB name (default: ngn_clean)}
                            {--report-json= : JSON report path (default: database/schema/ngn-clean/prod_alignment_report.json)}
                            {--report-md= : Markdown summary (default: database/schema/ngn-clean/prod_alignment_report.md)}
                            {--migration-folder= : ALTER/CREATE migrations (default: database/migrations/prod-align-ngnclean)}
                            {--generate-migrations : Write migration files to align production toward ngn_clean}';

    protected $description = 'Review every table/column: production (SYNC_PROD_*) vs ngn_clean. Optional alignment migrations (schema only, no data).';

    public function handle(): int
    {
        $prod = $this->productionConfig();
        if ($prod === null) {
            $this->error('Set SYNC_PROD_DB_* in .env (production read-only review).');

            return 1;
        }

        $connection = (string) ($this->option('connection') ?: config('database.default', 'mysql'));
        $ngnDb = (string) ($this->option('source-db') ?: env('NGN_CLEAN_SEED_SOURCE_DB', 'ngn_clean'));
        $cfg = config("database.connections.{$connection}");
        if (! is_array($cfg)) {
            $this->error("Invalid connection [{$connection}]");

            return 1;
        }

        $ngnConfig = [
            'host' => (string) ($cfg['host'] ?? ''),
            'port' => (int) ($cfg['port'] ?? 3306),
            'database' => $ngnDb,
            'username' => (string) ($cfg['username'] ?? ''),
            'password' => (string) ($cfg['password'] ?? ''),
        ];

        try {
            $prodPdo = ProdToLocalTableSync::connectSource($prod);
            $ngnPdo = ProdToLocalTableSync::connectSource($ngnConfig);
        } catch (\Throwable $e) {
            $this->error('Connection failed: '.$e->getMessage());

            return 1;
        }

        $this->info('Production: '.$prod['host'].':'.$prod['port'].'/'.$prod['database']);
        $this->info('Canonical (ngn_clean): '.$ngnConfig['host'].':'.$ngnConfig['port'].'/'.$ngnDb);

        $report = ProdNgnCleanSchemaReview::compare($prodPdo, $prod['database'], $ngnPdo, $ngnDb);

        $jsonPath = $this->reportJsonPath();
        $mdPath = $this->reportMdPath();
        $dir = dirname($jsonPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($jsonPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n");
        ProdNgnCleanSchemaReview::writeMarkdownSummary($report, $mdPath);

        $s = $report['summary'];
        $this->newLine();
        $this->info('Schema review complete (no data copied).');
        $this->line('Tables only in ngn_clean: '.$s['tables_only_ngn_clean']);
        $this->line('Tables only in production: '.$s['tables_only_production']);
        $this->line('Shared tables: '.$s['tables_shared']);
        $this->line('Tables needing column alignment on production: '.$s['tables_with_column_gaps']);
        $this->line('JSON: '.$jsonPath);
        $this->line('Markdown: '.$mdPath);

        $this->printSampleGaps($report);

        if ($this->option('generate-migrations')) {
            $folder = $this->migrationFolder();
            $files = ProdNgnCleanSchemaReview::writeAlignmentMigrations($report, $ngnPdo, $ngnDb, $folder);
            $this->newLine();
            $this->info('Alignment migrations written: '.count($files));
            $this->line('Folder: '.$folder);
            $this->line('On production: php artisan migrate --path='.trim(str_replace(base_path(), '', $folder), '/').' --force');
            $this->line('Then refresh snapshot: php artisan db:export-ngnclean-schema');
        } else {
            $this->newLine();
            $this->line('Generate migrations: php artisan db:review-prod-ngnclean-schema --generate-migrations');
        }

        return 0;
    }

    /**
     * @param  array<string, mixed>  $report
     */
    protected function printSampleGaps(array $report): void
    {
        $shown = 0;
        foreach ($report['tables'] as $table => $meta) {
            $missing = $meta['missing_on_production'] ?? [];
            if ($missing === []) {
                continue;
            }
            if ($shown >= 15) {
                $this->line('... (see JSON/MD for full list)');

                break;
            }
            $this->warn("  {$table}: missing ".implode(', ', $missing));
            $shown++;
        }
    }

    protected function reportJsonPath(): string
    {
        $path = trim((string) $this->option('report-json'));

        return $path !== ''
            ? (str_starts_with($path, '/') ? $path : base_path($path))
            : base_path('database/schema/ngn-clean/prod_alignment_report.json');
    }

    protected function reportMdPath(): string
    {
        $path = trim((string) $this->option('report-md'));

        return $path !== ''
            ? (str_starts_with($path, '/') ? $path : base_path($path))
            : base_path('database/schema/ngn-clean/prod_alignment_report.md');
    }

    protected function migrationFolder(): string
    {
        $path = trim((string) $this->option('migration-folder'));

        return $path !== ''
            ? (str_starts_with($path, '/') ? $path : base_path($path))
            : base_path('database/migrations/prod-align-ngnclean');
    }

    /**
     * @return array{host:string,port:int,database:string,username:string,password:string}|null
     */
    protected function productionConfig(): ?array
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
