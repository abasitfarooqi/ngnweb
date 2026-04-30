<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\AgreementAccess;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Rental agreement links — Flux Admin')]
class AgreementAccessIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-renting-page');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['customer:id,first_name,last_name'])
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('flux-admin.pages.finance.agreement-access-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return AgreementAccess::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(fn ($q) => $q->where('passcode', 'like', "%{$term}%")->orWhere('booking_id', $term)->orWhereHas('customer', fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")));
            });
    }
}
