<?php

namespace App\Livewire\FluxAdmin\Pages\Inventory;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\NgnModel;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Product Model — Flux Admin')]
class InventoryModelForm extends Component
{
    use WithAuthorization;

    public ?NgnModel $inventoryModel = null;

    public array $form = [];

    public function mount(?NgnModel $inventoryModel = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->inventoryModel = $inventoryModel?->id ? $inventoryModel : null;

        if ($this->inventoryModel) {
            $this->form = $this->inventoryModel->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.name'      => ['required', 'string', 'max:255', Rule::unique('ngn_models', 'name')->ignore($this->inventoryModel?->id)],
            'form.image_url' => ['nullable', 'string', 'max:1024'],
        ]);

        $payload = [
            'name'      => $this->form['name'],
            'image_url' => $this->form['image_url'] ?? null,
        ];

        if ($this->inventoryModel) {
            $this->inventoryModel->update($payload);
        } else {
            NgnModel::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Model saved.');
        $this->redirect(route('flux-admin.inventory-models.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.inventory.model-form');
    }
}
