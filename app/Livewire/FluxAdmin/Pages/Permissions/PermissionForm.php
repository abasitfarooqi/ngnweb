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

    public function mount(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->authorizeModule('see-menu-permissions');

        if ($id) {
            $model = config('permission.models.permission');
            $permission = $model::findOrFail($id);
            $this->permissionId = $permission->id;
            $this->form = [
                'name'       => $permission->name,
                'guard_name' => $permission->guard_name,
            ];
        }
    }

    public function save(): void
    {
        $model = config('permission.models.permission');

        $this->validate([
            'form.name' => [
                'required', 'string', 'max:125',
                Rule::unique((new $model)->getTable(), 'name')->ignore($this->permissionId),
            ],
            'form.guard_name' => ['required', 'string'],
        ]);

        $permission = $this->permissionId ? $model::findOrFail($this->permissionId) : new $model;
        $permission->fill($this->form)->save();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->dispatch('flux-admin:toast', type: 'success', message: $this->permissionId ? 'Permission updated.' : 'Permission created.');
        $this->redirect(route('flux-admin.permissions.index'), navigate: true);
    }

    public function render()
    {
        return view('flux-admin.pages.permissions.permission-form');
    }
}
