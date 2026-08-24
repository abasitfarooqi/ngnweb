<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Mail\RentalAgreement;
use App\Models\AgreementAccess;
use App\Models\CustomerAgreement;
use App\Models\RentingBooking;
use App\Support\RentalBookingLifecycle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Support\AgreementContractStorage;
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

        if ($this->accessHasExpired($access)) {
            $this->flashMessage = 'This agreement link has expired. Generate a new one first.';
            $this->flashType = 'error';

            return;
        }

        $url = AgreementAccess::customerSigningUrl((int) $access->customer_id, (string) $access->passcode);

        try {
            Mail::to([$customer->email, 'customerservice@neguinhomotors.co.uk'])->send(new RentalAgreement([
                'title' => 'Rental Agreement',
                'body' => 'Agreement link sent successfully. Please check the email you provided for your agreement link. If you can\'t see the email in your inbox, please check your Spam or Junk folder.',
                'url' => $url,
                'actionLabel' => 'Open rental agreement',
            ]));

            $this->agreementUrl = $url;
            $this->flashMessage = 'Agreement link sent successfully. Please check the email you provided for your agreement link. If you can\'t see the email in your inbox, please check your Spam or Junk folder.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            Log::error('Agreement link email failed: '.$e->getMessage());
            $this->flashMessage = 'Could not send email: '.$e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function verifySignedAgreement(int $agreementId): void
    {
        $booking = RentingBooking::findOrFail($this->bookingId);
        $row = CustomerAgreement::query()
            ->where('id', $agreementId)
            ->where('booking_id', $this->bookingId)
            ->where('customer_id', $booking->customer_id)
            ->first();

        if (! $row) {
            $this->flashMessage = 'Signed agreement not found for this booking.';
            $this->flashType = 'error';

            return;
        }

        $row->is_verified = true;
        $row->save();

        $this->flashMessage = 'Signed rental agreement verified.';
        $this->flashType = 'success';
        $this->dispatch('rental-updated');
    }

    protected function accessHasExpired(AgreementAccess $access): bool
    {
        if (! $access->expires_at) {
            return false;
        }

        try {
            $expires = $access->expires_at instanceof Carbon
                ? $access->expires_at
                : Carbon::parse($access->expires_at);

            return $expires->isPast();
        } catch (\Throwable) {
            return false;
        }
    }

    public function render()
    {
        $agreements = AgreementAccess::with('customer')
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get();

        $signedAgreements = CustomerAgreement::query()
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (CustomerAgreement $row) {
                $row->public_url = AgreementContractStorage::appUrl(
                    $row->file_path,
                    (bool) $row->sent_private
                );

                return $row;
            });

        return view('flux-admin.partials.rentals.agreement-tab', [
            'agreements' => $agreements,
            'signedAgreements' => $signedAgreements,
        ]);
    }
}
