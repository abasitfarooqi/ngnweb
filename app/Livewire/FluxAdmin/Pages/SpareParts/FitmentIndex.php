<?php

namespace App\Livewire\FluxAdmin\Pages\SpareParts;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\SpFitment;
use App\Models\SpModel;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Spare parts — Fitments')]
class FitmentIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return SpFitment::class; }

    protected function formRules(): array
    {
        return [
            'formData.model_id' => ['required', 'integer', 'exists:sp_models,id'],
            'formData.year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'formData.country_name' => ['nullable', 'string', 'max:120'],
            'formData.country_slug' => ['nullable', 'string', 'max:120'],
            'formData.colour_name' => ['nullable', 'string', 'max:120'],
            'formData.colour_slug' => ['nullable', 'string', 'max:120'],
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
        $this->fillFromModel(SpFitment::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        if (empty($this->formData['country_slug']) && ! empty($this->formData['country_name'])) {
            $this->formData['country_slug'] = Str::slug($this->formData['country_name']);
        }
        if (empty($this->formData['colour_slug']) && ! empty($this->formData['colour_name'])) {
            $this->formData['colour_slug'] = Str::slug($this->formData['colour_name']);
        }
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Fitment saved.');
    }

    public function delete(int $id): void
    {
        SpFitment::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = SpFitment::query()
            ->with('model:id,name,make_id')
            ->with('model.make:id,name')
            ->when($this->search, fn ($q, $v) => $q->whereHas('model', fn ($q) => $q->where('name', 'like', "%{$v}%")))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $models = SpModel::query()->with('make:id,name')->orderBy('name')->get();

        return view('flux-admin.pages.spare-parts.fitments-index', compact('rows', 'models'));
    }
}
