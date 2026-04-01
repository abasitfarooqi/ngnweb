<?php

namespace App\Livewire\Portal;

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

    public int $country_id = 3;

    public string $type = 'shipping';

    public string $successMessage = '';

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

    public function openNew(): void
    {
        $this->resetForm();
        $this->editId = null;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $customer = Auth::guard('customer')->user();
        $address = CustomerAddress::where('id', $id)
            ->where('customer_id', $customer->customer_id)
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
        $this->country_id = $address->country_id ?? 3;
        $this->type = $address->type ?? 'shipping';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $customer = Auth::guard('customer')->user();

        $data = [
            'customer_id' => $customer->customer_id,
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
            $address = CustomerAddress::where('id', $this->editId)
                ->where('customer_id', $customer->customer_id)
                ->firstOrFail();
            $address->update($data);
            $this->successMessage = 'Address updated.';
        } else {
            $hasDefault = CustomerAddress::where('customer_id', $customer->customer_id)->exists();
            $data['is_default'] = ! $hasDefault;
            CustomerAddress::create($data);
            $this->successMessage = 'Address added.';
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function setDefault(int $id): void
    {
        $customer = Auth::guard('customer')->user();

        CustomerAddress::where('customer_id', $customer->customer_id)
            ->update(['is_default' => false]);

        CustomerAddress::where('id', $id)
            ->where('customer_id', $customer->customer_id)
            ->update(['is_default' => true]);

        $this->successMessage = 'Default address updated.';
    }

    public function delete(int $id): void
    {
        $customer = Auth::guard('customer')->user();
        $address = CustomerAddress::where('id', $id)
            ->where('customer_id', $customer->customer_id)
            ->first();
        if (! $address) {
            return;
        }

        $wasDefault = (bool) $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $replacement = CustomerAddress::where('customer_id', $customer->customer_id)
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
        $this->country_id = 3;
        $this->type = 'shipping';
        $this->resetValidation();
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();
        $addresses = CustomerAddress::where('customer_id', $customer->customer_id)
            ->orderByDesc('is_default')
            ->get();
        $countries = SystemCountry::orderBy('name')->get();

        return view('livewire.portal.addresses', compact('addresses', 'countries'))
            ->layout('components.layouts.portal', [
                'title' => 'My Addresses | My Account',
            ]);
    }
}
