<?php

namespace App\Livewire\FluxAdmin\Pages\Access;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\RentingServiceVideo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Service videos — Flux Admin')]
class ServiceVideoIndex extends Component
{
    use WithAuthorization, WithDataTable, WithPagination;

    public function mount(): void { $this->authorizeModule('see-menu-renting-page'); $this->sortField = 'recorded_at'; }

    public function render()
    {
        $rows = RentingServiceVideo::query()
            ->when($this->search, fn ($q, $v) => $q->where('booking_id', $v))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.access.service-videos-index', ['rows' => $rows]);
    }
}
