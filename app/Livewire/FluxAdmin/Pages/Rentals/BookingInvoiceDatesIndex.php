<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\BookingInvoice;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Booking invoice dates — Flux Admin')]
class BookingInvoiceDatesIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public ?int $editingId = null;
    public string $editingDate = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function startEdit(int $invoiceId, string $currentDate): void
    {
        $this->editingId = $invoiceId;
        $this->editingDate = $currentDate;
        $this->resetErrorBag();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingDate = '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editingId' => ['required', 'integer', 'exists:booking_invoices,id'],
            'editingDate' => ['required', 'date'],
        ]);

        BookingInvoice::where('id', $this->editingId)->update(['invoice_date' => $this->editingDate]);
        session()->flash('status', 'Invoice #' . $this->editingId . ' updated.');
        $this->editingId = null;
        $this->editingDate = '';
    }

    public function render()
    {
        $query = DB::table('booking_invoices as i')
            ->join('renting_bookings as rb', 'rb.id', '=', 'i.booking_id')
            ->join('customers as c', 'c.id', '=', 'rb.customer_id')
            ->leftJoin('renting_booking_items as rbi', 'rbi.booking_id', '=', 'rb.id')
            ->leftJoin('motorbikes as mb', 'mb.id', '=', 'rbi.motorbike_id')
            ->leftJoin('motorbike_registrations as mr', function ($j) {
                $j->on('mr.motorbike_id', '=', 'mb.id')->where('mr.active', true);
            })
            ->whereNotNull('i.id')
            ->select([
                'i.id as invoice_id',
                'i.invoice_date',
                'i.amount',
                'i.state as invoice_state',
                'i.is_paid',
                'rb.id as booking_id',
                'c.first_name',
                'c.last_name',
                'c.email',
                'c.phone',
                'mr.registration_number as reg_no',
                'mb.make',
                'mb.model',
            ]);

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($w) use ($s) {
                $w->where('rb.id', 'like', $s)
                    ->orWhere('i.id', 'like', $s)
                    ->orWhere('c.first_name', 'like', $s)
                    ->orWhere('c.last_name', 'like', $s)
                    ->orWhere(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', $s)
                    ->orWhere('c.email', 'like', $s)
                    ->orWhere('c.phone', 'like', $s)
                    ->orWhere('mr.registration_number', 'like', $s);
            });
        }

        $paid = $this->filter('paid', '');
        if ($paid === 'paid') {
            $query->where('i.is_paid', true);
        } elseif ($paid === 'unpaid') {
            $query->where('i.is_paid', false);
        }

        $rows = $query->orderByDesc('rb.id')->orderBy('i.invoice_date')->paginate($this->perPage);

        return view('flux-admin.pages.rentals.booking-invoice-dates-index', compact('rows'));
    }
}
