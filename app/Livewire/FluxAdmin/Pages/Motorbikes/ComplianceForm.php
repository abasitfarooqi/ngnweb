<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\MotorbikeAnnualCompliance;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class ComplianceForm extends Component
{
    use WithAuthorization;

    public ?MotorbikeAnnualCompliance $compliance = null;

    public array $form = [];

    public function mount(?MotorbikeAnnualCompliance $compliance = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->compliance = $compliance;

        if ($compliance && $compliance->exists) {
            $attrs = $compliance->getAttributes();
            foreach (['mot_due_date', 'tax_due_date', 'insurance_due_date'] as $field) {
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
            $this->form = [];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.motorbike_id'       => ['required', 'integer', 'exists:motorbikes,id'],
            'form.year'               => ['required', 'string', 'max:4'],
            'form.mot_status'         => ['nullable', 'string', 'in:Valid,Invalid,Expired,Unknown'],
            'form.mot_due_date'       => ['nullable', 'date'],
            'form.road_tax_status'    => ['nullable', 'string', 'in:Valid,Invalid,Expired,Unknown'],
            'form.tax_due_date'       => ['nullable', 'date'],
            'form.insurance_status'   => ['nullable', 'string', 'in:Valid,Invalid,Expired,Unknown'],
            'form.insurance_due_date' => ['nullable', 'date'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->compliance && $this->compliance->exists) {
            $this->compliance->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Compliance record updated.');
        } else {
            MotorbikeAnnualCompliance::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Compliance record created.');
        }

        $this->redirect(route('flux-admin.motorbike-compliance.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.motorbikes.compliance-form');
    }
}
