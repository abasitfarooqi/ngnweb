<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\ContractExtraItem;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ContractExtraItemForm extends Component
{
    use WithAuthorization;

    public ?ContractExtraItem $contractExtraItem = null;

    public array $form = [];

    public function mount(?ContractExtraItem $contractExtraItem = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-finance-applications');
        $this->contractExtraItem = $contractExtraItem;

        if ($contractExtraItem && $contractExtraItem->exists) {
            $this->form = $contractExtraItem->getAttributes();
        } else {
            $this->form = ['quantity' => 1];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.application_id' => ['required', 'integer', 'exists:finance_applications,id'],
            'form.name'           => ['required', 'string', 'max:255'],
            'form.price'          => ['required', 'numeric', 'min:0'],
            'form.quantity'       => ['required', 'integer', 'min:1'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->contractExtraItem && $this->contractExtraItem->exists) {
            $this->contractExtraItem->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Item updated.');
        } else {
            ContractExtraItem::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Item created.');
        }

        $this->redirect(route('flux-admin.contract-extra-items.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.finance.contract-extra-item-form');
    }
}
