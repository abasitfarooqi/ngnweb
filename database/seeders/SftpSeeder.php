<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SftpSeeder extends Seeder
{
    public function run()
    {
        // Directory on SFTP server and local path
        $sftpDirectory = '/'; // Adjust if your files are in a specific subdirectory
        $localDirectory = storage_path('app/files/');

        // Ensure the local directory exists
        if (! file_exists($localDirectory)) {
            mkdir($localDirectory, 0755, true);
        }

        try {
            // List all files in the SFTP directory
            $files = Storage::disk('ftp1')->files($sftpDirectory);

            foreach ($files as $file) {
                $fileName = basename($file);
                $localPath = $localDirectory.$fileName;

                // Check if the file exists locally; if not, download from SFTP
                if (! file_exists($localPath)) {
                    $fileContents = Storage::disk('ftp1')->get($file);

                    // Save file locally
                    Storage::disk('local')->put('files/'.$fileName, $fileContents);

                    Log::info('File downloaded and saved locally: '.$fileName);
                } else {
                    Log::info('File already exists locally: '.$fileName);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to download files: '.$e->getMessage());
        }
    }
}
