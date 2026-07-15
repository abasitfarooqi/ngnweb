<?php

namespace App\Livewire\Portal;

use App\Models\ClubMember;
use App\Services\Club\ClubMemberDashboardData;
use App\Services\Club\ClubMemberRegistrationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Club extends Component
{
    public function render(ClubMemberRegistrationService $registration)
    {
        $customerAuth = Auth::guard('customer')->user();
        $clubMember = null;
        $dash = null;

        if ($customerAuth) {
            $customer = $customerAuth->customer;
            if ($customer) {
                $clubMember = $registration->resolveForCustomer($customer, $customerAuth->email);
            } elseif ($customerAuth->email) {
                $email = $registration->normaliseEmail((string) $customerAuth->email);
                $clubMember = ClubMember::query()
                    ->whereRaw('LOWER(TRIM(email)) = ?', [$email])
                    ->first();
            }

            if ($clubMember) {
                session(['club_member_id' => $clubMember->id]);
                $dash = ClubMemberDashboardData::forMember($clubMember);
            }
        }

        return view('livewire.portal.club', compact('clubMember', 'dash'))
            ->layout('components.layouts.portal', ['title' => 'NGN Club | My Account']);
    }
}
