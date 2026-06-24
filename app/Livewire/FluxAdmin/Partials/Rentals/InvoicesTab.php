<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use App\Models\PaymentMethod;
use App\Models\RentingTransaction;
use App\Support\RentalBookingLifecycle;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class InvoicesTab extends Component
{
    public int $bookingId;

    public ?int $payingInvoiceId = null;
    public ?int $paymentMethodId = null;
    public string $paymentAmount = '';

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function openPayModal(int $invoiceId): void
    {
        $invoice = BookingInvoice::findOrFail($invoiceId);
        $paid = (float) RentingTransaction::where('invoice_id', $invoiceId)->sum('amount');
        $remaining = max((float) $invoice->amount - $paid, 0);

        $this->payingInvoiceId = $invoiceId;
        $this->paymentMethodId = PaymentMethod::where('title', 'Cash')->value('id');
        $this->paymentAmount   = number_format($remaining, 2, '.', '');
        $this->dispatch('open-modal', name: 'pay-invoice-modal');
    }

    public function markPaid(): void
    {
        $this->validate([
            'paymentMethodId' => 'required|integer|exists:payment_methods,id',
            'paymentAmount'   => 'required|numeric|min:0.01',
            'payingInvoiceId' => 'required|integer',
        ]);

        try {
            app(RentalBookingLifecycle::class)->recordPayment(
                $this->bookingId,
                $this->payingInvoiceId,
                $this->paymentMethodId,
                (float) $this->paymentAmount
            );

            $this->payingInvoiceId = null;
            $this->paymentMethodId = null;
            $this->paymentAmount   = '';
            $this->dispatch('close-modal', name: 'pay-invoice-modal');
            $this->flashMessage = 'Payment recorded.';
            $this->flashType    = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType    = 'error';
        }
    }

    public function reversePayment(int $invoiceId): void
    {
        try {
            $invoice = BookingInvoice::where('booking_id', $this->bookingId)->findOrFail($invoiceId);
            app(RentalBookingLifecycle::class)->reversePayment($invoice);
            $this->flashMessage = 'Latest payment reversed for invoice #'.$invoiceId.'.';
            $this->flashType    = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType    = 'error';
        }
    }

    public function markWhatsAppSent(int $invoiceId): void
    {
        BookingInvoice::findOrFail($invoiceId)->update([
            'is_whatsapp_sent'               => true,
            'whatsapp_last_reminder_sent_at' => now(),
        ]);

        $this->flashMessage = 'WhatsApp reminder marked as sent.';
        $this->flashType    = 'success';
    }

    public function updateInvoiceDate(int $invoiceId, string $date): void
    {
        BookingInvoice::findOrFail($invoiceId)->update(['invoice_date' => $date]);
        $this->flashMessage = 'Invoice date updated.';
        $this->flashType    = 'success';
    }

    public function render()
    {
        $invoices = BookingInvoice::with('user')
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('invoice_date')
            ->get();

        $totalUnpaid = $invoices->where('is_paid', false)->sum('amount');
        $paymentMethods = PaymentMethod::query()->where('is_enabled', true)->orderBy('title')->get();

        return view('flux-admin.partials.rentals.invoices-tab', [
            'invoices'       => $invoices,
            'totalUnpaid'    => $totalUnpaid,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
