<?php

namespace App\Livewire\FluxAdmin\Pages\Rentals;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Models\RentingBooking;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Change booking start date — Flux Admin')]
class ChangeStartDateIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithPagination;

    public ?int $editingId = null;
    public string $editingStart = '';

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
    }

    public function startEdit(int $bookingId, string $current): void
    {
        $this->editingId = $bookingId;
        $this->editingStart = $current;
        $this->resetErrorBag();
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingStart = '';
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editingId' => ['required', 'integer', 'exists:renting_bookings,id'],
            'editingStart' => ['required', 'date'],
        ]);

        RentingBooking::where('id', $this->editingId)->update(['start_date' => $this->editingStart]);
        session()->flash('status', 'Booking #' . $this->editingId . ' start date updated.');
        $this->editingId = null;
        $this->editingStart = '';
    }

    public function render()
    {
        $query = DB::table('renting_bookings as rb')
            ->join('customers as c', 'c.id', '=', 'rb.customer_id')
            ->select([
                'rb.id as booking_id',
                'rb.start_date',
                'rb.due_date',
                'rb.state',
                'c.first_name',
                'c.last_name',
                'c.email',
                'c.phone',
            ]);

        if ($this->search !== '') {
            $s = '%'.$this->search.'%';
            $query->where(function ($w) use ($s) {
                $w->where('rb.id', 'like', $s)
                    ->orWhere('c.first_name', 'like', $s)
                    ->orWhere('c.last_name', 'like', $s)
                    ->orWhere(DB::raw("CONCAT(c.first_name, ' ', c.last_name)"), 'like', $s)
                    ->orWhere('c.email', 'like', $s)
                    ->orWhere('c.phone', 'like', $s);
            });
        }

        $rows = $query->orderByDesc('rb.id')->paginate($this->perPage);

        return view('flux-admin.pages.rentals.change-start-date-index', compact('rows'));
    }
}
