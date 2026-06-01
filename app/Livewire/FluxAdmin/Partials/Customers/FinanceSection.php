<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\CustomerAgreement;
use App\Models\CustomerContract;
use App\Models\FinanceApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            message: $result ? 'Contract moved to private.' : 'Contract could not be moved.'
        );
    }

    public function deleteAgreement(int $id): void
    {
        $agreement = CustomerAgreement::find($id);

        if (! $agreement || empty($agreement->file_path)) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Agreement file not found.');

            return;
        }

        $sourcePath  = ltrim($agreement->file_path, '/');
        $diskPublic  = Storage::disk('public');
        $diskLocal   = Storage::disk('local');
        $diskPrivate = Storage::disk('private');

        if ($diskPrivate->exists($sourcePath)) {
            $agreement->sent_private = true;
            $agreement->save();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Agreement already in private storage.');

            return;
        }

        $fromDisk = $diskPublic->exists($sourcePath) ? $diskPublic : ($diskLocal->exists($sourcePath) ? $diskLocal : null);

        if (! $fromDisk) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Agreement file not found on disk.');

            return;
        }

        try {
            $diskPrivate->makeDirectory(dirname($sourcePath));
            $diskPrivate->put($sourcePath, $fromDisk->get($sourcePath));
            $fromDisk->delete($sourcePath);
            $agreement->sent_private = true;
            $agreement->save();
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Agreement moved to private.');
        } catch (\Throwable $e) {
            Log::error("Failed moving agreement {$sourcePath}: {$e->getMessage()}");
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Failed to move agreement file.');
        }
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
