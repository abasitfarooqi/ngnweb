<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Mail\RentalAgreement;
use App\Models\AgreementAccess;
use App\Models\RentingBooking;
use App\Support\RentalBookingLifecycle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

    public function sendAgreementLinkEmail(?int $agreementAccessId = null): void
    {
        $booking = RentingBooking::with('customer')->findOrFail($this->bookingId);
        $customer = $booking->customer;

        if (! $customer?->email) {
            $this->flashMessage = 'Customer has no email address.';
            $this->flashType = 'error';

            return;
        }

        $access = $agreementAccessId
            ? AgreementAccess::where('booking_id', $this->bookingId)->whereKey($agreementAccessId)->first()
            : AgreementAccess::where('booking_id', $this->bookingId)->orderByDesc('created_at')->first();

        if (! $access) {
            $this->flashMessage = 'Generate an agreement link first, then send it by email.';
            $this->flashType = 'error';

            return;
        }

        if ($access->expires_at && $access->expires_at->isPast()) {
            $this->flashMessage = 'This agreement link has expired. Generate a new one first.';
            $this->flashType = 'error';

            return;
        }

        $url = AgreementAccess::customerSigningUrl((int) $access->customer_id, (string) $access->passcode);

        try {
            Mail::to([$customer->email, 'customerservice@neguinhomotors.co.uk'])->send(new RentalAgreement([
                'title' => 'Rental Agreement',
                'body' => 'Dear valued customer, please review and sign your rental agreement: '.$url,
                'url' => $url,
            ]));

            $this->agreementUrl = $url;
            $this->flashMessage = 'Signing link emailed to '.$customer->email.'.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            Log::error('Agreement link email failed: '.$e->getMessage());
            $this->flashMessage = 'Could not send email: '.$e->getMessage();
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
