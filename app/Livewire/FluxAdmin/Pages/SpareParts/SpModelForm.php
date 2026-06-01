<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SpMake;
use App\Models\SpModel;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('SP Model — Flux Admin')]
class SpModelForm extends Component
{
    use WithAuthorization;

    public ?SpModel $spModel = null;

    public array $form = [];

    public function mount(?SpModel $spModel = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spModel = $spModel?->id ? $spModel : null;

        if ($this->spModel) {
            $this->form = $this->spModel->getAttributes();
        } else {
            $this->form = ['is_active' => true];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.make_id'   => ['required', 'integer', 'exists:sp_makes,id'],
            'form.name'      => ['required', 'string', 'max:255'],
            'form.slug'      => ['nullable', 'string', 'max:255'],
            'form.is_active' => ['boolean'],
        ]);

        if (empty($this->form['slug']) && ! empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }

        $payload = [
            'make_id'   => $this->form['make_id'],
            'name'      => $this->form['name'],
            'slug'      => $this->form['slug'] ?? null,
            'is_active' => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->spModel) {
            $this->spModel->update($payload);
        } else {
            SpModel::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Model saved.');
        $this->redirect(route('flux-admin.sp-models.index'), navigate: true);
    }

    public function render()
    {
        $makes = SpMake::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.spare-parts.sp-model-form', compact('makes'));
    }
}
