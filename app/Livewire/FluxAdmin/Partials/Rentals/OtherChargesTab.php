<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Mail\OtherChargesReceipt;
use App\Mail\RentalOtherChargeReminderMail;
use App\Models\PaymentMethod;
use App\Models\RentingOtherCharge;
use App\Support\RentalBookingLifecycle;
use App\Support\RentalOtherChargeTabData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class OtherChargesTab extends Component
{
    public int $bookingId;

    public string $description = '';
    public string $amount = '';
    public ?int $payingChargeId = null;
    public ?int $paymentMethodId = null;
    public bool $showPayModal = false;

    public ?int $expandedChargeId = null;

    /** @var array<string, mixed>|null */
    public ?array $expandedDetail = null;

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function addCharge(): void
    {
        $this->validate([
            'description' => 'required|string|min:3|max:255',
            'amount'      => 'required|numeric|min:0.01',
        ]);

        RentingOtherCharge::create([
            'booking_id'  => $this->bookingId,
            'description' => $this->description,
            'amount'      => $this->amount,
            'is_paid'     => false,
        ]);

        $this->description = '';
        $this->amount = '';
        $this->resetValidation();
        $this->flashMessage = 'Additional charge added.';
        $this->flashType    = 'success';
    }

    public function toggleCharge(int $chargeId): void
    {
        $charge = RentingOtherCharge::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($chargeId)
            ->first();

        if (! $charge || (bool) $charge->getRawOriginal('is_paid')) {
            return;
        }

        if ($this->expandedChargeId === $chargeId) {
            $this->expandedChargeId = null;
            $this->expandedDetail = null;

            return;
        }

        $this->expandedChargeId = $chargeId;
        $this->expandedDetail = RentalOtherChargeTabData::detail($chargeId, $this->bookingId) ?? [];
    }

    public function openPayModal(int $chargeId): void
    {
        $charge = RentingOtherCharge::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($chargeId)
            ->first();

        if (! $charge) {
            $this->flashMessage = 'Charge not found for this booking.';
            $this->flashType = 'error';

            return;
        }

        if ((bool) $charge->getRawOriginal('is_paid')) {
            $this->flashMessage = 'Charge is already paid.';
            $this->flashType = 'error';

            return;
        }

        $this->resetValidation();
        $this->payingChargeId = $chargeId;
        $this->paymentMethodId = PaymentMethod::query()
            ->where('is_enabled', true)
            ->orderByRaw("CASE WHEN title = 'Cash' THEN 0 ELSE 1 END")
            ->orderBy('title')
            ->value('id');
        $this->showPayModal = true;
    }

    public function payCharge(): void
    {
        $this->validate([
            'payingChargeId'  => 'required|integer',
            'paymentMethodId' => 'required|integer|exists:payment_methods,id',
        ]);

        try {
            $charge = RentingOtherCharge::query()
                ->where('booking_id', $this->bookingId)
                ->whereKey($this->payingChargeId)
                ->firstOrFail();

            app(RentalBookingLifecycle::class)->payOtherCharge(
                (int) $charge->id,
                (int) $this->paymentMethodId
            );

            $emailSent = $this->sendPaymentReceipt($charge->fresh());

            $this->payingChargeId = null;
            $this->paymentMethodId = null;
            $this->expandedChargeId = null;
            $this->expandedDetail = null;
            $this->showPayModal = false;
            $this->flashMessage = $emailSent
                ? 'Charge paid and receipt emailed to customer and NGN.'
                : 'Charge paid and transaction recorded, but the receipt email could not be sent.';
            $this->flashType    = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType    = 'error';
        }
    }

    public function closePayModal(): void
    {
        $this->showPayModal = false;
        $this->payingChargeId = null;
        $this->paymentMethodId = null;
        $this->resetValidation();
    }

    public function sendWhatsAppReminder(int $chargeId): void
    {
        $detail = RentalOtherChargeTabData::detail($chargeId, $this->bookingId);
        if ($detail === null) {
            $this->flashMessage = 'Charge not found.';
            $this->flashType = 'error';

            return;
        }

        RentingOtherCharge::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($chargeId)
            ->firstOrFail()
            ->update([
                'is_whatsapp_sent' => true,
                'whatsapp_last_reminder_sent_at' => now(),
            ]);

        if ($this->expandedChargeId === $chargeId) {
            $this->expandedDetail = RentalOtherChargeTabData::detail($chargeId, $this->bookingId);
        }

        if (! empty($detail['whatsapp_url'])) {
            $this->js('window.open('.json_encode($detail['whatsapp_url']).', "_blank")');
        }

        $this->flashMessage = 'WhatsApp reminder marked as sent.'
            .(! empty($detail['whatsapp_url']) ? ' WhatsApp opened in a new tab.' : '');
        $this->flashType = 'success';
    }

    public function sendEmailReminder(int $chargeId): void
    {
        $detail = RentalOtherChargeTabData::detail($chargeId, $this->bookingId);
        if ($detail === null) {
            $this->flashMessage = 'Charge not found.';
            $this->flashType = 'error';

            return;
        }

        $email = trim((string) ($detail['customer_email'] ?? ''));
        if ($email === '') {
            $this->flashMessage = 'Customer has no email address on file.';
            $this->flashType = 'error';

            return;
        }

        try {
            Mail::to([$email, 'customerservice@neguinhomotors.co.uk'])
                ->send(new RentalOtherChargeReminderMail($detail));

            RentingOtherCharge::query()
                ->where('booking_id', $this->bookingId)
                ->whereKey($chargeId)
                ->update(['email_last_reminder_sent_at' => now()]);

            if ($this->expandedChargeId === $chargeId) {
                $this->expandedDetail = RentalOtherChargeTabData::detail($chargeId, $this->bookingId);
            }

            $this->flashMessage = 'Email reminder sent to '.$email.' and NGN.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = 'Email failed: '.$e->getMessage();
            $this->flashType = 'error';
        }
    }

    private function sendPaymentReceipt(RentingOtherCharge $charge): bool
    {
        try {
            $charge->loadMissing('booking.customer');

            $booking = $charge->booking;
            $customer = $booking?->customer;
            $email = trim((string) ($customer?->email ?? ''));

            if (! $booking || ! $customer || $email === '') {
                return false;
            }

            Mail::to([$email, 'customerservice@neguinhomotors.co.uk'])->send(new OtherChargesReceipt([
                'email' => [$email, 'customerservice@neguinhomotors.co.uk'],
                'title' => 'Rental Other Charge Payment Receipt',
                'body' => 'Find your other charge payment details below.',
                'customer_name' => trim($customer->first_name.' '.$customer->last_name),
                'booking_id' => $booking->id,
                'charges_id' => $charge->id,
                'charges_description' => $charge->description,
                'charges_date' => $charge->created_at,
                'transaction_date' => now(),
                'amount' => (float) str_replace(',', '', (string) $charge->amount),
            ]));

            return true;
        } catch (\Throwable $e) {
            Log::error('Failed to send other charge receipt from Flux Admin: '.$e->getMessage(), [
                'charge_id' => $charge->id,
                'booking_id' => $this->bookingId,
            ]);

            return false;
        }
    }

    public function render()
    {
        $charges = RentingOtherCharge::where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get();

        $totalAmount = $charges->sum(fn ($c) => (float) str_replace(',', '', $c->getRawOriginal('amount')));
        $paidAmount  = $charges->filter(fn ($c) => (bool) $c->getRawOriginal('is_paid'))
            ->sum(fn ($c) => (float) str_replace(',', '', $c->getRawOriginal('amount')));

        $paymentMethods = PaymentMethod::query()->where('is_enabled', true)->orderBy('title')->get();

        return view('flux-admin.partials.rentals.other-charges-tab', [
            'charges'        => $charges,
            'totalAmount'    => $totalAmount,
            'paidAmount'     => $paidAmount,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
