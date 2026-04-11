<?php

// app/Console/Kernel.php

namespace App\Console;

use App\Jobs\ProcessMitRetryJob;
use App\Jobs\ProduceNgnMitQueueJob;
use App\Jobs\SendWeeklyMitClosingReportJob;
use App\Jobs\SendWeeklyMitOpeningReportJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ImportNgnPOSProducts::class,
        \App\Console\Commands\ImportNgnOXProducts::class,
        \App\Console\Commands\ExportNgnPOSProducts::class,
        \App\Console\Commands\ImportStockMovementAdderUpdater::class,
        \App\Console\Commands\ImportStockMovementHandler::class,
        \App\Console\Commands\SendBatchEmails::class,
        \App\Console\Commands\GlobalStockCommand::class,
        \App\Console\Commands\AdministrativeEmailsCommand::class,
        \App\Console\Commands\PopulateMOTNotifier::class,
        \App\Console\Commands\SendMOTNotifications::class,
        \App\Console\Commands\FtpSync::class,
        \App\Console\Commands\SendWeeklyBusiestDaysReport::class,
        \App\Console\Commands\SendQuarterlyVehicleVisitsReport::class,
        \App\Console\Commands\CustomerDocsSecurelyTransferSFTP::class,
        \App\Console\Commands\ImportSparePartsCatalogue::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Keep Livewire temporary uploads tidy so stale chunks don't block new uploads.
        $schedule->call(function (): void {
            $diskName = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');
            $directory = trim((string) (config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp'), '/');
            $expirySeconds = 10 * 60;
            $now = now()->timestamp;

            try {
                $disk = Storage::disk($diskName);
                foreach ($disk->allFiles($directory) as $path) {
                    $age = $now - (int) $disk->lastModified($path);
                    if ($age > $expirySeconds) {
                        $disk->delete($path);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Livewire temp cleanup failed', [
                    'disk' => $diskName,
                    'directory' => $directory,
                    'message' => $e->getMessage(),
                ]);
            }
        })->everyMinute()->name('livewire-temp-cleanup');

        // // MIT Queue Generation - Mondays at 04:00
        // $schedule->job(new ProduceNgnMitQueueJob)
        //     ->weeklyOn(1, config('judopay.mit.queue_produce_time'))
        //     ->description('Produces the NGN MIT Queue using config-driven timing');

        // // Auto-add NGN MIT Queue to Judopay MIT Queue - Mondays at 08:30
        // $schedule->command('mit:auto-add-to-queue')
        //     ->weeklyOn(1, config('judopay.mit.auto_add_to_queue_time'))
        //     ->description('Automatically adds current week NGN MIT Queue items to Judopay MIT Queue');

        // // MIT Weekly Opening Report - Mondays at 08:45 (after queue generation)
        // $schedule->job(new SendWeeklyMitOpeningReportJob)
        //     ->weeklyOn(1, '08:45')
        //     ->description('Send weekly MIT opening report - expected collections for the week');

        // // // MIT Weekly Closing Report - Sundays at 23:45 (end of week summary)
        // // $schedule->job(new SendWeeklyMitClosingReportJob())
        // //     ->weeklyOn(0, '23:45')
        // //     ->description('Send weekly MIT closing report - collections vs declines summary');

        // // MIT Queue Processing - REMOVED: Now using data-driven individual job scheduling
        // // Each MIT payment schedules its own job at exact fire time via ProcessSingleMitPaymentJob

        // // MIT Retry Processing - config-driven retry for payment declines
        // $retryConfig = config('judopay.mit.retry_system');
        // if ($retryConfig['enabled']) {
        //     $retryTime = $retryConfig['retry_time'];
        //     $schedule->job(new ProcessMitRetryJob)
        //         ->dailyAt($retryTime)
        //         ->description('Process MIT retries for failed payments');
        // }

        // // PCN Email Job
        // $schedule->command('app:pcn-email-job')
        //     ->dailyAt('00:00');

        // // Renting Invoice Generate
        // $schedule->command('app:renting-invoice-generate')
        //     ->dailyAt('01:05');

        // // Finance Application forward
        // $schedule->command('app:instalment-notification')
        //     ->weeklyOn(4, '00:10');

        // // Renting Reminder
        // $schedule->command('app:renting-invoice-check')
        //     ->dailyAt('01:15');

        // // Vehicle DVLA Check
        // $schedule->command('dvla:check')
        //     ->dailyAt('01:20');

        // // Send Due Invoices Reminders
        // $schedule->command('email:due-invoices')
        //     ->dailyAt('01:25');

        // // // Global Stock Update DUE TO NONE USE
        // // $schedule->command('app:global-stock')
        // //     ->dailyAt('00:30');

        // // Administrative Emails Active Rentings To Thiago
        // $schedule->command('app:administrative-emails-command WeeklyRentingReport')
        //     ->weeklyOn(6, '17:45');

        // // Administrative Emails Active Rentings To Thiago
        // $schedule->command('app:administrative-emails-command WeeklyClubTopupReport')
        //     ->weeklyOn(1, '00:10'); // Monday just after midnight

        // // JudoPay Weekly Reports - Run on Mondays at 09:00 (reporting previous week)
        // $schedule->command('judopay:report-weekly-total-onboarded')
        //     ->weeklyOn(1, '09:00')
        //     ->description('Sends weekly JudoPay total onboarded report on Mondays at 9 AM.');

        // $schedule->command('judopay:report-weekly-finance-payments-received')
        //     ->weeklyOn(1, '09:05')
        //     ->description('Sends weekly JudoPay finance payments received report on Mondays at 9:05 AM.');

        // $schedule->command('judopay:report-weekly-finance-declined-payments')
        //     ->weeklyOn(1, '09:10')
        //     ->description('Sends weekly JudoPay finance declined payments report on Mondays at 9:10 AM.');

        // $schedule->command('judopay:report-weekly-finance-new-customers')
        //     ->weeklyOn(1, '09:15')
        //     ->description('Sends weekly JudoPay finance new customers report on Mondays at 9:15 AM.');

        // $schedule->command('judopay:report-weekly-rental-new-customers')
        //     ->weeklyOn(1, '09:20')
        //     ->description('Sends weekly JudoPay rental new customers report on Mondays at 9:20 AM.');

        // $schedule->command('judopay:report-weekly-rental-declined-payments')
        //     ->weeklyOn(1, '09:25')
        //     ->description('Sends weekly JudoPay rental declined payments report on Mondays at 9:25 AM.');

        // // Send PCN Reminders
        // // $schedule->command('app:send-pcn-reminders')
        // // ->weekly()
        // // ->at('19:00');

        // // Populate MOT Notifier
        // $schedule->command('mot:populate-notifier')
        //     ->dailyAt('02:00')
        //     ->description('Populates the MOT notifier table daily at 1 AM.');

        // // Send MOT Notifications
        // $schedule->command('mot:send-notifications')
        //     ->dailyAt('03:00')
        //     ->description('Sends out MOT notifications daily at 2 AM.');

        // // Motorbike Monthly Sales Report Runs on 1st of every month at 9am
        // $schedule->command('report:monthly-sales')
        //     ->monthlyOn(1, '09:00')
        //     ->description('Sends out the monthly sales report on the 1st of every month at 9 AM.');

        // // FTP Sync
        // $schedule->command('ftp:sync')
        //     ->dailyAt('04:00')
        //     ->description('Syncs the FTP directories daily at 3 AM.');

        // // Customer Docs Transfer - Move files from public to private every 2 days
        // $schedule->command('storage:customer-docs-transfer-sftp')
        //     ->cron('0 5 */2 * *')
        //     ->description('Moves customer documents from public/customers to private/customers every 2 days at 5:00 AM.');

        // // Send Weekly Busiest Days Report
        // // $schedule->command('app:send-weekly-busiest-days-report')
        // //     ->weeklyOn(6, '04:00')
        // //     ->description('Sends out the busiest days report weekly at 4 AM.');

        // $schedule->command('app:send-annual-busiest-days-report')
        //     ->monthlyOn(1, '07:00')
        //     ->description('Sends out the annual busiest days report on the 1st of every month at 7 AM.');

        // // Quarterly Vehicle Visits Report - Runs on 10th of Feb, May, Aug, Nov at 7:30 AM
        // // Reports last 3 months of data
        // $schedule->command('app:send-quarterly-vehicle-visits-report')
        //     ->cron('30 7 10 2,5,8,11 *')
        //     ->description('Sends the quarterly vehicle visits report on the 10th of February, May, August, and November at 7:30 AM (reports last 3 months).');

        // $schedule->command('email:due-invoices')->dailyAt('09:00');

        // // One-off: send NGN Club festive hours email on 26 Nov 2025 at 19:30
        // $schedule->command('ngn:club-festive-hours')
        //     ->cron('30 19 26 11 *')
        //     ->description('One-time run: sends NGN Club festive hours email (EN + PT) to all members on 26 November at 7:30 PM.');

    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // Block dangerous migration commands
        $this->blockDangerousCommands();
    }

    /**
     * Block potentially dangerous migration commands
     */
    private function blockDangerousCommands(): void
    {
        $dangerousCommands = [
            'migrate:fresh',
            'migrate:refresh',
            'migrate:reset',
            'db:wipe',
            'migrate:fresh --seed',
            'migrate:refresh --seed',
            'migrate:reset --seed',
            'db:wipe --seed',
            'migrate:fresh --seed --force',
            'migrate:refresh --seed --force',
            'migrate:reset --seed --force',
            'db:wipe --seed --force',
        ];

        foreach ($dangerousCommands as $command) {
            $this->app['events']->listen('Illuminate\Console\Events\CommandStarting', function ($event) use ($command) {
                if ($event->command === $command) {
                    $this->error('⛔ This command is blocked for safety reasons.');
                    $this->error('Please use individual migrations or contact your administrator.');
                    exit(1);
                }
            });
        }
    }

    protected function bootstrappers()
    {
        return array_merge(
            [\Bugsnag\BugsnagLaravel\OomBootstrapper::class],
            parent::bootstrappers(),
        );
    }
}
