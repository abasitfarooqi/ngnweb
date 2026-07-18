<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RecoveredMotorbike;
use App\Support\FluxAdminFormPayload;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class RecoveredMotorbikeForm extends Component
{
    use WithAuthorization;

    public ?RecoveredMotorbike $recoveredMotorbike = null;

    public array $form = [];

    public function mount(?RecoveredMotorbike $recoveredMotorbike = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->recoveredMotorbike = $recoveredMotorbike;

        if ($recoveredMotorbike && $recoveredMotorbike->exists) {
            $attrs = $recoveredMotorbike->getAttributes();
            foreach (['case_date', 'returned_date'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $attrs[$field] = Carbon::parse($attrs[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$field] = null;
                    }
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'case_date' => now()->toDateString(),
                'user_id'   => FluxAdminFormPayload::adminUserId(),
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.case_date'     => ['required', 'date'],
            'form.motorbike_id'  => ['required', 'integer'],
            'form.branch_id'     => ['nullable', 'integer'],
            'form.notes'         => ['nullable', 'string'],
            'form.returned_date' => ['nullable', 'date'],
            'form.user_id'       => ['nullable', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = FluxAdminFormPayload::onlyPersistable(RecoveredMotorbike::class, $data['form']);
        if (empty($payload['user_id'])) {
            $payload['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        if ($this->recoveredMotorbike && $this->recoveredMotorbike->exists) {
            $this->recoveredMotorbike->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Record updated.');
        } else {
            RecoveredMotorbike::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Record created.');
        }

        $this->redirect(route('flux-admin.recovered-motorbikes.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.vehicles.recovered-motorbike-form');
    }
}
