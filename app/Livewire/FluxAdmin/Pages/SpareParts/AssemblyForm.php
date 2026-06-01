<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SpAssembly;
use App\Models\SpFitment;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('SP Assembly — Flux Admin')]
class AssemblyForm extends Component
{
    use WithAuthorization;

    public ?SpAssembly $spAssembly = null;

    public array $form = [];

    public function mount(?SpAssembly $spAssembly = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spAssembly = $spAssembly?->id ? $spAssembly : null;

        if ($this->spAssembly) {
            $this->form = $this->spAssembly->getAttributes();
        } else {
            $this->form = ['is_active' => true, 'sort_order' => 0];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.fitment_id'  => ['required', 'integer', 'exists:sp_fitments,id'],
            'form.name'        => ['required', 'string', 'max:255'],
            'form.slug'        => ['nullable', 'string', 'max:255'],
            'form.external_id' => ['nullable', 'string', 'max:255'],
            'form.image_url'   => ['nullable', 'string', 'max:1024'],
            'form.diagram_url' => ['nullable', 'string', 'max:1024'],
            'form.sort_order'  => ['nullable', 'integer', 'min:0'],
            'form.is_active'   => ['boolean'],
        ]);

        if (empty($this->form['slug']) && ! empty($this->form['name'])) {
            $this->form['slug'] = Str::slug($this->form['name']);
        }

        $payload = [
            'fitment_id'  => $this->form['fitment_id'],
            'name'        => $this->form['name'],
            'slug'        => $this->form['slug'] ?? null,
            'external_id' => $this->form['external_id'] ?? null,
            'image_url'   => $this->form['image_url'] ?? null,
            'diagram_url' => $this->form['diagram_url'] ?? null,
            'sort_order'  => $this->form['sort_order'] ?? 0,
            'is_active'   => (bool) ($this->form['is_active'] ?? true),
        ];

        if ($this->spAssembly) {
            $this->spAssembly->update($payload);
        } else {
            SpAssembly::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Assembly saved.');
        $this->redirect(route('flux-admin.sp-assemblies.index'), navigate: true);
    }

    public function render()
    {
        $fitments = SpFitment::query()->with('model:id,name')->orderByDesc('id')->limit(500)->get();

        return view('flux-admin.pages.spare-parts.assembly-form', compact('fitments'));
    }
}
