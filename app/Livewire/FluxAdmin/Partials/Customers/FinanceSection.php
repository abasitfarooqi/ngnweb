<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\FinanceApplication;
use App\Support\AgreementContractStorage;
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

    public function destroyContract(int $id): void
    {
        $result = CustomerContract::deleteContractFile($id);

        $this->dispatch('flux-admin:toast',
            type: $result ? 'success' : 'error',
            message: $result ? 'Contract archived to secure storage.' : 'Contract could not be archived.'
        );
    }

    public function deleteAgreement(int $id): void
    {
        $agreement = CustomerAgreement::find($id);

        if (! $agreement || empty($agreement->file_path)) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Agreement file not found.');

            return;
        }

        $sourcePath = AgreementContractStorage::normalizePath($agreement->file_path);
        $result = AgreementContractStorage::archiveRecord(CustomerAgreement::class, (int) $agreement->id, $sourcePath);

        $this->dispatch('flux-admin:toast',
            type: $result ? 'success' : 'error',
            message: $result ? 'Agreement archived to secure storage.' : 'Agreement could not be archived.'
        );
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $applications = FinanceApplication::where('customer_id', $this->customer->id)
            ->with(['customerContracts'])
            ->orderByDesc('id')
            ->get();

        $agreements = CustomerAgreement::where('customer_id', $this->customer->id)->orderByDesc('id')->get();

        return view('flux-admin.partials.customers.finance-section', compact('applications', 'agreements'));
    }
}
