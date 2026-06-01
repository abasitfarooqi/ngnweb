<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\PcnCaseUpdate;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN case updates — Flux Admin')]
class PcnUpdateIndex extends Component
{
    use WithAuthorization;
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcn-portal');
        $this->exportable = true;
        $this->exportFilename = 'pcn-updates';
        $this->sortField = 'update_date';
    }

    protected function formModel(): string { return PcnCaseUpdate::class; }

    protected function formRules(): array
    {
        return [
            'formData.case_id'          => ['required', 'integer'],
            'formData.update_date'      => ['required', 'date'],
            'formData.is_appealed'      => ['boolean'],
            'formData.is_paid_by_owner' => ['boolean'],
            'formData.is_paid_by_keeper' => ['boolean'],
            'formData.is_transferred'   => ['boolean'],
            'formData.is_cancled'       => ['boolean'],
            'formData.additional_fee'   => ['nullable', 'numeric', 'min:0'],
            'formData.note'             => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'is_appealed'      => false,
            'is_paid_by_owner' => false,
            'is_paid_by_keeper' => false,
            'is_transferred'   => false,
            'is_cancled'       => false,
            'update_date'      => now()->format('Y-m-d'),
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $this->fillFromModel(PcnCaseUpdate::findOrFail($id));
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        foreach (['is_appealed', 'is_paid_by_owner', 'is_paid_by_keeper', 'is_transferred', 'is_cancled'] as $field) {
            $this->formData[$field] = (bool) ($this->formData[$field] ?? false);
        }
        $this->save();
        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN update saved.');
    }

    public function delete(int $id): void
    {
        PcnCaseUpdate::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'PCN update deleted.');
    }

    public function render()
    {
        $rows = $this->baseQuery()
            ->with(['pcncase:id,pcn_number,motorbike_id,user_id', 'pcncase.motorbike:id,reg_no', 'pcncase.user:id,first_name'])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.pcn.pcn-updates-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return PcnCaseUpdate::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(fn ($q) => $q->whereHas('pcncase', fn ($q) => $q->where('pcn_number', 'like', "%{$term}%"))->orWhereHas('pcncase.motorbike', fn ($q) => $q->where('reg_no', 'like', "%{$term}%")));
            })
            ->when($this->filter('is_appealed') !== '', fn ($q) => $q->where('is_appealed', $this->filter('is_appealed') === '1'))
            ->when($this->filter('paid_status'), function ($q, $v): void {
                match ($v) {
                    'owner' => $q->where('is_paid_by_owner', true),
                    'keeper' => $q->where('is_paid_by_keeper', true),
                    'cancelled' => $q->where('is_cancled', true),
                    default => null,
                };
            });
    }

    protected function exportQuery(): Builder { return $this->baseQuery()->with(['pcncase.motorbike', 'pcncase.user']); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'PCN Number' => fn ($r) => $r->pcncase?->pcn_number,
            'VRN' => fn ($r) => $r->pcncase?->motorbike?->reg_no,
            'Update date' => fn ($r) => $r->update_date ? \Carbon\Carbon::parse($r->update_date)->format('Y-m-d H:i') : '',
            'Appealed' => fn ($r) => $r->is_appealed ? 'Yes' : 'No',
            'Paid by NGN' => fn ($r) => $r->is_paid_by_owner ? 'Yes' : 'No',
            'Paid by keeper' => fn ($r) => $r->is_paid_by_keeper ? 'Yes' : 'No',
            'Cancelled' => fn ($r) => $r->is_cancled ? 'Yes' : 'No',
            'Transferred' => fn ($r) => $r->is_transferred ? 'Yes' : 'No',
            'Additional fee' => 'additional_fee',
            'Note' => 'note',
        ];
    }
}
