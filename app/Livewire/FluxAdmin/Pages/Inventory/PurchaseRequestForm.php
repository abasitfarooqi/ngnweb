<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Purchase Request — Flux Admin')]
class PurchaseRequestForm extends Component
{
    use WithAuthorization;

    public ?PurchaseRequest $purchaseRequest = null;

    public array $form = [];

    public function mount(?PurchaseRequest $purchaseRequest = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->purchaseRequest = $purchaseRequest;

        if ($purchaseRequest && $purchaseRequest->exists) {
            $attrs = $purchaseRequest->getAttributes();
            if (! empty($attrs['date'])) {
                try {
                    $attrs['date'] = \Carbon\Carbon::parse($attrs['date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['date'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'date'      => now()->toDateString(),
                'is_posted' => false,
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.date'      => ['required', 'date'],
            'form.note'      => ['nullable', 'string', 'max:1000'],
            'form.is_posted' => ['nullable', 'boolean'],
        ]);

        $data = [
            'date'      => $this->form['date'] ?? null,
            'note'      => $this->form['note'] ?? null,
            'is_posted' => (bool) ($this->form['is_posted'] ?? false),
        ];

        if ($this->purchaseRequest && $this->purchaseRequest->exists) {
            $this->purchaseRequest->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Purchase request updated.');
        } else {
            PurchaseRequest::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Purchase request created.');
        }

        $this->redirect(route('flux-admin.purchase-requests.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.inventory.purchase-request-form');
    }
}
