<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KillPill extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ngn:killpill';

    /**
     * The console command description.
     */
    protected $description = 'Delete specific migration records and drop listed Judopay tables (guarded by cutoff date).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Cutoff: command is allowed to run only on or before 05 Oct 2025 (inclusive)
        $cutoff = Carbon::create(2025, 10, 5, 23, 59, 59, config('app.timezone'));
        $now = Carbon::now(config('app.timezone'));

        if ($now->greaterThan($cutoff)) {
            $this->warn('Killpill is disabled after 05 Oct 2025. No actions performed.');

            return self::SUCCESS;
        }

        $migrationsToDelete = [
            '2025_08_25_192040_create_judopay-onboardings_table',
            '2025_08_25_202656_create_subscriptions_table',
            '2025_08_25_204000_create_cit_payment_sessions_table',
            '2025_08_25_205500_create_mit_payment_sessions_table',
            '2025_08_25_205600_create_payment_session_outcomes_table',
            '2025_09_27_000002_create_judopay_enquiry_records_table',
            '2025_10_03_020115_create_judopay_cit_accesses_able',
        ];

        $tablesToDrop = [
            'judopay_enquiry_records',
            'judopay_cit_accesses',
            'judopay_payment_session_outcomes',
            'judopay_cit_payment_sessions',
            'judopay_mit_payment_sessions',
            'judopay_subscriptions',
            'judopay_onboardings',
        ];

        DB::beginTransaction();
        try {
            // Delete migration rows idempotently
            $deleted = DB::table('migrations')
                ->whereIn('migration', $migrationsToDelete)
                ->delete();
            $this->info("Deleted {$deleted} migration record(s).");

            // Drop tables if exist, in safe order (children first if any FKs)
            foreach ($tablesToDrop as $table) {
                if (Schema::hasTable($table)) {
                    Schema::drop($table);
                    $this->info("Dropped table: {$table}");
                } else {
                    $this->line("Table not found (skipped): {$table}");
                }
            }

            DB::commit();
            $this->info('Killpill completed.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Killpill failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
