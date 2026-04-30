<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
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
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-pcn-portal');
        $this->exportable = true;
        $this->exportFilename = 'pcn-updates';
        $this->sortField = 'update_date';
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
