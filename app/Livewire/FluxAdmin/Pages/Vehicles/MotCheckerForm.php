<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\MotChecker;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class MotCheckerForm extends Component
{
    use WithAuthorization;

    public ?MotChecker $motChecker = null;

    public array $form = [];

    public function mount(?MotChecker $motChecker = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motChecker = $motChecker;

        if ($motChecker && $motChecker->exists) {
            $attrs = $motChecker->getAttributes();
            if (! empty($attrs['mot_due_date'])) {
                try {
                    $attrs['mot_due_date'] = Carbon::parse($attrs['mot_due_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['mot_due_date'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.vehicle_registration' => ['required', 'string', 'max:20'],
            'form.mot_due_date'         => ['required', 'date'],
            'form.email'                => ['required', 'email', 'max:255'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->motChecker && $this->motChecker->exists) {
            $this->motChecker->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Subscriber updated.');
        } else {
            MotChecker::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Subscriber created.');
        }

        $this->redirect(route('flux-admin.mot-checker.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.mot-checker-form');
    }
}
