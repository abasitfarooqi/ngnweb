<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Support\CustomerPortalCredentialIssuer;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ProfileSection extends Component
{
    public Customer $customer;

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function sendPortalCredentials(): void
    {
        if (! CustomerPortalCredentialIssuer::issueAndNotify($this->customer)) {
            $this->dispatch('flux-admin:toast', type: 'danger', message: 'Customer has no email address.');

            return;
        }

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Portal credentials sent via email and SMS.');
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        return view('flux-admin.partials.customers.profile-section');
    }
}
