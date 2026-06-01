<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeRepair;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike repairs — Flux Admin')]
class RepairIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->exportable = true;
        $this->exportFilename = 'motorbike-repairs';
        $this->sortField = 'arrival_date';
    }

    protected function formModel(): string { return MotorbikeRepair::class; }

    protected function formRules(): array
    {
        return [
            'formData.motorbike_id'  => ['required', 'integer'],
            'formData.fullname'      => ['required', 'string', 'max:255'],
            'formData.phone'         => ['nullable', 'string', 'max:50'],
            'formData.email'         => ['nullable', 'email', 'max:255'],
            'formData.arrival_date'  => ['nullable', 'date'],
            'formData.notes'         => ['nullable', 'string'],
            'formData.is_repaired'   => ['boolean'],
            'formData.repaired_date' => ['nullable', 'date'],
            'formData.is_returned'   => ['boolean'],
            'formData.returned_date' => ['nullable', 'date'],
            'formData.branch_id'     => ['nullable', 'integer'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'is_repaired'  => false,
            'is_returned'  => false,
            'arrival_date' => now()->format('Y-m-d'),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $r = MotorbikeRepair::findOrFail($id);
        $this->fillFromModel($r);
        foreach (['arrival_date', 'repaired_date', 'returned_date'] as $k) {
            if (! empty($this->formData[$k])) {
                $this->formData[$k] = Carbon::parse($this->formData[$k])->format('Y-m-d');
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        MotorbikeRepair::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
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

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with(['motorbike:id,reg_no,make,model', 'branch:id,name']);
    }

    protected function exportColumns(): array
    {
        return [
            'ID'           => 'id',
            'Arrival'      => fn ($r) => $r->arrival_date ? Carbon::parse($r->arrival_date)->format('Y-m-d H:i') : '',
            'Registration' => fn ($r) => $r->motorbike?->reg_no,
            'Customer'     => 'fullname',
            'Phone'        => 'phone',
            'Email'        => 'email',
            'Branch'       => fn ($r) => $r->branch?->name,
            'Repaired'     => fn ($r) => $r->is_repaired ? 'Yes' : 'No',
            'Returned'     => fn ($r) => $r->is_returned ? 'Yes' : 'No',
        ];
    }
}
