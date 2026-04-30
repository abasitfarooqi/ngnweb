<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentingBooking;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Active bookings summary — Flux Admin')]
class ActiveBookingsSummary extends Component
{
    use WithAuthorization;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->requireRole('Admin');
    }

    public function render()
    {
        $bookings = RentingBooking::with(['customer', 'rentingBookingItems.motorbike'])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($q) => $q->where('is_posted', true)->whereNull('end_date'))
            ->orderBy('start_date')
            ->get();

        $byWeekday = $bookings->groupBy(fn ($b) => $b->start_date ? Carbon::parse($b->start_date)->format('l') : 'Unknown');
        $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'Unknown'];

        $totalWeekly = (float) $bookings->flatMap->rentingBookingItems->whereNull('end_date')->sum('weekly_rent');

        return view('flux-admin.pages.rentals.active-bookings-summary', compact('bookings', 'byWeekday', 'weekdays', 'totalWeekly'));
    }

    protected function requireRole(string $role): void
    {
        $user = auth()->user();
        if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasRole($role)) {
            abort(403);
        }
    }
}
