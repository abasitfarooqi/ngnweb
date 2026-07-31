<?php

namespace App\Livewire\Portal;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
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

    public function render()
    {
        $profile = $this->resolvePortalCustomer();

        return view('livewire.portal.profile', [
            'fullName' => trim(($profile->first_name ?? '').' '.($profile->last_name ?? '')),
            'email' => (string) (Auth::guard('customer')->user()->email ?? ''),
            'phone' => (string) ($profile->phone ?? ''),
            'whatsapp' => (string) ($profile->whatsapp ?? ''),
        ])->layout('components.layouts.portal', ['title' => 'My Profile | My Account']);
    }
}
