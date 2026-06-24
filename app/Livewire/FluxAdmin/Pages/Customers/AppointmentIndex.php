<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\CustomerAppointments;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Customer appointments — Flux Admin')]
class AppointmentIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'appointment_date';
        $this->exportable = true;
        $this->exportFilename = 'customer-appointments';
    }

    public function delete(int $id): void
    {
        CustomerAppointments::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Appointment deleted.');
    }

    public function toggleResolved(int $id): void
    {
        $appointment = CustomerAppointments::findOrFail($id);
        $appointment->is_resolved = ! $appointment->is_resolved;
        $appointment->save();
    }

    public function render()
    {
        $appointments = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.customers.appointments-index', [
            'appointments' => $appointments,
        ]);
    }

    protected function baseQuery(): Builder
    {
        return CustomerAppointments::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('customer_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('contact_number', 'like', "%{$term}%")
                        ->orWhere('registration_number', 'like', "%{$term}%");
                });
            })
            ->when($this->filter('resolved') !== '', function ($q): void {
                $q->where('is_resolved', $this->filter('resolved') === '1');
            })
            ->when($this->filter('from'), fn ($q, $v) => $q->whereDate('appointment_date', '>=', $v))
            ->when($this->filter('to'), fn ($q, $v) => $q->whereDate('appointment_date', '<=', $v));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Appointment' => fn ($a) => $a->appointment_date?->format('Y-m-d H:i'),
            'Customer' => 'customer_name',
            'Registration' => 'registration_number',
            'Contact' => 'contact_number',
            'Email' => 'email',
            'Resolved' => fn ($a) => $a->is_resolved ? 'Yes' : 'No',
            'Reason' => 'booking_reason',
        ];
    }
}
