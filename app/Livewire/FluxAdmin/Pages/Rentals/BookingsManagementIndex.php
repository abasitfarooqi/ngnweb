<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Bookings management — Flux Admin')]
class BookingsManagementIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public string $pageTitle = 'Bookings management';
    public string $pageDescription = 'Active rental bookings — open a booking to manage documents, payments, issuance and closing.';

    /** @var 'active'|'inactive'|'all' */
    public string $scope = 'active';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    protected function bookingsQuery(): Builder
    {
        $q = DB::table('renting_bookings as rb')
            ->join('renting_booking_items as rbi', 'rbi.booking_id', '=', 'rb.id')
            ->join('customers as c', 'c.id', '=', 'rb.customer_id')
            ->join('motorbikes as mb', 'mb.id', '=', 'rbi.motorbike_id')
            ->leftJoin('motorbike_registrations as mr', function ($j) {
                $j->on('mr.motorbike_id', '=', 'mb.id')->where('mr.active', true);
            })
            ->where('rb.state', '!=', 'DRAFT')
            ->select([
                'rb.id as booking_id',
                'rbi.id as booking_item_id',
                'rb.state as booking_state',
                'rb.start_date as booking_start_date',
                'rb.due_date as booking_due_date',
                'rbi.start_date as item_start_date',
                'rbi.end_date as item_end_date',
                'rbi.weekly_rent',
                'c.id as customer_id',
                'c.first_name',
                'c.last_name',
                'c.email',
                'c.phone',
                'mb.make',
                'mb.model',
                'mr.registration_number as reg_no',
            ]);

        if ($this->scope === 'active') {
            $q->whereNull('rbi.end_date');
        } elseif ($this->scope === 'inactive') {
            $q->whereNotNull('rbi.end_date');
        }

        $state = $this->filter('state', '');
        if (is_string($state) && $state !== '') {
            $q->where('rb.state', $state);
        }

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $q->where(function ($w) use ($s) {
                $w->where('rb.id', 'like', $s)
                    ->orWhere('c.first_name', 'like', $s)
                    ->orWhere('c.last_name', 'like', $s)
                    ->orWhere(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', $s)
                    ->orWhere('c.email', 'like', $s)
                    ->orWhere('c.phone', 'like', $s)
                    ->orWhere('mr.registration_number', 'like', $s)
                    ->orWhere('mb.make', 'like', $s)
                    ->orWhere('mb.model', 'like', $s);
            });
        }

        $sortColumn = match ($this->sortField) {
            'booking_id' => 'rb.id',
            'customer' => 'c.first_name',
            'weekly_rent' => 'rbi.weekly_rent',
            'start_date' => 'rbi.start_date',
            'end_date' => 'rbi.end_date',
            default => 'rb.id',
        };

        return $q->orderBy($sortColumn, $this->sortDirection);
    }

    public function render()
    {
        $rows = $this->bookingsQuery()->paginate($this->perPage);

        $states = DB::table('renting_bookings')
            ->whereNotNull('state')
            ->where('state', '!=', 'DRAFT')
            ->distinct()
            ->pluck('state')
            ->filter()
            ->values();

        return view('flux-admin.pages.rentals.bookings-management-index', compact('rows', 'states'));
    }
}
