<?php

namespace App\Livewire\FluxAdmin\Partials\Customers;

use App\Models\Customer;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use App\Support\RentingReferralAccess;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class RentalsReferralsSection extends Component
{
    public Customer $customer;

    public string $newName = '';

    public string $newPhone = '';

    public string $newEmail = '';

    public function mount(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function createReferral(RentingReferralService $service): void
    {
        if (! RentingReferralAccess::canView()) {
            abort(403);
        }

        try {
            $service->create($this->customer, [
                'name' => $this->newName,
                'phone' => $this->newPhone,
                'email' => $this->newEmail !== '' ? $this->newEmail : null,
            ], RentingReferral::SOURCE_ADMIN, auth()->id());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('newName', $e->getMessage());

            return;
        }

        $this->reset(['newName', 'newPhone', 'newEmail']);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Referral recorded.');
    }

    public function render(RentingReferralService $service)
    {
        if (! Schema::hasTable('renting_referrals')) {
        return view('flux-admin.partials.customers.rentals-referrals-section', [
            'made' => collect(),
            'received' => collect(),
            'directAwards' => collect(),
            'availablePoints' => 0,
            'pendingPoints' => 0,
            'eligible' => false,
        ]);
        }

        $made = RentingReferral::query()
            ->where('referrer_customer_id', $this->customer->id)
            ->with(['referred', 'ledger'])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $received = RentingReferral::query()
            ->where('referred_customer_id', $this->customer->id)
            ->with(['referrer', 'ledger'])
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('flux-admin.partials.customers.rentals-referrals-section', [
            'made' => $made,
            'received' => $received,
            'directAwards' => $service->directAwardsForCustomer((int) $this->customer->id),
            'availablePoints' => $service->availablePoints((int) $this->customer->id),
            'pendingPoints' => $service->pendingPoints((int) $this->customer->id),
            'eligible' => $service->referrerIsEligible($this->customer),
        ]);
    }
}
