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

    /** PCN case typeahead */
    public string $caseSearch = '';
    public array $caseSuggestions = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-pcn-portal');

        if ($id) {
            $this->recordId = $id;
            $record         = PcnCaseUpdate::findOrFail($id);
            $this->form     = $record->getAttributes();

            if (! empty($this->form['update_date'])) {
                try {
                    $this->form['update_date'] = \Carbon\Carbon::parse($this->form['update_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $this->form['update_date'] = null;
                }
            }

            $case = $record->pcncase;
            $this->caseSearch = $case ? $case->pcn_number : '';
        } else {
            $this->form = [
                'is_appealed'       => false,
                'is_paid_by_owner'  => false,
                'is_paid_by_keeper' => false,
                'is_transferred'    => false,
                'is_cancled'        => false,
                'update_date'       => now()->format('Y-m-d'),
                'user_id'           => auth()->id(),
            ];
        }
    }

    public function updatingCaseSearch(): void
    {
        if (strlen($this->caseSearch) < 2) {
            $this->caseSuggestions = [];
            return;
        }
        $this->caseSuggestions = PcnCase::where('pcn_number', 'like', "%{$this->caseSearch}%")
            ->limit(8)->get(['id', 'pcn_number'])->map(fn ($c) => [
                'id'     => $c->id,
                'label'  => $c->pcn_number,
            ])->toArray();
    }

    public function selectCase(int $id, string $label): void
    {
        $this->form['case_id'] = $id;
        $this->caseSearch      = $label;
        $this->caseSuggestions = [];
    }

    public function save(): void
    {
        foreach (['is_appealed', 'is_paid_by_owner', 'is_paid_by_keeper', 'is_transferred', 'is_cancled'] as $field) {
            $this->form[$field] = (bool) ($this->form[$field] ?? false);
        }

        $this->validate([
            'form.case_id'           => ['required', 'integer'],
            'form.update_date'       => ['required', 'date'],
            'form.is_appealed'       => ['boolean'],
            'form.is_paid_by_owner'  => ['boolean'],
            'form.is_paid_by_keeper' => ['boolean'],
            'form.is_transferred'    => ['boolean'],
            'form.is_cancled'        => ['boolean'],
            'form.additional_fee'    => ['nullable', 'numeric', 'min:0'],
            'form.note'              => ['nullable', 'string', 'max:2000'],
        ]);

        $data = [
            'case_id'           => $this->form['case_id'],
            'update_date'       => $this->form['update_date'],
            'is_appealed'       => $this->form['is_appealed'],
            'is_paid_by_owner'  => $this->form['is_paid_by_owner'],
            'is_paid_by_keeper' => $this->form['is_paid_by_keeper'],
            'is_transferred'    => $this->form['is_transferred'],
            'is_cancled'        => $this->form['is_cancled'],
            'additional_fee'    => $this->form['additional_fee'] ?? null,
            'note'              => $this->form['note'] ?? null,
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
}
