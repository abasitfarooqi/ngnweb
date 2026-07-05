<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class CustomerForm extends Component
{
    use WithAuthorization;

    public ?Customer $customer = null;

    public array $form = [];

    public function mount(?Customer $customer = null): void
    {
        $this->resetErrorBag();
        $this->customer = $customer;

        if ($customer && $customer->exists) {
            $attrs = $customer->getAttributes();
            foreach (['dob', 'license_issuance_date', 'license_expiry_date'] as $field) {
                if (! empty($attrs[$field])) {
                    try {
                        $attrs[$field] = \Carbon\Carbon::parse($attrs[$field])->format('Y-m-d');
                    } catch (\Throwable) {
                        $attrs[$field] = null;
                    }
                }
            }
            $this->form = $attrs;
        } else {
            $this->form = [
                'verification_status' => 'unverified',
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.first_name'               => ['required', 'string', 'max:100'],
            'form.last_name'                => ['required', 'string', 'max:100'],
            'form.email'                    => ['nullable', 'email', 'max:200'],
            'form.phone'                    => ['nullable', 'string', 'max:50'],
            'form.whatsapp'                 => ['nullable', 'string', 'max:50'],
            'form.dob'                      => ['nullable', 'date'],
            'form.address'                  => ['nullable', 'string', 'max:500'],
            'form.postcode'                 => ['nullable', 'string', 'max:20'],
            'form.city'                     => ['nullable', 'string', 'max:100'],
            'form.country'                  => ['nullable', 'string', 'max:100'],
            'form.nationality'              => ['nullable', 'string', 'max:100'],
            'form.emergency_contact'        => ['nullable', 'string', 'max:100'],
            'form.license_number'           => ['nullable', 'string', 'max:100'],
            'form.license_issuance_date'    => ['nullable', 'date'],
            'form.license_expiry_date'      => ['nullable', 'date'],
            'form.license_issuance_authority' => ['nullable', 'string', 'max:100'],
            'form.reputation_note'          => ['nullable', 'string', 'max:2000'],
            'form.rating'                   => ['nullable', 'integer', 'min:1', 'max:5'],
            'form.preferred_branch_id'      => ['nullable', 'integer'],
            'form.verification_status'      => ['nullable', 'string', 'in:verified,pending,rejected,unverified'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];

        if ($this->customer && $this->customer->exists) {
            $this->customer->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Customer updated.');
        } else {
            Customer::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Customer created.');
        }

        $this->redirect(route('flux-admin.customers.index'), navigate: true);
    }

    public function setProfileEditingUnlocked(bool $unlocked): void
    {
        if (! $this->customer?->exists) {
            return;
        }

        $this->customer->update(['profile_editing_unlocked' => $unlocked]);
        $this->form['profile_editing_unlocked'] = $unlocked;
        $this->dispatch('flux-admin:toast', type: 'success', message: $unlocked
            ? 'Customer may edit their profile again.'
            : 'Customer profile editing locked.');
    }

    public function setDocumentReuploadUnlocked(bool $unlocked): void
    {
        if (! $this->customer?->exists) {
            return;
        }

        $this->customer->update(['document_reupload_unlocked' => $unlocked]);
        $this->form['document_reupload_unlocked'] = $unlocked;
        $this->dispatch('flux-admin:toast', type: 'success', message: $unlocked
            ? 'Customer may replace approved documents.'
            : 'Approved document re-upload locked.');
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.customers.form', compact('branches'));
    }
}
