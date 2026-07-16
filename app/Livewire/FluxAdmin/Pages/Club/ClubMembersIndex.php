<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClubMember;
use App\Support\ClubMemberStaffAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Members — Flux Admin')]
class ClubMembersIndex extends Component
{
    use WithDataTable;
    use WithPagination;

    public bool $activeOnly = true;

    public string $filterYear = '';

    public string $filterPartner = '';

    public function mount(): void
    {
        if (! ClubMemberStaffAccess::canAccessPortal()) {
            throw new AuthorizationException('You do not have permission to access this section.');
        }

        $this->sortField = 'full_name';
        $this->sortDirection = 'asc';
    }

    public function updatingActiveOnly(): void
    {
        $this->resetPage();
    }

    public function updatingFilterYear(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPartner(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $members = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.members-index', [
            'members' => $members,
        ]);
    }

    protected function baseQuery(): Builder
    {
        $term = trim($this->search);
        $phoneDigits = ClubMemberStaffAccess::digitsOnly($term);

        return ClubMember::with('partner')
            ->when($term !== '', fn ($q) => $q->where(function ($q) use ($term, $phoneDigits) {
                $q->where('full_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('vrm', 'like', "%{$term}%")
                    ->orWhere('make', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhere('year', 'like', "%{$term}%");

                if (strlen($phoneDigits) >= 3) {
                    $q->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?",
                        ['%'.$phoneDigits.'%']
                    );
                }
            }))
            ->when($this->activeOnly, fn ($q) => $q->where('is_active', true))
            ->when($this->filterYear !== '', fn ($q) => $q->where('year', $this->filterYear))
            ->when($this->filterPartner !== '', fn ($q) => $q->where('is_partner', (bool) $this->filterPartner));
    }
}
