<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
use App\Models\CustomerContract;
use App\Models\CustomerAgreement;
use App\Models\CustomerDocument;

class CustomerDocsSecurelyTransferSFTP extends Command
{
    protected $signature = 'storage:customer-docs-transfer-sftp {--dry-run}';
    protected $description = 'Dry-run or transfer all customer docs from public/customers to private/customers via SFTP for both domains';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $this->info($dryRun ? "=== DRY RUN MODE ===" : "=== REAL SFTP COPY MODE ===");
        $this->newLine();

        // All available domains
        $allDomains = [
            'localhost' => storage_path('app'), // storage/app
            'neguinhomotors.co.uk' => '/applications/nqfkhvtysa/public_html/storage/app',
            'ngnmotors.co.uk'       => '/applications/keqdryartf/public_html/storage/app',
        ];

        // Auto-detect current server and filter to only process this server's domain
        $domains = $this->detectAndFilterDomains($allDomains);
        
        $this->info("Detected server: <fg=cyan>" . implode(', ', array_keys($domains)) . "</>");
        Log::info("CustomerDocsSecurelyTransferSFTP started (dry-run: " . ($dryRun ? 'yes' : 'no') . ", processing: " . implode(', ', array_keys($domains)) . ")");

        $totalStats = [
            'scanned' => 0,
            'would_move' => 0,
            'moved' => 0,
            'skipped' => 0,
            'flags_would_update' => 0,
            'flags_updated' => 0,
        ];

        foreach ($domains as $domain => $root) {
            $this->info("Processing domain: <fg=cyan>$domain</>");
            $this->info("Root path: <fg=gray>$root</>");
            $this->newLine();

            $fs = $this->getFilesystem($domain, $root);

            $publicPath  = 'public/customers';
            $privatePath = 'private/customers';

            try {
                if (!$fs->directoryExists($publicPath)) {
                    $this->warn("  Directory does not exist: $publicPath");
                    $this->newLine();
                    continue;
                }
            } catch (\Exception $e) {
                $this->error("  Error checking directory: {$e->getMessage()}");
                $this->newLine();
                continue;
            }

            try {
                $files = iterator_to_array($fs->listContents($publicPath, true));
            } catch (\Exception $e) {
                $this->error("  Error listing contents: {$e->getMessage()}");
                $this->newLine();
                continue;
            }

            foreach ($files as $file) {
                if (!$file->isFile()) continue;

                $totalStats['scanned']++;
                $relativePath = str_replace($publicPath.'/', '', $file->path());
                $sourcePath = $file->path();
                $destPath   = $privatePath.'/'.$relativePath;

                // Get file size if available
                $fileSize = null;
                try {
                    $fileSize = $fs->fileSize($sourcePath);
                    $fileSizeFormatted = $this->formatBytes($fileSize);
                } catch (\Exception $e) {
                    $fileSizeFormatted = 'unknown size';
                }

                if ($fs->fileExists($destPath)) {
                    $totalStats['skipped']++;
                    $msg = "SKIPPED (already exists in private):";
                    $this->warn("  $msg");
                    $this->line("    Source: <fg=yellow>$sourcePath</>");
                    $this->line("    Dest:   <fg=yellow>$destPath</>");
                    $this->line("    Size:   <fg=gray>$fileSizeFormatted</>");
                    Log::info("Skipped (already private): $domain/$sourcePath → $destPath ($fileSizeFormatted)");
                    $this->newLine();
                    continue;
                }

                if ($dryRun) {
                    $totalStats['would_move']++;
                    $this->info("  [DRY RUN] WOULD MOVE:");
                    $this->line("    FROM: <fg=red>$sourcePath</>");
                    $this->line("    TO:   <fg=green>$destPath</>");
                    $this->line("    SIZE: <fg=gray>$fileSizeFormatted</>");
                    Log::info("[DRY RUN] Would move: $domain/$sourcePath → $destPath ($fileSizeFormatted)");
                    $this->newLine();
                } else {
                    // === REAL MOVE ===
                    $totalStats['moved']++;
                    
                    $stream = $fs->readStream($sourcePath);
                    $fs->writeStream($destPath, $stream);
                    fclose($stream);
                    $fs->delete($sourcePath);

                    $this->info("Moved: $domain/$sourcePath → $destPath");
                    Log::info("Moved: $domain/$sourcePath → $destPath");
                    
                }

                $flagsUpdated = $this->processFlags($domain, $sourcePath, $dryRun);
                if ($flagsUpdated) {
                    if ($dryRun) {
                        $totalStats['flags_would_update'] += $flagsUpdated;
                    } else {
                        $totalStats['flags_updated'] += $flagsUpdated;
                    }
                }
            }
        }

