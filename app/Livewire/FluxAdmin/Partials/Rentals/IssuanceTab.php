<?php

namespace App\Livewire\FluxAdmin\Partials\Rentals;

use App\Models\BookingIssuanceItem;
use App\Models\CustomerAgreement;
use App\Models\MotorbikeMaintenanceLog;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingServiceVideo;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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

    public bool $showExtras = false;

    public ?string $flashMessage = null;
    public ?string $flashType = null;

    public function mount(int $bookingId): void
    {
        $this->bookingId = $bookingId;

        $user = function_exists('backpack_user') ? backpack_user() : auth()->user();
        if ($user) {
            $name = trim(((string) ($user->first_name ?? '')).' '.((string) ($user->last_name ?? '')));
            $this->issuanceNotes = $name !== '' ? $name : (string) ($user->name ?? '');
        }
    }

    public function placeholder()
    {
        return view('flux-admin.partials.loading-placeholder');
    }

    public function issueMotorbike(): void
    {
        $this->validate([
            'issuanceNotes' => 'required|string|min:2',
            'currentMileage' => 'required|numeric|min:0',
            'isVideoRecorded' => 'accepted',
            'accessoriesChecked' => 'accepted',
            'issuanceBranch' => 'required|string|in:Catford,Tooting,Sutton',
        ]);

        $booking = RentingBooking::findOrFail($this->bookingId);

        if ($booking->state !== 'Completed') {
            $this->flashMessage = 'Booking must be in "Completed" state before ISSUE NOW (documents + payment done). Current: '.($booking->state ?: 'Unknown');
            $this->flashType = 'error';

            return;
        }

        $bookingItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->first();

        if (! $bookingItem) {
            $this->flashMessage = 'No open booking item found to issue.';
            $this->flashType = 'error';

            return;
        }

        $issuedBy = $this->resolveStaffUserId();
        if (! $issuedBy) {
            $this->flashMessage = 'You must be signed in to issue a motorbike.';
            $this->flashType = 'error';

            return;
        }

        try {
            DB::transaction(function () use ($booking, $bookingItem, $issuedBy) {
                $booking->update([
                    'state' => 'Completed & Issued',
                    'is_posted' => true,
                    'notes' => 'Issued on '.now()->toDateTimeString(),
                ]);

                BookingIssuanceItem::create([
                    'booking_item_id' => $bookingItem->id,
                    'issued_by_user_id' => $issuedBy,
                    'notes' => $this->issuanceNotes,
                    'is_insured' => $this->isInsured,
                    'current_mileage' => (int) $this->currentMileage,
                    'is_video_recorded' => $this->isVideoRecorded,
                    'accessories_checked' => $this->accessoriesChecked,
                    'issuance_branch' => $this->issuanceBranch,
                ]);
            });
        } catch (\Throwable $e) {
            $this->flashMessage = 'Issue failed: '.$e->getMessage();
            $this->flashType = 'error';

            return;
        }

        $this->flashMessage = 'Motorbike issued — state is now Completed & Issued.';
        $this->flashType = 'success';
        $this->resetForm();
        $this->dispatch('rental-updated');
    }

    public function reissueMotorbike(): void
    {
        $this->validate([
            'issuanceNotes' => 'required|string|min:2',
            'currentMileage' => 'required|numeric|min:0',
            'isVideoRecorded' => 'accepted',
            'accessoriesChecked' => 'accepted',
            'issuanceBranch' => 'required|string|in:Catford,Tooting,Sutton',
        ]);

        $booking = RentingBooking::findOrFail($this->bookingId);
        if ($booking->state !== 'Completed & Issued') {
            $this->flashMessage = 'Re-inspection is only for Completed & Issued bookings.';
            $this->flashType = 'error';

            return;
        }

        $bookingItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->first();

        if (! $bookingItem) {
            $this->flashMessage = 'No open booking item found.';
            $this->flashType = 'error';

            return;
        }

        $issuedBy = $this->resolveStaffUserId();
        if (! $issuedBy) {
            $this->flashMessage = 'You must be signed in to save an inspection log.';
            $this->flashType = 'error';

            return;
        }

        BookingIssuanceItem::create([
            'booking_item_id' => $bookingItem->id,
            'issued_by_user_id' => $issuedBy,
            'notes' => $this->issuanceNotes,
            'is_insured' => $this->isInsured,
            'current_mileage' => (int) $this->currentMileage,
            'is_video_recorded' => $this->isVideoRecorded,
            'accessories_checked' => $this->accessoriesChecked,
            'issuance_branch' => $this->issuanceBranch,
        ]);

        $this->flashMessage = 'Inspection and re-issuance saved.';
        $this->flashType = 'success';
        $this->resetForm();
        $this->dispatch('rental-updated');
    }

    public function uploadVideo(): void
    {
        $this->validate([
            'videoFile' => 'required|file|mimes:mp4,mov,avi,wmv,mkv|max:512000',
        ]);

        try {
            $timestamp = now()->format('Ymd_His');
            $extension = $this->videoFile->getClientOriginalExtension();
            $fileName = $this->bookingId.'_'.$timestamp.'.'.$extension;
            $storePath = $this->videoFile->storeAs('rental_service_videos', $fileName, 'public');

            RentingServiceVideo::create([
                'booking_id' => $this->bookingId,
                'video_path' => $storePath,
                'recorded_at' => now(),
            ]);

            $this->videoFile = null;
            $this->isVideoRecorded = true;
            $this->flashMessage = 'Service video uploaded.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = 'Video upload failed: '.$e->getMessage();
            $this->flashType = 'error';
        }
    }

    public function addMaintenanceLog(): void
    {
        $activeItem = RentingBookingItem::where('booking_id', $this->bookingId)
            ->whereNull('end_date')
            ->latest()
            ->first();

        if (! $activeItem) {
            $this->flashMessage = 'No open booking item for maintenance log.';
            $this->flashType = 'error';

            return;
        }

        $this->validate([
            'logDescription' => 'required|string|max:255',
            'logCost' => 'required|numeric|min:0',
            'logServicedAt' => 'required|date',
            'logNote' => 'nullable|string',
        ]);

        $staffUserId = $this->resolveStaffUserId();
        if (! $staffUserId) {
            $this->flashMessage = 'You must be signed in to save a maintenance log.';
            $this->flashType = 'error';

            return;
        }

        try {
            MotorbikeMaintenanceLog::create([
                'motorbike_id' => $activeItem->motorbike_id,
                'booking_id' => $this->bookingId,
                'user_id' => $staffUserId,
                'cost' => $this->logCost,
                'serviced_at' => $this->logServicedAt,
                'description' => $this->logDescription,
                'note' => $this->logNote ?: null,
            ]);

            $this->logDescription = '';
            $this->logCost = '';
            $this->logServicedAt = '';
            $this->logNote = '';
            $this->flashMessage = 'Maintenance log saved.';
            $this->flashType = 'success';
        } catch (\Throwable $e) {
            $this->flashMessage = 'Maintenance log failed: '.$e->getMessage();
            $this->flashType = 'error';
        }
    }

    private function resetForm(): void
    {
        $user = function_exists('backpack_user') ? backpack_user() : auth()->user();
        $name = '';
        if ($user) {
            $name = trim(((string) ($user->first_name ?? '')).' '.((string) ($user->last_name ?? '')));
            if ($name === '') {
                $name = (string) ($user->name ?? '');
            }
        }

        $this->issuanceNotes = $name;
        $this->currentMileage = '';
        $this->isVideoRecorded = false;
        $this->accessoriesChecked = false;
        $this->isInsured = false;
        $this->issuanceBranch = '';
        $this->resetValidation();
    }

    private function resolveStaffUserId(): ?int
    {
        if (function_exists('backpack_user') && backpack_user()) {
            return (int) backpack_user()->id;
        }

        return auth()->id();
    }

    public function formatMaintenanceDate(mixed $value): string
    {
        if ($value instanceof CarbonInterface) {
            return $value->format('d M Y');
        }

        if ($value === null || $value === '') {
            return '—';
        }

        try {
            return Carbon::parse((string) $value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
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

        $signedCount = CustomerAgreement::where('booking_id', $this->bookingId)->count();
        $signedVerifiedCount = CustomerAgreement::where('booking_id', $this->bookingId)->where('is_verified', true)->count();

        return view('flux-admin.partials.rentals.issuance-tab', [
            'booking' => $booking,
            'activeItem' => $activeItem,
            'issuanceHistory' => $issuanceHistory,
            'videos' => $videos,
            'maintenanceLogs' => $maintenanceLogs,
            'signedCount' => $signedCount,
            'signedVerifiedCount' => $signedVerifiedCount,
        ]);
    }
}
