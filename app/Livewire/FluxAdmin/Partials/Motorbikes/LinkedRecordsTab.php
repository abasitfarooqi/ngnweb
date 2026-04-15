<?php

namespace App\Livewire\FluxAdmin\Partials\Motorbikes;

use App\Models\ApplicationItem;
use App\Models\PcnCase;
use App\Models\RentingBookingItem;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class LinkedRecordsTab extends Component
{
    public int $motorbikeId;

    public function placeholder(): string
    {
        return view('flux-admin.partials.loading-placeholder')->render();
    }

    public function render()
    {
        $bookingItems = RentingBookingItem::with('booking.user')
            ->where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $applicationItems = ApplicationItem::with('application.user')
            ->where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $pcnCases = PcnCase::where('motorbike_id', $this->motorbikeId)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return view('flux-admin.partials.motorbikes.linked-records-tab', [
            'bookingItems' => $bookingItems,
            'applicationItems' => $applicationItems,
            'pcnCases' => $pcnCases,
        ]);
    }
}
