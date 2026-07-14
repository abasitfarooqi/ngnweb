<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SpPart;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('SP Part — Flux Admin')]
class PartForm extends Component
{
    use WithAuthorization;

    public ?SpPart $spPart = null;

    public array $form = [];

    public function mount(?SpPart $spPart = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spPart = $spPart?->id ? $spPart : null;

        if ($this->spPart) {
            $this->form = $this->spPart->getAttributes();
        } else {
            $this->form = ['is_active' => true, 'stock_status' => 'in_stock'];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.part_number'       => ['required', 'string', 'max:100', Rule::unique('sp_parts', 'part_number')->ignore($this->spPart?->id)],
            'form.name'              => ['required', 'string', 'max:255'],
            'form.note'              => ['nullable', 'string'],
            'form.stock_status'      => ['nullable', 'string', 'max:50'],
            'form.price_gbp_inc_vat' => ['nullable', 'numeric', 'min:0'],
            'form.global_stock'      => ['nullable', 'numeric', 'min:0'],
            'form.is_active'         => ['boolean'],
        ]);

        $payload = [
            'part_number'       => $this->form['part_number'],
            'name'              => $this->form['name'],
            'note'              => $this->form['note'] ?? null,
            'stock_status'      => $this->form['stock_status'] ?? null,
            'price_gbp_inc_vat' => $this->form['price_gbp_inc_vat'] ?? null,
            'global_stock'      => $this->form['global_stock'] ?? null,
            'is_active'         => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->spPart) {
            $this->spPart->update($payload);
        } else {
            SpPart::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Part saved.');
        $this->redirect(route('flux-admin.sp-parts.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.spare-parts.part-form');
    }
}
