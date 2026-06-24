<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\AgreementAccess;
use App\Models\RentingBooking;
use App\Support\RentalBookingLifecycle;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class AgreementTab extends Component
{
    public int $bookingId;

    public ?string $agreementUrl = null;
    public ?string $qrImage = null;
    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function generateAgreement(): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);

        if (! $booking->customer_id) {
            $this->flashMessage = 'No customer linked to this booking.';
            $this->flashType = 'error';

            return;
        }

        try {
            $result = app(RentalBookingLifecycle::class)->generateAgreementAccess(
                (int) $booking->customer_id,
                $this->bookingId
            );

            $this->agreementUrl = $result['url'];
            $this->qrImage = $result['qrImage'] ?: null;
            $this->flashMessage = $result['message'];
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function render()
    {
        $agreements = AgreementAccess::with('customer')
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get();

        return view('flux-admin.partials.rentals.agreement-tab', [
            'agreements' => $agreements,
        ]);
    }
}
