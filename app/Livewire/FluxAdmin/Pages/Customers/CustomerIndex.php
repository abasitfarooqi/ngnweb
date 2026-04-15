<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\Customer;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Customers — Flux Admin')]
class CustomerIndex extends Component
{
    use WithDataTable;
    use WithPagination;

    public string $filterVerification = '';

    public string $filterClub = '';

    public function render()
    {
        $customers = Customer::query()
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->search}%"]);
                });
            })
            ->when($this->filterVerification !== '', fn ($q) => $q->where('verification_status', $this->filterVerification))
            ->when($this->filterClub !== '', fn ($q) => $q->where('is_club', $this->filterClub === '1'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.customers.index', compact('customers'));
    }

    public function updatingFilterVerification(): void
    {
        $this->resetPage();
    }

    public function updatingFilterClub(): void
    {
        $this->resetPage();
    }
}
