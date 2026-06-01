<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Exports\MotorbikesSaleExport;
use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikesSale;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike sales — Flux Admin')]
class SaleIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'motorbike-sales';
    }

    protected function formModel(): string { return MotorbikesSale::class; }

    protected function formRules(): array
    {
        return [
            'formData.motorbike_id'  => ['required', 'integer'],
            'formData.condition'     => ['nullable', 'string', 'max:120'],
            'formData.mileage'       => ['nullable', 'integer'],
            'formData.price'         => ['nullable', 'numeric'],
            'formData.note'          => ['nullable', 'string'],
            'formData.is_sold'       => ['boolean'],
            'formData.buyer_name'    => ['nullable', 'string', 'max:255'],
            'formData.buyer_phone'   => ['nullable', 'string', 'max:50'],
            'formData.buyer_email'   => ['nullable', 'email', 'max:255'],
            'formData.buyer_address' => ['nullable', 'string', 'max:500'],
            'formData.v5_available'  => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_sold' => false, 'v5_available' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $s = MotorbikesSale::findOrFail($id);
        $this->fillFromModel($s);
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
        MotorbikesSale::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function exportSales()
    {
        return Excel::download(new MotorbikesSaleExport, 'motorbikes_sales_'.date('Y-m-d').'.xlsx');
    }

    public function render()
    {
        $sales = $this->baseQuery()
            ->with('motorbike:id,reg_no,make,model,year')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.sales-index', ['sales' => $sales]);
    }

    protected function baseQuery(): Builder
    {
        return MotorbikesSale::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('buyer_name', 'like', "%{$term}%")
                        ->orWhere('buyer_email', 'like', "%{$term}%")
                        ->orWhereHas('motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%")->orWhere('model', 'like', "%{$term}%"));
                });
            })
            ->when($this->filter('is_sold') !== '', function ($q): void {
                $q->where('is_sold', $this->filter('is_sold') === '1');
            });
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('motorbike:id,reg_no,make,model');
    }

    protected function exportColumns(): array
    {
        return [
            'ID'           => 'id',
            'Registration' => fn ($s) => $s->motorbike?->reg_no,
            'Make'         => fn ($s) => $s->motorbike?->make,
            'Model'        => fn ($s) => $s->motorbike?->model,
            'Mileage'      => 'mileage',
            'Price'        => 'price',
            'Purchased'    => fn ($s) => $s->date_of_purchase ? Carbon::parse($s->date_of_purchase)->format('Y-m-d') : '',
            'Sold'         => fn ($s) => $s->is_sold ? 'Yes' : 'No',
            'Buyer'        => 'buyer_name',
        ];
    }
}
