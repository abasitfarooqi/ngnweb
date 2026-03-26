<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        if (! session('club_member_id')) {
            $this->redirectRoute('ngnclub.register', navigate: false);
        }
    }

    public function logout(): void
    {
        session()->forget(['club_member_id', 'user_session_id']);
        $this->redirectRoute('ngnclub.home');
    }

    public function render()
    {
        $memberId = session('club_member_id');
        $member = ClubMember::find($memberId);

        if (! $member) {
            session()->forget('club_member_id');

            return $this->redirectRoute('ngnclub.register');
        }

        $purchases = ClubMemberPurchase::where('club_member_id', $member->id)->orderBy('id', 'desc')->get();
        $redemptions = ClubMemberRedeem::where('club_member_id', $member->id)->orderBy('id', 'desc')->get();

        $totalSpend = $purchases->sum('total');
        $totalDiscount = $purchases->sum('discount');
        $totalRedeemed = $redemptions->sum('redeem_total');
        $availableCredit = max(0, $totalDiscount - $totalRedeemed);
        $qualifiedReferral = $purchases->count() > 0;

        return view('livewire.site.club.dashboard', compact(
            'member', 'purchases', 'redemptions',
            'totalSpend', 'totalDiscount', 'totalRedeemed', 'availableCredit', 'qualifiedReferral'
        ))->layout('components.layouts.public', [
            'title' => 'NGN Club Dashboard | NGN Motors',
        ]);
    }
}
