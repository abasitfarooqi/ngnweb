<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\PurchaseUsedVehicle;
use App\Support\FluxAdminFormPayload;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Used vehicle purchases — Flux Admin')]
class PurchaseUsedIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'used-vehicle-purchases';
        $this->sortField = 'purchase_date';
    }

    protected function formModel(): string { return PurchaseUsedVehicle::class; }

    protected function formRules(): array
    {
        return [
            'formData.purchase_date'  => ['nullable', 'date'],
            'formData.full_name'      => ['required', 'string', 'max:255'],
            'formData.phone_number'   => ['nullable', 'string', 'max:50'],
            'formData.email'          => ['nullable', 'email', 'max:255'],
            'formData.address'        => ['nullable', 'string', 'max:1000'],
            'formData.postcode'       => ['nullable', 'string', 'max:20'],
            'formData.make'           => ['nullable', 'string', 'max:100'],
            'formData.model'          => ['nullable', 'string', 'max:100'],
            'formData.year'           => ['nullable', 'string', 'max:10'],
            'formData.colour'         => ['nullable', 'string', 'max:50'],
            'formData.reg_no'         => ['nullable', 'string', 'max:20'],
            'formData.vin'            => ['nullable', 'string', 'max:50'],
            'formData.price'          => ['nullable', 'numeric', 'min:0'],
            'formData.deposit'        => ['nullable', 'numeric', 'min:0'],
            'formData.outstanding'    => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function beforeSave(array $attributes): array
    {
        if (! $this->recordId) {
            $attributes['user_id'] = FluxAdminFormPayload::adminUserId();
        }

        return $attributes;
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'purchase_date' => now()->format('Y-m-d'),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(PurchaseUsedVehicle::findOrFail($id));
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
        PurchaseUsedVehicle::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
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
