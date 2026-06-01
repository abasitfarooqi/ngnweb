<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\VehicleNotification;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class VehicleNotificationForm extends Component
{
    use WithAuthorization;

    public ?VehicleNotification $vehicleNotification = null;

    public array $form = [];

    public function mount(?VehicleNotification $vehicleNotification = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->vehicleNotification = $vehicleNotification;

        if ($vehicleNotification && $vehicleNotification->exists) {
            $this->form = $vehicleNotification->getAttributes();
        } else {
            $this->form = [
                'notify_email' => true,
                'notify_phone' => true,
                'enable'       => true,
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.first_name'   => ['required', 'string', 'max:255'],
            'form.last_name'    => ['required', 'string', 'max:255'],
            'form.email'        => ['nullable', 'email', 'max:255'],
            'form.phone'        => ['nullable', 'string', 'max:50'],
            'form.reg_no'       => ['nullable', 'string', 'max:50'],
            'form.notify_email' => ['boolean'],
            'form.notify_phone' => ['boolean'],
            'form.enable'       => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->vehicleNotification && $this->vehicleNotification->exists) {
            $this->vehicleNotification->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Notification updated.');
        } else {
            VehicleNotification::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Notification created.');
        }

        $this->redirect(route('flux-admin.vehicle-notifications.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.vehicle-notification-form');
    }
}
