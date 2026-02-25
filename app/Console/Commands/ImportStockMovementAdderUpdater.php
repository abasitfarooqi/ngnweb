<?php

namespace App\Console\Commands;

use App\Imports\StockAdderUpdater;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ImportStockMovementAdderUpdater extends Command
{
    protected $signature = 'import:stock-movement-adder-updater';

    protected $description = 'Import stock movements from an Excel file, Add New Stock, Keep Existing Stock in Global and Branches Stock';

    public function handle()
    {
        // Define the hardcoded path to the Excel file in the app/Imports folder
        $filePath = app_path('Imports/pos_stock_handler.xlsx');

        // Check if the file exists
        if (! File::exists($filePath)) {
            $this->error('File not found: '.$filePath);

            return Command::FAILURE;
        }

        try {
            Excel::import(new StockAdderUpdater, $filePath);
            $this->info('Stock movements imported successfully!');
        } catch (\Exception $e) {
            $this->error('Error importing stock movements: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
