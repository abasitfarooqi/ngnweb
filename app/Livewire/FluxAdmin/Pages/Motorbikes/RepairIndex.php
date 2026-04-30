<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeRepair;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike repairs — Flux Admin')]
class RepairIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->exportable = true;
        $this->exportFilename = 'motorbike-repairs';
        $this->sortField = 'arrival_date';
    }

    public function render()
    {
        $repairs = $this->baseQuery()
            ->with(['motorbike:id,reg_no,make,model', 'branch:id,name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = \App\Models\Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.repairs-index', compact('repairs', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return MotorbikeRepair::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('fullname', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%"));
                });
            })
            ->when($this->filter('is_repaired') !== '', fn ($q) => $q->where('is_repaired', $this->filter('is_repaired') === '1'))
            ->when($this->filter('is_returned') !== '', fn ($q) => $q->where('is_returned', $this->filter('is_returned') === '1'))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v));
    }

    public function generatePdf(int $id)
    {
        $repair = MotorbikeRepair::with(['motorbike', 'branch', 'updates.services', 'observations'])->findOrFail($id);

        $pdf = \PDF::loadView('livewire.agreements.pdf.templates.repair_invoice', compact('repair'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Repair_Invoice_'.$repair->motorbike?->reg_no.'.pdf'
        );
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['motorbike:id,reg_no,make,model', 'branch:id,name']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Arrival' => fn ($r) => $r->arrival_date ? \Carbon\Carbon::parse($r->arrival_date)->format('Y-m-d H:i') : '',
            'Registration' => fn ($r) => $r->motorbike?->reg_no,
            'Customer' => 'fullname',
            'Phone' => 'phone',
            'Email' => 'email',
            'Branch' => fn ($r) => $r->branch?->name,
            'Repaired' => fn ($r) => $r->is_repaired ? 'Yes' : 'No',
            'Returned' => fn ($r) => $r->is_returned ? 'Yes' : 'No',
        ];
    }
}
