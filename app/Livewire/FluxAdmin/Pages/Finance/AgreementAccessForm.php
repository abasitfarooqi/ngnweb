<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\AgreementAccess;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Agreement link — Flux Admin')]
class AgreementAccessForm extends Component
{
    use WithAuthorization;

    public ?int $recordId = null;

    public array $form = [];

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');

        if ($id) {
            $this->recordId = $id;
            $record         = AgreementAccess::findOrFail($id);
            $this->form     = $record->getAttributes();

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
            'form.customer_id' => ['required', 'integer'],
            'form.booking_id'  => ['required', 'integer'],
            'form.passcode'    => ['required', 'string', 'max:100'],
            'form.expires_at'  => ['nullable', 'date'],
        ]);

        $data = [
            'customer_id' => $this->form['customer_id'],
            'booking_id'  => $this->form['booking_id'],
            'passcode'    => $this->form['passcode'],
            'expires_at'  => $this->form['expires_at'] ?? null,
        ];

        if ($this->recordId) {
            AgreementAccess::findOrFail($this->recordId)->update($data);
        } else {
            AgreementAccess::create($data);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Agreement link saved.');
        $this->redirect(route('flux-admin.agreement-access.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.finance.agreement-access-form');
    }
}
