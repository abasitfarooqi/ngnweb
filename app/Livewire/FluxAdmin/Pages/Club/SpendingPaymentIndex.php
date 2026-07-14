<?php

namespace App\Livewire\FluxAdmin\Pages\Club;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\ClubMemberSpendingPayment;
use App\Services\ClubSpendingPaymentAllocator;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Club spending payments — Flux Admin')]
class SpendingPaymentIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-club');
        $this->sortField = 'date';
        $this->exportable = true;
        $this->exportFilename = 'club-spending-payments';
    }

    public function delete(int $id, ClubSpendingPaymentAllocator $allocator): void
    {
        DB::transaction(function () use ($id, $allocator): void {
            $payment = ClubMemberSpendingPayment::findOrFail($id);
            $allocator->revert($payment);
            $payment->delete();
        });

        $this->dispatch('flux-admin:toast', type: 'success', message: 'Payment deleted and spending totals reverted.');
    }

    public function render()
    {
        $rows = ClubMemberSpendingPayment::query()
            ->with(['clubMember:id,full_name,phone,email'])
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($qq) use ($term): void {
                    $qq->where('pos_invoice', 'like', "%{$term}%")
                        ->orWhereHas('clubMember', function ($cq) use ($term): void {
                            $cq->where('full_name', 'like', "%{$term}%")
                                ->orWhere('phone', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%");
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.club.spending-payments-index', ['rows' => $rows]);
    }

    protected function exportQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return ClubMemberSpendingPayment::query()->with(['clubMember']);
    }

    protected function exportColumns(): array
    {
        return [
            'Date' => 'date',
            'Member' => fn ($r) => $r->clubMember?->full_name,
            'Phone' => fn ($r) => $r->clubMember?->phone,
            'Received' => 'received_total',
            'POS invoice' => 'pos_invoice',
            'Branch' => 'branch_id',
            'Note' => 'note',
        ];
    }
}
