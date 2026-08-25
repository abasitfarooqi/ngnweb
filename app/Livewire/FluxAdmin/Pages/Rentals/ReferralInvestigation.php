<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Services\Renting\RentingReferralInvestigation;
use App\Support\RentingReferralAccess;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Referral investigation — Flux Admin')]
class ReferralInvestigation extends Component
{
    use WithAuthorization;
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'kind', history: true)]
    public string $kind = 'all';

    #[Url(as: 'stage', history: true)]
    public string $stage = '';

    #[Url(as: 'warning', history: true)]
    public string $warning = '';

    #[Url(as: 'early', history: true)]
    public string $early = '';

    #[Url(as: 'source', history: true)]
    public string $source = '';

    #[Url(as: 'amin', history: true)]
    public string $amountMin = '';

    #[Url(as: 'amax', history: true)]
    public string $amountMax = '';

    #[Url(as: 'from', history: true)]
    public string $from = '';

    #[Url(as: 'to', history: true)]
    public string $to = '';

    #[Url(as: 'staff', history: true)]
    public string $staffId = '';

    public ?string $openKey = null;

    public function mount(): void
    {
        if (! RentingReferralAccess::canInvestigate()) {
            abort(403, 'Only Thiago and Super Admins can open referral investigation.');
        }

        $this->kind = in_array($this->kind, ['all', 'programme', 'direct'], true) ? $this->kind : 'all';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedKind(): void
    {
        $this->kind = in_array($this->kind, ['all', 'programme', 'direct'], true) ? $this->kind : 'all';
        $this->resetPage();
    }

    public function updatingStage(): void
    {
        $this->resetPage();
    }

    public function updatingWarning(): void
    {
        $this->resetPage();
    }

    public function updatingEarly(): void
    {
        $this->resetPage();
    }

    public function updatingSource(): void
    {
        $this->resetPage();
    }

    public function updatingAmountMin(): void
    {
        $this->resetPage();
    }

    public function updatingAmountMax(): void
    {
        $this->resetPage();
    }

    public function updatingFrom(): void
    {
        $this->resetPage();
    }

    public function updatingTo(): void
    {
        $this->resetPage();
    }

    public function updatingStaffId(): void
    {
        $this->resetPage();
    }

    public function toggleFlag(string $flag): void
    {
        if ($flag === 'warning') {
            $this->warning = $this->warning === 'yes' ? '' : 'yes';
        }
        if ($flag === 'early') {
            $this->early = $this->early === 'yes' ? '' : 'yes';
        }
        $this->resetPage();
    }

    public function setKind(string $kind): void
    {
        $this->kind = in_array($kind, ['all', 'programme', 'direct'], true) ? $kind : 'all';
        $this->resetPage();
    }

    public function setPreset(string $preset): void
    {
        $this->resetFilters();

        match ($preset) {
            'review' => $this->stage = 'review',
            'posted' => $this->stage = 'posted',
            'warnings' => $this->warning = 'yes',
            'month' => $this->from = now()->startOfMonth()->toDateString(),
            'days30' => $this->from = now()->subDays(30)->toDateString(),
            default => null,
        };

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->kind = 'all';
        $this->stage = '';
        $this->warning = '';
        $this->early = '';
        $this->source = '';
        $this->amountMin = '';
        $this->amountMax = '';
        $this->from = '';
        $this->to = '';
        $this->staffId = '';
        $this->openKey = null;
        $this->resetPage();
    }

    public function toggle(string $key): void
    {
        $this->openKey = $this->openKey === $key ? null : $key;
    }

    public function render()
    {
        $investigation = RentingReferralInvestigation::make($this->filterBag());

        return view('flux-admin.pages.rentals.referral-investigation', [
            'metrics' => $investigation->metrics(),
            'rows' => $investigation->feed($this->getPage(), 20),
            'staffChoices' => $investigation->staffChoices(),
            'filtersActive' => $this->filtersActive(),
        ]);
    }

    /** @return array<string, string> */
    private function filterBag(): array
    {
        return [
            'search' => $this->search,
            'kind' => $this->kind,
            'stage' => $this->stage,
            'warning' => $this->warning,
            'early' => $this->early,
            'source' => $this->source,
            'amount_min' => $this->amountMin,
            'amount_max' => $this->amountMax,
            'from' => $this->from,
            'to' => $this->to,
            'staff_id' => $this->staffId,
        ];
    }

    public function filtersActive(): bool
    {
        return $this->search !== ''
            || $this->kind !== 'all'
            || $this->stage !== ''
            || $this->warning !== ''
            || $this->early !== ''
            || $this->source !== ''
            || $this->amountMin !== ''
            || $this->amountMax !== ''
            || $this->from !== ''
            || $this->to !== ''
            || $this->staffId !== '';
    }
}
