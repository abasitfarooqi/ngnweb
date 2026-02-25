<?php

namespace App\Imports;

use App\Models\NgnProduct;
use App\Models\NgnStockMovement;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockHandler implements ToModel, WithHeadingRow
{
    // Mapping of branch names to branch IDs
    protected $branchIds = [
        'catford' => 1,
        'tooting' => 2,
        'sutton' => 3, // Added Sutton branch
    ];

    // Option to decide whether to process transactions for zero stock or not
    protected $updateWithZero;

    // Constructor to accept the updateWithZero flag
    public function __construct($updateWithZero = false)
    {
        $this->updateWithZero = $updateWithZero;
    }

    // Method to handle each row of data in the Excel sheet
    public function model(array $row)
    {
        Log::info('Processing Row:', $row);

        try {
            // Extract SKU and stock values for each branch from the row
            $sku = trim($row['sku'] ?? '');
            $catfordStockExcel = (int) ($row['catford'] ?? 0);
            $tootingStockExcel = (int) ($row['tooting'] ?? 0);
            $suttonStockExcel = (int) ($row['sutton'] ?? 0); // Added Sutton stock

            // Find the product by SKU
            $product = NgnProduct::where('sku', $sku)->first();

            if ($product) {
                // Get existing stock levels for each branch
                $existingCatfordStock = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['catford'])
                    ->sum('in') - NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['catford'])
                    ->sum('out');

                $existingTootingStock = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['tooting'])
                    ->sum('in') - NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['tooting'])
                    ->sum('out');

                $existingSuttonStock = NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['sutton'])
                    ->sum('in') - NgnStockMovement::where('product_id', $product->id)
                    ->where('branch_id', $this->branchIds['sutton'])
                    ->sum('out');

                // Log existing stock levels before processing
                Log::info("Existing Stock Before Processing - Catford: {$existingCatfordStock}, Tooting: {$existingTootingStock}, Sutton: {$existingSuttonStock}");
                Log::info("Excel Stock - Catford: {$catfordStockExcel}, Tooting: {$tootingStockExcel}, Sutton: {$suttonStockExcel}");

                // Track net stock movement for each branch
                $totalStock = $existingCatfordStock + $existingTootingStock + $existingSuttonStock;

                // Process each branch independently
                $totalStock = $this->processBranchStockMovement($product, 'catford', $catfordStockExcel, $existingCatfordStock, $totalStock);
                $totalStock = $this->processBranchStockMovement($product, 'tooting', $tootingStockExcel, $existingTootingStock, $totalStock);
                $totalStock = $this->processBranchStockMovement($product, 'sutton', $suttonStockExcel, $existingSuttonStock, $totalStock);

                // Final global stock calculation based on updated stock levels
                $product->global_stock = $totalStock;
                $product->save();

                // Final log after stock update
                Log::info("Product {$product->name} - Final Global Stock: {$product->global_stock}");

            } else {
                // Log if the product is not found in the database
                Log::warning('Product not found for SKU:', ['sku' => $sku]);
            }

        } catch (\Exception $e) {
            // Log any errors during the process
            Log::error('Error processing row: '.$e->getMessage(), ['row' => $row]);
        }
    }

    // Method to process stock movement for each branch
    protected function processBranchStockMovement(NgnProduct $product, string $branch, int $excelStock, int $existingStock, int $totalStock)
    {
        // Skip processing if Excel stock is zero and updateWithZero is false
        if ($excelStock === 0 && ! $this->updateWithZero) {
            Log::info("No stock movement for {$branch} (Excel stock is 0 and updateWithZero is false).");

            return $totalStock;
        }

        // Calculate the stock difference for the branch
        $stockDiff = $excelStock - $existingStock;

        // Log the stock difference
        Log::info("Stock Difference for {$branch} - Excel: {$excelStock}, Existing: {$existingStock}, Difference: {$stockDiff}");

        // If the stock difference is non-zero, update the stock movement
        if ($stockDiff !== 0) {
            $totalStock = $this->updateStockMovement($product, $this->branchIds[$branch], $excelStock, $stockDiff, $totalStock);
        } else {
            Log::info("No stock movement for {$branch} (No difference in stock).");
        }

        return $totalStock; // Return the updated total stock after processing
    }

    // Helper method to update stock movement for a specific branch
    protected function updateStockMovement(NgnProduct $product, int $branchId, float $excelStock, float $stockDiff, int $totalStock)
    {
        // Check if transactions exist for this branch and product
        $transactionsExist = NgnStockMovement::where('product_id', $product->id)
            ->where('branch_id', $branchId)->exists();

        // If no transactions exist, create initial stock movements based on Excel data
        if (! $transactionsExist && $excelStock > 0) {
            $this->createStockMovement($product, $branchId, $excelStock, 'IN', 'Opening Stock');
        }

        // Handle stock differences (positive or negative) for the branch
        if ($stockDiff > 0) {
            // If the stock difference is positive, it's a stock adjustment
            $this->createStockMovement($product, $branchId, $stockDiff, 'IN', 'Stock Adjustment');
        } elseif ($stockDiff < 0) {
            // If the stock difference is negative, it's a stock out (e.g., sale)
            $this->createStockMovement($product, $branchId, abs($stockDiff), 'OUT', 'Shop Sale');
        }

        // Update total stock after processing the movement
        return $totalStock + $stockDiff;
    }

    // Method to create stock movement entries in the database
    protected function createStockMovement(NgnProduct $product, int $branchId, float $stock, string $type, string $transactionType)
    {
        NgnStockMovement::create([
            'product_id' => $product->id,
            'branch_id' => $branchId,
            'in' => $type === 'IN' ? $stock : 0,
            'out' => $type === 'OUT' ? $stock : 0,
            'transaction_type' => $transactionType,
            'transaction_date' => now(),
            'user_id' => 93, // Adjust user ID as needed
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Log::info("Stock movement {$type} for product: {$product->name} at branch ID: {$branchId} with transaction type: {$transactionType}");
    }
}
