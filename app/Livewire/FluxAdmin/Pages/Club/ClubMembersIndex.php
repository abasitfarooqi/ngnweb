<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClubMember;
use App\Support\ClubMemberStaffAccess;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Members — Flux Admin')]
class ClubMembersIndex extends Component
{
    use WithDataTable;
    use WithPagination;

    #[Url(history: true, except: true)]
    public bool $activeOnly = true;

    #[Url(history: true, except: '')]
    public string $filterYear = '';

    #[Url(history: true, except: '')]
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

        $invoiceHits = ClubMemberStaffAccess::invoiceHitsForMembers(
            $members->pluck('id')->all(),
            trim($this->search)
        );

        return view('flux-admin.pages.club.members-index', [
            'members' => $members,
            'invoiceHits' => $invoiceHits,
        ]);
    }

    protected function baseQuery(): Builder
    {
        $term = trim($this->search);

        return ClubMember::with('partner')
            ->when($term !== '', fn (Builder $q) => ClubMemberStaffAccess::applyAdminListSearch($q, $term))
            ->when($term === '' && $this->activeOnly, fn (Builder $q) => $q->where('is_active', true))
            ->when($this->filterYear !== '', fn ($q) => $q->where('year', $this->filterYear))
            ->when($this->filterPartner !== '', fn ($q) => $q->where('is_partner', (bool) $this->filterPartner));
    }
}
