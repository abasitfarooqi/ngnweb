<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\VehicleNotification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle notifications — Flux Admin')]
class VehicleNotificationIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    protected function formModel(): string
    {
        return VehicleNotification::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.first_name'    => ['required', 'string', 'max:255'],
            'formData.last_name'     => ['required', 'string', 'max:255'],
            'formData.email'         => ['nullable', 'email', 'max:255'],
            'formData.phone'         => ['nullable', 'string', 'max:50'],
            'formData.reg_no'        => ['nullable', 'string', 'max:50'],
            'formData.notify_email'  => ['boolean'],
            'formData.notify_phone'  => ['boolean'],
            'formData.enable'        => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'notify_email' => true,
            'notify_phone' => true,
            'enable'       => true,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(VehicleNotification::findOrFail($id));
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
        VehicleNotification::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = VehicleNotification::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('reg_no', 'like', "%{$v}%")))
            ->when($this->filter('enable') !== '', fn ($q) => $q->where('enable', $this->filter('enable') === '1'))
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.vehicle-notifications-index', ['rows' => $rows]);
    }
}
