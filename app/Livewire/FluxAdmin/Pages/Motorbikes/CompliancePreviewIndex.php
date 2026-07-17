<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeAnnualCompliance;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT / TAX compliance — Flux Admin')]
class CompliancePreviewIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public ?int $previewId = null;

    public bool $showPreview = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-vehicles');
        $this->exportable = true;
        $this->exportFilename = 'motorbike-compliance-current';
        $this->sortField = 'motorbike_annual_compliance.updated_at';
        $this->sortDirection = 'desc';
    }

    public function openPreview(int $id): void
    {
        $this->previewId = $id;
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
        $this->previewId = null;
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with('motorbike:id,reg_no,make,model,year')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $preview = $this->previewId
            ? MotorbikeAnnualCompliance::with('motorbike:id,reg_no,make,model,year,engine,color')->find($this->previewId)
            : null;

        return view('flux-admin.pages.motorbikes.compliance-preview-index', [
            'rows' => $rows,
            'preview' => $preview,
        ]);
    }

    protected function baseQuery(): Builder
    {
        $query = MotorbikeAnnualCompliance::query();

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(function ($q) use ($term): void {
                $q->whereHas('motorbike', fn ($m) => $m->where('reg_no', 'like', "%{$term}%"))
                    ->orWhere('motorbike_annual_compliance.id', 'like', "%{$term}%")
                    ->orWhere('motorbike_annual_compliance.motorbike_id', 'like', "%{$term}%");
            });
        }

        if ($roadTax = $this->filter('road_tax_status')) {
            $query->where('road_tax_status', $roadTax);
        }

        if ($motStatus = $this->filter('mot_status')) {
            $query->where('mot_status', $motStatus);
        }

        return $query;
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('motorbike:id,reg_no,make,model,year');
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Motorbike ID' => 'motorbike_id',
            'REG. No' => fn ($r) => $r->motorbike?->reg_no,
            'Year' => 'year',
            'Road tax status' => 'road_tax_status',
            'Tax due' => fn ($r) => $r->tax_due_date ? \Carbon\Carbon::parse($r->tax_due_date)->format('Y-m-d') : '',
            'MOT status' => 'mot_status',
            'MOT due' => fn ($r) => $r->mot_due_date ? \Carbon\Carbon::parse($r->mot_due_date)->format('Y-m-d') : '',
            'Insurance status' => 'insurance_status',
            'Insurance due' => fn ($r) => $r->insurance_due_date ? \Carbon\Carbon::parse($r->insurance_due_date)->format('Y-m-d') : '',
            'Updated' => fn ($r) => optional($r->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
