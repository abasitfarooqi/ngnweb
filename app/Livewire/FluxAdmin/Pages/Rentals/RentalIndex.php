<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Support\RentalInvoiceTabData;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Merged active bookings list (formerly /rentals + /bookings-management).
 */
#[Layout('flux-admin.layouts.app')]
#[Title('Active bookings — Flux Admin')]
class RentalIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public string $pageTitle = 'Active bookings rental';

    public string $pageDescription = 'Active rentals — filters, outstanding balances, and open booking detail.';

    /** @var 'active'|'inactive'|'all' */
    public string $scope = 'active';

    #[Url(history: true)]
    public string $status = 'all';

    #[Url(history: true)]
    public string $filterMotorbikeId = '';

    #[Url(history: true)]
    public string $bookingStateFilter = '';

    #[Url(history: true)]
    public string $startDateFrom = '';

    #[Url(history: true)]
    public string $startDateTo = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');

        if ($this->scope === 'inactive') {
            $this->pageTitle = 'Inactive bookings';
            $this->pageDescription = 'Ended rentals — open a booking to view history and pendings.';
        } elseif ($this->scope === 'all') {
            $this->pageTitle = 'All bookings';
            $this->pageDescription = 'All rental booking items.';
        }
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMotorbikeId(): void
    {
        $this->resetPage();
    }

    public function updatingBookingStateFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStartDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatingStartDateTo(): void
    {
        $this->resetPage();
    }

    public function resetRentalFilters(): void
    {
        $this->search = '';
        $this->status = 'all';
        $this->filterMotorbikeId = '';
        $this->bookingStateFilter = '';
        $this->startDateFrom = '';
        $this->startDateTo = '';
        $this->resetPage();
    }

    public function openBooking(int $bookingId): void
    {
        $this->redirectRoute('flux-admin.rentals.show', $bookingId, navigate: true);
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
            ->leftJoin('motorbike_registrations as mr', function ($j) {
                $j->on('mr.motorbike_id', '=', 'mb.id')->where('mr.active', true);
            })
            ->leftJoinSub($invoiceSummary, 'invoice_summary', function ($join) {
                $join->on('invoice_summary.booking_id', '=', 'rb.id');
            })
            ->where('rb.state', '!=', 'DRAFT')
            ->select([
                'rb.id as booking_id',
                'rb.customer_id',
                'rb.deposit',
                'rb.start_date as booking_start_date',
                'rb.due_date as booking_due_date',
                'rb.state as booking_state',
                'rb.is_posted as booking_is_posted',
                'rbi.id as booking_item_id',
                'rbi.motorbike_id',
                'rbi.start_date as item_start_date',
                'rbi.end_date as item_end_date',
                'rbi.due_date as item_due_date',
                'rbi.weekly_rent',
                'c.first_name',
                'c.last_name',
                'c.phone',
                'c.email',
                'mb.make',
                'mb.model',
            ])
            ->selectRaw('COALESCE(mr.registration_number, mb.reg_no) as reg_no')
            ->selectRaw('COALESCE(invoice_summary.outstanding_amount, 0) as outstanding_amount')
            ->selectRaw('invoice_summary.next_unpaid_invoice_date as next_unpaid_invoice_date');

        if ($this->scope === 'active') {
            $query->whereNull('rbi.end_date');
        } elseif ($this->scope === 'inactive') {
            $query->whereNotNull('rbi.end_date');
        }

        if ($this->search !== '') {
            $search = '%'.$this->search.'%';
            $query->where(function ($q) use ($search) {
                $q->where('rb.id', 'like', $search)
                    ->orWhere('c.first_name', 'like', $search)
                    ->orWhere('c.last_name', 'like', $search)
                    ->orWhere(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', $search)
                    ->orWhere('c.email', 'like', $search)
                    ->orWhere('c.phone', 'like', $search)
                    ->orWhere('mb.reg_no', 'like', $search)
                    ->orWhere('mr.registration_number', 'like', $search)
                    ->orWhere('mb.make', 'like', $search)
                    ->orWhere('mb.model', 'like', $search);
            });
        }

        if ($this->status === 'payment_due') {
            $query->whereRaw('COALESCE(invoice_summary.outstanding_amount, 0) > 0');
        } elseif ($this->status === 'active') {
            $query->whereRaw('COALESCE(invoice_summary.outstanding_amount, 0) = 0');
        }

        if ($this->filterMotorbikeId !== '') {
            $query->where('rbi.motorbike_id', (int) $this->filterMotorbikeId);
        }

        if ($this->bookingStateFilter !== '') {
            $query->where('rb.state', $this->bookingStateFilter);
        }

        if ($this->startDateFrom !== '') {
            $query->where('rbi.start_date', '>=', $this->startDateFrom);
        }

        if ($this->startDateTo !== '') {
            $query->where('rbi.start_date', '<=', $this->startDateTo);
        }

        $sortColumn = match ($this->sortField) {
            'booking_id' => 'rb.id',
            'ren_no' => 'rb.id',
            'reg_no' => DB::raw('COALESCE(mr.registration_number, mb.reg_no)'),
            'customer' => 'c.first_name',
            'weekly_rent' => 'rbi.weekly_rent',
            'start_date' => 'rbi.start_date',
            'end_date' => 'rbi.end_date',
            'due_date' => $this->scope === 'active' ? 'next_unpaid_invoice_date' : 'rbi.due_date',
            'outstanding' => 'outstanding_amount',
            default => 'rb.id',
        };

        return $query->orderBy($sortColumn, $this->sortDirection);
    }

    protected function stats(): array
    {
        $activeBookings = RentingBooking::with([
            'rentingBookingItems' => fn ($q) => $q->whereNull('end_date'),
        ])
            ->where('is_posted', true)
            ->where('state', '!=', 'DRAFT')
            ->whereHas('rentingBookingItems', fn ($q) => $q->whereNull('end_date'))
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

    public function listCountLabel(): string
    {
        if ($this->scope === 'inactive') {
            return 'ended bookings';
        }

        if ($this->scope === 'all') {
            return 'bookings in total';
        }

        return match ($this->status) {
            'payment_due' => 'bookings with payment due',
            'active' => 'bookings with no amount due',
            default => 'active bookings',
        };
    }

    public function render()
    {
        $states = DB::table('renting_bookings')
            ->whereNotNull('state')
            ->where('state', '!=', 'DRAFT')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->filter()
            ->values();

        $rows = $this->rowsQuery()->paginate($this->perPage);

        return view('flux-admin.pages.rentals.index', [
            'rows' => $rows,
            'stats' => $this->scope === 'active' ? $this->stats() : null,
            'states' => $states,
            'listCountLabel' => $this->listCountLabel(),
        ]);
    }
}
