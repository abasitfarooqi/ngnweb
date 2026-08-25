<?php

namespace App\Livewire\Admin\Rentals;

use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Support\RentalInvoiceTabData;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class OperationsDashboard extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 20;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    protected function rowsQuery(): Builder
    {
        $invoiceSummary = RentalInvoiceTabData::outstandingByBookingSubquery();

        $query = RentingBookingItem::query()
            ->toBase()
            ->from('renting_booking_items as rbi')
            ->join('renting_bookings as rb', 'rb.id', '=', 'rbi.booking_id')
            ->join('customers as c', 'c.id', '=', 'rb.customer_id')
            ->join('motorbikes as mb', 'mb.id', '=', 'rbi.motorbike_id')
            ->leftJoinSub($invoiceSummary, 'invoice_summary', function ($join) {
                $join->on('invoice_summary.booking_id', '=', 'rb.id');
            })
            ->where('rb.is_posted', true)
            ->where('rbi.is_posted', true)
            ->whereNull('rbi.end_date')
            ->select([
                'rb.id as booking_id',
                'rb.customer_id',
                'rb.deposit',
                'rb.start_date as booking_start_date',
                'rb.due_date as booking_due_date',
                'rb.state as booking_state',
                'rbi.id as booking_item_id',
                'rbi.motorbike_id',
                'rbi.start_date as item_start_date',
                'rbi.due_date as item_due_date',
                'rbi.weekly_rent',
                'c.first_name',
                'c.last_name',
                'c.phone',
                'c.email',
                'c.license_expiry_date',
                'mb.reg_no',
                'mb.make',
                'mb.model',
            ])
            ->selectRaw('COALESCE(invoice_summary.outstanding_amount, 0) as outstanding_amount')
            ->selectRaw('invoice_summary.next_unpaid_invoice_date as next_unpaid_invoice_date');

        if ($this->search !== '') {
            $search = '%'.$this->search.'%';

            $query->where(function ($innerQuery) use ($search) {
                $innerQuery->where('rb.id', 'like', $search)
                    ->orWhere('c.first_name', 'like', $search)
                    ->orWhere('c.last_name', 'like', $search)
                    ->orWhere(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', $search)
                    ->orWhere('c.email', 'like', $search)
                    ->orWhere('c.phone', 'like', $search)
                    ->orWhere('mb.reg_no', 'like', $search)
                    ->orWhere('mb.make', 'like', $search)
                    ->orWhere('mb.model', 'like', $search);
            });
        }

        if ($this->status === 'payment_due') {
            $query->whereRaw('COALESCE(invoice_summary.outstanding_amount, 0) > 0');
        } elseif ($this->status === 'active') {
            $query->whereRaw('COALESCE(invoice_summary.outstanding_amount, 0) = 0');
        }

        return $query->orderByDesc('rb.id');
    }

    protected function stats(): array
    {
        $activeBookings = RentingBooking::with([
            'rentingBookingItems' => fn ($query) => $query->whereNull('end_date'),
        ])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', fn ($query) => $query->whereNull('end_date'))
            ->get();

        $bookingIds = $activeBookings->pluck('id');
        $outstanding = $bookingIds->isEmpty()
            ? (object) ['unpaid_invoices' => 0, 'due_payments' => 0]
            : DB::query()
                ->fromSub(RentalInvoiceTabData::outstandingByBookingSubquery(), 'os')
                ->whereIn('os.booking_id', $bookingIds)
                ->selectRaw('COALESCE(SUM(os.outstanding_amount), 0) as unpaid_invoices')
                ->selectRaw('COALESCE(SUM(os.due_invoice_count), 0) as due_payments')
                ->first();

        return [
            'active_rentals' => $activeBookings->flatMap->rentingBookingItems->count(),
            'weekly_revenue' => $activeBookings->flatMap->rentingBookingItems->sum('weekly_rent'),
            'due_payments' => (int) ($outstanding->due_payments ?? 0),
            'unpaid_invoices' => (float) ($outstanding->unpaid_invoices ?? 0),
        ];
    }

    public function render()
    {
        return view('livewire.admin.rentals.operations-dashboard', [
            'rows' => $this->rowsQuery()->paginate($this->perPage),
            'stats' => $this->stats(),
        ]);
    }
}
