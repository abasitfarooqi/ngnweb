<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BookingInvoice;
use App\Support\FluxAdminFormPayload;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Booking Invoice — Flux Admin')]
class BookingInvoiceForm extends Component
{
    use WithAuthorization;

    public ?int $invoiceId = null;

    public array $form = [];

    public function mount(?BookingInvoice $bookingInvoice = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');

        if ($bookingInvoice !== null) {
            $this->invoiceId = $bookingInvoice->id;
            $attrs = $bookingInvoice->getAttributes();
            $this->form = [
                'booking_id'   => $attrs['booking_id'] ?? '',
                'invoice_date' => $attrs['invoice_date'] ? \Carbon\Carbon::parse($attrs['invoice_date'])->format('Y-m-d') : '',
                'amount'       => $attrs['amount'] ?? '',
                'deposit'      => $attrs['deposit'] ?? '',
                'state'        => $attrs['state'] ?? '',
                'is_paid'      => (bool) ($attrs['is_paid'] ?? false),
                'paid_date'    => $attrs['paid_date'] ? \Carbon\Carbon::parse($attrs['paid_date'])->format('Y-m-d') : '',
                'notes'        => $attrs['notes'] ?? '',
            ];
        } else {
            $this->form = [
                'booking_id'   => '',
                'invoice_date' => now()->format('Y-m-d'),
                'amount'       => '',
                'deposit'      => '',
                'state'        => '',
                'is_paid'      => false,
                'paid_date'    => '',
                'notes'        => '',
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'form.booking_id'   => ['required', 'integer', 'exists:renting_bookings,id'],
            'form.invoice_date' => ['nullable', 'date'],
            'form.amount'       => ['nullable', 'numeric', 'min:0'],
            'form.deposit'      => ['nullable', 'numeric', 'min:0'],
            'form.state'        => ['nullable', 'string', 'max:50'],
            'form.is_paid'      => ['boolean'],
            'form.paid_date'    => ['nullable', 'date'],
            'form.notes'        => ['nullable', 'string'],
        ]);

        $data = FluxAdminFormPayload::onlyPersistable(BookingInvoice::class, [
            'booking_id'   => $this->form['booking_id'],
            'invoice_date' => $this->form['invoice_date'] ?: null,
            'amount'       => $this->form['amount'] ?: null,
            'deposit'      => $this->form['deposit'] ?: null,
            'state'        => $this->form['state'] ?: null,
            'is_paid'      => (bool) ($this->form['is_paid'] ?? false),
            'paid_date'    => $this->form['paid_date'] ?: null,
            'notes'        => $this->form['notes'] ?: null,
        ]);

        if (empty($data['user_id'])) {
            $data['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        if ($this->invoiceId) {
            BookingInvoice::findOrFail($this->invoiceId)->update($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Invoice updated.');
        } else {
            BookingInvoice::create($data);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Invoice created.');
        }

        $this->redirect(route('flux-admin.booking-invoices.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.rentals.booking-invoice-form');
    }
}
