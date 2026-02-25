<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;

class FtpSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bi-directional sync using SFTP';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // Set up SFTP connection for Directory A
            $connectionProviderA = SftpConnectionProvider::fromArray([
                'host' => env('DATASYNC_FTP_HOST'),
                'username' => env('DATASYNC_FTP_USERNAME'),
                'password' => env('DATASYNC_FTP_PASSWORD'),
                'port' => (int) env('DATASYNC_FTP_PORT', 22),
                'timeout' => 30,
            ]);
            $filesystemA = new Filesystem(new SftpAdapter($connectionProviderA, '/applications/nqfkhvtysa/public_html/storage/app'));

            // Set up SFTP connection for Directory B
            $connectionProviderB = SftpConnectionProvider::fromArray([
                'host' => env('DATASYNC_FTP_HOST'),
                'username' => env('DATASYNC_FTP_USERNAME'),
                'password' => env('DATASYNC_FTP_PASSWORD'),
                'port' => (int) env('DATASYNC_FTP_PORT', 22),
                'timeout' => 30,
            ]);
            $filesystemB = new Filesystem(new SftpAdapter($connectionProviderB, '/applications/keqdryartf/public_html/storage/app'));

            // Sync from A to B
            $this->syncFiles($filesystemA, $filesystemB, 'A', 'B');

            // Sync from B to A
            $this->syncFiles($filesystemB, $filesystemA, 'B', 'A');

            $this->info('Bi-directional sync complete.');
            Log::info('Bi-directional sync complete.');

            return 0;
        } catch (\Exception $e) {
            Log::error('Error during SFTP sync: '.$e->getMessage());

            return 1;
        }
    }

    private function syncFiles($source, $destination, $sourceName, $destinationName)
    {
        try {
            $sourceFiles = iterator_to_array($source->listContents('', true));
            $destinationFiles = iterator_to_array($destination->listContents('', true));

            foreach ($sourceFiles as $file) {
                if ($file->isFile()) {
                    $path = $file->path();
                    $sourceTimestamp = $file->lastModified();
                    $destinationFile = array_filter($destinationFiles, fn ($f) => $f->path() === $path);

                    if (empty($destinationFile) || reset($destinationFile)->lastModified() < $sourceTimestamp) {
                        // Copy file from source to destination
                        $stream = $source->readStream($path);
                        $destination->writeStream($path, $stream);
                        fclose($stream);
                        $this->info("Synced: $path from $sourceName to $destinationName");
                        Log::info("Synced: $path from $sourceName to $destinationName");
                    } else {
                        $this->info("File $path does not need to be synced.");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error during file sync: '.$e->getMessage());
        }
    }
}
