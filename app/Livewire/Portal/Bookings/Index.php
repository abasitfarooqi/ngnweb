<?php

namespace App\Livewire\Portal\Bookings;

use App\Models\CustomerAppointments;
use App\Models\MOTBooking;
use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'all';

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function cancelMotBooking(int $bookingId): void
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $email = strtolower(trim((string) ($customerAuth->email ?? '')));
        $booking = MOTBooking::query()
            ->with('branch:id,name')
            ->whereKey($bookingId)
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->where('status', '!=', MOTBooking::STATUS_CANCELLED)
            ->first();

        if (! $booking) {
            $this->dispatch('flux-admin:toast', type: 'error', message: 'Booking not found or already cancelled.');

            return;
        }

        $booking->markAsCancelled("This canceled by website frontend user.\nWe are going to edit it later we made the process ahead.");

        $mailPayload = [
            'subject' => 'MOT booking cancelled - NGN Motors',
            'heading' => 'MOT booking cancelled',
            'greetingName' => trim((string) ($booking->customer_name ?: 'Customer')),
            'introLines' => [
                'Your MOT booking has been cancelled from the portal.',
                'The reserved slot is now released and can be booked again.',
            ],
            'details' => [
                'Branch' => $booking->branch?->name ?? 'Catford',
                'Registration' => $booking->vehicle_registration,
                'Preferred Date' => Carbon::parse($booking->date_of_appointment)->format('Y-m-d'),
                'Preferred Time' => Carbon::parse($booking->start ?? $booking->date_of_appointment)->format('H:i'),
                'Status' => 'Cancelled',
                'Notes' => $booking->notes ?: 'N/A',
            ],
            'outroLines' => [
                'If you need to rebook, please submit a new MOT booking request.',
            ],
        ];

        $inbox = config('mail.contact_inbox', 'customerservice@neguinhomotors.co.uk');

        try {
            if ($booking->customer_email) {
                Mail::send('emails.templates.universal', $mailPayload, function ($message) use ($booking): void {
                    $message->to($booking->customer_email, $booking->customer_name)->subject('MOT booking cancelled - NGN Motors');
                });
            }

            Mail::send('emails.templates.universal', $mailPayload, function ($message) use ($inbox): void {
                $message->to($inbox)->subject('MOT booking cancelled - NGN Motors');
            });
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'MOT booking cancelled.');
        $this->dispatch('flux-admin:toast', type: 'success', message: 'MOT booking cancelled.');
    }

    public function render()
    {
        $customerAuth = Auth::guard('customer')->user();
        if (! $customerAuth) {
            abort(403);
        }

        $email = strtolower(trim((string) ($customerAuth->email ?? '')));

        $motBookings = MOTBooking::query()
            ->with('branch:id,name')
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
