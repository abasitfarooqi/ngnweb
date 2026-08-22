<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Motorbike;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class EbikeForm extends Component
{
    use WithAuthorization;

    public ?Motorbike $motorbike = null;

    public array $form = [];

    public function mount(?Motorbike $motorbike = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motorbike = $motorbike;

        if ($motorbike && $motorbike->exists) {
            $this->form = $motorbike->getAttributes();
        } else {
            $this->form = ['is_ebike' => true];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.reg_no'     => ['nullable', 'string', 'max:20'],
            'form.make'       => ['nullable', 'string', 'max:120'],
            'form.model'      => ['nullable', 'string', 'max:120'],
            'form.year'       => ['nullable', 'integer'],
            'form.color'      => ['nullable', 'string', 'max:80'],
            'form.vin_number' => ['nullable', 'string', 'max:80'],
            'form.engine'     => ['nullable', 'string', 'max:80'],
            'form.branch_id'  => ['nullable', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];
        $payload['is_ebike'] = true;
        $payload['engine'] = trim((string) ($payload['engine'] ?? '')) ?: 'electric';

        if ($this->motorbike && $this->motorbike->exists) {
            $this->motorbike->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'E-bike updated.');
        } else {
            Motorbike::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'E-bike created.');
        }

        $this->redirect(route('flux-admin.ebikes.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.ebike-form', compact('branches'));
    }
}
