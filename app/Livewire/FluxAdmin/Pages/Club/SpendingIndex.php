<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ClubMemberSpending;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club spending — Flux Admin')]
class SpendingIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithExport, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-club');
        $this->exportable = true;
        $this->exportFilename = 'club-spending';
        $this->sortField = 'date';
    }

    protected function formModel(): string { return ClubMemberSpending::class; }

    protected function formRules(): array
    {
        return [
            'formData.date'           => ['required', 'date'],
            'formData.club_member_id' => ['required', 'integer'],
            'formData.pos_invoice'    => ['nullable', 'string', 'max:255'],
            'formData.total'          => ['required', 'numeric'],
            'formData.paid_amount'    => ['nullable', 'numeric'],
            'formData.branch_id'      => ['nullable', 'integer'],
            'formData.is_paid'        => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['date' => now()->toDateString(), 'user_id' => backpack_user()->id, 'is_paid' => false];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = ClubMemberSpending::findOrFail($id);
        $this->fillFromModel($record);
        $this->formData['date'] = $record->date ? Carbon::parse($record->date)->format('Y-m-d') : null;
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Saved.');
    }

    public function delete(int $id): void
    {
        ClubMemberSpending::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['clubMember.customer:id,first_name,last_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.spending-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return ClubMemberSpending::query()
            ->when($this->search, fn ($q, $v) => $q->where('pos_invoice', 'like', "%{$v}%")->orWhereHas('clubMember.customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->when($this->filter('is_paid') !== '', fn ($q) => $q->where('is_paid', $this->filter('is_paid') === '1'));
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['clubMember.customer']); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id', 'Date' => fn ($r) => $r->date ? Carbon::parse($r->date)->format('Y-m-d') : '',
            'POS invoice' => 'pos_invoice', 'Branch ID' => 'branch_id',
            'Member' => fn ($r) => $r->clubMember?->customer ? $r->clubMember->customer->first_name.' '.$r->clubMember->customer->last_name : '',
            'Total' => 'total', 'Paid' => 'paid_amount',
            'Settled' => fn ($r) => $r->is_paid ? 'Yes' : 'No',
        ];
    }
}
