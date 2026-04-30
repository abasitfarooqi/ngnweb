<?php

namespace App\Livewire\FluxAdmin\Pages\Permissions;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use Illuminate\Validation\Rule;
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

    public bool $editorOpen = false;

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $form = ['name' => '', 'guard_name' => 'web'];

    public function mount(): void
    {
        $this->authorizeModule('see-menu-permissions');
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
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

    public function openCreate(): void
    {
        $this->reset('form', 'editingId');
        $this->form = ['name' => '', 'guard_name' => 'web'];
        $this->editorOpen = true;
    }

    public function openEdit(int $id): void
    {
        $model = config('permission.models.permission');
        $permission = $model::findOrFail($id);
        $this->editingId = $permission->id;
        $this->form = [
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
        ];
        $this->editorOpen = true;
    }

    public function save(): void
    {
        $model = config('permission.models.permission');

        $this->validate([
            'form.name' => [
                'required', 'string', 'max:125',
                Rule::unique((new $model)->getTable(), 'name')->ignore($this->editingId),
            ],
            'form.guard_name' => ['required', 'string'],
        ]);

        $permission = $this->editingId ? $model::findOrFail($this->editingId) : new $model;
        $permission->fill($this->form)->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->editorOpen = false;
        session()->flash('flux-admin.flash', $this->editingId ? 'Permission updated.' : 'Permission created.');
    }

    public function deletePermission(int $id): void
    {
        $model = config('permission.models.permission');
        $model::findOrFail($id)->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        session()->flash('flux-admin.flash', 'Permission deleted.');
    }
}
