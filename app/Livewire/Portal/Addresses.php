<?php

namespace App\Livewire\Portal;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\SystemCountry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Addresses extends Component
{
    public bool $showForm = false;

    public ?int $editId = null;

    public string $first_name = '';

    public string $last_name = '';

    public string $company_name = '';

    public string $street_address = '';

    public string $street_address_plus = '';

    public string $postcode = '';

    public string $city = '';

    public string $phone_number = '';

    public int $country_id;

    public string $type = 'shipping';

    public string $successMessage = '';

    public function mount(): void
    {
        $this->country_id = SystemCountry::defaultId();
    }

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'street_address' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'phone_number' => 'required|string|max:30',
            'country_id' => 'required|integer|exists:system_countries,id',
            'type' => 'required|in:shipping,billing',
        ];
    }

    protected function customerProfile(): ?Customer
    {
        return Auth::guard('customer')->user()?->customer;
    }

    protected function customerId(): ?int
    {
        $auth = Auth::guard('customer')->user();

        return $auth?->customer_id ? (int) $auth->customer_id : null;
    }

    protected function assertCanManageAddresses(): bool
    {
        $profile = $this->customerProfile();
        if (! $profile || ! $this->customerId()) {
            session()->flash('error', 'Please complete your profile before managing delivery addresses.');

            return false;
        }

        if (! $profile->canCustomerManageAddresses()) {
            session()->flash('error', 'Address management is unavailable on your account.');

            return false;
        }

        return true;
    }

    protected function prefillFromProfile(): void
    {
        $profile = $this->customerProfile();
        if (! $profile) {
            return;
        }

        if ($this->first_name === '') {
            $this->first_name = (string) ($profile->first_name ?? '');
        }
        if ($this->last_name === '') {
            $this->last_name = (string) ($profile->last_name ?? '');
        }
        if ($this->phone_number === '') {
            $this->phone_number = (string) ($profile->phone ?? '');
        }
    }

    public function openNew(): void
    {
        if (! $this->assertCanManageAddresses()) {
            return;
        }

        $this->resetForm();
        $this->editId = null;
        $this->prefillFromProfile();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        if (! $this->assertCanManageAddresses()) {
            return;
        }

        $customerId = $this->customerId();
        $address = CustomerAddress::query()
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $this->editId = $id;
        $this->first_name = $address->first_name;
        $this->last_name = $address->last_name;
        $this->company_name = $address->company_name ?? '';
        $this->street_address = $address->street_address;
        $this->street_address_plus = $address->street_address_plus ?? '';
        $this->postcode = $address->postcode;
        $this->city = $address->city;
        $this->phone_number = $address->phone_number;
        $this->country_id = $address->country_id ?? SystemCountry::defaultId();
        $this->type = $address->type ?? 'shipping';
        $this->showForm = true;
    }

    public function save(): void
    {
        if (! $this->assertCanManageAddresses()) {
            return;
        }

        $this->validate();

        if (! SystemCountry::query()->whereKey($this->country_id)->exists()) {
            $this->country_id = SystemCountry::defaultId();
        }

        $customerId = $this->customerId();
        if (! $customerId) {
            return;
        }

        $data = [
            'customer_id' => $customerId,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company_name' => $this->company_name ?: '-',
            'street_address' => $this->street_address,
            'street_address_plus' => $this->street_address_plus ?: '-',
            'postcode' => $this->postcode,
            'city' => $this->city,
            'phone_number' => $this->phone_number,
            'country_id' => $this->country_id,
            'type' => $this->type,
        ];

        if ($this->editId) {
            $address = CustomerAddress::query()
                ->where('id', $this->editId)
                ->where('customer_id', $customerId)
                ->firstOrFail();
            $address->update($data);
            $this->successMessage = 'Address updated.';
        } else {
            $hasDefault = CustomerAddress::query()->where('customer_id', $customerId)->exists();
            $data['is_default'] = ! $hasDefault;
            CustomerAddress::query()->create($data);
            $this->successMessage = 'Address added.';
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function setDefault(int $id): void
    {
        if (! $this->assertCanManageAddresses()) {
            return;
        }

        $customerId = $this->customerId();
        if (! $customerId) {
            return;
        }

        CustomerAddress::query()
            ->where('customer_id', $customerId)
            ->update(['is_default' => false]);

        CustomerAddress::query()
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->update(['is_default' => true]);

        $this->successMessage = 'Default address updated.';
    }

    public function delete(int $id): void
    {
        if (! $this->assertCanManageAddresses()) {
            return;
        }

        $customerId = $this->customerId();
        if (! $customerId) {
            return;
        }

        $address = CustomerAddress::query()
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();
        if (! $address) {
            return;
        }

        $wasDefault = (bool) $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $replacement = CustomerAddress::query()
                ->where('customer_id', $customerId)
                ->orderByDesc('id')
                ->first();
            if ($replacement) {
                $replacement->is_default = true;
                $replacement->save();
            }
        }

        $this->successMessage = 'Address removed.';
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    protected function resetForm(): void
    {
        $this->first_name = '';
        $this->last_name = '';
        $this->company_name = '';
        $this->street_address = '';
        $this->street_address_plus = '';
        $this->postcode = '';
        $this->city = '';
        $this->phone_number = '';
        $this->country_id = SystemCountry::defaultId();
        $this->type = 'shipping';
        $this->resetValidation();
    }

    public function render()
    {
        $profile = $this->customerProfile();
        $customerId = $this->customerId();
        $addresses = $customerId
            ? CustomerAddress::query()->where('customer_id', $customerId)->orderByDesc('is_default')->get()
            : collect();
        $countries = SystemCountry::query()->orderBy('name')->get();
        $canManageAddresses = $profile && $profile->canCustomerManageAddresses();

        return view('livewire.portal.addresses', compact('addresses', 'countries', 'canManageAddresses', 'profile'))
            ->layout('components.layouts.portal', [
                'title' => 'My Addresses | My Account',
            ]);
    }
}
