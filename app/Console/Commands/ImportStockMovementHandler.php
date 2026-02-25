<?php

namespace App\Console\Commands;

use App\Imports\StockHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ImportStockMovementHandler extends Command
{
    protected $signature = 'import:stock-movement-handler';

    protected $description = 'Import stock movements, UPDATES The Stock (overwrites the existing stock) with our current Physical Global and Branches Stock ';

    public function handle()
    {
        // Define the hardcoded path to the Excel file in the app/Imports folder
        $filePath = app_path('Imports/NEW-ADDED-AND-STOCK-UPDATES-11-16-2024.xlsx');

        // Check if the file exists
        if (! File::exists($filePath)) {
            $this->error('File not found: '.$filePath);

            return Command::FAILURE;
        }

        try {
            // Get the value of update-with-zero from the request or default to false
            $updateWithZero = $this->confirm('Do you want to update with zero stock?', false);

            // Pass the option to the StockHandler
            Excel::import(new StockHandler($updateWithZero), $filePath);

            $this->info('Stock movements imported successfully!');
        } catch (\Exception $e) {
            $this->error('Error importing stock movements: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
