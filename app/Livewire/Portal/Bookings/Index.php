<?php

namespace App\Livewire\Portal\Bookings;

use App\Models\MotorbikeRepair;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'all';

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function downloadRepairReport(int $repairId)
    {
        $customerAuth = Auth::guard('customer')->user();
        $customerEmail = trim((string) ($customerAuth?->email ?? ''));
        if ($customerEmail === '') {
            abort(403);
        }

        $repair = MotorbikeRepair::query()
            ->with(['motorbike', 'branch', 'updates.services', 'observations'])
            ->where('id', $repairId)
            ->whereRaw('LOWER(email) = ?', [strtolower($customerEmail)])
            ->firstOrFail();

        $pdf = \PDF::loadView('livewire.agreements.pdf.templates.repair_invoice', compact('repair'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $regNo = $repair->motorbike?->reg_no ?: 'report';

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'repair-report-'.$regNo.'-'.$repair->id.'.pdf'
        );
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();
        $customerId = $customer?->customer_id;

        $motBookings = \App\Models\MOTBooking::where('customer_email', $customer->email)
            ->orderBy('date_of_appointment', 'desc')
            ->get();

        $repairs = \App\Models\MotorbikeRepair::where('email', $customer->email)
            ->with(['branch', 'motorbike'])
            ->orderByDesc('arrival_date')
            ->get();

        $rentals = $customerId
            ? \App\Models\RentingBooking::where('customer_id', $customerId)->with(['rentingBookingItems.motorbike'])->orderBy('created_at', 'desc')->take(10)->get()
            : collect();

        // Merge MOT + rental into one unified bookings collection for the tab view.
        // Use plain collections to avoid Eloquent collection model-key behaviour.
        $motItems = collect($motBookings->all())->map(fn ($m) => (object) [
            'id' => 'mot-'.$m->id,
            'type' => 'MOT',
            'date' => $m->date_of_appointment,
            'status' => $m->status ?? 'Pending',
            'label' => 'MOT Appointment',
            'source' => $m,
        ]);

        $rentalItems = collect($rentals->all())->map(function ($r) {
            $items = $r->rentingBookingItems ?? collect();
            $hasActiveItem = $items->contains(fn ($item) => empty($item->end_date));
            $latestEndDate = $items->whereNotNull('end_date')->sortByDesc('end_date')->first()?->end_date;

            return (object) [
                'id' => 'rental-'.$r->id,
                'type' => 'Rental',
                'date' => $hasActiveItem ? ($r->start_date ?? $r->created_at) : ($latestEndDate ?? $r->created_at),
                'status' => $hasActiveItem ? ($r->state ?? 'ACTIVE') : 'ENDED',
                'label' => 'Rental Booking',
                'source' => $r,
            ];
        });

        $allBookings = $motItems
            ->merge($rentalItems)
            ->merge(collect($repairs->all())->map(fn ($repair) => (object) [
                'id' => 'repair-'.$repair->id,
                'type' => 'Repair',
                'date' => $repair->arrival_date?->toDateString(),
                'status' => $repair->is_returned ? 'completed' : ($repair->is_repaired ? 'in progress' : 'pending'),
                'label' => 'Repair Booking',
                'source' => $repair,
            ]))
            ->sortByDesc('date')
            ->values();

        $bookings = match ($this->activeTab) {
            'upcoming' => $allBookings->filter(fn ($b) => $b->date && $b->date >= now()->toDateString()),
            'completed' => $allBookings->filter(fn ($b) => in_array(strtolower((string) $b->status), ['completed', 'done', 'expired', 'ended'])),
            default => $allBookings,
        };

        return view('livewire.portal.bookings.index', compact('bookings', 'motBookings', 'rentals'))
            ->layout('components.layouts.portal', ['title' => 'My Bookings | My Account']);
    }
}