        $this->newLine();
        $this->info("=== Summary ===");
        $this->line("  Files scanned:        <fg=cyan>{$totalStats['scanned']}</>");
        
        if ($dryRun) {
            $this->line("  Would move:           <fg=yellow>{$totalStats['would_move']}</>");
            $this->line("  Skipped (exists):     <fg=gray>{$totalStats['skipped']}</>");
            $this->line("  Flags would update:   <fg=cyan>{$totalStats['flags_would_update']}</>");
        } else {
            $this->line("  Moved:                <fg=green>{$totalStats['moved']}</>");
            $this->line("  Skipped (exists):     <fg=gray>{$totalStats['skipped']}</>");
            $this->line("  Flags updated:        <fg=cyan>{$totalStats['flags_updated']}</>");
        }
        
        $this->newLine();
        $this->info("=== Customer docs SFTP transfer complete ===");
    }

    protected function getFilesystem($domain, $root): Filesystem
    {
        // Use local filesystem for localhost, SFTP for remote domains
        if ($domain === 'localhost') {
            return new Filesystem(new LocalFilesystemAdapter($root));
        }

        // Use SFTP for remote domains
        $connectionProvider = SftpConnectionProvider::fromArray([
            'host' => env('DATASYNC_FTP_HOST'),
            'username' => env('DATASYNC_FTP_USERNAME'),
            'password' => env('DATASYNC_FTP_PASSWORD'),
            'port' => (int) env('DATASYNC_FTP_PORT', 22),
            'timeout' => 30,
        ]);

        return new Filesystem(new SftpAdapter($connectionProvider, $root));
    }

    protected function processFlags($domain, $path, $dryRun): int
    {
        $models = [
            'Contract' => CustomerContract::class,
            'Agreement' => CustomerAgreement::class,
            'Document' => CustomerDocument::class,
        ];

        $updatedCount = 0;

        // Extract filename from path (remove public/customers/ prefix if present)
        $searchPath = str_replace('public/customers/', 'customers/', $path);
        $filename = basename($searchPath);
        
        // Create a version-agnostic search pattern (remove version patterns like -v6-, -v3-, etc.)
        $filenamePattern = preg_replace('/-v\d+-/', '-', $filename);
        $filenamePattern = preg_replace('/-v\d+\./', '-', $filenamePattern);

        foreach ($models as $type => $model) {
            // Try matching with full path first, then filename, then version-agnostic pattern
            $record = $model::where('file_path', 'like', "%$searchPath%")
                ->orWhere('file_path', 'like', "%$filename%")
                ->orWhere('file_path', 'like', "%$filenamePattern%")
                ->first();
            
            if ($record) {
                $updatedCount++;
                if ($dryRun) {
                    $msg = "    [FLAG] $type ID {$record->id} would have <fg=cyan>sent_private = true</>";
                    $this->line($msg);
                    Log::info("[DRY RUN] $type ID {$record->id} on $domain would have sent_private = true for path: $path (matched DB path: {$record->file_path})");
                } else {
                    // === REAL FLAG UPDATE ===
                    
                    $record->sent_private = true;
                    $record->save();
                    $this->info("$type ID {$record->id} on $domain marked as private");
                    Log::info("$type ID {$record->id} on $domain marked as private");
                    
                }
            }
        }

        return $updatedCount;
    }

    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        if ($bytes === null || $bytes === 0) {
            return '0 B';
        }

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    protected function detectAndFilterDomains(array $allDomains): array
    {
        // Check storage path to detect which server we're on
        $storagePath = storage_path('app');
        
        // Check if we're on nqfkhvtysa server
        if (str_contains($storagePath, 'nqfkhvtysa')) {
            return ['neguinhomotors.co.uk' => $allDomains['neguinhomotors.co.uk']];
        }
        
        // Check if we're on keqdryartf server
        if (str_contains($storagePath, 'keqdryartf')) {
            return ['ngnmotors.co.uk' => $allDomains['ngnmotors.co.uk']];
        }
        
        // Fallback: check APP_URL
        $appUrl = env('APP_URL', '');
        if (str_contains($appUrl, 'nqfkhvtysa') || str_contains($appUrl, 'neguinhomotors')) {
            return ['neguinhomotors.co.uk' => $allDomains['neguinhomotors.co.uk']];
        }
        
        if (str_contains($appUrl, 'keqdryartf') || str_contains($appUrl, 'ngnmotors')) {
            return ['ngnmotors.co.uk' => $allDomains['ngnmotors.co.uk']];
        }
        
        // If can't detect, return all (for localhost/testing)
        return $allDomains;
    }

}
