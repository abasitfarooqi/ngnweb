<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\RentingBooking;
use App\Models\RentingServiceVideo;
use App\Support\UploadLimit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('flux-admin.layouts.app')]
class ServiceVideoForm extends Component
{
    use WithAuthorization;
    use WithFileUploads;

    public ?RentingServiceVideo $serviceVideo = null;

    public array $form = [];

    public string $bookingSearch = '';

    public ?string $selectedBookingLabel = null;

    public $videoFile = null;

    public function mount(?RentingServiceVideo $serviceVideo = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-renting-page');
        $this->serviceVideo = $serviceVideo;

        if ($serviceVideo && $serviceVideo->exists) {
            $attrs = $serviceVideo->getAttributes();
            if (! empty($attrs['recorded_at'])) {
                try {
                    $attrs['recorded_at'] = Carbon::parse($attrs['recorded_at'])->format('Y-m-d\TH:i');
                } catch (\Throwable) {
                    $attrs['recorded_at'] = null;
                }
            }
            $this->form = $attrs;
            $this->loadSelectedBookingLabel((int) $serviceVideo->booking_id);
        } else {
            $this->form = ['recorded_at' => now()->format('Y-m-d\TH:i')];

            $prefillBookingId = (int) request()->query('booking_id', 0);
            if ($prefillBookingId > 0) {
                $this->form['booking_id'] = $prefillBookingId;
                $this->loadSelectedBookingLabel($prefillBookingId);
            }
        }
    }

    public function updatedBookingSearch(): void
    {
        if ($this->selectedBookingLabel !== null && $this->bookingSearch !== $this->selectedBookingLabel) {
            $this->form['booking_id'] = null;
            $this->selectedBookingLabel = null;
        }
    }

    public function selectBooking(int $bookingId): void
    {
        $booking = RentingBooking::query()
            ->with(['customer', 'rentingBookingItems.motorbike'])
            ->findOrFail($bookingId);

        $this->form['booking_id'] = $booking->id;
        $this->selectedBookingLabel = $this->formatBookingLabel($booking);
        $this->bookingSearch = $this->selectedBookingLabel;
    }

    public function clearBooking(): void
    {
        $this->form['booking_id'] = null;
        $this->selectedBookingLabel = null;
        $this->bookingSearch = '';
    }

    protected function formRules(): array
    {
        $rules = [
            'form.booking_id' => ['required', 'integer', 'exists:renting_bookings,id'],
            'form.recorded_at' => ['required', 'date'],
        ];

        if ($this->serviceVideo && $this->serviceVideo->exists) {
            $rules['videoFile'] = ['nullable', 'file', 'mimes:mp4,mov,avi,wmv,mkv', 'max:'.UploadLimit::maxKilobytes()];
        } else {
            $rules['videoFile'] = ['required', 'file', 'mimes:mp4,mov,avi,wmv,mkv', 'max:'.UploadLimit::maxKilobytes()];
        }

        return $rules;
    }

    public function save(): void
    {
        $data = $this->validate($this->formRules());
        $payload = $data['form'];
        $payload['recorded_at'] = Carbon::parse($payload['recorded_at']);

        if ($this->videoFile) {
            $bookingId = (int) $payload['booking_id'];
            $timestamp = now()->format('Ymd_His');
            $extension = $this->videoFile->getClientOriginalExtension();
            $fileName = $bookingId.'_'.$timestamp.'.'.$extension;
            $payload['video_path'] = $this->videoFile->storeAs('rental_service_videos', $fileName, 'public');
        }

        if ($this->serviceVideo && $this->serviceVideo->exists) {
            $this->serviceVideo->update($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Video updated.');
        } else {
            RentingServiceVideo::create($payload);
            $this->dispatch('flux-admin:toast', type: 'success', message: 'Video uploaded.');
        }

        $this->redirect(route('flux-admin.service-videos.index'), navigate: true);
    }

    private function loadSelectedBookingLabel(int $bookingId): void
    {
        $booking = RentingBooking::query()
            ->with(['customer', 'rentingBookingItems.motorbike'])
            ->find($bookingId);

        if (! $booking) {
            return;
        }

        $this->selectedBookingLabel = $this->formatBookingLabel($booking);
        $this->bookingSearch = $this->selectedBookingLabel;
    }

    private function formatBookingLabel(RentingBooking $booking): string
    {
        $customerModel = $booking->customer;
        $customer = trim((string) (($customerModel->first_name ?? '').' '.($customerModel->last_name ?? '')));
        $phone = trim((string) ($customerModel->phone ?? ''));
        $reg = $booking->rentingBookingItems->first()?->motorbike?->reg_no ?? '—';
        $start = $booking->start_date?->format('d M Y H:i') ?? '—';

        return '#'.$booking->id.' · '.($customer !== '' ? $customer : 'Unknown').' · '.$reg.' · '.$start.($phone !== '' ? ' · '.$phone : '');
    }

    /** @return Collection<int, RentingBooking> */
    private function bookingSearchResults(): Collection
    {
        $term = trim($this->bookingSearch);

        if ($term === '' || ($this->selectedBookingLabel !== null && $term === $this->selectedBookingLabel)) {
            return collect();
        }

        return RentingBooking::query()
            ->with(['customer', 'rentingBookingItems.motorbike'])
            ->when(ctype_digit($term), fn ($q) => $q->where('id', (int) $term))
            ->when(! ctype_digit($term), function ($q) use ($term) {
                $q->where(function ($inner) use ($term) {
                    $inner->whereHas('customer', function ($customerQuery) use ($term) {
                        $customerQuery->where('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%")
                            ->orWhere('phone', 'like', "%{$term}%")
                            ->orWhere('whatsapp', 'like', "%{$term}%")
                            ->orWhere('postcode', 'like', "%{$term}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                    })->orWhereHas('rentingBookingItems.motorbike', function ($bikeQuery) use ($term) {
                        $bikeQuery->where('reg_no', 'like', "%{$term}%")
                            ->orWhere('make', 'like', "%{$term}%")
                            ->orWhere('model', 'like', "%{$term}%");
                    });
                });
            })
            ->orderByDesc('start_date')
            ->limit(15)
            ->get();
    }

    public function render()
    {
        return view('flux-admin.pages.access.service-video-form', [
            'bookingResults' => $this->bookingSearchResults(),
            'maxUploadBytes' => UploadLimit::maxBytes(),
            'maxUploadLabel' => UploadLimit::label(),
        ]);
    }
}
