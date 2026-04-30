<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
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
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcn-portal');
        $this->sortField = 'request_date';
    }

    public function generatePdf(int $id)
    {
        $tolRequest = PcnTolRequest::with(['pcnCaseUpdate.pcnCase.customer', 'pcnCaseUpdate.pcnCase.motorbike', 'user'])->findOrFail($id);

        $pdf = Pdf::loadView('pcn.template.tol_letter', [
            'tolRequest' => $tolRequest,
            'pcnNumber' => $tolRequest->pcnCaseUpdate->pcnCase->pcn_number ?? '',
            'customerName' => optional($tolRequest->pcnCaseUpdate->pcnCase->customer)->full_name ?? '',
            'vehicleVrm' => $tolRequest->pcnCaseUpdate->pcnCase->motorbike->reg_no ?? '',
            'userName' => $tolRequest->user->full_name ?? '',
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
