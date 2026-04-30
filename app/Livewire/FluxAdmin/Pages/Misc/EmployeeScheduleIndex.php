<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\EmployeeSchedule;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Employee schedules — Flux Admin')]
class EmployeeScheduleIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    protected function formModel(): string { return EmployeeSchedule::class; }

    protected function formRules(): array
    {
        return [
            'formData.user_id' => ['required', 'integer', 'exists:users,id'],
            'formData.off_day' => ['required', 'date'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['off_day' => now()->toDateString()];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $s = EmployeeSchedule::findOrFail($id);
        $this->recordId = $s->id;
        $this->formData = [
            'user_id' => $s->user_id,
            'off_day' => \Carbon\Carbon::parse($s->getRawOriginal('off_day'))->format('Y-m-d'),
        ];
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
        EmployeeSchedule::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = EmployeeSchedule::query()
            ->with('user:id,first_name,last_name,email')
            ->when($this->search, fn ($q, $v) => $q->whereHas('user', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->orderByDesc('off_day')
            ->paginate($this->perPage);

        $users = User::query()->orderBy('first_name')->limit(500)->get(['id', 'first_name', 'last_name']);

        return view('flux-admin.pages.misc.employee-schedules-index', compact('rows', 'users'));
    }
}
