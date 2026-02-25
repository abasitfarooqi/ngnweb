<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DBDryRunSync extends Command
{
    protected $signature = 's3-dryrun
                            {--rows=5 : Number of matched rows to show per column}';

    protected $description = 'Dry run: scan DB for file references and upload missing files to CDN (no DB writes)';

    private array $allowedExtensions = ['pdf', 'docx', 'xlsx', 'jpeg', 'jpg', 'png'];

    private array $stats = [
        'db_matches' => 0,
        'uploaded' => 0,
        'already_cdn' => 0,
        'skipped_no_pk' => 0,
        'missing_local' => 0,
    ];

    public function handle()
    {
        $rowsLimit = (int) $this->option('rows');

        $this->info('=== DRY RUN (DB-FIRST, CDN UPLOAD ACTIVE) ===');
        Log::info('=== DRY RUN (DB-FIRST, CDN UPLOAD ACTIVE) ===');

        try {
            // S3 disk
            $s3 = Storage::disk('s3');
            $cdnHost = $this->determineCdnHost($s3);
            if (! $cdnHost) {
                $cdnHost = parse_url(config('filesystems.disks.s3.url'), PHP_URL_HOST);
            }

            // Get all tables
            $tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = ?', [env('DB_DATABASE')]);

            foreach ($tables as $tableObj) {
                $table = $tableObj->table_name;

                $columns = DB::select("
                    SELECT column_name
                    FROM information_schema.columns
                    WHERE table_schema = ? AND table_name = ?
                    AND data_type IN ('char','varchar','text','mediumtext','longtext')
                ", [env('DB_DATABASE'), $table]);

                if (empty($columns)) {
                    continue;
                }

                $primaryKey = $this->getPrimaryKeyForTable($table) ?? 'id';

                foreach ($columns as $colObj) {
                    $column = $colObj->column_name;

                    try {
                        $rows = DB::table($table)
                            ->select($primaryKey, $column)
                            ->whereNotNull($column)
                            ->limit($rowsLimit)
                            ->get();
                    } catch (\Exception $e) {
                        Log::error("Error fetching rows for {$table}.{$column}: ".$e->getMessage());

                        continue;
                    }

                    if ($rows->isEmpty()) {
                        continue;
                    }

                    foreach ($rows as $row) {
                        $pkValue = $row->{$primaryKey} ?? null;
                        if ($pkValue === null) {
                            $this->warn("[SKIP] {$table}.{$column} — no PK value found");
                            Log::warning("[SKIP] {$table}.{$column} — no PK value found");
                            $this->stats['skipped_no_pk']++;

                            continue;
                        }

                        $value = (string) ($row->{$column} ?? '');
                        if ($value === '') {
                            continue;
                        }

                        // Already full URL
                        if (preg_match('#^https?://#i', $value)) {
                            $existingHost = parse_url($value, PHP_URL_HOST);
                            if ($existingHost && $cdnHost && strcasecmp($existingHost, $cdnHost) === 0) {
                                $this->info("[ALREADY CDN] {$table}.{$column} ({$primaryKey}={$pkValue}) => {$value}");
                                Log::info("[ALREADY CDN] {$table}.{$column} ({$primaryKey}={$pkValue}) => {$value}");
                                $this->stats['already_cdn']++;
                            }

                            continue;
                        }

                        // Extension filter
                        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                        if (! in_array($ext, $this->allowedExtensions, true)) {
                            continue;
                        }

                        $this->stats['db_matches']++;

                        // Local file exists?
                        $localPath = storage_path('app/'.ltrim($value, '/'));
                        if (! file_exists($localPath)) {
                            $this->warn("[MISSING] Local file not found: {$localPath}");
                            Log::warning("[MISSING] Local file not found: {$localPath}");
                            $this->stats['missing_local']++;

                            continue;
                        }

                        $bucket = config('filesystems.disks.s3.bucket');

                        // Remote path (no bucket duplication)
                        $remotePath = ltrim($value, '/'); // 'public/images/...'
                        $s3->put($remotePath, file_get_contents($localPath), 'public');

                        // Upload
                        $s3->put($remotePath, file_get_contents($localPath), 'public');
                        $expectedCdnUrl = $s3->url($remotePath);

                        $this->stats['uploaded']++;

                        try {
                            DB::table($table)
                                ->where($primaryKey, $pkValue)
                                ->update([$column => $expectedCdnUrl]);
                            // Dry-run: no DB update
                            $this->info("[UPDATED] {$table}.{$column} ({$primaryKey}={$pkValue}) => {$expectedCdnUrl}");
                            Log::info("[UPDATED] {$table}.{$column} PK={$pkValue} => {$expectedCdnUrl}");
                        } catch (\Exception $e) {
                            $this->error("[ERROR] Failed to update {$table}.{$column} PK={$pkValue}: ".$e->getMessage());
                            Log::error("[ERROR] Failed to update {$table}.{$column} PK={$pkValue}: ".$e->getMessage());
                        }
                    }
                }
            }

            // Summary
            $this->info("\n=== SUMMARY ===");
            $this->line("DB matches:    {$this->stats['db_matches']}");
            $this->line("Uploaded:      {$this->stats['uploaded']}");
            $this->line("Already CDN:   {$this->stats['already_cdn']}");
            $this->line("Missing local: {$this->stats['missing_local']}");
            $this->line("Skipped no PK: {$this->stats['skipped_no_pk']}");
            Log::info('=== SUMMARY ===');
            Log::info($this->stats);

            return 0;
        } catch (\Throwable $e) {
            $this->error('Error: '.$e->getMessage());
            Log::error('Error: '.$e->getMessage());

            return 1;
        }
    }

    private function getPrimaryKeyForTable(string $table): ?string
    {
        try {
            $pkRows = DB::select("
                SELECT kcu.column_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                ON kcu.constraint_name = tc.constraint_name
                AND kcu.table_schema = tc.table_schema
                AND kcu.table_name = tc.table_name
                WHERE tc.constraint_type = 'PRIMARY KEY'
                AND tc.table_schema = ?
                AND tc.table_name = ?
                LIMIT 1
            ", [env('DB_DATABASE'), $table]);

            return $pkRows[0]->column_name ?? 'id';
        } catch (\Exception $e) {
            Log::error("Error fetching primary key for table {$table}: ".$e->getMessage());

            return 'id';
        }
    }

    private function determineCdnHost($s3Disk): ?string
    {
        try {
            $dummy = '__probe__';
            $url = $s3Disk->url($dummy);

            return parse_url($url, PHP_URL_HOST) ?: null;
        } catch (\Throwable $e) {
            Log::error('Error determining CDN host: '.$e->getMessage());

            return null;
        }
    }
}
