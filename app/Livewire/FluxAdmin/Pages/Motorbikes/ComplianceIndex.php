<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeAnnualCompliance;
use App\Support\VehicleDatabaseQuery;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle database — Flux Admin')]
class ComplianceIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-vehicles');
        $this->exportable = true;
        $this->exportFilename = 'vehicle-database';
        $this->sortField = 'motorbike_annual_compliance.id';
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with('motorbike:id,reg_no,make,model,year,engine,color')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.compliance-index', [
            'rows' => $rows,
            'associationOptions' => VehicleDatabaseQuery::associationFilterOptions(),
        ]);
    }

    protected function baseQuery(): Builder
    {
        $query = VehicleDatabaseQuery::applySelect(
            VehicleDatabaseQuery::applyJoins(MotorbikeAnnualCompliance::query())
        );

        if ($this->search !== '') {
            $term = $this->search;
            $query->whereHas('motorbike', function ($q) use ($term): void {
                $q->where('reg_no', 'like', "%{$term}%")
                    ->orWhere('make', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%");
            });
        }

        if ($roadTax = $this->filter('road_tax_status')) {
            $query->where('road_tax_status', $roadTax);
        }

        if ($motStatus = $this->filter('mot_status')) {
            $query->where('mot_status', $motStatus);
        }

        if ($association = $this->filter('association_status')) {
            $query->whereRaw(
                '('.VehicleDatabaseQuery::associationStatusFilterSql().') = ?',
                [$association]
            );
        }

        return $query;
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('motorbike:id,reg_no,make,model,year,engine,color');
    }

    protected function exportColumns(): array
    {
        return [
            'REG. No' => fn ($r) => $r->motorbike?->reg_no,
            'MAKE' => fn ($r) => $r->motorbike?->make,
            'MODEL' => fn ($r) => $r->motorbike?->model,
            'YEAR' => fn ($r) => $r->motorbike?->year,
            'ENGINE' => fn ($r) => $r->motorbike?->engine,
            'COLOR' => fn ($r) => $r->motorbike?->color,
            'STATUS' => 'association_status',
            'ROAD TAX' => 'road_tax_status',
            'TAX DUE' => fn ($r) => $r->tax_due_date ? \Carbon\Carbon::parse($r->tax_due_date)->format('Y-m-d') : '',
            'MOT' => 'mot_status',
            'MOT DUE' => fn ($r) => $r->mot_due_date ? \Carbon\Carbon::parse($r->mot_due_date)->format('Y-m-d') : '',
        ];
    }
}
