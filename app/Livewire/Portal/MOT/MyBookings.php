<?php

namespace App\Livewire\Portal\MOT;

use App\Models\MOTBooking;
use App\Support\BookingSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class MyBookings extends Component
{
    public function cancelMotBooking(int $bookingId): void
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            abort(403);
        }

        $email = strtolower(trim((string) ($customer->email ?? '')));
        $booking = MOTBooking::query()
            ->with('branch:id,name')
            ->whereKey($bookingId)
            ->whereRaw('LOWER(customer_email) = ?', [$email])
            ->where('status', '!=', MOTBooking::STATUS_CANCELLED)
            ->first();

        if (! $booking) {
            session()->flash('success', 'Booking not found or already cancelled.');

            return;
        }

        $booking->markAsCancelled("This canceled by website frontend user.\nWe are going to edit it later we made the process ahead.");

        $payload = [
            'subject' => 'MOT booking cancelled - NGN Motors',
            'heading' => 'MOT booking cancelled',
            'greetingName' => trim((string) ($booking->customer_name ?: 'Customer')),
            'introLines' => [
                'Your MOT booking has been cancelled from the portal.',
                'The reserved slot has been released.',
            ],
            'details' => [
                'Branch' => $booking->branch?->name ?? 'Catford',
                'Registration' => $booking->vehicle_registration,
                'Preferred Date' => Carbon::parse($booking->date_of_appointment)->format('Y-m-d'),
                'Preferred Time' => BookingSchedule::formatTimeAmPm(Carbon::parse($booking->start ?? $booking->date_of_appointment)->format('H:i')),
                'Status' => 'Cancelled',
                'Notes' => $booking->notes ?: 'N/A',
            ],
            'outroLines' => [
                'If you need to rebook, please submit a new MOT request.',
            ],
        ];

        $inbox = config('mail.contact_inbox', 'customerservice@neguinhomotors.co.uk');

        try {
            if ($booking->customer_email) {
                Mail::send('emails.templates.universal', $payload, function ($message) use ($booking): void {
                    $message->to($booking->customer_email, $booking->customer_name)->subject('MOT booking cancelled - NGN Motors');
                });
            }

            Mail::send('emails.templates.universal', $payload, function ($message) use ($inbox): void {
                $message->to($inbox)->subject('MOT booking cancelled - NGN Motors');
            });
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'MOT booking cancelled.');
    }

    public function render()
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            abort(403);
        }

        $bookings = MOTBooking::query()
            ->with('branch:id,name')
            ->whereRaw('LOWER(customer_email) = ?', [strtolower((string) $customer->email)])
            ->orderBy('date_of_appointment', 'desc')
            ->get();

        return view('livewire.portal.mot.my-bookings', compact('bookings'))
            ->layout('components.layouts.portal');
    }
}
