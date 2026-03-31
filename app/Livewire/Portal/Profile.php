<?php

namespace App\Livewire\Portal;

use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public string $phone = '';

    public string $whatsapp = '';

    public string $postcode = '';

    public string $city = '';

    public string $country = 'United Kingdom';

    public string $preferred_branch_id = '';

    public string $dob = '';

    public string $nationality = '';

    public string $address = '';

    public string $license_number = '';

    public string $license_issuance_authority = '';

    public string $license_issuance_date = '';

    public string $license_expiry_date = '';

    public string $emergency_contact_name = '';

    public function mount(): void
    {
        $profile = $this->resolvePortalCustomer();

        $this->hydrateFromProfile($profile);
    }

    protected function resolvePortalCustomer(): Customer
    {
        $authUser = Auth::guard('customer')->user();

        if ($authUser->customer) {
            return $authUser->customer;
        }

        $customer = Customer::query()->create([
            'first_name' => 'Customer',
            'last_name' => (string) ($authUser->email ?? 'Account'),
            'username' => 'customer_'.$authUser->id,
            'email' => (string) ($authUser->email ?? ''),
            'country' => 'United Kingdom',
            'verification_status' => 'draft',
        ]);

        $authUser->customer_id = $customer->id;
        $authUser->save();

        return $customer;
    }

    protected function hydrateFromProfile(Customer $profile): void
    {
        $this->first_name = (string) ($profile->first_name ?? '');
        $this->last_name = (string) ($profile->last_name ?? '');
        $this->phone = (string) ($profile->phone ?? '');
        $this->whatsapp = (string) ($profile->whatsapp ?? '');
        $this->postcode = (string) ($profile->postcode ?? '');
        $this->city = (string) ($profile->city ?? '');
        $this->country = (string) ($profile->country ?? 'United Kingdom');
        $this->preferred_branch_id = $profile->preferred_branch_id ? (string) $profile->preferred_branch_id : '';
        $this->dob = $profile->dob ? $profile->dob->format('Y-m-d') : '';
        $this->nationality = (string) ($profile->nationality ?? '');
        $this->address = (string) ($profile->address ?? '');
        $this->license_number = (string) ($profile->license_number ?? '');
        $this->license_issuance_authority = (string) ($profile->license_issuance_authority ?? '');
        $this->license_issuance_date = $profile->license_issuance_date ? $profile->license_issuance_date->format('Y-m-d') : '';
        $this->license_expiry_date = $profile->license_expiry_date ? $profile->license_expiry_date->format('Y-m-d') : '';

        $ec = $profile->emergency_contact;
        if (is_array($ec)) {
            $this->emergency_contact_name = (string) ($ec['name'] ?? '');
        } elseif (is_string($ec) && $ec !== '') {
            $this->emergency_contact_name = $ec;
        }
    }

    public function save(): void
    {
        $profile = $this->resolvePortalCustomer();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'postcode' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'preferred_branch_id' => 'nullable|integer|exists:branches,id',
            'dob' => 'required|date',
            'nationality' => 'nullable|string|max:100',
            'address' => 'required|string|max:500',
            'license_number' => 'required|string|max:50',
            'license_issuance_authority' => 'nullable|string|max:100',
            'license_issuance_date' => 'required|date',
            'license_expiry_date' => 'required|date|after:license_issuance_date',
            'emergency_contact_name' => 'nullable|string|max:255',
        ];

        foreach (['first_name', 'last_name', 'dob', 'address', 'license_number', 'license_issuance_date', 'license_expiry_date'] as $field) {
            if ($profile->isFieldLocked($field)) {
                unset($rules[$field]);
            }
        }
        if ($profile->isFieldLocked('license_number')) {
            unset($rules['license_issuance_authority'], $rules['license_issuance_date'], $rules['license_expiry_date']);
        }

        $this->validate($rules);

        $data = [
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'country' => $this->country ?: 'United Kingdom',
            'preferred_branch_id' => $this->preferred_branch_id ? (int) $this->preferred_branch_id : null,
            'nationality' => $this->nationality,
            'emergency_contact' => $this->emergency_contact_name,
        ];

        if (! $profile->isFieldLocked('first_name')) {
            $data['first_name'] = $this->first_name;
        }
        if (! $profile->isFieldLocked('last_name')) {
            $data['last_name'] = $this->last_name;
        }
        if (! $profile->isFieldLocked('dob')) {
            $data['dob'] = $this->dob;
        }
        if (! $profile->isFieldLocked('address')) {
            $data['address'] = $this->address;
        }
        if (! $profile->isFieldLocked('license_number')) {
            $data['license_number'] = $this->license_number;
            $data['license_issuance_authority'] = $this->license_issuance_authority;
            $data['license_issuance_date'] = $this->license_issuance_date;
            $data['license_expiry_date'] = $this->license_expiry_date;
        }

        $profile->update($data);
        $profile->refresh();

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        $profile = $this->resolvePortalCustomer();
        $branches = Branch::orderBy('name')->get();

        return view('livewire.portal.profile', compact('branches', 'profile'))
            ->layout('components.layouts.portal', ['title' => 'My Profile | My Account']);
    }
}
