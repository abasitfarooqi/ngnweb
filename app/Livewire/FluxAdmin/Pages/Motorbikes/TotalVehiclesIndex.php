<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\CompanyVehicle;
use App\Models\Motorbike;
use App\Support\TotalVehiclesQuery;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Internal fleet overview: all NGN vehicles (vehicle profile 1).
 * /flux-admin/total-vehicles
 */
#[Layout('flux-admin.layouts.app')]
#[Title('Total NGN Vehicles — Flux Admin')]
class TotalVehiclesIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public string $branch = '';

    public string $filterColour = '';

    public string $filterYear = '';

    public string $filterMotStatus = '';

    /** @var ''|'rental'|'finance_new'|'finance_used'|'company'|'sale_rental'|'for_sale' */
    #[Url]
    public string $filterCategory = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'reg_no';
        $this->sortDirection = 'asc';
        $this->exportFilename = 'total-vehicles';
        $this->exportable = true;
    }

    public function updatingBranch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterColour(): void
    {
        $this->resetPage();
    }

    public function updatingFilterYear(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMotStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $rolesMap = TotalVehiclesQuery::rolesMapForIds($rows->getCollection()->pluck('id'));

        return view('flux-admin.pages.motorbikes.total-vehicles-index', [
            'motorbikes' => $rows,
            'rolesMap' => $rolesMap,
            'branches' => Branch::orderBy('name')->get(),
            'categoryCounts' => TotalVehiclesQuery::categoryCounts(),
        ]);
    }

    protected function baseQuery(): Builder
    {
        $query = TotalVehiclesQuery::base()
            ->with(['vehicleProfile', 'branch', 'latestCompliance'])
            ->withCount('annualCompliances', 'repairs', 'rentingBookingItems');

        if ($this->filterCategory === 'rental') {
            $query->whereIn('motorbikes.id', TotalVehiclesQuery::activeRentalMotorbikeIdsSubquery());
        } elseif ($this->filterCategory === 'finance_new') {
            $query->whereIn('motorbikes.id', TotalVehiclesQuery::activeFinanceNewMotorbikeIdsSubquery());
        } elseif ($this->filterCategory === 'finance_used') {
            $query->whereIn('motorbikes.id', TotalVehiclesQuery::activeFinanceUsedMotorbikeIdsSubquery());
        } elseif ($this->filterCategory === 'company') {
            $query->whereIn('motorbikes.id', CompanyVehicle::query()->select('motorbike_id'));
        } elseif (in_array($this->filterCategory, ['for_sale', 'sale_rental'], true)) {
            $query->whereIn('motorbikes.id', TotalVehiclesQuery::saleRentalMotorbikeIdsSubquery());
        }

        return $query
            ->when($this->search !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('motorbikes.reg_no', 'like', $term)
                        ->orWhere('motorbikes.make', 'like', $term)
                        ->orWhere('motorbikes.model', 'like', $term)
                        ->orWhere('motorbikes.vin_number', 'like', $term);
                });
            })
            ->when($this->branch !== '', fn ($q) => $q->where('motorbikes.branch_id', $this->branch))
            ->when($this->filterColour !== '', fn ($q) => $q->where('motorbikes.color', 'like', '%'.$this->filterColour.'%'))
            ->when($this->filterYear !== '', fn ($q) => $q->where('motorbikes.year', $this->filterYear))
            ->when($this->filterMotStatus === '1', fn ($q) => $q->whereHas('annualCompliances'))
            ->when($this->filterMotStatus === '0', fn ($q) => $q->whereDoesntHave('annualCompliances'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Reg No' => 'reg_no',
            'Make' => 'make',
            'Model' => 'model',
            'Colour' => 'color',
            'Year' => 'year',
            'Roles' => function (Motorbike $r) {
                $map = TotalVehiclesQuery::rolesMapForIds([$r->id]);

                return implode(', ', $map[$r->id] ?? []);
            },
            'MOT Due' => fn ($r) => $r->latestCompliance?->mot_due_date ?? '',
            'Road Tax Due' => fn ($r) => $r->latestCompliance?->tax_due_date ?? '',
            'Branch' => fn ($r) => $r->branch?->name ?? '',
        ];
    }
}
