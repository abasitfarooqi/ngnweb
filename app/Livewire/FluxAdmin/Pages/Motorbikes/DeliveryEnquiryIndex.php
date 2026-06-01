<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Delivery order enquiries — Flux Admin')]
class DeliveryEnquiryIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'delivery-enquiries';
    }

    protected function formModel(): string { return MotorbikeDeliveryOrderEnquiries::class; }

    protected function formRules(): array
    {
        return [
            'formData.full_name'        => ['required', 'string', 'max:255'],
            'formData.phone'            => ['nullable', 'string', 'max:50'],
            'formData.email'            => ['nullable', 'email', 'max:255'],
            'formData.vrm'              => ['nullable', 'string', 'max:20'],
            'formData.pickup_postcode'  => ['nullable', 'string', 'max:20'],
            'formData.dropoff_postcode' => ['nullable', 'string', 'max:20'],
            'formData.pickup_address'   => ['nullable', 'string', 'max:500'],
            'formData.dropoff_address'  => ['nullable', 'string', 'max:500'],
            'formData.pick_up_datetime' => ['nullable', 'date'],
            'formData.distance'         => ['nullable', 'numeric'],
            'formData.total_cost'       => ['nullable', 'numeric'],
            'formData.note'             => ['nullable', 'string'],
            'formData.branch_id'        => ['nullable', 'integer'],
            'formData.is_dealt'         => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_dealt' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $r = MotorbikeDeliveryOrderEnquiries::findOrFail($id);
        $this->fillFromModel($r);
        if (! empty($this->formData['pick_up_datetime'])) {
            $this->formData['pick_up_datetime'] = Carbon::parse($this->formData['pick_up_datetime'])->format('Y-m-d\TH:i');
        }
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
        MotorbikeDeliveryOrderEnquiries::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function toggleDealt(int $id): void
    {
        $enquiry = MotorbikeDeliveryOrderEnquiries::findOrFail($id);
        $enquiry->is_dealt = ! $enquiry->is_dealt;
        $enquiry->dealt_by_user_id = backpack_user()->id;
        $enquiry->save();

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Updated.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['branch:id,name', 'vehicleType:id,name', 'dealtByUser:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = \App\Models\Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.motorbikes.delivery-enquiries-index', compact('rows', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return MotorbikeDeliveryOrderEnquiries::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('order_id', 'like', "%{$term}%")
                        ->orWhere('full_name', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('vrm', 'like', "%{$term}%");
                });
            })
            ->when($this->filter('is_dealt') !== '', fn ($q) => $q->where('is_dealt', $this->filter('is_dealt') === '1'))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['branch:id,name', 'vehicleType:id,name']); }

    protected function exportColumns(): array
    {
        return [
            'Order ID'         => 'order_id',
            'Name'             => 'full_name',
            'Phone'            => 'phone',
            'Email'            => 'email',
            'VRM'              => 'vrm',
            'Vehicle type'     => fn ($r) => $r->vehicleType?->name,
            'Pickup postcode'  => 'pickup_postcode',
            'Dropoff postcode' => 'dropoff_postcode',
            'Pickup'           => fn ($r) => $r->pick_up_datetime ? Carbon::parse($r->pick_up_datetime)->format('Y-m-d H:i') : '',
            'Distance'         => 'distance',
            'Total cost'       => 'total_cost',
            'Branch'           => fn ($r) => $r->branch?->name,
            'Dealt'            => fn ($r) => $r->is_dealt ? 'Yes' : 'No',
        ];
    }
}
