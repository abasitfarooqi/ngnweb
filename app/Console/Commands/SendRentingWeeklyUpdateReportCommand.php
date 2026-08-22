<?php

namespace App\Console\Commands;

use App\Services\Renting\RentingWeeklyUpdateReportService;
use Illuminate\Console\Command;

class SendRentingWeeklyUpdateReportCommand extends Command
{
    protected $signature = 'renting-weekly-updates:report {--send : Email the director and copy customer service} {--open : Use the current Monday–Saturday window even if it has not closed}';

    protected $description = 'Build the weekly rental follow-up snapshot. Use --send to email the director.';

    public function handle(RentingWeeklyUpdateReportService $service): int
    {
        if (! $service->tablesReady()) {
            $this->warn('renting_weekly_updates table is not installed.');

            return self::SUCCESS;
        }

        [$start, $end] = $this->option('open')
            ? $service->openPeriod()
            : $service->completedPeriod();

        $report = $service->build($start, $end);
        $this->info('Period '.$start->format('d M Y H:i').' → '.$end->format('d M Y H:i'));
        $this->info('Entries: '.$report['summary']['entries'].' · Customers: '.$report['summary']['customers']);

        $path = $service->storePdf($report);
        $this->info('PDF stored: '.$path);

        if ($this->option('send')) {
            $service->send($report, $path);
            $this->info('Emailed '.RentingWeeklyUpdateReportService::DIRECTOR_EMAIL.' (cc '.RentingWeeklyUpdateReportService::CC_EMAIL.').');
        }

        return self::SUCCESS;
    }
}
