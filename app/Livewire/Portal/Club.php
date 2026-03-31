<?php

namespace App\Livewire\Portal;

use App\Models\ClubMember;
use App\Services\Club\ClubMemberDashboardData;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Club extends Component
{
    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        $clubMember = null;
        $dash = null;

        if ($customerAuth) {
            $clubMember = $customerAuth->customer?->clubMember;
            if (! $clubMember && $customerAuth->email) {
                $clubMember = ClubMember::where('email', $customerAuth->email)->first();
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
