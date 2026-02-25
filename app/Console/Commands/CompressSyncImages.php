<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CompressSyncImages extends Command
{
    protected $signature = 'images:sync-compress';

    protected $description = 'Sync and compress images between two Laravel apps (dry-run by default)';

    public function handle()
    {
        $this->info('🟡 Starting image sync & compression (dry-run)');
        Log::info('[CompressSyncImages] Starting dry-run sync');

        $peer = rtrim(env('PEER_DOMAIN'), '/');
        $token = env('SYNC_SECRET');
        $extensions = ['jpg', 'jpeg', 'png'];

        // Get local image list
        $localFiles = collect(File::allFiles(storage_path('app')))
            ->mapWithKeys(fn ($f) => [
                str_replace(storage_path('app').'/', '', $f->getPathname()) => $f->getMTime(),
            ])
            ->filter(fn ($_, $file) => in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $extensions));

        // Get remote file list
        $this->info('Fetching remote image list...');
        $remoteFiles = Http::withHeaders(['X-SYNC-TOKEN' => $token])
            ->get("$peer/api/image-sync/files")
            ->json();

        // ========== DOWNLOAD ==========
        foreach ($remoteFiles as $file => $mtime) {
            if (! isset($localFiles[$file]) || $localFiles[$file] < $mtime) {
                $this->info("⬇️ Would download and compress: $file");
                Log::info("[CompressSyncImages] Would download and compress: $file");

                $response = Http::withHeaders(['X-SYNC-TOKEN' => $token])
                    ->get("$peer/storage/$file");

                if ($response->ok()) {
                    try {
                        $image = Image::make($response->body())->encode(pathinfo($file, PATHINFO_EXTENSION), 75);

                        $localPath = storage_path('app/'.$file);
                        File::ensureDirectoryExists(dirname($localPath));

                        // Storage::put($file, (string) $image); // 🔴 DEACTIVATED
                        Log::info("[CompressSyncImages] Compressed image ready to save: $file");
                    } catch (\Exception $e) {
                        Log::error("[CompressSyncImages] Compression failed: $file — ".$e->getMessage());
                    }
                } else {
                    Log::warning("[CompressSyncImages] Failed to fetch: $file");
                }
            }
        }

        // ========== UPLOAD ==========
        foreach ($localFiles as $file => $mtime) {
            if (! isset($remoteFiles[$file]) || $remoteFiles[$file] < $mtime) {
                $this->info("⬆️ Would upload compressed image: $file");
                Log::info("[CompressSyncImages] Would upload compressed image: $file");

                try {
                    $original = Storage::get($file);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    $image = Image::make($original)->encode($extension, 75);

                    // Http::withHeaders(['X-SYNC-TOKEN' => $token])
                    //     ->attach('file', (string) $image, basename($file))
                    //     ->post("$peer/api/image-sync/upload", ['path' => $file]); // 🔴 DEACTIVATED

                    Log::info("[CompressSyncImages] Compressed and ready to upload: $file");
                } catch (\Exception $e) {
                    Log::error("[CompressSyncImages] Upload compression failed: $file — ".$e->getMessage());
                }
            }
        }

        $this->info('✅ Sync complete — no files written (dry-run)');
        Log::info('[CompressSyncImages] Dry-run completed');
    }
}
