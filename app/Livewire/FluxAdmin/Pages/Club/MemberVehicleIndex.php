<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ClubMember;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club member vehicles — Flux Admin')]
class MemberVehicleIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'club-member-vehicles';
        $this->sortField = 'full_name';
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.member-vehicle-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return ClubMember::query()
            ->when($this->search, function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('full_name', 'like', "%{$v}%")
                        ->orWhere('email', 'like', "%{$v}%")
                        ->orWhere('phone', 'like', "%{$v}%")
                        ->orWhere('vrm', 'like', "%{$v}%")
                        ->orWhere('make', 'like', "%{$v}%")
                        ->orWhere('model', 'like', "%{$v}%");
                });
            });
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'Member' => 'full_name', 'Email' => 'email', 'Phone' => 'phone',
            'VRM' => 'vrm', 'Make' => 'make', 'Model' => 'model', 'Year' => 'year',
        ];
    }
}
