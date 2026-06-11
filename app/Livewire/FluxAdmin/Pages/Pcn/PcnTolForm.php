<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PcnCaseUpdate;
use App\Models\PcnTolRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN TOL request — Flux Admin')]
class PcnTolForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public string $updateDisplay = '';

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcn-portal');

        if ($id) {
            $this->recordId = $id;
            $record         = PcnTolRequest::findOrFail($id);
            $this->form     = $record->getAttributes();

            foreach (['request_date', 'letter_sent_at'] as $field) {
                if (! empty($this->form[$field])) {
                    try {
                        $this->form[$field] = \Carbon\Carbon::parse($this->form[$field])->format(
                            $field === 'letter_sent_at' ? 'Y-m-d\TH:i' : 'Y-m-d'
                        );
                    } catch (\Throwable) {
                        $this->form[$field] = null;
                    }
                }
            }
        } else {
            $this->form = [
                'status'       => 'pending',
                'request_date' => now()->format('Y-m-d'),
                'update_id' => request()->integer('update_id') ?: null,
            ];
        }

        $this->refreshUpdateDisplay();
    }

    public function updatedFormUpdateId(): void
    {
        $this->refreshUpdateDisplay();
    }

    public function save(): void
    {
        $this->validate([
            'form.update_id'      => ['required', 'integer', 'exists:pcn_case_updates,id'],
            'form.request_date'   => ['required', 'date'],
            'form.status'         => ['required', 'string', 'in:pending,sent,approved,rejected'],
            'form.letter_sent_at' => ['nullable', 'date'],
            'form.note'           => ['nullable', 'string', 'max:2000'],
        ]);

        $data = [
            'update_id'      => $this->form['update_id'],
            'request_date'   => $this->form['request_date'],
            'status'         => $this->form['status'],
            'letter_sent_at' => $this->form['letter_sent_at'] ?? null,
            'note'           => $this->form['note'] ?? null,
        ];

        if ($this->recordId) {
            $tolRequest = PcnTolRequest::findOrFail($this->recordId);
            $tolRequest->update($data);
        } else {
            $data['user_id'] = auth()->id();
            $tolRequest = PcnTolRequest::create($data);
        }

        $this->syncCaseAndPdf($tolRequest->refresh());

        $this->dispatch('flux-admin:toast', type: 'success', message: 'TOL request saved.');
        $this->redirect(route('flux-admin.pcn-tol-requests.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.pcn-tol-form');
    }

    protected function refreshUpdateDisplay(): void
    {
        $updateId = (int) ($this->form['update_id'] ?? 0);
        $update = $updateId > 0
            ? PcnCaseUpdate::with(['pcnCase.customer', 'pcnCase.motorbike'])->find($updateId)
            : null;

        $this->updateDisplay = $update
            ? trim(($update->pcnCase?->pcn_number ?? '').' | Update #'.$update->id.' | '.($update->pcnCase?->customer?->full_name ?? '').' | '.($update->pcnCase?->motorbike?->reg_no ?? ''))
            : '';
    }

    protected function syncCaseAndPdf(PcnTolRequest $tolRequest): void
    {
        $tolRequest->loadMissing(['pcnCaseUpdate.pcnCase.customer', 'pcnCaseUpdate.pcnCase.motorbike', 'user']);

        if ($tolRequest->pcnCaseUpdate) {
            $tolRequest->pcn_case_id = $tolRequest->pcnCaseUpdate->case_id;
        }

        $directory = storage_path('app/public/tol_letters');
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $pdf = Pdf::loadView('pcn.template.tol_letter', [
            'tolRequest' => $tolRequest,
            'pcnNumber' => $tolRequest->pcnCaseUpdate?->pcnCase?->pcn_number ?? '',
            'customerName' => optional($tolRequest->pcnCaseUpdate?->pcnCase?->customer)->full_name ?? '',
            'vehicleVrm' => $tolRequest->pcnCaseUpdate?->pcnCase?->motorbike?->reg_no ?? '',
            'userName' => $tolRequest->user?->full_name ?? '',
        ]);

        $fileName = 'tol_request_'.$tolRequest->id.'.pdf';
        $pdf->save($directory.'/'.$fileName);

        $tolRequest->forceFill([
            'full_path' => 'storage/tol_letters/'.$fileName,
        ])->save();
    }
}
