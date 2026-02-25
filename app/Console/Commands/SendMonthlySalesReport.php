<?php

namespace App\Console\Commands;

use App\Models\MotorbikeSaleLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMonthlySalesReport extends Command
{
    protected $signature = 'report:monthly-sales';

    protected $description = 'Send monthly motorbike sales report';

    public function handle()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // Only include logs where bike was marked as sold
        $logs = MotorbikeSaleLog::where('is_sold', true)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        if ($logs->isEmpty()) {
            $this->info('No sales this month.');

            return;
        }

        $data = $logs->map(function ($log) {
            return [
                'date' => $log->created_at->format('d-m-Y H:i'),
                'reg_no' => $log->reg_no,
                'motorbike_id' => $log->motorbike_id,
                'status' => 'Sold',
                'user' => $log->username,
                'buyer_name' => $log->buyer_name,
                'buyer_phone' => $log->buyer_phone,
                'buyer_email' => $log->buyer_email,
                'buyer_address' => $log->buyer_address,
            ];
        });

        // Send email (you can use Mailable)
        Mail::send('emails.monthly_sales_report', ['data' => $data], function ($m) {
            $m->to('thiago@neguinhmotors.co.uk')
                ->subject('Monthly Motorbike Sales Report');
        });

        $this->info('Monthly sales report sent!');
    }
}
