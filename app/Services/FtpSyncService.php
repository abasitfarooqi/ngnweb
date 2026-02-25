<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;

class FtpSyncService
{
    protected Filesystem $filesystemA_storageApp;

    protected Filesystem $filesystemB_storageApp;

    protected Filesystem $filesystemA_storageAppPublic;

    protected Filesystem $filesystemB_storageAppPublic;

    protected Filesystem $filesystemA_public;

    protected Filesystem $filesystemB_public;

    protected string $currentDomain;

    public function __construct()
    {
        // Use config() instead of env()
        $host = config('filesystems.disks.datasync.host');
        $username = config('filesystems.disks.datasync.username');
        $password = config('filesystems.disks.datasync.password');
        $port = (int) config('filesystems.disks.datasync.port', 22);

        if (! $host || ! $username || ! $password) {
            Log::error('FtpSyncService: Missing SFTP credentials in config');
            throw new \Exception('SFTP credentials not properly configured.');
        }

        $connectionProviderA = SftpConnectionProvider::fromArray([
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'timeout' => 30,
        ]);

        $connectionProviderB = SftpConnectionProvider::fromArray([
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'port' => $port,
            'timeout' => 30,
        ]);

        $this->filesystemA_storageApp = new Filesystem(new SftpAdapter($connectionProviderA, '/applications/nqfkhvtysa/public_html/storage/app'));
        $this->filesystemA_storageAppPublic = new Filesystem(new SftpAdapter($connectionProviderA, '/applications/nqfkhvtysa/public_html/storage/app/public'));
        $this->filesystemA_public = new Filesystem(new SftpAdapter($connectionProviderA, '/applications/nqfkhvtysa/public_html/public'));

        $this->filesystemB_storageApp = new Filesystem(new SftpAdapter($connectionProviderB, '/applications/keqdryartf/public_html/storage/app'));
        $this->filesystemB_storageAppPublic = new Filesystem(new SftpAdapter($connectionProviderB, '/applications/keqdryartf/public_html/storage/app/public'));
        $this->filesystemB_public = new Filesystem(new SftpAdapter($connectionProviderB, '/applications/keqdryartf/public_html/public'));

        $appUrl = env('APP_URL');
        if (str_contains($appUrl, 'nqfkhvtysa')) {
            $this->currentDomain = 'A';
        } elseif (str_contains($appUrl, 'keqdryartf')) {
            $this->currentDomain = 'B';
        } else {
            Log::error("FtpSyncService: Unknown current domain from APP_URL: $appUrl");
            $this->currentDomain = 'A'; // fallback
        }
    }

    /**
     * Simulate upload by logging actions instead of actual upload.
     */
    public function uploadFile(string $localFullPath): bool
    {
        try {
            if (! file_exists($localFullPath)) {
                Log::error("FtpSyncService: Local file does not exist: $localFullPath");

                return false;
            }

            if ($this->startsWithStorageAppPublic($localFullPath)) {
                $baseFolder = 'storage/app/public';
            } elseif ($this->startsWithStorageApp($localFullPath)) {
                $baseFolder = 'storage/app';
            } elseif ($this->startsWithPublic($localFullPath)) {
                $baseFolder = 'public';
            } else {
                Log::error("FtpSyncService: File outside allowed base folders: $localFullPath");

                return false;
            }

            $relativePath = $this->getRelativePath($localFullPath, $baseFolder);

            if ($this->currentDomain === 'A') {
                $targetDomain = 'B';
                $targetFilesystemDesc = match ($baseFolder) {
                    'storage/app/public' => 'Domain B storage/app/public',
                    'storage/app' => 'Domain B storage/app',
                    'public' => 'Domain B public',
                };
            } else {
                $targetDomain = 'A';
                $targetFilesystemDesc = match ($baseFolder) {
                    'storage/app/public' => 'Domain A storage/app/public',
                    'storage/app' => 'Domain A storage/app',
                    'public' => 'Domain A public',
                };
            }

            Log::info("FtpSyncService: [DRY RUN] Would upload local file '$localFullPath'");
            Log::info("FtpSyncService: [DRY RUN] Detected base folder: $baseFolder");
            Log::info("FtpSyncService: [DRY RUN] Relative path on remote: $relativePath");
            Log::info("FtpSyncService: [DRY RUN] Target domain: $targetDomain");
            Log::info("FtpSyncService: [DRY RUN] Target filesystem: $targetFilesystemDesc");

            // Uncomment below to activate actual upload

            $targetFilesystem = null;

            if ($this->currentDomain === 'A') {
                $targetFilesystem = match ($baseFolder) {
                    'storage/app/public' => $this->filesystemB_storageAppPublic,
                    'storage/app' => $this->filesystemB_storageApp,
                    'public' => $this->filesystemB_public,
                };
            } else {
                $targetFilesystem = match ($baseFolder) {
                    'storage/app/public' => $this->filesystemA_storageAppPublic,
                    'storage/app' => $this->filesystemA_storageApp,
                    'public' => $this->filesystemA_public,
                };
            }

            $stream = fopen($localFullPath, 'r');
            if ($stream === false) {
                Log::error("FtpSyncService: Failed to open local file stream: $localFullPath");

                return false;
            }

            $writeResult = $targetFilesystem->writeStream($relativePath, $stream);
            fclose($stream);

            if ($writeResult === false) {
                Log::error("FtpSyncService: Failed to write file to remote: $relativePath");

                return false;
            }

            Log::info("FtpSyncService: Successfully uploaded $relativePath to remote domain");

            return true;

        } catch (\Exception $e) {
            Log::error('FtpSyncService: Exception during uploadFile: '.$e->getMessage());

            return false;
        }
    }

    private function startsWithStorageAppPublic(string $path): bool
    {
        return str_contains($path, DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'public');
    }

    private function startsWithStorageApp(string $path): bool
    {
        if ($this->startsWithStorageAppPublic($path)) {
            return false;
        }

        return str_contains($path, DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.'app');
    }

    private function startsWithPublic(string $path): bool
    {
        return str_contains($path, DIRECTORY_SEPARATOR.'public');
    }

    private function getRelativePath(string $fullPath, string $baseFolder): string
    {
        $pos = strpos($fullPath, DIRECTORY_SEPARATOR.$baseFolder.DIRECTORY_SEPARATOR);
        if ($pos === false) {
            return basename($fullPath);
        }

        return substr($fullPath, $pos + strlen($baseFolder) + 2);
    }
}
