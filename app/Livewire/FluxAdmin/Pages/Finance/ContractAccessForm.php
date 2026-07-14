<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ContractAccess;
use App\Services\FinanceContractLinkResolver;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Contract link — Flux Admin')]
class ContractAccessForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public array $contractLinks = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-finance-applications');

        if ($id) {
            $this->recordId = $id;
            $record         = ContractAccess::with('application')->findOrFail($id);
            $this->form     = $record->getAttributes();
            $this->contractLinks = FinanceContractLinkResolver::linksForContractAccess($record);

            if (! empty($this->form['expires_at'])) {
                try {
                    $this->form['expires_at'] = \Carbon\Carbon::parse($this->form['expires_at'])->format('Y-m-d\TH:i');
                } catch (\Throwable) {
                    $this->form['expires_at'] = null;
                }
            }
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.customer_id'    => ['required', 'integer'],
            'form.application_id' => ['required', 'integer'],
            'form.passcode'       => ['required', 'string', 'max:100'],
            'form.expires_at'     => ['nullable', 'date'],
        ]);

        $data = [
            'customer_id'    => $this->form['customer_id'],
            'application_id' => $this->form['application_id'],
            'passcode'       => $this->form['passcode'],
            'expires_at'     => $this->form['expires_at'] ?? null,
        ];

        if ($this->recordId) {
            ContractAccess::findOrFail($this->recordId)->update($data);
        } else {
            ContractAccess::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Contract link saved.');
        $this->redirect(route('flux-admin.contract-access.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.finance.contract-access-form');
    }
}
