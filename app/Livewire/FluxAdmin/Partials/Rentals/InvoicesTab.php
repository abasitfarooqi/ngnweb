<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\PaymentMethod;
use App\Services\RentingInvoiceSyncService;
use App\Support\RentalBookingLifecycle;
use App\Support\RentalInvoiceTabData;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class InvoicesTab extends Component
{
    public int $bookingId;

    public bool $showPayModal = false;

    public ?int $payingInvoiceId = null;

    public ?int $paymentMethodId = null;

    public string $paymentAmount = '';

    public float $paymentOutstanding = 0.0;

    public ?int $expandedInvoiceId = null;

    /** @var array<string, mixed>|null */
    public ?array $expandedDetail = null;

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    #[On('rental-updated')]
    public function refreshInvoices(): void
    {
        $this->expandedDetail = $this->expandedInvoiceId
            ? RentalInvoiceTabData::detail($this->expandedInvoiceId)
            : null;
    }

    public function toggleInvoice(int $invoiceId): void
    {
        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($invoiceId)
            ->first();

        if (! $invoice || $invoice->is_paid) {
            return;
        }

        if ($this->expandedInvoiceId === $invoiceId) {
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;

            return;
        }

        $this->expandedInvoiceId = $invoiceId;
        $this->expandedDetail = RentalInvoiceTabData::detail($invoiceId) ?? [];
    }

    public function openPayModal(int $invoiceId, float $outstandingBalance): void
    {
        $remaining = max($outstandingBalance, 0);

        $this->payingInvoiceId = $invoiceId;
        $this->paymentOutstanding = $remaining;
        $this->paymentMethodId = PaymentMethod::where('title', 'Cash')->value('id');
        $this->paymentAmount = number_format($remaining, 2, '.', '');
        $this->showPayModal = true;
    }

    public function closePayModal(): void
    {
        $this->showPayModal = false;
        $this->payingInvoiceId = null;
        $this->paymentAmount = '';
        $this->paymentOutstanding = 0.0;
    }

    public function markPaid(): void
    {
        $this->validate([
            'paymentMethodId' => 'required|integer|exists:payment_methods,id',
            'paymentAmount' => 'required|numeric|min:0.01',
            'payingInvoiceId' => 'required|integer',
        ]);

        try {
            $result = app(RentalBookingLifecycle::class)->recordPayment(
                $this->bookingId,
                $this->payingInvoiceId,
                $this->paymentMethodId,
                (float) $this->paymentAmount
            );

            $this->closePayModal();
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;

            $balance = (float) ($result['balance'] ?? 0);
            $this->flashMessage = $balance > 0
                ? 'Payment received. Remaining balance on this invoice: £'.number_format($balance, 2).'.'
                : 'Payment recorded. Invoice marked as paid.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function reversePayment(int $invoiceId): void
    {
        try {
            $invoice = BookingInvoice::where('booking_id', $this->bookingId)->findOrFail($invoiceId);
            app(RentalBookingLifecycle::class)->reversePayment($invoice);
            $this->expandedInvoiceId = null;
            $this->expandedDetail = null;
            $this->flashMessage = 'Latest payment reversed for invoice #'.$invoiceId.'. Customer notified.';
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function sendWhatsAppReminder(int $invoiceId): void
    {
        $detail = RentalInvoiceTabData::detail($invoiceId);
        if ($detail === null) {
            $this->flashMessage = 'Invoice not found.';
            $this->flashType = 'error';

            return;
        }

        BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($invoiceId)
            ->firstOrFail()
            ->update([
                'is_whatsapp_sent' => true,
                'whatsapp_last_reminder_sent_at' => now(),
            ]);

        if ($this->expandedInvoiceId === $invoiceId) {
            $this->expandedDetail = RentalInvoiceTabData::detail($invoiceId);
        }

        if (! empty($detail['whatsapp_url'])) {
            $this->js('window.open('.json_encode($detail['whatsapp_url']).', "_blank")');
        }

        $this->flashMessage = 'WhatsApp reminder marked as sent.'
            .(! empty($detail['whatsapp_url']) ? ' WhatsApp opened in a new tab.' : '');
        $this->flashType = 'success';
    }

    public function updateInvoiceDate(int $invoiceId, string $date): void
    {
        validator(['date' => $date], ['date' => ['required', 'date']])->validate();

        $invoice = BookingInvoice::query()
            ->where('booking_id', $this->bookingId)
            ->whereKey($invoiceId)
            ->firstOrFail();

        try {
            $result = app(RentingInvoiceSyncService::class)->resequenceUnpaidInvoiceDatesFrom($invoice->id, $date);
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';

            return;
        }

        if ($this->expandedInvoiceId === $invoiceId) {
            $this->expandedDetail = RentalInvoiceTabData::detail($invoiceId);
        }

        $this->flashMessage = ((int) $result['updated'] > 1)
            ? 'Invoice date updated. '.$result['updated'].' unpaid invoices were realigned to weekly dates.'
            : 'Invoice date updated.';
        $this->flashType = 'success';
        $this->dispatch('rental-updated');
    }

    public function render()
    {
        $invoices = RentalInvoiceTabData::rows($this->bookingId);
        $totalUnpaid = $invoices->where('is_paid', false)->sum('outstanding_balance');
        $paymentMethods = PaymentMethod::query()->where('is_enabled', true)->orderBy('title')->get();

        return view('flux-admin.partials.rentals.invoices-tab', [
            'invoices' => $invoices,
            'totalUnpaid' => $totalUnpaid,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
