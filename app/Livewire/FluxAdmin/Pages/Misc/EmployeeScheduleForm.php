<?php

namespace App\Livewire\FluxAdmin\Pages\Misc;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\EmployeeSchedule;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Employee Schedule — Flux Admin')]
class EmployeeScheduleForm extends Component
{
    use WithAuthorization;

    public ?EmployeeSchedule $employeeSchedule = null;

    public array $form = [];

    public function mount(?EmployeeSchedule $employeeSchedule = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->employeeSchedule = $employeeSchedule?->id ? $employeeSchedule : null;

        if ($this->employeeSchedule) {
            $this->form = [
                'user_id' => $this->employeeSchedule->user_id,
                'off_day' => Carbon::parse($this->employeeSchedule->getRawOriginal('off_day'))->format('Y-m-d'),
            ];
        } else {
            $this->form = ['off_day' => now()->toDateString()];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.user_id' => ['required', 'integer', 'exists:users,id'],
            'form.off_day' => ['required', 'date'],
        ]);

        $payload = [
            'user_id' => $this->form['user_id'],
            'off_day' => $this->form['off_day'],
        ];

        if ($this->employeeSchedule) {
            $this->employeeSchedule->update($payload);
        } else {
            EmployeeSchedule::create($payload);
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Schedule saved.');
        $this->redirect(route('flux-admin.employee-schedules.index'), navigate: true);
    }

    public function render()
    {
        $users = User::query()->orderBy('first_name')->limit(500)->get(['id', 'first_name', 'last_name']);

        return view('flux-admin.pages.misc.employee-schedule-form', compact('users'));
    }
}
