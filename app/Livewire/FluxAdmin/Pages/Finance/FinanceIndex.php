<?php

namespace App\Livewire\FluxAdmin\Pages\Finance;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\FinanceApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Finance Applications — Flux Admin')]
class FinanceIndex extends Component
{
    use WithDataTable, WithPagination;

    public string $contractType = '';

    public string $status = '';

    public function updatingContractType(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = FinanceApplication::with('customer', 'user')
            ->withCount('items');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', function ($cq) {
                        $cq->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->contractType !== '') {
            $query->where($this->contractType, true);
        }

        if ($this->status === 'active') {
            $query->where(function ($q) {
                $q->where('is_cancelled', false)->orWhereNull('is_cancelled');
            });
        } elseif ($this->status === 'cancelled') {
            $query->where('is_cancelled', true);
        }

        $applications = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.finance.index', [
            'applications' => $applications,
        ]);
    }
}
