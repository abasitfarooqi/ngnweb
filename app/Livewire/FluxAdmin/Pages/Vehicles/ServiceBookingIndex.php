<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ServiceBooking;
use App\Support\FluxAdminFormPayload;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Service bookings — Flux Admin')]
class ServiceBookingIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->exportable = true;
        $this->exportFilename = 'service-bookings';
        $this->sortField = 'booking_date';
    }

    protected function formModel(): string { return ServiceBooking::class; }

    protected function formRules(): array
    {
        return [
            'formData.fullname'      => ['required', 'string', 'max:255'],
            'formData.phone'         => ['nullable', 'string', 'max:50'],
            'formData.email'         => ['nullable', 'email', 'max:255'],
            'formData.reg_no'        => ['nullable', 'string', 'max:20'],
            'formData.enquiry_type'  => ['nullable', 'string', 'in:service_booking,general'],
            'formData.service_type'  => ['nullable', 'string', 'max:100'],
            'formData.subject'       => ['nullable', 'string', 'max:255'],
            'formData.description'   => ['nullable', 'string', 'max:5000'],
            'formData.booking_date'  => ['nullable', 'date'],
            'formData.booking_time'  => ['nullable', 'string', 'max:20'],
            'formData.status'        => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'formData.notes'         => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'status'        => 'pending',
            'enquiry_type'  => 'service_booking',
            'booking_date'  => now()->format('Y-m-d'),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(ServiceBooking::findOrFail($id));
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
        ServiceBooking::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function markAsDealt(int $id): void
    {
        $booking = ServiceBooking::findOrFail($id);

        if ($booking->is_dealt) {
            return;
        }

        $booking->update([
            'is_dealt' => true,
            'dealt_by_user_id' => FluxAdminFormPayload::adminUserId(),
        ]);

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Marked as dealt.');
    }

    public function render()
    {
        $bookings = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.service-bookings-index', ['bookings' => $bookings]);
    }

    protected function baseQuery(): Builder
    {
        return ServiceBooking::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('fullname', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone', 'like', "%{$v}%")->orWhere('reg_no', 'like', "%{$v}%")))
            ->when($this->filter('is_dealt') !== '', fn ($q) => $q->where('is_dealt', $this->filter('is_dealt') === '1'))
            ->when($this->filter('enquiry_type'), fn ($q, $v) => $q->where('enquiry_type', $v))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Subject' => 'subject', 'Type' => 'enquiry_type', 'Service' => 'service_type',
            'Customer' => 'fullname', 'Phone' => 'phone', 'Email' => 'email', 'VRM' => 'reg_no',
            'Date' => fn ($b) => $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('Y-m-d') : '',
            'Time' => 'booking_time', 'Status' => 'status', 'Dealt' => fn ($b) => $b->is_dealt ? 'Yes' : 'No',
        ];
    }
}
