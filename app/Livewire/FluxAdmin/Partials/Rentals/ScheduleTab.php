<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingClosing;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Services\RentingInvoiceSyncService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

/**
 * Change booking start date / invoice weekday on the rental itself
 * (replaces the standalone Change start date + Adjust weekday pages).
 */
#[Lazy]
class ScheduleTab extends Component
{
    public int $bookingId;

    public string $newStartDate = '';

    public string $targetWeekday = '';

    public ?string $flashMessage = null;

    public ?string $flashType = null;

    /** @var list<string> */
    public array $weekdays = [
        'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
    ];

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
        $booking = RentingBooking::query()->findOrFail($bookingId);
        $this->newStartDate = $booking->start_date
            ? Carbon::parse($booking->start_date)->toDateString()
            : '';

        $weekday = $booking->start_date
            ? Carbon::parse($booking->start_date)->format('l')
            : 'Thursday';

        // Payments are Mon–Fri only — nudge weekend starts onto Friday in the same week.
        if (in_array($weekday, ['Saturday', 'Sunday'], true)) {
            $weekday = 'Friday';
        }

        $this->targetWeekday = $weekday;
    }

    public function saveStartDate(RentingInvoiceSyncService $sync): void
    {
        $this->validate([
            'newStartDate' => ['required', 'date'],
        ]);

        $start = Carbon::parse($this->newStartDate);
        if ($start->isWeekend()) {
            $this->addError('newStartDate', 'Saturday and Sunday are blocked — pick a weekday (Mon–Fri).');

            return;
        }

        try {
            $message = DB::transaction(function () use ($sync) {
                $booking = RentingBooking::query()->findOrFail($this->bookingId);
                $booking->start_date = $this->newStartDate;
                $booking->save();

                $syncResult = $sync->syncFutureInvoicesForBooking($booking->id);
                $this->targetWeekday = Carbon::parse($this->newStartDate)->format('l');

                $message = 'Start date updated. Upcoming invoices use '.$this->targetWeekday.'.';
                if (! $syncResult['skipped'] && ($syncResult['deleted'] > 0 || $syncResult['created'] > 0)) {
                    $message .= " {$syncResult['deleted']} future invoice(s) removed, {$syncResult['created']} created.";
                } elseif ($syncResult['skipped']) {
                    $message .= ' (No open rental item with weekly rent — unpaid future invoices were not auto-rebuilt; adjust individual invoice dates on the Invoices tab if needed.)';
                }

                return $message;
            });

            $this->flashMessage = $message;
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function adjustWeekday(RentingInvoiceSyncService $sync): void
    {
        $this->validate([
            'targetWeekday' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday'],
        ]);

        try {
            $message = DB::transaction(function () use ($sync) {
                $booking = RentingBooking::query()->findOrFail($this->bookingId);
                if (! $booking->start_date) {
                    throw new \RuntimeException('Booking has no start date to adjust.');
                }

                $current = Carbon::parse($booking->start_date)->startOfDay();
                $adjusted = $this->sameWeekOnWeekday($current, $this->targetWeekday);
                $booking->start_date = $adjusted->toDateString();
                $booking->save();
                $this->newStartDate = $adjusted->toDateString();

                $syncResult = $sync->syncFutureInvoicesForBooking($booking->id);

                $message = 'Invoice weekday set to '.$this->targetWeekday.' (start date '.$adjusted->format('d M Y').').';
                if (! $syncResult['skipped'] && ($syncResult['deleted'] > 0 || $syncResult['created'] > 0)) {
                    $message .= " {$syncResult['deleted']} future invoice(s) removed, {$syncResult['created']} created.";
                }

                return $message;
            });

            $this->flashMessage = $message;
            $this->flashType = 'success';
            $this->dispatch('rental-updated');
        } catch (\Throwable $e) {
            $this->flashMessage = $e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function render()
    {
        $booking = RentingBooking::query()
            ->with(['rentingBookingItems' => fn ($q) => $q->orderByDesc('id')])
            ->findOrFail($this->bookingId);

        $closing = BookingClosing::query()->where('booking_id', $this->bookingId)->first();
        $endedItem = $booking->rentingBookingItems->first(fn (RentingBookingItem $i) => $i->end_date !== null);
        $hasOpenItem = $booking->rentingBookingItems->contains(fn (RentingBookingItem $i) => $i->end_date === null);

        return view('flux-admin.partials.rentals.schedule-tab', [
            'booking' => $booking,
            'closing' => $closing,
            'endedItem' => $endedItem,
            'hasOpenItem' => $hasOpenItem,
            'currentWeekday' => $booking->start_date
                ? Carbon::parse($booking->start_date)->format('l')
                : null,
        ]);
    }

    protected function sameWeekOnWeekday(Carbon $start, string $weekdayName): Carbon
    {
        $offsets = [
            'Monday' => 0,
            'Tuesday' => 1,
            'Wednesday' => 2,
            'Thursday' => 3,
            'Friday' => 4,
            'Saturday' => 5,
            'Sunday' => 6,
        ];

        return $start->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->addDays($offsets[$weekdayName] ?? 0)
            ->startOfDay();
    }
}
