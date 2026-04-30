<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\ClubMemberRedeem;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club redemptions — Flux Admin')]
class RedeemIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'club-redemptions';
        $this->sortField = 'date';
    }

    protected function formModel(): string { return ClubMemberRedeem::class; }

    protected function formRules(): array
    {
        return [
            'formData.club_member_id' => ['required', 'integer'],
            'formData.date' => ['required', 'date'],
            'formData.redeem_total' => ['required', 'numeric', 'min:0'],
            'formData.pos_invoice' => ['nullable', 'string', 'max:120'],
            'formData.branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'formData.note' => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'date' => now()->toDateString(),
            'redeem_total' => 0,
            'user_id' => auth()->id(),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(ClubMemberRedeem::findOrFail($id));
        if (! empty($this->formData['date'])) {
            $this->formData['date'] = \Carbon\Carbon::parse($this->formData['date'])->format('Y-m-d');
        }
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['user_id'] = $this->formData['user_id'] ?? auth()->id();
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        ClubMemberRedeem::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('flux-admin.pages.club.redeem-index', compact('rows', 'branches'));
    }

    protected function baseQuery(): Builder
    {
        return ClubMemberRedeem::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('pos_invoice', 'like', "%{$v}%")->orWhere('club_member_id', $v)))
            ->when($this->filter('branch_id'), fn ($q, $v) => $q->where('branch_id', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Date' => fn ($r) => $r->date ? \Carbon\Carbon::parse($r->date)->format('Y-m-d') : '',
            'Member ID' => 'club_member_id', 'POS invoice' => 'pos_invoice',
            'Total (£)' => 'redeem_total', 'Branch' => 'branch_id',
        ];
    }
}
