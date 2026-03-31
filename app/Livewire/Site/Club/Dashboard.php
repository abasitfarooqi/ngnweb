<?php

namespace App\Livewire\Site\Club;

use App\Models\ClubMember;
use App\Services\Club\ClubMemberDashboardData;
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

        $dash = ClubMemberDashboardData::forMember($member);

        return view('livewire.site.club.dashboard', [
            'member' => $member,
            'dash' => $dash,
        ])->layout('components.layouts.public', [
            'title' => 'NGN Club Dashboard | NGN Motors',
        ]);
    }
}
