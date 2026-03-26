<?php

namespace App\Livewire\Portal\Rentals;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyRentals extends Component
{
    public bool $showPaymentHistory = false;

    public bool $showExtendModal = false;

    public bool $showReturnModal = false;

    public ?int $selectedBooking = null;

    public int $extendWeeks = 4;

    public string $returnNotice = '';

    public function showPayments(int $bookingId): void
    {
        $this->selectedBooking = $bookingId;
        $this->showPaymentHistory = true;
    }

    public function closePayments(): void
    {
        $this->showPaymentHistory = false;
        $this->selectedBooking = null;
    }

    public function openExtendModal(int $bookingId): void
    {
        $this->selectedBooking = $bookingId;
        $this->showExtendModal = true;
    }

    public function closeExtendModal(): void
    {
        $this->showExtendModal = false;
        $this->selectedBooking = null;
    }

    public function openReturnModal(int $bookingId): void
    {
        $this->selectedBooking = $bookingId;
        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->selectedBooking = null;
    }

    public function extendRental(): void
    {
        $this->validate(['extendWeeks' => 'required|integer|min:1|max:52']);
        session()->flash('success', 'Extension request submitted for Booking #'.$this->selectedBooking.'. We will confirm shortly.');
        $this->closeExtendModal();
    }

    public function submitReturnNotice(): void
    {
        $this->validate(['returnNotice' => 'required|string|min:10']);
        session()->flash('success', 'Return notice submitted for Booking #'.$this->selectedBooking.'. We will be in touch.');
        $this->closeReturnModal();
    }

    public function render()
    {
        $profile = Auth::guard('customer')->user()->profile;
        $bookings = $profile
            ? $profile->rentingBookings()
                ->with(['rentingBookingItems.motorbike', 'bookingInvoices'])
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        $paymentHistory = collect();
        if ($this->showPaymentHistory && $this->selectedBooking) {
            try {
                $paymentHistory = \App\Models\RentingTransaction::where('booking_id', $this->selectedBooking)
                    ->with(['transactionType', 'paymentMethod'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                $paymentHistory = collect();
            }
        }

        return view('livewire.portal.rentals.my-rentals', compact('bookings', 'paymentHistory'))
            ->layout('components.layouts.portal', ['title' => 'My Rentals | My Account']);
    }
}
