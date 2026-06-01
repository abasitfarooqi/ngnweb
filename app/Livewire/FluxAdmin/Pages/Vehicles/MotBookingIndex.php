<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MOTBooking;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT bookings — Flux Admin')]
class MotBookingIndex extends Component
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
        $this->exportFilename = 'mot-bookings';
        $this->sortField = 'date_of_appointment';
    }

    protected function formModel(): string { return MOTBooking::class; }

    protected function formRules(): array
    {
        return [
            'formData.branch_id'            => ['nullable', 'integer', 'exists:branches,id'],
            'formData.title'                => ['nullable', 'string', 'max:255'],
            'formData.customer_name'        => ['required', 'string', 'max:255'],
            'formData.customer_contact'     => ['nullable', 'string', 'max:50'],
            'formData.customer_email'       => ['nullable', 'email', 'max:255'],
            'formData.vehicle_registration' => ['required', 'string', 'max:20'],
            'formData.date_of_appointment'  => ['nullable', 'date'],
            'formData.status'               => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'formData.is_paid'              => ['boolean'],
            'formData.notes'               => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'status'              => 'pending',
            'is_paid'             => false,
            'date_of_appointment' => now()->format('Y-m-d'),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(MOTBooking::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['is_paid'] = (bool) ($this->formData['is_paid'] ?? false);
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        MOTBooking::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $bookings = $this->baseQuery()
            ->with('branch:id,name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = \App\Models\Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.vehicles.mot-bookings-index', compact('bookings', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return MOTBooking::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('vehicle_registration', 'like', "%{$v}%")->orWhere('customer_name', 'like', "%{$v}%")->orWhere('customer_email', 'like', "%{$v}%")->orWhere('customer_contact', 'like', "%{$v}%")))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v))
            ->when($this->filter('is_paid') !== '', fn ($q) => $q->where('is_paid', $this->filter('is_paid') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('branch');
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Title' => 'title', 'Branch' => fn ($b) => $b->branch?->name,
            'Date' => fn ($b) => $b->date_of_appointment ? \Carbon\Carbon::parse($b->date_of_appointment)->format('Y-m-d') : '',
            'VRM' => 'vehicle_registration', 'Customer' => 'customer_name', 'Phone' => 'customer_contact',
            'Email' => 'customer_email', 'Status' => 'status', 'Paid' => fn ($b) => $b->is_paid ? 'Yes' : 'No',
        ];
    }
}
