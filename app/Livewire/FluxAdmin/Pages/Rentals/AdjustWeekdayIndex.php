<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentingBooking;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Adjust booking weekday — Flux Admin')]
class AdjustWeekdayIndex extends Component
{
    use WithAuthorization;

    public ?int $selectedBookingId = null;

    public string $targetWeekday = 'Monday';

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function adjust(): void
    {
        $this->validate([
            'selectedBookingId' => ['required', 'integer', 'exists:renting_bookings,id'],
            'targetWeekday' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
        ]);

        $booking = RentingBooking::findOrFail($this->selectedBookingId);
        $currentStart = Carbon::parse($booking->start_date);
        $target = $currentStart->copy()->next($this->targetWeekday);
        $booking->update(['start_date' => $target->toDateString()]);

        $this->dispatch('flux-admin:toast', type: 'success', message: "Booking #{$booking->id} shifted to {$this->targetWeekday} ({$target->format('d M Y')}).");
        $this->reset('selectedBookingId');
    }

    public function render()
    {
        $bookings = RentingBooking::with('customer')
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($q) => $q->where('is_posted', 1)->whereNull('end_date'))
            ->orderBy('id')
            ->get()
            ->map(fn ($b) => (object) [
                'id' => $b->id,
                'start_date' => $b->start_date,
                'customer' => $b->customer ? $b->customer->first_name.' '.$b->customer->last_name : '—',
                'current_weekday' => $b->start_date ? Carbon::parse($b->start_date)->format('l') : '—',
            ]);

        return view('flux-admin.pages.rentals.adjust-weekday-index', ['bookings' => $bookings]);
    }
}
