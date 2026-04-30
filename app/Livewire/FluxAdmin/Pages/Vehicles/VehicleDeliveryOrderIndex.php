<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\DeliveryVehicleType;
use App\Models\VehicleDeliveryOrder;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Vehicle delivery orders — Flux Admin')]
class VehicleDeliveryOrderIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'vehicle-delivery-orders';
        $this->sortField = 'quote_date';
    }

    protected function formModel(): string { return VehicleDeliveryOrder::class; }

    protected function formRules(): array
    {
        return [
            'formData.quote_date' => ['required', 'date'],
            'formData.pickup_date' => ['nullable', 'date'],
            'formData.total_distance' => ['nullable', 'numeric', 'min:0'],
            'formData.surcharge' => ['nullable', 'numeric', 'min:0'],
            'formData.delivery_vehicle_type_id' => ['required', 'integer', 'exists:delivery_vehicle_types,id'],
            'formData.branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'formData.vrm' => ['nullable', 'string', 'max:20'],
            'formData.full_name' => ['required', 'string', 'max:255'],
            'formData.phone_number' => ['nullable', 'string', 'max:40'],
            'formData.email' => ['nullable', 'email', 'max:255'],
            'formData.notes' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'quote_date' => now()->toDateString(),
            'user_id' => auth()->id(),
            'surcharge' => 0,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(VehicleDeliveryOrder::findOrFail($id));
        foreach (['quote_date', 'pickup_date'] as $k) {
            if (! empty($this->formData[$k])) {
                $this->formData[$k] = \Carbon\Carbon::parse($this->formData[$k])->format('Y-m-d');
            }
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['user_id'] = $this->formData['user_id'] ?? auth()->id();
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        VehicleDeliveryOrder::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $types = DeliveryVehicleType::query()->orderBy('name')->get(['id', 'name']);
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.vehicles.vehicle-delivery-orders-index', compact('rows', 'types', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return VehicleDeliveryOrder::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('full_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('phone_number', 'like', "%{$v}%")->orWhere('vrm', 'like', "%{$v}%")))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v))
            ->when($this->filter('delivery_vehicle_type_id'), fn ($q, $v) => $q->where('delivery_vehicle_type_id', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Quote date' => fn ($r) => $r->quote_date ? \Carbon\Carbon::parse($r->quote_date)->format('Y-m-d') : '',
            'Pickup' => fn ($r) => $r->pickup_date ? \Carbon\Carbon::parse($r->pickup_date)->format('Y-m-d') : '',
            'VRM' => 'vrm', 'Full name' => 'full_name', 'Phone' => 'phone_number', 'Email' => 'email',
            'Distance' => 'total_distance', 'Surcharge' => 'surcharge',
        ];
    }
}
