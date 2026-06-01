<?php

namespace App\Livewire\FluxAdmin\Pages\Surveys;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnSurvey;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Surveys — Flux Admin')]
class SurveyIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-surveys'); }

    protected function formModel(): string { return NgnSurvey::class; }

    protected function formRules(): array
    {
        return [
            'formData.title'       => ['required', 'string', 'max:255'],
            'formData.slug'        => ['nullable', 'string', 'max:255'],
            'formData.description' => ['nullable', 'string'],
            'formData.is_active'   => ['boolean'],
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
        $this->fillFromModel(NgnSurvey::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Survey saved.');
    }

    public function delete(int $id): void
    {
        NgnSurvey::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Survey deleted.');
    }

    public function toggleActive(int $id): void
    {
        $s = NgnSurvey::findOrFail($id);
        $s->is_active = ! $s->is_active;
        $s->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Updated.');
    }

    public function render()
    {
        $rows = NgnSurvey::query()
            ->withCount('questions')
            ->when($this->search, fn ($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.surveys.index', ['rows' => $rows]);
    }
}
