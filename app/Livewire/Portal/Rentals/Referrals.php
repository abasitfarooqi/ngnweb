<?php

namespace App\Livewire\Portal\Rentals;

use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use App\Support\RentingReferralSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Referrals extends Component
{
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public bool $acceptTerms = false;

    public function submit(RentingReferralService $service): void
    {
        $customer = Auth::guard('customer')->user()?->customer;
        if (! $customer) {
            abort(403);
        }

        $this->validate([
            'acceptTerms' => 'accepted',
        ], [
            'acceptTerms.accepted' => 'Please confirm you have read and accept the rental referral terms.',
        ]);

        try {
            $service->create($customer, [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email !== '' ? $this->email : null,
            ], RentingReferral::SOURCE_PORTAL);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('name', $e->getMessage());

            return;
        }

        $this->reset(['name', 'phone', 'email', 'acceptTerms']);
        session()->flash('success', 'Referral sent.');
    }

    public function render(RentingReferralService $service)
    {
        $customer = Auth::guard('customer')->user()?->customer;
        if (! $customer) {
            abort(403);
        }

        $eligible = Schema::hasTable('renting_referrals') && $service->referrerIsEligible($customer);
        $rows = Schema::hasTable('renting_referrals')
            ? RentingReferral::query()
                ->where('referrer_customer_id', $customer->id)
                ->with('ledger')
                ->orderByDesc('id')
                ->get()
            : collect();

        $share = $rows->first();

        return view('livewire.portal.rentals.referrals', [
            'eligible' => $eligible,
            'rows' => $rows,
            'availablePoints' => $service->availablePoints((int) $customer->id),
            'pendingPoints' => $service->pendingPoints((int) $customer->id),
            'redeemedPoints' => $service->portalRedeemedPoints((int) $customer->id),
            'redeemedFreeWeeks' => $service->appliedFreeWeekCountForCustomer((int) $customer->id),
            'pointsPerWeek' => RentingReferralSettings::pointsPerQualifiedReferral(),
            'shareUrl' => $share?->shareUrl(),
            'shareCode' => $share?->referral_code,
        ])->layout('components.layouts.portal', [
            'title' => 'Refer a friend | My Account',
        ]);
    }
}
