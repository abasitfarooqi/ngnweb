<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ClubMember;
use App\Support\ClubMemberStaffAccess;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club Members — Flux Admin')]
class ClubIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    #[Url(as: 'active', except: true)]
    public bool $activeOnly = true;

    public string $filterYear = '';

    public string $filterPartner = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-club');
        $this->sortField = 'full_name';
        $this->sortDirection = 'asc';
        $this->exportFilename = 'club-members';
        $this->exportable = true;
    }

    protected function formModel(): string
    {
        return ClubMember::class;
    }

    protected function formRules(): array
    {
        return [
            'formData.full_name'  => ['required', 'string', 'max:200'],
            'formData.email'      => ['nullable', 'email', 'max:200'],
            'formData.phone'      => ['nullable', 'string', 'max:50'],
            'formData.vrm'        => ['nullable', 'string', 'max:20'],
            'formData.make'       => ['nullable', 'string', 'max:100'],
            'formData.model'      => ['nullable', 'string', 'max:100'],
            'formData.year'       => ['nullable', 'integer', 'min:1990', 'max:2100'],
            'formData.passkey'    => ['nullable', 'string', 'max:100'],
            'formData.is_active'  => ['boolean'],
            'formData.is_partner' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['is_active' => true, 'is_partner' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(ClubMember::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['is_active']  = (bool) ($this->formData['is_active'] ?? false);
        $this->formData['is_partner'] = (bool) ($this->formData['is_partner'] ?? false);
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Club member saved.');
    }

    public function delete(int $id): void
    {
        ClubMember::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Club member deleted.');
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

        return view('flux-admin.pages.club.index', [
            'members' => $members,
        ]);
    }

    protected function baseQuery(): Builder
    {
        $term = trim($this->search);

        return ClubMember::with('partner', 'customer')
            ->when($term !== '', fn (Builder $q) => ClubMemberStaffAccess::applyAdminListSearch($q, $term))
            ->when($term === '' && $this->activeOnly, fn (Builder $q) => $q->where('is_active', true))
            ->when($this->filterYear !== '', fn ($q) => $q->where('year', $this->filterYear))
            ->when($this->filterPartner !== '', fn ($q) => $q->where('is_partner', (bool) $this->filterPartner));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID'         => 'id',
            'Name'       => 'full_name',
            'Email'      => 'email',
            'Phone'      => 'phone',
            'Year'       => 'year',
            'Is paid'    => fn ($r) => $r->is_paid ? 'Yes' : 'No',
            'Email sent' => fn ($r) => $r->email_sent ? 'Yes' : 'No',
            'TC agreed'  => fn ($r) => $r->tc_agreed ? 'Yes' : 'No',
            'Created at' => fn ($r) => $r->created_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }
}

