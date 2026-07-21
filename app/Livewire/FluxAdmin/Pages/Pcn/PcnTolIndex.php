<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\PcnTolRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN TOL requests — Flux Admin')]
class PcnTolIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcns');
        $this->sortField = 'request_date';
    }

    protected function formModel(): string { return PcnTolRequest::class; }

    protected function formRules(): array
    {
        return [
            'formData.update_id'     => ['required', 'integer', 'exists:pcn_case_updates,id'],
            'formData.request_date'  => ['required', 'date'],
            'formData.status'        => ['required', 'string', 'in:pending,sent,approved,rejected'],
            'formData.letter_sent_at' => ['nullable', 'date'],
            'formData.note'          => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['status' => 'pending', 'request_date' => now()->format('Y-m-d')];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(PcnTolRequest::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'TOL request saved.');
    }

    public function delete(int $id): void
    {
        PcnTolRequest::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'TOL request deleted.');
    }

    public function generatePdf(int $id)
    {
        $tolRequest = PcnTolRequest::with(['pcnCaseUpdate.pcnCase.customer', 'pcnCaseUpdate.pcnCase.motorbike', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('pcn.template.tol_letter', [
            'tolRequest' => $tolRequest,
            'pcnNumber' => $tolRequest->pcnCaseUpdate?->pcnCase?->pcn_number ?? '',
            'customerName' => $tolRequest->pcnCaseUpdate?->pcnCase?->customer?->full_name ?? '',
            'vehicleVrm' => $tolRequest->pcnCaseUpdate?->pcnCase?->motorbike?->reg_no ?? '',
            'userName' => $tolRequest->user?->full_name ?? '',
        ]);

        return response()->streamDownload(fn () => print($pdf->output()), 'tol_request_'.$tolRequest->id.'.pdf');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['pcnCaseUpdate.pcnCase:id,pcn_number', 'user:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.pcn.pcn-tol-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return PcnTolRequest::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->whereHas('pcnCaseUpdate.pcnCase', fn ($q) => $q->where('pcn_number', 'like', "%{$term}%"));
            })
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v));
    }
}
