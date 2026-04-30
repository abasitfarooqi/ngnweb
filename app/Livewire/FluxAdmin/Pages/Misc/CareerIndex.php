<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnCareer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Careers — Flux Admin')]
class CareerIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return NgnCareer::class; }

    protected function formRules(): array
    {
        return [
            'formData.job_title' => ['required', 'string', 'max:255'],
            'formData.description' => ['nullable', 'string'],
            'formData.employment_type' => ['nullable', 'string', 'max:120'],
            'formData.location' => ['nullable', 'string', 'max:255'],
            'formData.salary' => ['nullable', 'string', 'max:120'],
            'formData.contact_email' => ['nullable', 'email', 'max:255'],
            'formData.job_posted' => ['nullable', 'date'],
            'formData.expire_date' => ['nullable', 'date'],
            'formData.is_active' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_active' => true, 'job_posted' => now()->toDateString()];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $c = NgnCareer::findOrFail($id);
        $this->fillFromModel($c);
        foreach (['job_posted', 'expire_date'] as $k) {
            if (! empty($this->formData[$k])) {
                $this->formData[$k] = \Carbon\Carbon::parse($this->formData[$k])->format('Y-m-d');
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Job saved.');
    }

    public function delete(int $id): void
    {
        NgnCareer::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function toggleActive(int $id): void
    {
        $c = NgnCareer::findOrFail($id);
        $c->is_active = ! $c->is_active;
        $c->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Updated.');
    }

    public function render()
    {
        $rows = NgnCareer::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('job_title', 'like', "%{$v}%")->orWhere('location', 'like', "%{$v}%")))
            ->when($this->filter('is_active') !== '', fn ($q) => $q->where('is_active', $this->filter('is_active') === '1'))
            ->when($this->filter('employment_type'), fn ($q, $v) => $q->where('employment_type', $v))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.misc.careers-index', ['rows' => $rows]);
    }
}
