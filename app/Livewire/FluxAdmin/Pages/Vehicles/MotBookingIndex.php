<?php

namespace App\Livewire\FluxAdmin\Pages\Vehicles;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Models\Branch;
use App\Models\MOTBooking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('MOT bookings — Flux Admin')]
class MotBookingIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-mot-bookings');
        $this->exportable = true;
        $this->exportFilename = 'mot-bookings';
        $this->sortField = 'start';
    }

    public function delete(int $id): void
    {
        MOTBooking::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Deleted.');
    }

    public function render()
    {
        $this->perPage = max(10, min((int) $this->perPage, 50));

        $bookings = $this->baseQuery()
            ->with('branch:id,name')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $branch = $this->catfordBranch();

        return view('flux-admin.pages.vehicles.mot-bookings-index', compact('bookings', 'branch'));
    }

    protected function baseQuery(): Builder
    {
        $search = trim($this->search);
        $compactSearch = strtoupper(preg_replace('/\s+/', '', $search) ?? '');

        return MOTBooking::query()
            ->when($this->catfordBranch(), fn ($q, Branch $branch) => $q->where('branch_id', $branch->id))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('vehicle_registration', 'like', "%{$search}%")
                ->orWhereRaw("REPLACE(UPPER(COALESCE(vehicle_registration, '')), ' ', '') LIKE ?", ["%{$compactSearch}%"])
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%")
                ->orWhere('customer_contact', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('payment_link', 'like', "%{$search}%")))
            ->when($this->filter('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($this->filter('is_paid') !== '', fn ($q) => $q->where('is_paid', $this->filter('is_paid') === '1'));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery()->with('branch');
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Title' => 'title',
            'Branch' => fn ($b) => $b->branch?->name,
            'Start' => fn ($b) => $b->start ? Carbon::parse($b->start)->format('Y-m-d H:i') : '',
            'End' => fn ($b) => $b->end ? Carbon::parse($b->end)->format('Y-m-d H:i') : '',
            'VRM' => 'vehicle_registration',
            'Customer' => 'customer_name',
            'Phone' => 'customer_contact',
            'Email' => 'customer_email',
            'Status' => 'status',
            'Payment link' => 'payment_link',
            'Paid' => fn ($b) => $b->is_paid ? 'Yes' : 'No',
        ];
    }

    private function catfordBranch(): ?Branch
    {
        return Branch::query()
            ->where('name', 'like', '%Catford%')
            ->orderBy('id')
            ->first(['id', 'name']);
    }
}
