<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\BookingInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental due payments — Flux Admin')]
class DuePaymentsIndex extends Component
{
    use WithAuthorization;

    public function mount(): void { $this->authorizeModule('see-menu-commons'); }

    public function markInvoiceReminderSent(int $invoiceId): void
    {
        BookingInvoice::where('id', $invoiceId)->update([
            'is_whatsapp_sent' => true,
            'whatsapp_last_reminder_sent_at' => now(),
        ]);
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Reminder marked sent.');
    }

    public function render()
    {
        $records = DB::table('renting_bookings')
            ->select([
                'renting_bookings.id AS booking_no',
                DB::raw("CONCAT(customers.first_name, ' ', customers.last_name) AS customer"),
                'motorbikes.reg_no',
                'renting_booking_items.weekly_rent AS weekly',
                'booking_invoices.invoice_date',
                'booking_invoices.id AS invoice_id',
                'booking_invoices.is_whatsapp_sent',
                'booking_invoices.whatsapp_last_reminder_sent_at',
                'customers.whatsapp',
                'customers.phone',
            ])
            ->join('customers', 'customers.id', '=', 'renting_bookings.customer_id')
            ->join('renting_booking_items', 'renting_booking_items.booking_id', '=', 'renting_bookings.id')
            ->join('motorbikes', 'motorbikes.id', '=', 'renting_booking_items.motorbike_id')
            ->join('booking_invoices', 'booking_invoices.booking_id', '=', 'renting_bookings.id')
            ->where('renting_bookings.is_posted', 1)
            ->where('renting_booking_items.is_posted', 1)
            ->whereNull('renting_booking_items.end_date')
            ->where('booking_invoices.is_paid', 0)
            ->where('booking_invoices.invoice_date', '<=', now())
            ->orderBy('renting_bookings.id')
            ->get()
            ->map(function ($row) {
                $number = $row->whatsapp ?: $row->phone;
                $number = preg_replace('/\s+|^0/', '', (string) $number);
                $number = preg_replace('/^(\+44)+/', '', $number);
                $number = preg_replace('/^44/', '', $number);
                $number = '+44'.$number;
                $number = preg_replace('/\s+/', '', $number);

                $message = "Dear {$row->customer}, this is a reminder regarding your Weekly Rental payment for motorbike {$row->reg_no}. The outstanding amount of £"
                    .number_format((float) $row->weekly, 2).' is due on '.Carbon::parse($row->invoice_date)->format('d M Y')
                    .'. Please contact us at 0208 314 1498 or WhatsApp 07951790568, NGN Motors.';

                $row->whatsapp_number = $number;
                $row->whatsapp_url = "https://wa.me/{$number}?text=".rawurlencode($message);

                return $row;
            });

        return view('flux-admin.pages.rentals.due-payments-index', ['records' => $records]);
    }
}
