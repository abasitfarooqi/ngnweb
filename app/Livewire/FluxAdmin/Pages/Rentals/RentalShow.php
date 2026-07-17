<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Models\BookingClosing;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Support\RentalBookingLifecycle;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
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

    public bool $prefillCollect = false;

    public function mount(RentingBooking $booking): void
    {
        if (request()->query('activeTab')) {
            $this->activeTab = (string) request()->query('activeTab');
        } elseif (request()->query('tab')) {
            $this->activeTab = (string) request()->query('tab');
        }

        $this->booking = $booking->load(['customer', 'rentingBookingItems.motorbike', 'bookingClosing']);

        if (session()->has('status')) {
            $this->flashMessage = (string) session('status');
            $this->flashType = 'success';
        }

        if ($this->activeTab === 'restart' && $this->lifecycleStatus() === RentalBookingLifecycle::STATUS_ACTIVE) {
            $this->activeTab = 'items';
        }
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
            $this->redirect(route('flux-admin.rentals.index'), navigate: true);
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function startEndRental(): void
    {
        $this->prefillCollect = true;
        $this->activeTab = 'closing';
        $this->dispatch('prefill-collect-motorbike');
    }

    public function refreshBooking(): void
    {
        $this->booking = $this->booking->fresh(['customer', 'rentingBookingItems.motorbike', 'bookingClosing']);
    }

    #[On('rental-updated')]
    public function onRentalUpdated(): void
    {
        $this->refreshBooking();
    }

    #[On('set-rental-tab')]
    public function setRentalTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /** @return array{date: ?string, time: ?string, label: string}|null */
    public function endedMeta(): ?array
    {
        $endedItem = $this->booking->rentingBookingItems->first(
            fn (RentingBookingItem $item) => $item->end_date !== null
        );

        if (! $endedItem) {
            return null;
        }

        /** @var BookingClosing|null $closing */
        $closing = $this->booking->bookingClosing
            ?? BookingClosing::query()->where('booking_id', $this->booking->id)->first();

        $date = Carbon::parse($endedItem->end_date)->format('d M Y');
        $time = null;
        if ($closing?->collect_time) {
            $time = substr((string) $closing->collect_time, 0, 5);
        }

        $label = $time ? "{$date} at {$time}" : $date;

        return ['date' => $date, 'time' => $time, 'label' => $label];
    }

    public function render()
    {
        $lifecycle = $this->lifecycleStatus();
        $docChecklist = app(RentalBookingLifecycle::class)->documentChecklist($this->booking);
        $missingDocs = collect($docChecklist)->reject(fn ($d) => $d['approved'])->count();
        $endedMeta = $lifecycle === 'ended' ? $this->endedMeta() : null;

        return view('flux-admin.pages.rentals.show', compact('lifecycle', 'docChecklist', 'missingDocs', 'endedMeta'));
    }
}
