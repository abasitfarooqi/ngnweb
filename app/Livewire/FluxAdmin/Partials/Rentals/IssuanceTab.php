<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingIssuanceItem;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class IssuanceTab extends Component
{
    public int $bookingId;

    // Form fields
    public string $issuanceNotes = '';
    public string $currentMileage = '';
    public bool $isVideoRecorded = false;
    public bool $accessoriesChecked = false;
    public bool $isInsured = false;
    public string $issuanceBranch = '';

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function issueMotorbike(): void
    {
        $this->validate([
            'issuanceNotes'    => 'required|string|min:3',
            'currentMileage'   => 'required|numeric|min:0',
            'isVideoRecorded'  => 'accepted',
            'accessoriesChecked' => 'accepted',
            'issuanceBranch'   => 'required|string',
        ], [
            'issuanceNotes.required'      => 'Please enter who issued the motorbike.',
            'currentMileage.required'     => 'Current mileage is required.',
            'isVideoRecorded.accepted'    => 'Please confirm you have recorded the video.',
            'accessoriesChecked.accepted' => 'Please confirm you have checked the accessories.',
            'issuanceBranch.required'     => 'Please select the branch.',
        ]);

        $booking = RentingBooking::findOrFail($this->bookingId);
        $bookingItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $booking->update([
                'state'    => 'Completed & Issued',
                'is_posted' => true,
            ]);

            BookingIssuanceItem::create([
                'booking_item_id'    => $bookingItem->id,
                'issued_by_user_id'  => auth()->id(),
                'notes'              => $this->issuanceNotes,
                'is_insured'         => false,
                'current_mileage'    => (int) $this->currentMileage,
                'is_video_recorded'  => $this->isVideoRecorded,
                'accessories_checked' => $this->accessoriesChecked,
                'issuance_branch'    => $this->issuanceBranch,
            ]);

            DB::commit();

            $this->flashMessage = 'Motorbike issued successfully. Booking state set to "Completed & Issued".';
            $this->flashType    = 'success';
            $this->resetForm();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->flashMessage = 'Error issuing motorbike: '.$e->getMessage();
            $this->flashType    = 'error';
        }
    }

    public function reissueMotorbike(): void
    {
        $this->validate([
            'issuanceNotes'    => 'required|string|min:3',
            'currentMileage'   => 'required|numeric|min:0',
            'isVideoRecorded'  => 'accepted',
            'accessoriesChecked' => 'accepted',
            'issuanceBranch'   => 'required|string',
        ], [
            'issuanceNotes.required'      => 'Please enter notes / inspector name.',
            'currentMileage.required'     => 'Current mileage is required.',
            'isVideoRecorded.accepted'    => 'Please confirm you have recorded the video.',
            'accessoriesChecked.accepted' => 'Please confirm you have checked the accessories.',
            'issuanceBranch.required'     => 'Please select the branch.',
        ]);

        $bookingItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->firstOrFail();

        BookingIssuanceItem::create([
            'booking_item_id'    => $bookingItem->id,
            'issued_by_user_id'  => auth()->id(),
            'notes'              => $this->issuanceNotes,
            'is_insured'         => $this->isInsured,
            'current_mileage'    => (int) $this->currentMileage,
            'is_video_recorded'  => $this->isVideoRecorded,
            'accessories_checked' => $this->accessoriesChecked,
            'issuance_branch'    => $this->issuanceBranch,
        ]);

        $this->flashMessage = 'Inspection & re-issuance record saved successfully.';
        $this->flashType    = 'success';
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->issuanceNotes      = '';
        $this->currentMileage     = '';
        $this->isVideoRecorded    = false;
        $this->accessoriesChecked = false;
        $this->isInsured          = false;
        $this->issuanceBranch     = '';
        $this->resetValidation();
    }

    public function render()
    {
        $booking = RentingBooking::findOrFail($this->bookingId);

        $activeItem = RentingBookingItem::with('motorbike')
            ->where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->first();

        $issuanceHistory = BookingIssuanceItem::with('issuedBy')
            ->whereHas('bookingItem', fn ($q) => $q->where('booking_id', $this->bookingId))
            ->orderByDesc('created_at')
            ->get();

        return view('flux-admin.partials.rentals.issuance-tab', [
            'booking'         => $booking,
            'activeItem'      => $activeItem,
            'issuanceHistory' => $issuanceHistory,
        ]);
    }
}
