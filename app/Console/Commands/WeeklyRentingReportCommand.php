<?php

namespace App\Console\Commands;

use App\Mail\WeeklyRentingReportMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class WeeklyRentingReportCommand extends Command
{
    protected $signature = 'report:weekly-renting';
    protected $description = 'Send weekly renting profit report for active and recently ended bookings';

    public function handle()
    {
        $this->info('Preparing Weekly Renting Report');

        // Determine the previous full week (Monday 00:00:00 to Sunday 23:59:59)
        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY)->setTime(0,0,0)->toDateTimeString();
        $weekEnd = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY)->setTime(23,59,59)->toDateTimeString();
        $weekStartDate = Carbon::parse($weekStart)->toDateString();
        $weekEndDate = Carbon::parse($weekEnd)->toDateString();

        $rows = DB::table('renting_bookings AS b')
            ->join('renting_booking_items AS rbi', 'b.id', '=', 'rbi.booking_id')
            ->join('customers AS c', 'b.customer_id', '=', 'c.id')
            ->join('motorbikes AS m', 'rbi.motorbike_id', '=', 'm.id')
            ->leftJoin('renting_pricings AS rp', function($join) {
                $join->on('rp.motorbike_id', '=', 'rbi.motorbike_id')->where('rp.iscurrent', 1);
            })
            ->select(
                DB::raw('b.id AS booking_id'),
                DB::raw("CONCAT(c.first_name, ' ', c.last_name) AS customer"),
                DB::raw('m.reg_no AS bike_reg'),
                DB::raw("(SELECT COALESCE(SUM(CASE 
                        WHEN is_paid = 1 
                             AND paid_date BETWEEN '{$weekStart}' AND '{$weekEnd}'
                        THEN amount 
                        ELSE 0 
                    END), 0)
                         FROM booking_invoices 
                         WHERE booking_id = b.id AND is_posted = 1) AS this_week_income"),
                DB::raw('rp.weekly_price AS weekly_rate'),
                DB::raw("(SELECT COALESCE(SUM(amount), 0) 
                         FROM booking_invoices 
                         WHERE booking_id = b.id AND is_paid = 1) AS total_rental_income"),
                DB::raw("(SELECT COALESCE(SUM(amount), 0)
                         FROM booking_invoices 
                         WHERE booking_id = b.id AND is_paid = 0 AND invoice_date < '{$weekStart}') AS previous_pending"),
                DB::raw("CASE 
                            WHEN rbi.end_date IS NULL THEN 'Active'
                            WHEN rbi.end_date BETWEEN '{$weekStartDate}' AND '{$weekEndDate}' THEN 'Ended This Week'
                            ELSE 'Ended'
                        END AS status"),
                DB::raw('b.start_date'),
                DB::raw("CASE 
                            WHEN rbi.end_date IS NULL THEN 'Still Active'
                            ELSE rbi.end_date
                         END AS end_date")
            )
            ->where('b.is_posted', 1)
            ->where('rbi.is_posted', 1)
            ->where(function($q) use ($weekStartDate, $weekEndDate) {
                // Include bookings that were active during the week:
                // 1. Currently active (end_date IS NULL)
                // 2. Ended during the week (end_date BETWEEN dates)
                // 3. Were active during the week (started before/during week AND ended after week started)
                $q->whereNull('rbi.end_date')
                  ->orWhereBetween('rbi.end_date', [$weekStartDate, $weekEndDate])
                  ->orWhere(function($subQ) use ($weekStartDate, $weekEndDate) {
                      $subQ->whereNotNull('rbi.end_date')
                           ->where('rbi.end_date', '>=', $weekStartDate)
                           ->where('b.start_date', '<=', $weekEndDate);
                  });
            })
            ->groupBy(
                'b.id', 
                'c.first_name', 
                'c.last_name', 
                'm.reg_no', 
                'rp.weekly_price', 
                'b.start_date', 
                'rbi.end_date'
            )
            ->orderByDesc('this_week_income')
            ->orderByDesc('previous_pending')
            ->orderByDesc('weekly_rate')
            ->orderBy('status')
            ->orderBy('booking_id')
            ->get();

        Mail::to('thiago@neguinhomotors.co.uk')
            ->bcc(['support@neguinhomotors.co.uk'])
            ->send(new WeeklyRentingReportMailer($rows));

        $this->info('Weekly renting report sent.');
    }
}
