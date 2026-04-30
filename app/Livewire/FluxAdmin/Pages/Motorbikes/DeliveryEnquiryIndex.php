<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikeDeliveryOrderEnquiries;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Delivery order enquiries — Flux Admin')]
class DeliveryEnquiryIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'delivery-enquiries';
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
            'Order ID' => 'order_id', 'Name' => 'full_name', 'Phone' => 'phone', 'Email' => 'email',
            'VRM' => 'vrm', 'Vehicle type' => fn ($r) => $r->vehicleType?->name,
            'Pickup postcode' => 'pickup_postcode', 'Dropoff postcode' => 'dropoff_postcode',
            'Pickup' => fn ($r) => $r->pick_up_datetime ? \Carbon\Carbon::parse($r->pick_up_datetime)->format('Y-m-d H:i') : '',
            'Distance' => 'distance', 'Total cost' => 'total_cost',
            'Branch' => fn ($r) => $r->branch?->name, 'Dealt' => fn ($r) => $r->is_dealt ? 'Yes' : 'No',
        ];
    }
}
