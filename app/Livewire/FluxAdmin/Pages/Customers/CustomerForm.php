<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerProfile;
use App\Support\FluxAdminFormPayload;
use App\Support\CustomerPortalCredentialIssuer;
use App\Support\FluxAdminAccess;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
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
            $customer->loadMissing('customerAuth');
            $attrs['is_active'] = (bool) ($customer->is_active ?? true);
            $attrs['portal_upload_access'] = (bool) ($customer->portal_upload_access ?? false);
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
                'verification_status' => 'pending',
            ];
        }
    }

    protected function formRules(): array
    {
        return [
            'form.first_name'               => ['required', 'string', 'max:100'],
            'form.last_name'                => ['required', 'string', 'max:100'],
            'form.email'                    => ['required', 'email', 'max:200', Rule::unique('customers', 'email')->ignore($this->customer?->id)],
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
            'form.is_active'                => ['nullable', 'boolean'],
            'form.portal_upload_access'     => ['nullable', 'boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules(), [
            'form.email.unique' => 'This email is already in use. Please use a different email.',
        ]);
        $payload = FluxAdminFormPayload::onlyPersistable(Customer::class, $this->sanitisePayload($data['form']));

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

    public function toggleCustomerActive(): void
    {
        $this->requirePortalUserAdmin();
        $active = ! (bool) ($this->form['is_active'] ?? true);
        $this->customer?->forceFill(['is_active' => $active])->save();
        $this->customer?->customerAuth?->forceFill(['is_active' => $active])->save();
        if ($this->customer?->customerAuth) {
            CustomerProfile::query()->where('customer_auth_id', $this->customer->customerAuth->id)->update(['is_active' => $active]);
        }
        $this->form['is_active'] = $active;
        $this->dispatch('flux-admin:toast', type: 'success', message: $active ? 'Customer activated.' : 'Customer deactivated.');
    }

    public function togglePortalActive(): void
    {
        $this->requirePortalUserAdmin();
        if (! $this->customer?->customerAuth) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Create portal access by sending credentials first.');
            return;
        }
        $active = ! (bool) $this->customer->customerAuth->is_active;
        $this->customer->customerAuth->forceFill(['is_active' => $active])->save();
        $this->customer->forceFill(['is_register' => $active])->save();
        CustomerProfile::query()->where('customer_auth_id', $this->customer->customerAuth->id)->update(['is_active' => $active]);
        $this->customer->refresh()->load('customerAuth');
        $this->form['is_register'] = $active;
        $this->dispatch('flux-admin:toast', type: 'success', message: $active ? 'Portal activated.' : 'Portal deactivated.');
    }

    public function togglePortalUploadAccess(): void
    {
        $this->requirePortalUserAdmin();
        $allowed = ! (bool) ($this->form['portal_upload_access'] ?? false);
        $this->customer?->forceFill(['portal_upload_access' => $allowed])->save();
        if ($this->customer?->customerAuth) {
            CustomerProfile::query()->where('customer_auth_id', $this->customer->customerAuth->id)->update(['portal_upload_access' => $allowed]);
        }
        $this->form['portal_upload_access'] = $allowed;
        $this->dispatch('flux-admin:toast', type: 'success', message: $allowed ? 'Document uploads allowed on portal and app.' : 'Document uploads disabled.');
    }

    public function sendPortalCredentials(string $channel = 'both'): void
    {
        $this->requirePortalUserAdmin();
        if (! $this->customer || ! CustomerPortalCredentialIssuer::issueAndNotify($this->customer, $channel)) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Customer has no email address.');
            return;
        }
        $this->customer->load('customerAuth');
        $this->dispatch('flux-admin:toast', type: 'success', message: $channel === 'sms' ? 'Credentials sent by SMS.' : ($channel === 'email' ? 'Credentials sent by email.' : 'Credentials sent by email and SMS.'));
    }

    private function requirePortalUserAdmin(): void
    {
        if (! FluxAdminAccess::canManagePortalUsers()) {
            abort(403);
        }
    }

    /** @param  array<string, mixed>  $payload */
    private function sanitisePayload(array $payload): array
    {
        static $allowed = null;
        $allowed ??= array_flip(array_diff(
            Schema::getColumnListing('customers'),
            ['id', 'created_at', 'updated_at']
        ));

        return array_intersect_key($payload, $allowed);
    }

    public function render()
    {
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.customers.form', compact('branches'));
    }
}
