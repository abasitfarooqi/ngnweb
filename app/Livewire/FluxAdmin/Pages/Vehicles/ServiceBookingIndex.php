<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ServiceBooking;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Service bookings — Flux Admin')]
class ServiceBookingIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-services-and-repairs-and-report');
        $this->exportable = true;
        $this->exportFilename = 'service-bookings';
        $this->sortField = 'booking_date';
    }

    public function toggleDealt(int $id): void
    {
        $b = ServiceBooking::findOrFail($id);
        $b->is_dealt = ! $b->is_dealt;
        $b->dealt_by_user_id = backpack_user()->id;
        $b->save();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Updated.');
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
