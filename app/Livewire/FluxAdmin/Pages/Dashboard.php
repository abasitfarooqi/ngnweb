<?php

namespace App\Livewire\FluxAdmin\Pages;

use App\Models\BookingInvoice;
use App\Models\ClubMember;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Dashboard extends Component
{
    public function refreshStats(): void
    {
        Cache::forget('flux-admin-dashboard-stats');
    }

    protected function stats(): array
    {
        return Cache::remember('flux-admin-dashboard-stats', 300, function () {
            $activeBookings = RentingBooking::with([
                'rentingBookingItems' => fn ($q) => $q->whereNull('end_date'),
                'bookingInvoices' => fn ($q) => $q->where('is_paid', false),
            ])
                ->where('is_posted', true)
                ->whereHas('rentingBookingItems', fn ($q) => $q->whereNull('end_date'))
                ->get();

            return [
                'total_motorbikes' => Motorbike::count(),
                'active_rentals' => $activeBookings->flatMap->rentingBookingItems->count(),
                'weekly_revenue' => $activeBookings->flatMap->rentingBookingItems->sum('weekly_rent'),
                'unpaid_invoices' => $activeBookings->sum(
                    fn ($b) => $b->bookingInvoices->where('invoice_date', '<=', now())->sum('amount')
                ),
                'open_pcn_cases' => PcnCase::where('isClosed', false)->count(),
                'active_finance' => FinanceApplication::where('is_cancelled', false)
                    ->where('is_posted', true)->count(),
                'total_customers' => \App\Models\Customer::count(),
                'active_club_members' => ClubMember::where('is_active', true)->count(),
            ];
        });
    }

    public function render()
    {
        return view('livewire.flux-admin.pages.dashboard', [
            'stats' => $this->stats(),
        ])->layout('components.layouts.flux-admin', ['title' => 'Dashboard']);
    }
}
