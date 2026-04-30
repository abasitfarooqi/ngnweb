<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\BookingInvoice;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Booking invoices — Flux Admin')]
class BookingInvoiceIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
        $this->exportable = true;
        $this->exportFilename = 'booking-invoices';
        $this->sortField = 'invoice_date';
    }

    public function render()
    {
        $invoices = $this->baseQuery()
            ->with(['booking.customer:id,first_name,last_name', 'user:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.rentals.booking-invoices-index', ['invoices' => $invoices]);
    }

    protected function baseQuery(): Builder
    {
        return BookingInvoice::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(fn ($q) => $q->where('booking_id', $term)->orWhere('state', 'like', "%{$term}%")->orWhereHas('booking.customer', fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")));
            })
            ->when($this->filter('is_paid') !== '', fn ($q) => $q->where('is_paid', $this->filter('is_paid') === '1'))
            ->when($this->filter('is_posted') !== '', fn ($q) => $q->where('is_posted', $this->filter('is_posted') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['booking.customer']); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Booking' => 'booking_id',
            'Customer' => fn ($i) => $i->booking?->customer ? $i->booking->customer->first_name.' '.$i->booking->customer->last_name : '',
            'Invoice date' => fn ($i) => $i->invoice_date ? \Carbon\Carbon::parse($i->invoice_date)->format('Y-m-d') : '',
            'Amount' => 'amount', 'Deposit' => 'deposit', 'Paid date' => 'paid_date',
            'State' => 'state', 'Posted' => fn ($i) => $i->is_posted ? 'Yes' : 'No',
            'Paid' => fn ($i) => $i->is_paid ? 'Yes' : 'No',
        ];
    }
}
