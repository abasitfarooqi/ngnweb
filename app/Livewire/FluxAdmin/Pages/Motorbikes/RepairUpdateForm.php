<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\MotorbikeRepairServicesList;
use App\Models\MotorbikeRepairUpdate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Repair update — Flux Admin')]
class RepairUpdateForm extends Component
{
    use WithAuthorization;

    public ?MotorbikeRepairUpdate $motorbikeRepairUpdate = null;

    public array $form = [];

    public function mount(?MotorbikeRepairUpdate $motorbikeRepairUpdate = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motorbikeRepairUpdate = $motorbikeRepairUpdate;

        if ($motorbikeRepairUpdate && $motorbikeRepairUpdate->exists) {
            $this->form = $motorbikeRepairUpdate->getAttributes();
            $this->form['services'] = $motorbikeRepairUpdate->services
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();
        } else {
            $this->form = [
                'price' => 0,
                'services' => [],
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.motorbike_repair_id' => ['required', 'integer'],
            'form.job_description' => ['required', 'string'],
            'form.price' => ['required', 'numeric', 'min:0'],
            'form.note' => ['nullable', 'string'],
            'form.services' => ['array'],
            'form.services.*' => ['integer', 'exists:motorbike_repair_services_lists,id'],
        ]);

        $payload = collect($this->form)->only([
            'motorbike_repair_id', 'job_description', 'price', 'note',
        ])->all();

        if ($this->motorbikeRepairUpdate && $this->motorbikeRepairUpdate->exists) {
            $this->motorbikeRepairUpdate->update($payload);
            $model = $this->motorbikeRepairUpdate;
            $message = 'Repair update saved.';
        } else {
            $model = MotorbikeRepairUpdate::create($payload);
            $message = 'Repair update created.';
        }

        $ids = collect($this->form['services'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $model->services()->sync($ids);

        $this->dispatch('flux-admin:toast', type: 'success', message: $message);
        $this->redirect(route('flux-admin.motorbike-repair-updates.index'), navigate: true);
    }

    public function render()
    {
        $services = MotorbikeRepairServicesList::query()->orderBy('name')->get(['id', 'name', 'price']);

        return view('flux-admin.pages.motorbikes.repair-update-form', compact('services'));
    }
}
