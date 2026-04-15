<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AddressesSection extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $addresses = $this->customer->customerAddresses()->orderByDesc('is_default')->get();

        return view('flux-admin.partials.customers.addresses-section', compact('addresses'));
    }
}
