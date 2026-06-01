<?php

namespace App\Livewire\FluxAdmin\Pages\Branches;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Branch — Flux Admin')]
class BranchForm extends Component
{
    use WithAuthorization;

    public ?Branch $branch = null;

    public array $form = [];

    public function mount(?Branch $branch = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->branch = $branch?->id ? $branch : null;

        if ($this->branch) {
            $this->form = $this->branch->getAttributes();
        } else {
            $this->form = [];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.name'        => ['required', 'string', 'max:255'],
            'form.address'     => ['nullable', 'string', 'max:500'],
            'form.city'        => ['nullable', 'string', 'max:120'],
            'form.postal_code' => ['nullable', 'string', 'max:20'],
            'form.latitude'    => ['nullable', 'numeric'],
            'form.longitude'   => ['nullable', 'numeric'],
        ]);

        $payload = [
            'name'        => $this->form['name'],
            'address'     => $this->form['address'] ?? null,
            'city'        => $this->form['city'] ?? null,
            'postal_code' => $this->form['postal_code'] ?? null,
            'latitude'    => $this->form['latitude'] ?? null,
            'longitude'   => $this->form['longitude'] ?? null,
        ];

        if ($this->branch) {
            $this->branch->update($payload);
        } else {
            Branch::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Branch saved.');
        $this->redirect(route('flux-admin.branches.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.branches.branch-form');
    }
}
