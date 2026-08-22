<?php

namespace App\Livewire\Site\Rentals;

use App\Models\RentingReferral;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ReferLanding extends Component
{
    public string $code = '';

    public ?RentingReferral $referral = null;

    public function mount(string $code): void
    {
        $this->code = strtoupper(trim($code));

        if (Schema::hasTable('renting_referrals')) {
            $this->referral = RentingReferral::query()
                ->where('referral_code', $this->code)
                ->first();
        }

        if ($this->referral) {
            session(['renting_referral_code' => $this->referral->referral_code]);
        }
    }

    public function render()
    {
        return view('livewire.site.rentals.refer-landing')
            ->layout('components.layouts.public', [
                'title' => 'Rental referral | NGN Motors',
            ]);
    }
}
