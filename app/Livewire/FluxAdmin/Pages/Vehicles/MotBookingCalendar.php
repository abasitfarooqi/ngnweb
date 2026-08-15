<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\Branch;
use App\Models\MOTBooking;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT calendar — Flux Admin')]
class MotBookingCalendar extends Component
{
    use WithAuthorization;

    public string $branchId = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-mot-bookings');
        $this->branchId = (string) ($this->catfordBranch()?->id ?? '');
    }

    public function updatedBranchId(): void
    {
        $this->dispatch('mot-booking-calendar-refetch');
    }

    /** @return array<int, array<string, mixed>> */
    public function fetchEvents(string $start, string $end): array
    {
        $rangeStart = Carbon::parse($start);
        $rangeEnd = Carbon::parse($end);

        return MOTBooking::query()
            ->when($this->catfordBranch(), fn ($q, Branch $branch) => $q->where('branch_id', $branch->id))
            ->whereNotNull('start')
            ->where('start', '<', $rangeEnd)
            ->where(function ($q) use ($rangeStart) {
                $q->whereNull('end')->orWhere('end', '>', $rangeStart);
            })
            ->orderBy('start')
            ->get(['id', 'start', 'end', 'status', 'vehicle_registration', 'customer_name', 'background_color', 'text_color', 'all_day'])
            ->map(fn (MOTBooking $booking) => $this->toCalendarEvent($booking))
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    private function toCalendarEvent(MOTBooking $booking): array
    {
        $vrm = strtoupper(trim((string) $booking->vehicle_registration));
        $customer = trim((string) $booking->customer_name);
        $status = (string) ($booking->status ?: MOTBooking::STATUS_PENDING);

        $labelParts = array_filter([
            $vrm !== '' ? $vrm : null,
            $customer !== '' ? $customer : null,
            ucfirst($status),
        ]);

        $title = $labelParts !== [] ? implode(' · ', $labelParts) : 'MOT booking';

        [$background, $text] = $this->coloursForBooking($booking, $status);

        return [
            'id' => (string) $booking->id,
            'title' => $title,
            'start' => Carbon::parse($booking->start)->toIso8601String(),
            'end' => $booking->end ? Carbon::parse($booking->end)->toIso8601String() : null,
            'allDay' => (bool) $booking->all_day,
            'backgroundColor' => $background,
            'borderColor' => $background,
            'textColor' => $text,
            'extendedProps' => [
                'status' => $status,
            ],
        ];
    }

    /** @return array{0: string, 1: string} */
    private function coloursForBooking(MOTBooking $booking, string $status): array
    {
        return match ($status) {
            MOTBooking::STATUS_AVAILABLE => ['#16a34a', '#ffffff'],
            MOTBooking::STATUS_BOOKED => ['#2563eb', '#ffffff'],
            MOTBooking::STATUS_COMPLETED => ['#166534', '#ffffff'],
            MOTBooking::STATUS_CANCELLED => ['#dc2626', '#ffffff'],
            MOTBooking::STATUS_PENDING => ['#d97706', '#ffffff'],
            default => ['#71717a', '#ffffff'],
        };
    }

    public function render()
    {
        $branch = $this->catfordBranch();

        return view('flux-admin.pages.vehicles.mot-bookings-calendar', compact('branch'));
    }

    private function catfordBranch(): ?Branch
    {
        return Branch::query()
            ->where('name', 'like', '%Catford%')
            ->orderBy('id')
            ->first(['id', 'name']);
    }
}
