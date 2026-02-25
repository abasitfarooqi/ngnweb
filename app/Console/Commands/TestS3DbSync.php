<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestS3DbSync extends Command
{
    protected $signature = 'files:s3-test-sync';

    protected $description = 'Test sync: Limit 5 records, check file exists in S3, update DB value with CDN URL if valid';

    public function handle()
    {
        try {
            $s3 = Storage::disk('s3');
            $patterns = ['http', 'https', 'storage', 'app', 'public', 'laravel'];

            $this->info('=== TEST SYNC (LIMIT 5 RECORDS) ===');

            // Loop through all tables with a limit of 5 updates max
            $tables = DB::select('SELECT table_name FROM information_schema.tables WHERE table_schema = ?', [env('DB_DATABASE')]);

            $updatedCount = 0;

            foreach ($tables as $tableObj) {
                $table = $tableObj->table_name;

                $columns = DB::select("
                    SELECT column_name 
                    FROM information_schema.columns 
                    WHERE table_schema = ? AND table_name = ?
                      AND data_type IN ('char','varchar','text','mediumtext','longtext')
                ", [env('DB_DATABASE'), $table]);

                foreach ($columns as $colObj) {
                    $column = $colObj->column_name;

                    $rows = DB::table($table)->select('id', $column)->limit(10)->get(); // fetch a few rows for scan

                    foreach ($rows as $row) {
                        $value = $row->$column;

                        if ($value && preg_match('/'.implode('|', $patterns).'/i', $value)) {
                            // --- Normalise path ---
                            $relativePath = $this->normalisePath($value);

                            if (! $relativePath) {
                                $this->warn("[SKIP] Could not normalise path: $value");

                                continue;
                            }

                            // --- Check S3 existence ---
                            if ($s3->exists($relativePath)) {
                                $cdnUrl = $s3->url($relativePath);

                                if ($value !== $cdnUrl) {
                                    // --- Perform real update ---
                                    DB::table($table)
                                        ->where('id', $row->id)
                                        ->update([$column => $cdnUrl]);

                                    $this->info("[UPDATED] {$table}.{$column} (id {$row->id}) → $cdnUrl");
                                    Log::info("[UPDATED] {$table}.{$column} (id {$row->id}) from '$value' to '$cdnUrl'");

                                    $updatedCount++;
                                    if ($updatedCount >= 5) {
                                        $this->info('Limit reached (5 updates). Stopping.');

                                        return;
                                    }
                                } else {
                                    $this->info("[SKIP] Already up-to-date: {$table}.{$column} (id {$row->id})");
                                }
                            } else {
                                $this->warn("[MISSING] File not in S3: {$table}.{$column} (id {$row->id}) → $relativePath");
                            }
                        }
                    }
                }
            }

            $this->info("=== TEST SYNC COMPLETE: $updatedCount rows updated ===");

        } catch (\Exception $e) {
            Log::error('Test sync error: '.$e->getMessage());
            $this->error('Error: '.$e->getMessage());
        }
    }

    /**
     * Turn any DB value into a relative path usable for S3.
     */
    private function normalisePath(string $value): ?string
    {
        // Remove domain if present
        $value = preg_replace('#^https?://[^/]+/#', '', $value);

        // Strip leading "storage/app/" or "public/"
        $value = preg_replace('#^(storage/app/|public/)#', '', $value);

        // If after cleaning nothing remains, return null
        return trim($value) !== '' ? $value : null;
    }
}
