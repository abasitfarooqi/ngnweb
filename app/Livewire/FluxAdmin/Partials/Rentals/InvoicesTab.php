<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class InvoicesTab extends Component
{
    public int $bookingId;

    public ?int $payingInvoiceId = null;
    public string $paymentMethod = '';
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
        $this->payingInvoiceId = $invoiceId;
        $this->paymentMethod   = '';
        $this->paymentAmount   = number_format((float) $invoice->amount, 2);
        $this->dispatch('open-modal', name: 'pay-invoice-modal');
    }

    public function markPaid(): void
    {
        $this->validate([
            'paymentMethod' => 'required|in:Cash,Card',
            'paymentAmount' => 'required|numeric|min:0.01',
        ], [
            'paymentMethod.required' => 'Please select a payment method.',
            'paymentAmount.required' => 'Please enter the amount received.',
        ]);

        $invoice = BookingInvoice::findOrFail($this->payingInvoiceId);
        $invoice->update([
            'is_paid'   => true,
            'paid_date' => now()->toDateString(),
            'state'     => 'Completed',
            'notes'     => ($invoice->notes ? $invoice->notes.'; ' : '').'Paid via '.$this->paymentMethod.' — £'.$this->paymentAmount.' on '.now()->format('d M Y'),
        ]);

        $this->payingInvoiceId = null;
        $this->paymentMethod   = '';
        $this->paymentAmount   = '';
        $this->dispatch('close-modal', name: 'pay-invoice-modal');

        $this->flashMessage = 'Invoice marked as paid.';
        $this->flashType    = 'success';
    }

    public function markWhatsAppSent(int $invoiceId): void
    {
        BookingInvoice::findOrFail($invoiceId)->update([
            'is_whatsapp_sent'                => true,
            'whatsapp_last_reminder_sent_at'  => now(),
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

        return view('flux-admin.partials.rentals.invoices-tab', [
            'invoices'     => $invoices,
            'totalUnpaid'  => $totalUnpaid,
        ]);
    }
}
