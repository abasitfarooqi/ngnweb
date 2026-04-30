<?php

namespace App\Livewire\FluxAdmin\Pages\Roles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Roles — Flux Admin')]
class RoleIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-permissions');
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $model = config('permission.models.role');

        $roles = $model::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount(['users', 'permissions'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.roles.index', [
            'roles' => $roles,
        ]);
    }
}
