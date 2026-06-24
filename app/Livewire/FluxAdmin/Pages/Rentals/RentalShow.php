<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Models\RentingBooking;
use App\Support\RentalBookingLifecycle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
class RentalShow extends Component
{
    public RentingBooking $booking;

    #[Url]
    public string $activeTab = 'items';

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function mount(RentingBooking $booking): void
    {
        if (request()->query('tab')) {
            $this->activeTab = (string) request()->query('tab');
        }

        $this->booking = $booking->load(['customer', 'rentingBookingItems.motorbike']);
    }

    public function getTitle(): string
    {
        return "Booking #{$this->booking->id} — Flux Admin";
    }

    public function lifecycleStatus(): string
    {
        return app(RentalBookingLifecycle::class)->lifecycleStatus($this->booking);
    }

    public function activateRental(): void
    {
        try {
            app(RentalBookingLifecycle::class)->activateRental($this->booking);
            $this->refreshBooking();
            $this->flashMessage = 'Rental activated for today.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function abortIntake(): void
    {
        try {
            app(RentalBookingLifecycle::class)->abortUnposted($this->booking);
            session()->flash('status', 'Intake removed.');
            $this->redirect(route('flux-admin.bookings-management.index'), navigate: true);
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function refreshBooking(): void
    {
        $this->booking = $this->booking->fresh(['customer', 'rentingBookingItems.motorbike']);
    }

    #[On('rental-updated')]
    public function onRentalUpdated(): void
    {
        $this->refreshBooking();
    }

    public function render()
    {
        $lifecycle = $this->lifecycleStatus();
        $docChecklist = app(RentalBookingLifecycle::class)->documentChecklist($this->booking);
        $missingDocs = collect($docChecklist)->reject(fn ($d) => $d['approved'])->count();

        return view('flux-admin.pages.rentals.show', compact('lifecycle', 'docChecklist', 'missingDocs'));
    }
}
