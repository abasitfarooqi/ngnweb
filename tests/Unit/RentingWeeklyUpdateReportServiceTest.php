<?php

namespace Tests\Unit;

use App\Services\Renting\RentingWeeklyUpdateReportService;
use Carbon\Carbon;
use Tests\TestCase;

class RentingWeeklyUpdateReportServiceTest extends TestCase
{
    public function test_completed_period_closes_on_saturday_afternoon(): void
    {
        $service = new RentingWeeklyUpdateReportService;

        [$start, $end] = $service->completedPeriod(Carbon::parse('2026-08-22 15:45:00', 'Europe/London'));
        $this->assertSame('2026-08-17 09:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-22 15:45:00', $end->format('Y-m-d H:i:s'));

        [$start, $end] = $service->completedPeriod(Carbon::parse('2026-08-21 12:00:00', 'Europe/London'));
        $this->assertSame('2026-08-10 09:00:00', $start->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-15 15:45:00', $end->format('Y-m-d H:i:s'));
    }

    public function test_build_does_not_throw_when_tables_exist(): void
    {
        $service = new RentingWeeklyUpdateReportService;
        [$start, $end] = $service->completedPeriod(Carbon::parse('2026-08-22 16:00:00', 'Europe/London'));
        $report = $service->build($start, $end);

        $this->assertArrayHasKey('entries', $report);
        $this->assertArrayHasKey('accounts', $report);
        $this->assertArrayHasKey('email_accounts', $report);
        $this->assertArrayHasKey('intro', $report);
        $this->assertArrayHasKey('summary', $report);
        $this->assertGreaterThanOrEqual(0, $report['summary']['entries']);
    }
}
