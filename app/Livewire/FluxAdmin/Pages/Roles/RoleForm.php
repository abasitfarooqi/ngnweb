<?php

namespace App\Livewire\FluxAdmin\Pages\Roles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Support\FluxAdminAccess;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('flux-admin.layouts.app')]
#[Title('Role — Flux Admin')]
class RoleForm extends Component
{
    use WithAuthorization;

    public ?int $roleId = null;

    public string $name = '';

    public string $guardName = 'web';

    /** @var array<int, int> */
    public array $selectedPermissions = [];

    public string $permissionSearch = '';

    public function mount(?int $role = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-permissions');

        if ($role !== null) {
            $model = config('permission.models.role');
            $roleModel = $model::with('permissions:id')->findOrFail($role);
            $this->roleId = $roleModel->id;
            $this->name = $roleModel->name;
            $this->guardName = $roleModel->guard_name;
            $this->selectedPermissions = $roleModel->permissions->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        }
    }

    public function render()
    {
        $permissionModel = config('permission.models.permission');
        $permissions = $permissionModel::query()
            ->when(
                ! FluxAdminAccess::canAssignCommunicationsPermission(),
                fn ($q) => $q->where('name', '!=', FluxAdminAccess::COMMUNICATIONS_PERMISSION),
            )
            ->when($this->permissionSearch, fn ($q) => $q->where('name', 'like', "%{$this->permissionSearch}%"))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('flux-admin.pages.roles.form', [
            'permissions' => $permissions,
        ]);
    }

    public function save()
    {
        $roleModel = config('permission.models.role');
        $table = (new $roleModel)->getTable();

        $this->validate([
            'name' => ['required', 'string', 'max:125', Rule::unique($table, 'name')->ignore($this->roleId)],
            'guardName' => ['required', 'string'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = $this->roleId
            ? $roleModel::findOrFail($this->roleId)
            : new $roleModel;

        $role->name = $this->name;
        $role->guard_name = $this->guardName;
        $role->save();

        $permissionModel = config('permission.models.permission');
        $communicationsPermissionId = $permissionModel::query()
            ->where('name', FluxAdminAccess::COMMUNICATIONS_PERMISSION)
            ->value('id');

        $rolePermissionIds = array_map('intval', $this->selectedPermissions);

        if ($communicationsPermissionId) {
            $communicationsPermissionId = (int) $communicationsPermissionId;
            $rolePermissionIds = array_values(array_filter(
                $rolePermissionIds,
                fn (int $id) => $id !== $communicationsPermissionId,
            ));

            $keepCommunications = FluxAdminAccess::canAssignCommunicationsPermission()
                ? in_array($communicationsPermissionId, array_map('intval', $this->selectedPermissions), true)
                : $role->permissions()->where('permissions.id', $communicationsPermissionId)->exists();

            if ($keepCommunications) {
                $rolePermissionIds[] = $communicationsPermissionId;
            }
        }

        $role->permissions()->sync($rolePermissionIds);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        session()->flash('flux-admin.flash', $this->roleId ? 'Role updated.' : 'Role created.');

        return redirect()->route('flux-admin.roles.edit', ['role' => $role->id]);
    }
}
