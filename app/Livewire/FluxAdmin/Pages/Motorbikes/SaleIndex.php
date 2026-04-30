<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\MotorbikesSale;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Motorbike sales — Flux Admin')]
class SaleIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'motorbike-sales';
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
            'ID' => 'id',
            'Registration' => fn ($s) => $s->motorbike?->reg_no,
            'Make' => fn ($s) => $s->motorbike?->make,
            'Model' => fn ($s) => $s->motorbike?->model,
            'Mileage' => 'mileage',
            'Price' => 'price',
            'Purchased' => fn ($s) => $s->date_of_purchase ? \Carbon\Carbon::parse($s->date_of_purchase)->format('Y-m-d') : '',
            'Sold' => fn ($s) => $s->is_sold ? 'Yes' : 'No',
            'Buyer' => 'buyer_name',
        ];
    }
}
