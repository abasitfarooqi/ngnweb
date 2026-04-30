<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Motorcycle;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('New motorbikes for sale — Flux Admin')]
class ForSaleIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'new-motorbikes-for-sale';
    }

    public function render()
    {
        $bikes = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.for-sale-index', ['bikes' => $bikes]);
    }

    protected function baseQuery(): Builder
    {
        return Motorcycle::query()->where('availability', 'for sale')
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(fn ($q) => $q->where('make', 'like', "%{$term}%")->orWhere('model', 'like', "%{$term}%"));
            })
            ->when($this->filter('type'), fn ($q, $v) => $q->where('type', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Make' => 'make', 'Model' => 'model', 'Year' => 'year',
            'Type' => 'type', 'Engine' => 'engine', 'Colour' => 'colour', 'Sale price' => 'sale_new_price',
        ];
    }
}
