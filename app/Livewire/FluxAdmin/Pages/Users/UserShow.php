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
        $this->user = $user->load(['roles:id,name', 'permissions:id,name']);
    }

    public function render()
    {
        return view('flux-admin.pages.users.show');
    }
}
