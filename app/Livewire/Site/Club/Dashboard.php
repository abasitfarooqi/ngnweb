<?php

namespace App\Livewire\Site\Club;

use App\Services\Club\ClubMemberDashboardData;
use App\Services\Club\ClubMemberSession;
use Livewire\Component;

class Dashboard extends Component
{
    public function mount(): void
    {
        if (! ClubMemberSession::check()) {
            $this->redirectRoute('ngnclub.login', navigate: false);
        }
    }

    public function logout(): void
    {
        ClubMemberSession::logout();
        $this->redirectRoute('ngnclub.home');
    }

    public function render()
    {
        $member = ClubMemberSession::member();

        if (! $member) {
            ClubMemberSession::logout();

            return $this->redirectRoute('ngnclub.login');
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
