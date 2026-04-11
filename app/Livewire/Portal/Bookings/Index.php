<?php

namespace App\Livewire\Portal\Bookings;

use App\Models\CustomerAppointments;
use App\Models\MOTBooking;
use App\Models\ServiceBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'all';

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $email = strtolower(trim((string) ($customerAuth->email ?? '')));

        $motBookings = MOTBooking::query()
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->latest('date_of_appointment')
            ->get();

        $repairsAppointments = CustomerAppointments::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->latest('appointment_date')
            ->get();

        $repairsEnquiries = ServiceBooking::query()
            ->forPortalCustomer($customerAuth)
            ->where(function ($query): void {
                $query
                    ->whereIn('enquiry_type', ['service', 'repairs'])
                    ->orWhere('service_type', 'like', '%repair%')
                    ->orWhere('subject', 'like', '%repair%')
                    ->orWhere('description', 'like', '%repair%');
            })
            ->with('conversation')
            ->latest('id')
            ->get();

        $motItems = collect($motBookings->all())->map(fn ($m) => (object) [
            'id' => 'mot-'.$m->id,
            'type' => 'MOT',
            'date' => $m->date_of_appointment,
            'status' => $m->status ?? 'Pending',
            'label' => 'MOT appointment',
            'source' => $m,
        ]);

        $repairsAppointmentItems = collect($repairsAppointments->all())->map(function ($appointment) {
            return (object) [
                'id' => 'repairs-appointment-'.$appointment->id,
                'type' => 'REPAIRS_APPOINTMENT',
                'date' => $appointment->appointment_date,
                'status' => $appointment->is_resolved ? 'completed' : 'pending',
                'label' => 'Repairs appointment',
                'source' => $appointment,
            ];
        });

        $repairsEnquiryItems = collect($repairsEnquiries->all())->map(function ($enquiry) {
            return (object) [
                'id' => 'repairs-enquiry-'.$enquiry->id,
                'type' => 'REPAIRS_ENQUIRY',
                'date' => $enquiry->created_at,
                'status' => $enquiry->status ?: 'Pending',
                'label' => 'Repair enquiry',
                'source' => $enquiry,
            ];
        });

        $allBookings = $motItems
            ->merge($repairsAppointmentItems)
            ->merge($repairsEnquiryItems)
            ->sortByDesc('date')
            ->values();

        $bookings = match ($this->activeTab) {
            'mot' => $allBookings->where('type', 'MOT'),
            'repairs_appointments' => $allBookings->where('type', 'REPAIRS_APPOINTMENT'),
            'repairs_enquiries' => $allBookings->where('type', 'REPAIRS_ENQUIRY'),
            default => $allBookings,
        };

        return view('livewire.portal.bookings.index', compact('bookings'))
            ->layout('components.layouts.portal', ['title' => 'My Bookings | My Account']);
    }
}
