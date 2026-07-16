<?php

namespace App\Livewire\FluxAdmin\Pages\Users;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('flux-admin.layouts.app')]
#[Title('User — Flux Admin')]
class UserShow extends Component
{
    use WithAuthorization;

    public User $user;

    public function mount(User $user): void
    {
        $this->authorizeModule('see-menu-permissions');
        $this->user = $user->load(['roles.permissions:id,name', 'roles:id,name', 'permissions:id,name']);
    }

    public function deleteUser(): void
    {
        $this->authorizeModule('see-menu-permissions');

        if (backpack_user()->id === $this->user->id) {
            session()->flash('flux-admin.error', 'You cannot delete your own account.');

            return;
        }

        $this->user->delete();

        session()->flash('flux-admin.flash', 'User deleted.');

        $this->redirect(route('flux-admin.users.index'), navigate: true);
    }

    public function render()
    {
        $rolePermissions = $this->user->roles
            ->flatMap(fn ($role) => $role->permissions)
            ->unique('id')
            ->sortBy('name')
            ->values();

        return view('flux-admin.pages.users.show', [
            'rolePermissions' => $rolePermissions,
        ]);
    }
}
