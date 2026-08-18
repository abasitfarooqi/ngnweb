<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\MotorbikeCatB;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class CatBForm extends Component
{
    use WithAuthorization;

    public ?MotorbikeCatB $motorbikeCatB = null;

    public array $form = [];

    public function mount(?MotorbikeCatB $motorbikeCatB = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-commons');
        $this->motorbikeCatB = $motorbikeCatB;

        if ($motorbikeCatB && $motorbikeCatB->exists) {
            $attrs = $motorbikeCatB->getAttributes();
            if (! empty($attrs['dop'])) {
                try {
                    $attrs['dop'] = Carbon::parse($attrs['dop'])->format('Y-m-d');
                } catch (\Throwable) {
                    $attrs['dop'] = null;
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
            'form.motorbike_id' => ['required', 'integer', Rule::unique('motorbikes_cat_b', 'motorbike_id')->ignore($this->motorbikeCatB?->id)],
            'form.dop'          => ['nullable', 'date'],
            'form.notes'        => ['nullable', 'string'],
            'form.branch_id'    => ['nullable', 'integer'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->motorbikeCatB && $this->motorbikeCatB->exists) {
            $this->motorbikeCatB->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Cat-B record updated.');
        } else {
            MotorbikeCatB::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Cat-B record created.');
        }

        $this->redirect(route('flux-admin.motorbike-cat-b.index'), navigate: true);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.cat-b-form', compact('branches'));
    }
}
