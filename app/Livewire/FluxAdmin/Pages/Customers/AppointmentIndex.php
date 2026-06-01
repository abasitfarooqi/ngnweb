<?php

namespace App\Livewire\FluxAdmin\Pages\Customers;

use App\Livewire\FluxAdmin\Concerns\WithAuthorization;
use App\Livewire\FluxAdmin\Concerns\WithCrudForm;
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
    use WithCrudForm;
    use WithDataTable;
    use WithExport;
    use WithPagination;

    public bool $showForm = false;

    public function mount(): void
    {
        $this->authorizeModule('see-menu-commons');
        $this->sortField = 'appointment_date';
        $this->exportable = true;
        $this->exportFilename = 'customer-appointments';
    }

    protected function formModel(): string { return CustomerAppointments::class; }

    protected function formRules(): array
    {
        return [
            'formData.appointment_date'    => ['required', 'date'],
            'formData.customer_name'       => ['required', 'string', 'max:255'],
            'formData.registration_number' => ['nullable', 'string', 'max:20'],
            'formData.contact_number'      => ['nullable', 'string', 'max:40'],
            'formData.email'               => ['nullable', 'email', 'max:191'],
            'formData.booking_reason'      => ['nullable', 'string', 'max:2000'],
            'formData.is_resolved'         => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->recordId = null;
        $this->formData = [
            'appointment_date'    => now()->format('Y-m-d\TH:i'),
            'customer_name'       => '',
            'registration_number' => '',
            'contact_number'      => '',
            'email'               => '',
            'booking_reason'      => '',
            'is_resolved'         => false,
            'send_email'          => false,
        ];
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $a = CustomerAppointments::findOrFail($id);
        $this->fillFromModel($a);
        $this->formData['appointment_date'] = $a->appointment_date?->format('Y-m-d\TH:i') ?? '';
        $this->formData['send_email'] = false;
        $this->showForm = true;
    }

    public function saveForm(): void
    {
        $this->formData['is_resolved'] = (bool) ($this->formData['is_resolved'] ?? false);
        $this->formData['registration_number'] = strtoupper((string) ($this->formData['registration_number'] ?? ''));
        $sendEmail = ! empty($this->formData['send_email']) && ! empty($this->formData['email']);

        $model = $this->save();

        if ($sendEmail) {
            $this->sendEmail($model);
        }

        $this->showForm = false;
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Appointment saved.');
    }

    public function delete(int $id): void
    {
        CustomerAppointments::findOrFail($id)->delete();
        $this->dispatch('flux-admin:toast', type: 'success', message: 'Appointment deleted.');
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

    public function toggleResolved(int $id): void
    {
        $a = CustomerAppointments::findOrFail($id);
        $a->is_resolved = ! $a->is_resolved;
        $a->save();
    }
}
