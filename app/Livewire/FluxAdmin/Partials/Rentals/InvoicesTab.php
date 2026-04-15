<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingInvoice;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class InvoicesTab extends Component
{
    public int $bookingId;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function render()
    {
        $invoices = BookingInvoice::with('user')
            ->where('booking_id', $this->bookingId)
            ->orderByDesc('invoice_date')
            ->get();

        return view('flux-admin.partials.rentals.invoices-tab', [
            'invoices' => $invoices,
        ]);
    }
}
