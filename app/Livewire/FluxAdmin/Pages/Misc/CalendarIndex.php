<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Calander;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Calendar — Flux Admin')]
class CalendarIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'start';
        $this->sortDirection = 'desc';
    }

    protected function formModel(): string { return Calander::class; }

    protected function formRules(): array
    {
        return [
            'formData.title' => ['required', 'string', 'max:255'],
            'formData.start' => ['required', 'date'],
            'formData.end' => ['nullable', 'date', 'after_or_equal:formData.start'],
            'formData.background_color' => ['nullable', 'string', 'max:20'],
            'formData.text_color' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['start' => now()->format('Y-m-d\TH:i'), 'background_color' => '#2563eb', 'text_color' => '#ffffff'];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(Calander::findOrFail($id));
        foreach (['start', 'end'] as $k) {
            if (! empty($this->formData[$k])) {
                $this->formData[$k] = \Carbon\Carbon::parse($this->formData[$k])->format('Y-m-d\TH:i');
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        Calander::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = Calander::query()
            ->when($this->search, fn ($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.misc.calendar-index', ['rows' => $rows]);
    }
}
