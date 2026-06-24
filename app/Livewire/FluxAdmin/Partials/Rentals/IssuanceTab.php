<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingIssuanceItem;
use App\Models\MotorbikeMaintenanceLog;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingServiceVideo;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy]
class IssuanceTab extends Component
{
    use WithFileUploads;

    public int $bookingId;

    public string $issuanceNotes = '';
    public string $currentMileage = '';
    public bool $isVideoRecorded = false;
    public bool $accessoriesChecked = false;
    public bool $isInsured = false;
    public string $issuanceBranch = '';

    public $videoFile = null;

    public string $logDescription = '';
    public string $logCost = '';
    public string $logServicedAt = '';
    public string $logNote = '';

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function issueMotorbike(): void
    {
        $this->validate([
            'issuanceNotes'        => 'required|string|min:3',
            'currentMileage'       => 'required|numeric|min:0',
            'isVideoRecorded'      => 'accepted',
            'accessoriesChecked'   => 'accepted',
            'issuanceBranch'       => 'required|string',
        ]);

        $booking = RentingBooking::findOrFail($this->bookingId);
        $bookingItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->firstOrFail();

        DB::transaction(function () use ($booking, $bookingItem) {
            $booking->update(['state' => 'Completed & Issued', 'is_posted' => true]);

            BookingIssuanceItem::create([
                'booking_item_id'     => $bookingItem->id,
                'issued_by_user_id'   => auth()->id(),
                'notes'               => $this->issuanceNotes,
                'is_insured'          => $this->isInsured,
                'current_mileage'     => (int) $this->currentMileage,
                'is_video_recorded'   => $this->isVideoRecorded,
                'accessories_checked' => $this->accessoriesChecked,
                'issuance_branch'     => $this->issuanceBranch,
            ]);
        });

        $this->flashMessage = 'Motorbike issued successfully.';
        $this->flashType    = 'success';
        $this->resetForm();
        $this->dispatch('rental-updated');
    }

    public function reissueMotorbike(): void
    {
        $this->validate([
            'issuanceNotes'      => 'required|string|min:3',
            'currentMileage'     => 'required|numeric|min:0',
            'isVideoRecorded'    => 'accepted',
            'accessoriesChecked' => 'accepted',
            'issuanceBranch'     => 'required|string',
        ]);

        $bookingItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->firstOrFail();

        BookingIssuanceItem::create([
            'booking_item_id'     => $bookingItem->id,
            'issued_by_user_id'   => auth()->id(),
            'notes'               => $this->issuanceNotes,
            'is_insured'          => $this->isInsured,
            'current_mileage'     => (int) $this->currentMileage,
            'is_video_recorded'   => $this->isVideoRecorded,
            'accessories_checked' => $this->accessoriesChecked,
            'issuance_branch'     => $this->issuanceBranch,
        ]);

        $this->flashMessage = 'Inspection and re-issuance saved.';
        $this->flashType    = 'success';
        $this->resetForm();
    }

    public function uploadVideo(): void
    {
        $this->validate([
            'videoFile' => 'required|file|mimes:mp4,mov,avi,wmv,mkv|max:512000',
        ]);

        $timestamp = now()->format('Ymd_His');
        $extension = $this->videoFile->getClientOriginalExtension();
        $fileName = $this->bookingId.'_'.$timestamp.'.'.$extension;
        $storePath = $this->videoFile->storeAs('public/rental_service_videos', $fileName);

        RentingServiceVideo::create([
            'booking_id'  => $this->bookingId,
            'video_path'  => $storePath,
            'recorded_at' => now(),
        ]);

        $this->videoFile = null;
        $this->flashMessage = 'Service video uploaded.';
        $this->flashType = 'success';
    }

    public function addMaintenanceLog(): void
    {
        $activeItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->firstOrFail();

        $this->validate([
            'logDescription' => 'required|string|max:255',
            'logCost'          => 'required|numeric|min:0',
            'logServicedAt'    => 'required|date',
            'logNote'          => 'nullable|string',
        ]);

        MotorbikeMaintenanceLog::create([
            'motorbike_id' => $activeItem->motorbike_id,
            'cost'         => $this->logCost,
            'serviced_at'  => $this->logServicedAt,
            'description'  => $this->logDescription,
            'note'         => $this->logNote ?: null,
        ]);

        $this->logDescription = '';
        $this->logCost = '';
        $this->logServicedAt = '';
        $this->logNote = '';
        $this->flashMessage = 'Maintenance log saved.';
        $this->flashType = 'success';
    }

    private function resetForm(): void
    {
        $this->issuanceNotes = '';
        $this->currentMileage = '';
        $this->isVideoRecorded = false;
        $this->accessoriesChecked = false;
        $this->isInsured = false;
        $this->issuanceBranch = '';
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

        $videos = RentingServiceVideo::where('booking_id', $this->bookingId)
            ->orderByDesc('created_at')
            ->get();

        $maintenanceLogs = $activeItem
            ? MotorbikeMaintenanceLog::where('motorbike_id', $activeItem->motorbike_id)
                ->orderByDesc('serviced_at')
                ->limit(10)
                ->get()
            : collect();

        return view('flux-admin.partials.rentals.issuance-tab', [
            'booking'         => $booking,
            'activeItem'      => $activeItem,
            'issuanceHistory' => $issuanceHistory,
            'videos'          => $videos,
            'maintenanceLogs' => $maintenanceLogs,
        ]);
    }
}
