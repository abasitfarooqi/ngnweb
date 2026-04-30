<?php

namespace App\Livewire\FluxAdmin\Pages\Motorbikes;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Motorbike;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('E-bike manager — Flux Admin')]
class EbikeIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function render()
    {
        $bikes = $this->baseQuery()
            ->with([
                'registrations' => fn ($q) => $q->orderByDesc('start_date'),
                'rentingPricings' => fn ($q) => $q->where('iscurrent', true)->orderByDesc('update_date'),
            ])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.motorbikes.ebikes-index', ['bikes' => $bikes]);
    }

    protected function baseQuery(): Builder
    {
        return Motorbike::query()->where('is_ebike', true)
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('make', 'like', "%{$term}%")
                        ->orWhere('model', 'like', "%{$term}%")
                        ->orWhere('vin_number', 'like', "%{$term}%")
                        ->orWhere('reg_no', 'like', "%{$term}%");
                });
            })
            ->when($this->filter('make'), fn ($q, $v) => $q->where('make', 'like', "%{$v}%"));
    }
}
