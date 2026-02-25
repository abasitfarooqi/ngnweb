<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SyncStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Two-way sync of storage files with peer Laravel app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting full bi-directional sync...');
        $peer = rtrim(env('PEER_DOMAIN'), '/');
        $token = env('SYNC_SECRET');

        $allowedExtensions = ['pdf', 'jpg', 'png', 'jpeg', 'docx'];

        try {
            $localFiles = collect(File::allFiles(storage_path('app')))
                ->mapWithKeys(fn ($f) => [
                    str_replace(storage_path('app').'/', '', $f->getPathname()) => $f->getMTime(),
                ])
                ->filter(function ($file, $path) use ($allowedExtensions) {
                    return in_array(pathinfo($path, PATHINFO_EXTENSION), $allowedExtensions);
                });
        } catch (\Exception $e) {
            $this->warn('Warning: Unable to list some files due to permission issues.');
            Log::warning('Unable to list some files: '.$e->getMessage());
            $localFiles = collect(); // Continue with an empty collection if listing fails
        }

        $this->info("Getting file list from peer: $peer");
        $remoteFiles = Http::withHeaders(['X-SYNC-TOKEN' => $token])
            ->get("$peer/api/sync/files")
            ->json();

        // Ensure local directories match peer's structure
        foreach ($remoteFiles as $file => $mtime) {
            $localDir = dirname($file);
            if (! Storage::exists($localDir)) {
                $this->info("Creating local directory: $localDir");
                Log::info("Creating local directory: $localDir");
                Storage::makeDirectory($localDir);
            }

            if (! isset($localFiles[$file]) || $localFiles[$file] < $mtime) {
                $this->info("Downloading $file ← peer");
                Log::info("Downloading $file from peer");
                $response = Http::withHeaders(['X-SYNC-TOKEN' => $token])
                    ->get("$peer/storage/$file");

                if ($response->ok()) {
                    Storage::put($file, $response->body());
                }
            }
        }

        // Ensure peer directories match local structure
        foreach ($localFiles as $file => $mtime) {
            $remoteDir = dirname($file);
            if (! isset($remoteFiles[$file]) || $remoteFiles[$file] < $mtime) {
                $this->info("Uploading $file → peer");
                Log::info("Uploading $file to peer");
                Http::withHeaders(['X-SYNC-TOKEN' => $token])
                    ->attach('file', Storage::get($file), basename($file))
                    ->post("$peer/api/sync/upload", ['path' => $file]);
            }
        }

        $this->info('Full bi-directional sync complete.');
    }
}
