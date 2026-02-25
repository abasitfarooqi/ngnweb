<?php

namespace App\Console\Commands;

use App\Mail\ActiveRentingWeaklyMailer;
use App\Mail\WeeklyClubTopupReportMailer;
use App\Models\BookingInvoice;
use App\Models\ClubMemberPurchase;
use App\Models\RentingBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AdministrativeEmailsCommand extends Command
{
    protected $signature = 'app:administrative-emails-command {type : The type of report to generate (WeeklyRentingReport/WeeklyClubTopupReport)}';

    protected $description = 'Generates administrative email reports based on type';

    public function handle()
    {
        $type = $this->argument('type');

        if ($type == 'WeeklyRentingReport') {
            $this->info('Weekly Renting Report Email To Thiago');
            $this->weeklyRentingReport();
        } elseif ($type == 'WeeklyClubTopupReport') {
            $this->weeklyClubTopupReport();
        }

    }

    private function weeklyRentingReport()
    {
        $this->info('Weekly Active Rentings Email To Thiago');

        // TOTAL ACTIVE RENTINGS
        $activeBookings = RentingBooking::with([
            'customer',
            'rentingBookingItems.motorbike',
            'bookingInvoices' => function ($query) {
                $query->where('is_paid', false)
                    ->orderBy('invoice_date', 'desc');
            },
        ])
            ->where('is_posted', true)
            ->whereHas('rentingBookingItems', function ($query) {
                $query->where('is_posted', true)
                    ->whereNull('end_date');
            })
            ->get();

        // Find the latest unpaid invoice IDs for each booking
        $latestUnpaidInvoiceIds = BookingInvoice::selectRaw('MAX(id) as id')
            ->whereIn('booking_id', $activeBookings->pluck('id'))
            ->where('is_paid', false)
            ->where('invoice_date', '<=', now())
            ->groupBy('booking_id')
            ->pluck('id');

        // Calculate statistics
        $stats = [
            'active_rentals' => $activeBookings->flatMap->rentingBookingItems
                ->whereNull('end_date')
                ->count(),
            'weekly_revenue' => $activeBookings->flatMap->rentingBookingItems
                ->whereNull('end_date')
                ->sum('weekly_rent'),
            'due_payments' => $latestUnpaidInvoiceIds->count(),
            'total_deposits' => $activeBookings->sum('deposit'),
            'unpaid_invoices' => BookingInvoice::whereIn('id', $latestUnpaidInvoiceIds)
                ->sum('amount'),
        ];

        $data = [
            'active_bookings' => $activeBookings,
            'stats' => $stats,
        ];

        Mail::to('thiago@neguinhomotors.co.uk')
            ->bcc('support@neguinhomotors.co.uk')
            ->send(new ActiveRentingWeaklyMailer($data));

    }

    private function weeklyClubTopupReport()
    {
        $this->info('Weekly Club Topup Report Email To Thiago');

        // Monday-to-Monday period
        $weekStart = Carbon::now()->startOfWeek()->subWeek();
        $weekEnd   = Carbon::now()->startOfWeek();
        
        $activeBookings = ClubMemberPurchase::whereBetween('created_at', [$weekStart, $weekEnd])
            ->orderBy('total', 'desc')
            ->get();

        $data = [
            'active_bookings' => $activeBookings,
        ];

        Mail::to('thiago@neguinhomotors.co.uk')
            ->bcc('support@neguinhomotors.co.uk')
            ->send(new WeeklyClubTopupReportMailer($data));
    }

}
