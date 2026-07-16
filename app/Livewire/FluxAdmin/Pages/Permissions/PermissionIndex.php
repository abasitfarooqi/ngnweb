<?php

namespace App\Livewire\FluxAdmin\Pages\Permissions;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\PermissionRegistrar;

#[Layout('flux-admin.layouts.app')]
#[Title('Permissions — Flux Admin')]
class PermissionIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public bool $allowCreate = true;

    public bool $allowUpdate = true;

    public bool $allowDelete = true;

    public bool $multipleGuards = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-permissions');
        $this->sortField = request()->query('sort', 'name');
        $this->sortDirection = request()->query('dir', 'asc');
        $this->allowCreate = (bool) config('backpack.permissionmanager.allow_permission_create', true);
        $this->allowUpdate = (bool) config('backpack.permissionmanager.allow_permission_update', true);
        $this->allowDelete = (bool) config('backpack.permissionmanager.allow_permission_delete', true);
        $this->multipleGuards = (bool) config('backpack.permissionmanager.multiple_guards', false);
    }

    public function render()
    {
        $model = config('permission.models.permission');
        $permissions = $model::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('roles')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.permissions.index', [
            'permissions' => $permissions,
        ]);
    }

    public function deletePermission(int $id): void
    {
        if (! $this->allowDelete) {
            return;
        }

        $model = config('permission.models.permission');
        $model::findOrFail($id)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        session()->flash('flux-admin.flash', 'Permission deleted.');
    }
}
