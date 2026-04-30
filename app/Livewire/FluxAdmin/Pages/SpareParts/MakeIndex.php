<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SpMake;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Makes')]
class MakeIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return SpMake::class; }

    protected function formRules(): array
    {
        return [
            'formData.name' => ['required', 'string', 'max:255'],
            'formData.slug' => ['nullable', 'string', 'max:255'],
            'formData.source' => ['nullable', 'string', 'max:120'],
            'formData.is_active' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_active' => true];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(SpMake::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        if (empty($this->formData['slug']) && ! empty($this->formData['name'])) {
            $this->formData['slug'] = Str::slug($this->formData['name']);
        }
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Make saved.');
    }

    public function delete(int $id): void
    {
        SpMake::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = SpMake::query()
            ->when($this->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%"))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'))
            ->orderBy('name')
            ->paginate($this->perPage);

        return view('flux-admin.pages.spare-parts.makes-index', ['rows' => $rows]);
    }
}
