<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class DocumentsSection extends Component
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
        $documents = $this->customer->customerDocuments()->with('documentType')->orderByDesc('id')->get();

        return view('flux-admin.partials.customers.documents-section', compact('documents'));
    }
}
