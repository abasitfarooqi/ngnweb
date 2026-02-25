<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestMotNotifierInsert extends Command
{
    protected $signature = 'mot:test-insert';

    protected $description = 'Test direct insertion into ngn_mot_notifier table';

    public function handle()
    {
        try {
            Log::info('Starting test insertion...');

            // Direct insertion using DB facade
            $result = DB::table('ngn_mot_notifier')->insert([
                'motorbike_id' => 1, // Replace with an actual motorbike ID
                'motorbike_reg' => 'TEST123',
                'mot_due_date' => Carbon::now()->addDays(30)->toDateString(),
                'tax_due_date' => Carbon::now()->addDays(60)->toDateString(),
                'insurance_due_date' => Carbon::now()->addDays(90)->toDateString(),
                'customer_name' => 'Test Customer',
                'customer_contact' => '1234567890',
                'customer_email' => 'test@example.com',
                'mot_notify_email' => true,
                'mot_notify_phone' => false,
                'mot_status' => 'Valid',
                'notes' => 'Test insertion',
                'mot_is_notified_30' => true,
                'mot_is_notified_10' => false,
                'mot_last_email_notification_date' => Carbon::now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Test insertion result: '.($result ? 'Success' : 'Failed'));

            // Verify the insertion
            $count = DB::table('ngn_mot_notifier')->count();
            Log::info('Total records in ngn_mot_notifier: '.$count);

            $this->info('Test insertion completed. Check logs for details.');
        } catch (\Exception $e) {
            Log::error('Error in test insertion: '.$e->getMessage());
            Log::error($e->getTraceAsString());
            $this->error('Error: '.$e->getMessage());
        }
    }
}
