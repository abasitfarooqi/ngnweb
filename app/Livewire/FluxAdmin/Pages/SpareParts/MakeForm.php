<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SpMake;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('SP Make — Flux Admin')]
class MakeForm extends Component
{
    use WithAuthorization;

    public ?SpMake $spMake = null;

    public array $form = [];

    public function mount(?SpMake $spMake = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spMake = $spMake?->id ? $spMake : null;

        if ($this->spMake) {
            $this->form = $this->spMake->getAttributes();
        } else {
            $this->form = ['is_active' => true];
        }
    }

    public function save(): void
    {
        if (empty($this->form['slug']) && ! empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }

        $this->validate([
            'form.name'      => ['required', 'string', 'max:255'],
            'form.slug'      => ['nullable', 'string', 'max:255', Rule::unique('sp_makes', 'slug')->ignore($this->spMake?->id)],
            'form.source'    => ['nullable', 'string', 'max:120'],
            'form.is_active' => ['boolean'],
        ], [], ['form.slug' => 'slug']);

        $payload = [
            'name'      => $this->form['name'],
            'slug'      => $this->form['slug'] ?? null,
            'source'    => ($this->form['source'] ?? null) ?: 'internal',
            'is_active' => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->spMake) {
            $this->spMake->update($payload);
        } else {
            SpMake::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Make saved.');
        $this->redirect(route('flux-admin.sp-makes.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.spare-parts.make-form');
    }
}
