<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use App\Models\Motorbike;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('All bookings — Flux Admin')]
class AllBookingsIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'booking_id';
    }

    protected function rowsQuery()
    {
        $q = DB::table('renting_bookings as rb')
            ->join('renting_booking_items as rbi', 'rbi.booking_id', '=', 'rb.id')
            ->join('customers as c', 'c.id', '=', 'rb.customer_id')
            ->join('motorbikes as mb', 'mb.id', '=', 'rbi.motorbike_id')
            ->leftJoin('motorbike_registrations as mr', function ($j) {
                $j->on('mr.motorbike_id', '=', 'mb.id')->where('mr.active', true);
            })
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

        $customerId = $this->filter('customer_id', '');
        if (is_numeric($customerId) && (int) $customerId > 0) {
            $q->where('rb.customer_id', (int) $customerId);
        }

        $motorbikeId = $this->filter('motorbike_id', '');
        if (is_numeric($motorbikeId) && (int) $motorbikeId > 0) {
            $q->where('rbi.motorbike_id', (int) $motorbikeId);
        }

        $state = $this->filter('state', '');
        if (is_string($state) && $state !== '') {
            $q->where('rb.state', $state);
        }

        $status = $this->filter('status', '');
        if ($status === 'ONGOING') {
            $q->where(function ($w) {
                $w->whereNull('rbi.end_date')->orWhere('rbi.end_date', '')->orWhere('rbi.end_date', 'N/A');
            });
        } elseif ($status === 'ENDED') {
            $q->whereNotNull('rbi.end_date')->where('rbi.end_date', '!=', '')->where('rbi.end_date', '!=', 'N/A');
        } elseif ($status === 'NA') {
            $q->where(function ($w) {
                $w->where('rbi.end_date', '')->orWhere('rbi.end_date', 'N/A');
            });
        }

        $from = $this->filter('from', '');
        $to = $this->filter('to', '');
        if (is_string($from) && $from !== '') {
            $q->whereDate('rb.start_date', '>=', $from);
        }
        if (is_string($to) && $to !== '') {
            $q->whereDate('rb.start_date', '<=', $to);
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
        $rows = $this->rowsQuery()->paginate($this->perPage);

        $customers = Customer::query()
            ->select(['id', 'first_name', 'last_name'])
            ->orderBy('first_name')
            ->limit(500)
            ->get();

        $motorbikes = Motorbike::query()
            ->leftJoin('motorbike_registrations as mr', function ($j) {
                $j->on('mr.motorbike_id', '=', 'motorbikes.id')->where('mr.active', true);
            })
            ->select(['motorbikes.id', 'motorbikes.make', 'motorbikes.model', 'mr.registration_number as reg_no'])
            ->orderBy('mr.registration_number')
            ->limit(500)
            ->get();

        $states = DB::table('renting_bookings')->whereNotNull('state')->distinct()->pluck('state')->filter()->values();

        return view('flux-admin.pages.rentals.all-bookings-index', compact('rows', 'customers', 'motorbikes', 'states'));
    }
}
