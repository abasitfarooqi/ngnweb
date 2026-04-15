<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\PcnCase;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class PcnSection extends Component
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
        $cases = PcnCase::where('customer_id', $this->customer->id)
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.customers.pcn-section', compact('cases'));
    }
}
