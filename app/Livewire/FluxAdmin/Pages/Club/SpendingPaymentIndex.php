<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\ClubMemberSpendingPayment;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club spending payments — Flux Admin')]
class SpendingPaymentIndex extends Component
{
    use WithAuthorization, WithCrudForm, WithDataTable, WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-club');
        $this->sortField = 'date';
    }

    protected function formModel(): string { return ClubMemberSpendingPayment::class; }

    protected function formRules(): array
    {
        return [
            'formData.date'           => ['required', 'date'],
            'formData.club_member_id' => ['required', 'integer'],
            'formData.spending_id'    => ['nullable', 'integer'],
            'formData.pos_invoice'    => ['nullable', 'string', 'max:255'],
            'formData.received_total' => ['required', 'numeric'],
            'formData.branch_id'      => ['nullable', 'integer'],
            'formData.note'           => ['nullable', 'string'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = ['date' => now()->toDateString(), 'user_id' => backpack_user()->id];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $record = ClubMemberSpendingPayment::findOrFail($id);
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
        ClubMemberSpendingPayment::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $rows = ClubMemberSpendingPayment::query()
            ->with(['clubMember.customer:id,first_name,last_name'])
            ->when($this->search, fn ($q, $v) => $q->where('pos_invoice', 'like', "%{$v}%")->orWhereHas('clubMember.customer', fn ($q) => $q->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%")))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.spending-payments-index', ['rows' => $rows]);
    }
}
