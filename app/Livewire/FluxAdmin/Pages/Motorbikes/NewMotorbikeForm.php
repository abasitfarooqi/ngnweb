<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\NewMotorbike;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class NewMotorbikeForm extends Component
{
    use WithAuthorization;

    public ?NewMotorbike $newMotorbike = null;

    public array $form = [];

    public function mount(?NewMotorbike $newMotorbike = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->newMotorbike = $newMotorbike;

        if ($newMotorbike && $newMotorbike->exists) {
            $attrs = $newMotorbike->getAttributes();
            if (! empty($attrs['purchase_date'])) {
                try {
                    $attrs['purchase_date'] = Carbon::parse($attrs['purchase_date'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['purchase_date'] = null;
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = ['is_migrated' => false];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.VRM'           => ['nullable', 'string', 'max:20'],
            'form.make'          => ['nullable', 'string', 'max:120'],
            'form.model'         => ['nullable', 'string', 'max:120'],
            'form.year'          => ['nullable', 'integer'],
            'form.colour'        => ['nullable', 'string', 'max:80'],
            'form.engine'        => ['nullable', 'string', 'max:80'],
            'form.VIM'           => ['nullable', 'string', 'max:80'],
            'form.branch_id'     => ['nullable', 'integer'],
            'form.status'        => ['nullable', 'string', 'max:80'],
            'form.purchase_date' => ['nullable', 'date'],
            'form.is_migrated'   => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->newMotorbike && $this->newMotorbike->exists) {
            $this->newMotorbike->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike updated.');
        } else {
            NewMotorbike::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Motorbike created.');
        }

        $this->redirect(route('flux-admin.motorbike-new.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.new-motorbike-form', compact('branches'));
    }
}
