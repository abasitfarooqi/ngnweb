<?php

namespace App\Livewire\FluxAdmin\Pages\Users;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Users — Flux Admin')]
class UserIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-permissions');
        $this->exportable = true;
        $this->exportFilename = 'users';
    }

    public function render()
    {
        $roleModel = config('permission.models.role');
        $roles = $roleModel::query()->orderBy('name')->get(['id', 'name']);

        $users = $this->baseQuery()
            ->with(['roles:id,name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.users.index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    protected function baseQuery(): Builder
    {
        return User::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('username', 'like', "%{$term}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                });
            })
            ->when($this->filter('role'), function ($q, $roleId): void {
                $q->whereHas('roles', fn ($q) => $q->where('roles.id', $roleId));
            })
            ->when($this->filter('admin') !== '', function ($q): void {
                $q->where('is_admin', $this->filter('admin') === '1');
            });
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('roles:id,name');
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'First name' => 'first_name',
            'Last name' => 'last_name',
            'Username' => 'username',
            'Email' => 'email',
            'Admin' => fn ($u) => $u->is_admin ? 'Yes' : 'No',
            'Roles' => fn ($u) => $u->roles->pluck('name')->implode(', '),
            'Created' => fn ($u) => $u->created_at?->format('Y-m-d H:i'),
        ];
    }

    public function deleteUser(int $id): void
    {
        $this->authorizeModule('see-menu-permissions');

        if (backpack_user()->id === $id) {
            session()->flash('flux-admin.error', 'You cannot delete your own account.');

            return;
        }

        User::findOrFail($id)->delete();
        session()->flash('flux-admin.flash', 'User deleted.');
    }
}
