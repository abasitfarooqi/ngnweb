<?php

namespace App\Console\Commands;

use App\Exports\NgnPOSProductsExport;
use App\Models\NgnProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage; // Correctly import the NgnProduct model
use Maatwebsite\Excel\Facades\Excel;

class ExportNgnPOSProducts extends Command
{
    protected $signature = 'export:ngn-products';

    protected $description = 'Export NgnProduct data to an Excel file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $fileName = 'fetch_ngn_pos_products.xlsx';
        $localPath = 'exports/'.$fileName;
        $destinationPath = 'app/Exports/'.$fileName;

        // Save the file to the local storage directory
        Excel::store(new NgnPOSProductsExport, $localPath, 'local');

        // Move the file to the desired directory
        $localFilePath = storage_path('app/'.$localPath);
        if (file_exists($localFilePath)) {
            rename($localFilePath, base_path($destinationPath));
            $this->info("Exported NgnProduct data to {$destinationPath}");
        } else {
            $this->error('Failed to export NgnProduct data.');
        }
    }
}
