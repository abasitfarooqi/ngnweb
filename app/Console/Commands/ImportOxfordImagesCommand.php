<?php

namespace App\Console\Commands;

use App\Imports\OxfordImageImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportOxfordImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-oxford-images {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ONE TIME ONLY: Import Oxford Images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Oxford Images import...');

        // Create storage directory if it doesn't exist
        if (! Storage::disk('public')->exists('products/oxford')) {
            Storage::disk('public')->makeDirectory('products/oxford');
            $this->info('Created directory: products/oxford');
        }

        // Create storage symlink if it doesn't exist
        if (! file_exists(public_path('storage'))) {
            $this->call('storage:link');
            $this->info('Created storage symlink.');
        }

        $fileName = $this->argument('filename');
        $filePath = "app/Imports/{$fileName}";

        if (! file_exists($filePath)) {
            $this->error("Excel file not found at path: {$filePath}");

            return 1; // Exit with error code
        }

        try {
            Excel::import(new OxfordImageImport, $filePath);
            $this->info('Import completed successfully!');
        } catch (\Exception $e) {
            $this->error('Import failed: '.$e->getMessage());

            return 1; // Exit with error code
        }

        return 0; // Exit with success code
    }
}
