<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PcnCase;
use App\Models\PcnCaseUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN update — Flux Admin')]
class PcnUpdateForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public string $caseSearch = '';

    public array $caseSuggestions = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcns');

        if ($id) {
            $this->recordId = $id;
            $record = PcnCaseUpdate::with('pcnCase')->findOrFail($id);
            $this->form = $record->getAttributes();

            if (! empty($this->form['update_date'])) {
                try {
                    $this->form['update_date'] = \Carbon\Carbon::parse($this->form['update_date'])->format('Y-m-d\TH:i');
                } catch (\Throwable) {
                    $this->form['update_date'] = null;
                }
            }

            $this->caseSearch = $record->pcnCase?->pcn_number ?? '';
        } else {
            $this->form = [
                'is_appealed' => false,
                'is_tol_requested' => false,
                'is_appeal_rejected' => false,
                'is_paid_by_owner' => false,
                'is_paid_by_keeper' => false,
                'is_transferred' => false,
                'is_cancled' => false,
                'update_date' => now()->format('Y-m-d\TH:i'),
                'user_id' => auth()->id(),
                'additional_fee' => '0',
                'note' => '',
            ];

            $caseId = request()->integer('case_id') ?: null;
            if ($caseId) {
                $this->applyCaseDefaults($caseId);
            }
        }
    }

    public function updatedCaseSearch(string $value): void
    {
        if (strlen($value) < 2) {
            $this->caseSuggestions = [];

            return;
        }

        $this->caseSuggestions = PcnCase::where('pcn_number', 'like', "%{$value}%")
            ->limit(8)->get(['id', 'pcn_number'])->map(fn ($c) => [
                'id' => $c->id,
                'label' => $c->pcn_number,
            ])->toArray();
    }

    public function selectCase(int $id): void
    {
        $case = PcnCase::find($id);
        if (! $case) {
            return;
        }

        $this->applyCaseDefaults($case->id);
        $this->caseSuggestions = [];
    }

    public function save(): void
    {
        foreach (['is_appealed', 'is_tol_requested', 'is_appeal_rejected', 'is_paid_by_owner', 'is_paid_by_keeper', 'is_transferred', 'is_cancled'] as $field) {
            $this->form[$field] = (bool) ($this->form[$field] ?? false);
        }

        $this->validate([
            'form.case_id' => ['required', 'integer', 'exists:pcn_cases,id'],
            'form.update_date' => ['required', 'date'],
            'form.is_appealed' => ['boolean'],
            'form.is_tol_requested' => ['boolean'],
            'form.is_appeal_rejected' => ['boolean'],
            'form.is_paid_by_owner' => ['boolean'],
            'form.is_paid_by_keeper' => ['boolean'],
            'form.is_transferred' => ['boolean'],
            'form.is_cancled' => ['boolean'],
            'form.additional_fee' => ['nullable', 'numeric', 'min:0'],
            'form.note' => ['required', 'string'],
        ]);

        $data = [
            'case_id' => $this->form['case_id'],
            'update_date' => $this->form['update_date'],
            'is_appealed' => $this->form['is_appealed'],
            'is_tol_requested' => $this->form['is_tol_requested'],
            'is_appeal_rejected' => $this->form['is_appeal_rejected'],
            'is_paid_by_owner' => $this->form['is_paid_by_owner'],
            'is_paid_by_keeper' => $this->form['is_paid_by_keeper'],
            'is_transferred' => $this->form['is_transferred'],
            'is_cancled' => $this->form['is_cancled'],
            'additional_fee' => ($this->form['additional_fee'] ?? '') !== '' ? $this->form['additional_fee'] : null,
            'note' => $this->form['note'],
        ];

        if ($this->recordId) {
            PcnCaseUpdate::findOrFail($this->recordId)->update($data);
        } else {
            $data['user_id'] = auth()->id();
            PcnCaseUpdate::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN update saved.');
        $this->redirect(route('flux-admin.pcn-updates.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.pcn.pcn-update-form');
    }

    protected function applyCaseDefaults(int $caseId): void
    {
        $case = PcnCase::find($caseId);
        if (! $case) {
            return;
        }

        $this->form['case_id'] = $case->id;
        $this->caseSearch = $case->pcn_number;

        $last = PcnCaseUpdate::where('case_id', $caseId)->latest('id')->first();
        if (! $last || $this->recordId) {
            return;
        }

        $this->form['is_appealed'] = (bool) $last->is_appealed;
        $this->form['is_tol_requested'] = (bool) $last->is_tol_requested;
        $this->form['is_appeal_rejected'] = (bool) $last->is_appeal_rejected;
        $this->form['is_paid_by_owner'] = (bool) $last->is_paid_by_owner;
        $this->form['is_paid_by_keeper'] = (bool) $last->is_paid_by_keeper;
        $this->form['is_transferred'] = (bool) $last->is_transferred;
        $this->form['is_cancled'] = (bool) $last->is_cancled;
        $this->form['additional_fee'] = $last->additional_fee !== null ? (string) $last->additional_fee : '0';
    }
}
