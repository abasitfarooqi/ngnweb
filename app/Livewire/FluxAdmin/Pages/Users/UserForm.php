<?php

namespace App\Livewire\FluxAdmin\Pages\Users;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('flux-admin.layouts.app')]
#[Title('User — Flux Admin')]
class UserForm extends Component
{
    use WithAuthorization;

    public ?int $userId = null;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $username = '';

    public string $employee_id = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $is_admin = false;

    public bool $is_client = false;

    public ?int $role_id = null;

    /** @var array<int, int> */
    public array $selectedRoles = [];

    /** @var array<int, int> */
    public array $selectedPermissions = [];

    public string $permissionSearch = '';

    public function mount(?int $user = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-permissions');

        if ($user !== null) {
            $u = User::with(['roles:id', 'permissions:id'])->findOrFail($user);
            $this->userId = $u->id;
            $this->first_name = (string) $u->first_name;
            $this->last_name = (string) $u->last_name;
            $this->email = (string) $u->email;
            $this->username = (string) $u->username;
            $this->employee_id = (string) ($u->employee_id ?? '');
            $this->is_admin = (bool) $u->is_admin;
            $this->is_client = (bool) $u->is_client;
            $this->role_id = $u->role_id ? (int) $u->role_id : null;
            $this->selectedRoles = $u->roles->pluck('id')->map(fn ($id) => (int) $id)->toArray();
            $this->selectedPermissions = $u->permissions->pluck('id')->map(fn ($id) => (int) $id)->toArray();
        }
    }

    public function updatedRoleId(?int $value): void
    {
        if ($value && ! in_array($value, $this->selectedRoles, true)) {
            $this->selectedRoles[] = $value;
        }
    }

    /** @return array<int, int> */
    public function getInheritedPermissionIdsProperty(): array
    {
        if ($this->selectedRoles === []) {
            return [];
        }

        $roleModel = config('permission.models.role');

        return $roleModel::query()
            ->with('permissions:id')
            ->whereIn('id', $this->selectedRoles)
            ->get()
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function render()
    {
        $roleModel = config('permission.models.role');
        $permissionModel = config('permission.models.permission');

        $roles = $roleModel::query()->orderBy('name')->get(['id', 'name']);
        $permissions = $permissionModel::query()
            ->when($this->permissionSearch, fn ($q) => $q->where('name', 'like', "%{$this->permissionSearch}%"))
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('flux-admin.pages.users.form', [
            'roles' => $roles,
            'permissions' => $permissions,
            'inheritedPermissionIds' => $this->inheritedPermissionIds,
        ]);
    }

    public function save()
    {
        $roleModel = config('permission.models.role');
        $permissionModel = config('permission.models.permission');

        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($this->userId)],
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($this->userId)],
            'employee_id' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'selectedRoles' => ['array'],
            'selectedRoles.*' => ['integer', 'exists:roles,id'],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ];

        $this->validate($rules);

        if ($this->role_id && ! in_array($this->role_id, $this->selectedRoles, true)) {
            $this->selectedRoles[] = $this->role_id;
        }

        $user = $this->userId ? User::findOrFail($this->userId) : new User;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->name = trim($this->first_name.' '.$this->last_name);
        $user->email = $this->email;
        $user->username = $this->username;
        $user->employee_id = $this->employee_id ?: null;
        $user->is_admin = $this->is_admin;
        $user->is_client = $this->is_client;
        $user->role_id = $this->role_id;

        if (! $this->userId) {
            $user->avatar_type = 'gravatar';
            $user->opt_in = 0;
        }

        if ($this->password !== '') {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        $roleNames = $roleModel::query()
            ->whereIn('id', $this->selectedRoles)
            ->pluck('name')
            ->all();
        $permissionNames = $permissionModel::query()
            ->whereIn('id', $this->selectedPermissions)
            ->pluck('name')
            ->all();

        $user->syncRoles($roleNames);
        $user->syncPermissions($permissionNames);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        session()->flash('flux-admin.flash', $this->userId ? 'User updated.' : 'User created.');

        return redirect()->route('flux-admin.users.edit', ['user' => $user->id]);
    }
}
