<?php

namespace App\Livewire\FluxAdmin\Pages;

use App\Models\ClubMember;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Dashboard — Flux Admin')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = Cache::remember('flux-admin.dashboard.stats', now()->addMinutes(5), function () {
            $activeRentals = RentingBookingItem::whereNull('end_date')
                ->whereHas('booking', fn ($q) => $q->where('is_posted', true))
                ->where('is_posted', true)
                ->count();

            return [
                'total_motorbikes' => Motorbike::count(),
                'active_rentals' => $activeRentals,
                'finance_applications' => FinanceApplication::where('is_cancelled', false)->count(),
                'open_pcn_cases' => PcnCase::where('isClosed', false)->count(),
                'club_members' => ClubMember::where('is_active', true)->count(),
                'total_bookings' => RentingBooking::where('is_posted', true)->count(),
            ];
        });

        return view('flux-admin.pages.dashboard', compact('stats'));
    }

    public function refreshStats(): void
    {
        Cache::forget('flux-admin.dashboard.stats');
    }
}
