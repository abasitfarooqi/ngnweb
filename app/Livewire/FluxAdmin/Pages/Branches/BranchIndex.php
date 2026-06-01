<?php

namespace App\Livewire\FluxAdmin\Pages\Branches;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Models\Branch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Branches — Flux Admin')]
class BranchIndex extends Component
{
    use WithAuthorization, WithCrudForm;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return Branch::class; }

    protected function formRules(): array
    {
        return [
            'formData.name'        => ['required', 'string', 'max:255'],
            'formData.address'     => ['nullable', 'string', 'max:500'],
            'formData.city'        => ['nullable', 'string', 'max:120'],
            'formData.postal_code' => ['nullable', 'string', 'max:20'],
            'formData.latitude'    => ['nullable', 'numeric'],
            'formData.longitude'   => ['nullable', 'numeric'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(Branch::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Branch saved.');
    }

    public function delete(int $id): void
    {
        Branch::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Branch deleted.');
    }

    public function render()
    {
        $branches = Branch::withCount('motorbikes')->orderBy('name')->get();

        return view('flux-admin.pages.branches.index', compact('branches'));
    }
}
