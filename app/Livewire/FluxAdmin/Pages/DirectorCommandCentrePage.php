<?php

namespace App\Livewire\FluxAdmin\Pages;

use App\Services\Director\DirectorCommandCentre;
use App\Support\RentingReferralAccess;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Director panel — Flux Admin')]
class DirectorCommandCentrePage extends Component
{
    #[Url(as: 'module', history: true)]
    public string $module = 'overview';

    #[Url(as: 'focus', history: true)]
    public string $focus = 'all';

    #[Url(as: 'from', history: true)]
    public string $from = '';

    #[Url(as: 'to', history: true)]
    public string $to = '';

    public function mount(): void
    {
        if (! RentingReferralAccess::canInvestigate()) {
            abort(403, 'Only Thiago and Super Admins can open the director panel.');
        }

        $this->module = in_array($this->module, DirectorCommandCentre::MODULES, true) ? $this->module : 'overview';
        if ($this->from === '' && $this->to === '') {
            $this->from = now()->startOfWeek(Carbon::MONDAY)->toDateString();
            $this->to = now()->toDateString();
        }
    }

    public function setModule(string $module): void
    {
        $this->module = in_array($module, DirectorCommandCentre::MODULES, true) ? $module : 'overview';
        $this->focus = 'all';
    }

    public function setFocus(string $focus): void
    {
        $this->focus = $focus !== '' ? $focus : 'all';
    }

    public function setPreset(string $preset): void
    {
        [$from, $to] = match ($preset) {
            'week' => [now()->startOfWeek(Carbon::MONDAY)->toDateString(), now()->toDateString()],
            'month' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
            'year' => [now()->startOfYear()->toDateString(), now()->toDateString()],
            'days30' => [now()->subDays(30)->toDateString(), now()->toDateString()],
            default => [$this->from, $this->to],
        };

        $this->from = $from;
        $this->to = $to;
    }

    public function resetFilters(): void
    {
        $this->module = 'overview';
        $this->focus = 'all';
        $this->from = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $this->to = now()->toDateString();
    }

    public function render()
    {
        $panel = DirectorCommandCentre::make([
            'module' => $this->module,
            'focus' => $this->focus,
            'from' => $this->from,
            'to' => $this->to,
        ])->build();

        return view('flux-admin.pages.director-command-centre', [
            'panel' => $panel,
            'filtersActive' => $this->filtersActive(),
        ]);
    }

    public function filtersActive(): bool
    {
        $defaultFrom = now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $defaultTo = now()->toDateString();

        return $this->module !== 'overview'
            || $this->focus !== 'all'
            || $this->from !== $defaultFrom
            || $this->to !== $defaultTo;
    }
}
