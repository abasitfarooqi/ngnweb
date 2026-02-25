<?php

namespace App\Console\Commands;

use App\Imports\NgnOXProductsImport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ImportNgnOXProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ngn:import-ox-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import POS products from an Excel file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting the product import...');

        // Define the path to the Excel file in app/Imports folder
        // $filePath = app_path('Imports/pos.xlsx');

        $filePath = app_path('Imports/2025-04_alloxford_data.xlsx');
        // $filePath = app_path('Imports/oxford_importer_structure_example.xlsx');

        // Check if the file exists
        if (! File::exists($filePath)) {
            $this->error('File not found: '.$filePath);

            return Command::FAILURE;
        }

        // Try to import the file
        try {
            Excel::import(new NgnOXProductsImport, $filePath);
            $this->info('OX Products imported successfully.');
        } catch (\Exception $e) {
            $this->error('Error during import: '.$e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
