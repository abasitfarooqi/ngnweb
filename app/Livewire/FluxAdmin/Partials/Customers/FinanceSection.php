<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\FinanceApplication;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class FinanceSection extends Component
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
        $applications = FinanceApplication::where('customer_id', $this->customer->id)
            ->orderByDesc('id')
            ->get();

        return view('flux-admin.partials.customers.finance-section', compact('applications'));
    }
}
