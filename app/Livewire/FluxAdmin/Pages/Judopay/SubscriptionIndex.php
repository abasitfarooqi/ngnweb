<?php

namespace App\Livewire\FluxAdmin\Pages\Judopay;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\JudopaySubscription;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Judopay subscriptions — Flux Admin')]
class SubscriptionIndex extends Component
{
    use WithAuthorization, WithDataTable, WithExport, WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->exportable = true;
        $this->exportFilename = 'judopay-subscriptions';
        $this->sortField = 'date';
    }

    public function render()
    {
        $rows = $this->baseQuery()->orderBy($this->sortField, $this->sortDirection)->paginate($this->perPage);

        return view('flux-admin.pages.judopay.subscriptions-index', ['rows' => $rows]);
    }

    protected function baseQuery(): Builder
    {
        return JudopaySubscription::query()
            ->when($this->search, fn ($q, $v) => $q->where(fn ($q) => $q->where('consumer_reference', 'like', "%{$v}%")->orWhere('card_last_four', 'like', "%{$v}%")->orWhere('receipt_id', 'like', "%{$v}%")->orWhere('auth_code', 'like', "%{$v}%")))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('billing_frequency'), fn ($q, $v) => $q->where('billing_frequency', $v));
    }

    protected function exportQuery(): Builder { return $this->baseQuery(); }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Date' => fn ($r) => $r->date ? \Carbon\Carbon::parse($r->date)->format('Y-m-d') : '',
            'Consumer' => 'consumer_reference', 'Card' => 'card_last_four',
            'Billing frequency' => 'billing_frequency', 'Billing day' => 'billing_day', 'Amount' => 'amount',
            'Start' => fn ($r) => $r->start_date ? \Carbon\Carbon::parse($r->start_date)->format('Y-m-d') : '',
            'End' => fn ($r) => $r->end_date ? \Carbon\Carbon::parse($r->end_date)->format('Y-m-d') : '',
            'Status' => 'status',
        ];
    }
}
