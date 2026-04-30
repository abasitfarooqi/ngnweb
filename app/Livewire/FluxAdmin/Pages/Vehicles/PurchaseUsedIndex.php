<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\PurchaseUsedVehicle;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Used vehicle purchases — Flux Admin')]
class PurchaseUsedIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'used-vehicle-purchases';
        $this->sortField = 'purchase_date';
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.vehicles.purchase-used-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return PurchaseUsedVehicle::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('full_name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%")->orWhere('reg_no', 'like', "%{$v}%")->orWhere('phone_number', 'like', "%{$v}%")));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Purchase date' => fn ($r) => $r->purchase_date ? \Carbon\Carbon::parse($r->purchase_date)->format('Y-m-d') : '',
            'Seller' => 'full_name', 'Phone' => 'phone_number', 'Email' => 'email',
            'Address' => 'address', 'Postcode' => 'postcode',
            'Make' => 'make', 'Model' => 'model', 'Year' => 'year', 'Colour' => 'colour', 'Reg' => 'reg_no',
            'VIN' => 'vin', 'Mileage' => 'current_mileage',
            'Price' => 'price', 'Deposit' => 'deposit', 'Outstanding' => 'outstanding', 'Total' => 'total_to_pay',
        ];
    }
}
