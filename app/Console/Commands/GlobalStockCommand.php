<?php

namespace App\Console\Commands;

use App\Mail\CronJobReportMailer;
use App\Models\NgnProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GlobalStockCommand extends Command
{
    protected $signature = 'app:global-stock';

    protected $description = 'Update global stock for all products based on stock movements';

    public function handle()
    {
        $this->info('Starting global stock update...');

        // Get all products with their total stock movements
        $products = NgnProduct::select('ngn_products.id')
            ->leftJoin('ngn_stock_movements', 'ngn_products.id', '=', 'ngn_stock_movements.product_id')
            ->groupBy('ngn_products.id')
            ->select('ngn_products.id',
                DB::raw('COALESCE(SUM(ngn_stock_movements.in), 0) - COALESCE(SUM(ngn_stock_movements.out), 0) as global_stock'))
            ->get();

        $count = 0;
        $positiveStock = 0;
        $zeroStock = 0;
        $negativeStock = 0;
        $totalStock = 0;

        $status_message_success = 'Global stock update completed successfully!';
        $status_message_error = 'Global stock update encountered an issue!';

        $status_message = $status_message_success;

        foreach ($products as $product) {
            try {
                NgnProduct::where('id', $product->id)
                    ->update(['global_stock' => $product->global_stock]);

                $totalStock += $product->global_stock;

                if ($product->global_stock > 0) {
                    $positiveStock++;
                } elseif ($product->global_stock < 0) {
                    $negativeStock++;
                } else {
                    $zeroStock++;
                }

                $count++;

            } catch (\Exception $e) {
                Log::error("Error updating global stock for product ID {$product->id}: ".$e->getMessage());
                $status_message = $status_message_error;
            }

        }

        // Display summary
        $this->info("\nStock Update Summary:");
        $this->info('------------------------');
        $this->info("Total products processed: {$count}");
        $this->info("Products with positive stock: {$positiveStock}");
        $this->info("Products with zero stock: {$zeroStock}");
        $this->info("Products with negative stock: {$negativeStock}");
        $this->info("Total stock across all products: {$totalStock}");
        $this->info('------------------------');
        $this->info($status_message);

        // Send email to IT Team with the summary
        $data = [
            'title' => 'Global Stock Update',
            'status' => $status_message,
            'data' => [
                'total_products' => $count,
                'positive_stock' => $positiveStock,
                'zero_stock' => $zeroStock,
                'negative_stock' => $negativeStock,
                'total_stock' => $totalStock,
            ],
        ];

        Log::info('Global Stock Update Data: ', $data);

        try {
            Mail::to(['support@neguinhomotors.co.uk', 'admin@neguinhomotors.co.uk'])->send(new CronJobReportMailer($data));
        } catch (\Exception $e) {
            Log::error('Error sending email: '.$e->getMessage());
            Mail::to('support@neguinhomotors.co.uk')->send(new CronJobReportMailer($data));
        }

        $this->info('Email sent to IT Team with the summary');

    }
}
