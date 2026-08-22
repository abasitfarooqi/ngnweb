<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\RentingBooking;
use App\Models\RentingReferral;
use App\Services\Renting\RentingReferralService;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class ReferralCard extends Component
{
    public int $bookingId;

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;
    }

    public function render(RentingReferralService $service)
    {
        $booking = RentingBooking::query()->find($this->bookingId);
        $referredBy = null;
        $availablePoints = 0;
        $pendingPoints = 0;

        if ($booking && Schema::hasTable('renting_referrals')) {
            $referredBy = RentingReferral::query()
                ->where('referred_customer_id', $booking->customer_id)
                ->whereIn('status', RentingReferral::ACTIVE_ATTRIBUTION_STATUSES)
                ->with(['referrer', 'referrerQualifyingBooking'])
                ->orderBy('created_at')
                ->first();

            if ($booking->customer_id) {
                $availablePoints = $service->availablePoints((int) $booking->customer_id);
                $pendingPoints = $service->pendingPoints((int) $booking->customer_id);
            }
        }

        $referrerActiveBooking = ($referredBy?->referrer && $referredBy->created_at)
            ? $service->activePostedBookingAt($referredBy->referrer, $referredBy->created_at)
            : null;

        return view('flux-admin.partials.rentals.referral-card', compact(
            'referredBy',
            'availablePoints',
            'pendingPoints',
            'booking',
            'referrerActiveBooking'
        ));
    }
}
