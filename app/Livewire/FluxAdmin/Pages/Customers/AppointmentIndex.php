<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithDataTable;
use App\Livewire\FluxAdmin\Concerns\WithExport;
use App\Mail\CustomerAppointmentNotification;
use App\Models\CustomerAppointments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('flux-admin.layouts.app')]
#[Title('Customer appointments — Flux Admin')]
class AppointmentIndex extends Component
{
    use WithAuthorization;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $editorOpen = false;

    public ?int $editingId = null;

    /** @var array<string, mixed> */
    public array $form = [
        'appointment_date' => '',
        'customer_name' => '',
        'registration_number' => '',
        'contact_number' => '',
        'email' => '',
        'booking_reason' => '',
        'is_resolved' => false,
        'send_email' => false,
    ];

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'appointment_date';
        $this->exportable = true;
        $this->exportFilename = 'customer-appointments';
    }

    public function render()
    {
        $appointments = $this->baseQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('flux-admin.pages.customers.appointments-index', [
            'appointments' => $appointments,
        ]);
    }

    protected function baseQuery(): Builder
    {
        return CustomerAppointments::query()
            ->when($this->search, function ($q): void {
                $term = $this->search;
                $q->where(function ($q) use ($term): void {
                    $q->where('customer_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('contact_number', 'like', "%{$term}%")
                        ->orWhere('registration_number', 'like', "%{$term}%");
                });
            })
            ->when($this->filter('resolved') !== '', function ($q): void {
                $q->where('is_resolved', $this->filter('resolved') === '1');
            })
            ->when($this->filter('from'), fn ($q, $v) => $q->whereDate('appointment_date', '>=', $v))
            ->when($this->filter('to'), fn ($q, $v) => $q->whereDate('appointment_date', '<=', $v));
    }

    protected function exportQuery(): Builder
    {
        return $this->baseQuery();
    }

    protected function exportColumns(): array
    {
        return [
            'ID' => 'id',
            'Appointment' => fn ($a) => $a->appointment_date?->format('Y-m-d H:i'),
            'Customer' => 'customer_name',
            'Registration' => 'registration_number',
            'Contact' => 'contact_number',
            'Email' => 'email',
            'Resolved' => fn ($a) => $a->is_resolved ? 'Yes' : 'No',
            'Reason' => 'booking_reason',
        ];
    }

    public function openCreate(): void
    {
        $this->reset('form', 'editingId');
        $this->form = [
            'appointment_date' => now()->format('Y-m-d\TH:i'),
            'customer_name' => '',
            'registration_number' => '',
            'contact_number' => '',
            'email' => '',
            'booking_reason' => '',
            'is_resolved' => false,
            'send_email' => false,
        ];
        $this->editorOpen = true;
    }

    public function openEdit(int $id): void
    {
        $a = CustomerAppointments::findOrFail($id);
        $this->editingId = $a->id;
        $this->form = [
            'appointment_date' => $a->appointment_date?->format('Y-m-d\TH:i') ?? '',
            'customer_name' => (string) $a->customer_name,
            'registration_number' => (string) $a->registration_number,
            'contact_number' => (string) $a->contact_number,
            'email' => (string) $a->email,
            'booking_reason' => (string) $a->booking_reason,
            'is_resolved' => (bool) $a->is_resolved,
            'send_email' => false,
        ];
        $this->editorOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'form.appointment_date' => ['required', 'date'],
            'form.customer_name' => ['required', 'string', 'max:255'],
            'form.registration_number' => ['nullable', 'string', 'max:20'],
            'form.contact_number' => ['nullable', 'string', 'max:40'],
            'form.email' => ['nullable', 'email', 'max:191'],
            'form.booking_reason' => ['nullable', 'string', 'max:2000'],
            'form.is_resolved' => ['boolean'],
            'form.send_email' => ['boolean'],
        ]);

        $a = $this->editingId
            ? CustomerAppointments::findOrFail($this->editingId)
            : new CustomerAppointments;

        $data = collect($this->form)->except('send_email')->toArray();
        $data['registration_number'] = strtoupper((string) ($data['registration_number'] ?? ''));
        $a->fill($data)->save();

        if (! empty($this->form['send_email']) && ! empty($this->form['email'])) {
            $this->sendEmail($a);
        }

        $this->editorOpen = false;
        $this->editingId = null;
        session()->flash('flux-admin.flash', $this->editingId ? 'Appointment updated.' : 'Appointment created.');
    }

    private function sendEmail(CustomerAppointments $a): void
    {
        $recipients = array_filter([$a->email, 'customerservice@neguinhomotors.co.uk']);
        $data = [
            'appointment_date' => $a->appointment_date,
            'is_resolved' => $a->is_resolved,
            'customer_name' => $a->customer_name,
            'registration_number' => $a->registration_number,
            'contact_number' => $a->contact_number,
            'email' => $a->email,
            'booking_reason' => $a->booking_reason,
        ];

        try {
            Mail::to($recipients)->send(new CustomerAppointmentNotification($data));
        } catch (\Throwable $e) {
            Log::error('Flux Admin appointment email failed: '.$e->getMessage());
        }
    }

    public function deleteAppointment(int $id): void
    {
        CustomerAppointments::findOrFail($id)->delete();
        session()->flash('flux-admin.flash', 'Appointment deleted.');
    }

    public function toggleResolved(int $id): void
    {
        $a = CustomerAppointments::findOrFail($id);
        $a->is_resolved = ! $a->is_resolved;
        $a->save();
    }
}
