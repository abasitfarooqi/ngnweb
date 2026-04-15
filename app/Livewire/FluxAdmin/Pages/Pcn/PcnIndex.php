<?php

namespace App\Livewire\FluxAdmin\Pages\Pcn;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\PcnCase;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('PCN Cases — Flux Admin')]
class PcnIndex extends Component
{
    use WithDataTable, WithPagination;

    public string $status = '';

    public string $isPolice = '';

    public function mount(): void
    {
        $this->sortField = 'date_of_contravention';
        $this->sortDirection = 'desc';
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingIsPolice(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PcnCase::with('customer', 'motorbike', 'user');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('pcn_number', 'like', "%{$this->search}%")
                    ->orWhereHas('customer', function ($cq) {
                        $cq->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('motorbike', function ($mq) {
                        $mq->where('reg_no', 'like', "%{$this->search}%");
                    });
            });
        }

        if ($this->status === 'open') {
            $query->where('isClosed', false);
        } elseif ($this->status === 'closed') {
            $query->where('isClosed', true);
        }

        if ($this->isPolice === 'yes') {
            $query->where('is_police', true);
        } elseif ($this->isPolice === 'no') {
            $query->where(function ($q) {
                $q->where('is_police', false)->orWhereNull('is_police');
            });
        }

        $cases = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.pcn.index', [
            'cases' => $cases,
        ]);
    }
}
