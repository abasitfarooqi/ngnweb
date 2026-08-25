<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Services\Renting\RentingWeeklyUpdateReportService;
use App\Support\RentingReferralAccess;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('flux-admin.layouts.app')]
#[Title('Weekly follow-up report — Flux Admin')]
class WeeklyUpdateReport extends Component
{
    use WithAuthorization;

    public string $periodKey = '';

    public function mount(RentingWeeklyUpdateReportService $service): void
    {
        if (! RentingReferralAccess::canInvestigate()) {
            $this->authorizeModule('see-menu-rentals');
        }
        $this->periodKey = $service->recentPeriods()[0]['key'] ?? '';
    }

    public function downloadPdf(RentingWeeklyUpdateReportService $service): StreamedResponse
    {
        [$start, $end] = $this->selectedPeriod($service);
        $report = $service->build($start, $end);
        $binary = $service->pdfBinary($report);
        $filename = $service->filename($report);

        return response()->streamDownload(static function () use ($binary): void {
            echo $binary;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function emailDirector(RentingWeeklyUpdateReportService $service): void
    {
        [$start, $end] = $this->selectedPeriod($service);
        $report = $service->build($start, $end);
        $service->send($report);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Report emailed to the director. Customer service was copied.');
    }

    public function render(RentingWeeklyUpdateReportService $service)
    {
        [$start, $end] = $this->selectedPeriod($service);
        $report = $service->build($start, $end);

        return view('flux-admin.pages.rentals.weekly-update-report', [
            'periods' => $service->recentPeriods(),
            'report' => $report,
        ]);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function selectedPeriod(RentingWeeklyUpdateReportService $service): array
    {
        foreach ($service->recentPeriods() as $period) {
            if ($period['key'] === $this->periodKey) {
                return [
                    Carbon::parse($period['start'], config('app.timezone')),
                    Carbon::parse($period['end'], config('app.timezone')),
                ];
            }
        }

        return $service->completedPeriod();
    }
}
