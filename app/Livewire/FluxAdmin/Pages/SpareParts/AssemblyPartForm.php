<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\SpAssembly;
use App\Models\SpAssemblyPart;
use App\Models\SpPart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Assembly part — Flux Admin')]
class AssemblyPartForm extends Component
{
    use WithAuthorization;

    public ?SpAssemblyPart $spAssemblyPart = null;

    public array $form = [];

    public function mount(?SpAssemblyPart $spAssemblyPart = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->spAssemblyPart = $spAssemblyPart;

        if ($spAssemblyPart && $spAssemblyPart->exists) {
            $this->form = $spAssemblyPart->getAttributes();
        } else {
            $this->form = ['qty_used' => 1, 'sort_order' => 0];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.assembly_id' => ['required', 'integer', 'exists:sp_assemblies,id'],
            'form.part_id' => ['required', 'integer', 'exists:sp_parts,id'],
            'form.qty_used' => ['required', 'integer', 'min:1'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.note_override' => ['nullable', 'string'],
            'form.price_override' => ['nullable', 'numeric', 'min:0'],
            'form.stock_override' => ['nullable', 'numeric', 'min:0'],
        ]);

        $payload = collect($this->form)->only([
            'assembly_id', 'part_id', 'qty_used', 'sort_order',
            'note_override', 'price_override', 'stock_override',
        ])->all();

        foreach (['price_override', 'stock_override'] as $key) {
            if (isset($payload[$key]) && $payload[$key] === '') {
                $payload[$key] = null;
            }
        }

        if ($this->spAssemblyPart && $this->spAssemblyPart->exists) {
            $this->spAssemblyPart->update($payload);
            $message = 'Assembly part saved.';
        } else {
            SpAssemblyPart::create($payload);
            $message = 'Assembly part created.';
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: $message);
        $this->redirect(route('flux-admin.sp-assembly-parts.index'), navigate: true);
    }

    public function render()
    {
        $assemblies = SpAssembly::query()->orderByDesc('id')->limit(500)->get(['id', 'name']);
        $parts = SpPart::query()->orderBy('part_number')->limit(1000)->get(['id', 'part_number', 'name']);

        return view('flux-admin.pages.spare-parts.assembly-part-form', compact('assemblies', 'parts'));
    }
}
