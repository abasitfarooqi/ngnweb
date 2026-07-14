<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\BookingClosing;
use App\Models\BookingInvoice;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Ended with pendings — Flux Admin')]
class EndedWithPendingsIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-rentals');
        $this->sortField = 'booking_id';
        $this->sortDirection = 'desc';
    }

    public function render()
    {
        $bookingIds = BookingClosing::query()
            ->whereNotNull('collect_proceeded_anyway_user_id')
            ->pluck('booking_id')
            ->unique()
            ->values()
            ->all();

        if ($bookingIds === []) {
            return view('flux-admin.pages.rentals.ended-with-pendings-index', [
                'rows' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage),
            ]);
        }

        $closings = BookingClosing::query()
            ->whereNotNull('collect_proceeded_anyway_user_id')
            ->with('collectProceededAnywayUser:id,first_name,last_name')
            ->get()
            ->keyBy('booking_id');

        $query = DB::table('renting_bookings as rb')
            ->join('renting_booking_items as rbi', 'rbi.booking_id', '=', 'rb.id')
            ->join('customers as c', 'c.id', '=', 'rb.customer_id')
            ->join('motorbikes as mb', 'mb.id', '=', 'rbi.motorbike_id')
            ->whereIn('rb.id', $bookingIds)
            ->whereNotNull('rbi.end_date')
            ->select([
                'rb.id as booking_id',
                'rbi.id as booking_item_id',
                'rb.start_date',
                'rbi.end_date',
                'rbi.weekly_rent',
                'c.first_name',
                'c.last_name',
                'c.phone',
                'c.email',
                'mb.reg_no',
                'mb.make',
                'mb.model',
            ]);

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($w) use ($s) {
                $w->where('rb.id', 'like', $s)
                    ->orWhere('c.first_name', 'like', $s)
                    ->orWhere('c.last_name', 'like', $s)
                    ->orWhere('c.phone', 'like', $s)
                    ->orWhere('mb.reg_no', 'like', $s);
            });
        }

        $sort = match ($this->sortField) {
            'end_date' => 'rbi.end_date',
            'reg_no' => 'mb.reg_no',
            default => 'rb.id',
        };

        $page = $query->orderBy($sort, $this->sortDirection)->paginate($this->perPage);

        $page->getCollection()->transform(function ($row) use ($closings) {
            $pendings = $this->pendingsForBooking((int) $row->booking_id);
            $closing = $closings->get($row->booking_id);
            $row->rental_left = $pendings['rental'];
            $row->additional_left = $pendings['additional'];
            $row->pcn_left = $pendings['pcn'];
            $row->proceeded_by = trim(($closing?->collectProceededAnywayUser?->first_name ?? '').' '.($closing?->collectProceededAnywayUser?->last_name ?? '')) ?: '—';
            $row->proceeded_at = $closing?->updated_at;

            return $row;
        });

        return view('flux-admin.pages.rentals.ended-with-pendings-index', [
            'rows' => $page,
        ]);
    }

    /** @return array{rental: float, additional: float, pcn: float} */
    private function pendingsForBooking(int $bookingId): array
    {
        $booking = RentingBooking::with(['rentingBookingItems.motorbike'])->find($bookingId);
        if (! $booking) {
            return ['rental' => 0.0, 'additional' => 0.0, 'pcn' => 0.0];
        }

        $motorbikeId = $booking->rentingBookingItems->first()?->motorbike_id;

        $additional = (float) DB::table('renting_other_charges')
            ->where('booking_id', $booking->id)
            ->where(function ($q) {
                $q->where('is_paid', 0)->orWhereNull('is_paid');
            })
            ->sum('amount');

        $pcn = 0.0;
        if ($motorbikeId) {
            $item = $booking->rentingBookingItems->first();
            $startDate = optional($booking->start_date)->toDateString();
            $endDate = optional($item?->end_date)->toDateString();

            $pcnQuery = PcnCase::query()
                ->where('motorbike_id', $motorbikeId)
                ->where('customer_id', $booking->customer_id)
                ->where('isClosed', false);

            if ($startDate) {
                $pcnQuery->whereDate('date_of_contravention', '>=', $startDate);
            }
            if ($endDate) {
                $pcnQuery->whereDate('date_of_contravention', '<=', $endDate);
            }

            $pcn = (float) $pcnQuery->sum('reduced_amount');
        }

        $rental = (float) BookingInvoice::query()
            ->where('booking_id', $booking->id)
            ->where('is_paid', false)
            ->whereDate('invoice_date', '<=', now()->toDateString())
            ->sum('amount');

        return ['rental' => $rental, 'additional' => $additional, 'pcn' => $pcn];
    }
}
