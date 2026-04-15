<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\ClubMember;
use App\Models\Customer;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ClubSection extends Component
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
        $member = ClubMember::where('customer_id', $this->customer->id)->first();

        return view('flux-admin.partials.customers.club-section', compact('member'));
    }
}
