<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\RentingServiceVideo;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Service videos — Flux Admin')]
class ServiceVideoIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
        $this->sortField = 'recorded_at';
    }

    protected function formModel(): string
    {
        return RentingServiceVideo::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.booking_id' => ['required', 'integer'],
            'formData.video_path' => ['nullable', 'string', 'max:1000'],
            'formData.recorded_at' => ['nullable', 'date'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['recorded_at' => now()->toDateString()];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = RentingServiceVideo::findOrFail($id);
        $this->fillFromModel($record);
        $this->formData['recorded_at'] = $record->recorded_at
            ? Carbon::parse($record->recorded_at)->format('Y-m-d')
            : null;
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        RentingServiceVideo::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = RentingServiceVideo::query()
            ->with(['rentingBooking.customer', 'rentingBooking.rentingBookingItems.motorbike'])
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->where(function ($inner) use ($term) {
                    if (ctype_digit($term)) {
                        $inner->where('booking_id', (int) $term);
                    }
                    $inner->orWhereHas('rentingBooking.customer', function ($customerQuery) use ($term) {
                        $customerQuery->where('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                    })->orWhereHas('rentingBooking.rentingBookingItems.motorbike', function ($bikeQuery) use ($term) {
                        $bikeQuery->where('reg_no', 'like', "%{$term}%");
                    });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.access.service-videos-index', ['rows' => $rows]);
    }
}
