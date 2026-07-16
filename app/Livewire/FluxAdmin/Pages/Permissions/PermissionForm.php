<?php

namespace App\Livewire\FluxAdmin\Pages\Permissions;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\PermissionRegistrar;

#[Layout('flux-admin.layouts.app')]
#[Title('Permission — Flux Admin')]
class PermissionForm extends Component
{
    use WithAuthorization;

    public ?int $permissionId = null;

    public array $form = ['name' => '', 'guard_name' => 'web'];

    public bool $allowCreate = true;

    public bool $allowUpdate = true;

    public bool $multipleGuards = false;

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-permissions');

        $this->allowCreate = (bool) config('backpack.permissionmanager.allow_permission_create', true);
        $this->allowUpdate = (bool) config('backpack.permissionmanager.allow_permission_update', true);
        $this->multipleGuards = (bool) config('backpack.permissionmanager.multiple_guards', false);

        if ($id) {
            abort_unless($this->allowUpdate, 403);

            $model = config('permission.models.permission');
            $permission = $model::findOrFail($id);
            $this->permissionId = $permission->id;
            $this->form = [
                'name' => $permission->name,
                'guard_name' => $permission->guard_name,
            ];
        } else {
            abort_unless($this->allowCreate, 403);
        }
    }

    public function save(): void
    {
        $model = config('permission.models.permission');

        $rules = [
            'form.name' => [
                'required', 'string', 'max:255',
                Rule::unique((new $model)->getTable(), 'name')->ignore($this->permissionId),
            ],
        ];

        if ($this->multipleGuards) {
            $rules['form.guard_name'] = ['required', 'string'];
        }

        $this->validate($rules);

        if (! $this->multipleGuards) {
            $this->form['guard_name'] = 'web';
        }

        $permission = $this->permissionId ? $model::findOrFail($this->permissionId) : new $model;
        $permission->fill($this->form)->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        session()->flash('flux-admin.flash', $this->permissionId ? 'Permission updated.' : 'Permission created.');

        $this->redirect(route('flux-admin.permissions.index', [
            'sort' => 'name',
            'dir' => 'asc',
        ]), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.permissions.permission-form', [
            'guardOptions' => collect(config('auth.guards', []))->keys(),
        ]);
    }
}
