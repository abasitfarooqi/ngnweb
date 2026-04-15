<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Customer — Flux Admin')]
class CustomerShow extends Component
{
    public Customer $customer;

    public string $activeTab = 'profile';

    public function mount(Customer $customer): void
    {
        $this->customer = $customer->load([
            'customerAddresses',
            'customerDocuments.documentType',
            'clubMember',
        ]);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('flux-admin.pages.customers.show');
    }
}
