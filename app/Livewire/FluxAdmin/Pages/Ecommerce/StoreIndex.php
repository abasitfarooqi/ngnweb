<?php

namespace App\Livewire\FluxAdmin\Pages\Ecommerce;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\NgnProduct;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Store front — Flux Admin')]
class StoreIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-ecommerce');
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.ecommerce.store-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return NgnProduct::with(['brand', 'category', 'productModel', 'stockMovements.branch'])
            ->where('normal_price', '>', 0)
            ->where(function ($q) {
                $q->where('is_oxford', 1)->orWhere('is_ecommerce', 1);
            })
            ->when($this->search, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('name', 'like', "%{$v}%")->orWhere('sku', 'like', "%{$v}%");
                });
            })
            ->when($this->filter('is_oxford') !== '', fn ($q) => $q->where('is_oxford', $this->filter('is_oxford')))
            ->when($this->filter('is_ecommerce') !== '', fn ($q) => $q->where('is_ecommerce', $this->filter('is_ecommerce')));
    }
}
