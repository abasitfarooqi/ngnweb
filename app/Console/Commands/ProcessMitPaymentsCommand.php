<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMitPayments;
use Illuminate\Console\Command;

// It should not be used.
class ProcessMitPaymentsCommand extends Command
{
    protected $signature = 'judopay:process-mit-payments';

    protected $description = 'Process recurring MIT payments for eligible subscriptions';

    public function handle(): int
    {
        $this->info('Dispatching MIT payment processing job...');

        ProcessMitPayments::dispatch();

        $this->info('MIT payment processing job dispatched successfully.');

        return Command::SUCCESS;
    }
}
