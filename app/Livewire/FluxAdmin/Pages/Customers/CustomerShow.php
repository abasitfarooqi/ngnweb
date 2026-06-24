<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Models\Customer;
use App\Models\FinanceApplication;
use App\Models\PcnCase;
use App\Models\RentingBooking;
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
        $id = $this->customer->id;

        $tabCounts = [
            'bookings' => RentingBooking::where('customer_id', $id)->count(),
            'finance'  => FinanceApplication::where('customer_id', $id)->count(),
            'pcn'      => PcnCase::where('customer_id', $id)->count(),
        ];

        return view('flux-admin.pages.customers.show', compact('tabCounts'));
    }
}
