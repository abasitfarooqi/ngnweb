<?php

namespace App\Console\Commands;

use App\Mail\InstalmentNotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class InstalmentNotification extends Command
{
    protected $signature = 'app:instalment-notification';

    protected $description = 'Command description';

    public function handle()
    {
        $array_failed = [];

        $customers = DB::table('application_items as ai')
            ->join('finance_applications as fa', 'fa.id', '=', 'ai.application_id')
            ->join('motorbikes as mb', 'mb.id', '=', 'ai.motorbike_id')
            ->join('customers as c', 'c.id', '=', 'fa.customer_id')
            ->select(
                'fa.customer_id',
                'ai.motorbike_id',
                'mb.reg_no as regno',
                DB::raw('CONCAT(c.first_name, " ", c.last_name) as fullname'),
                'c.email',
                'c.phone',
                DB::raw('COUNT(DISTINCT ai.motorbike_id) as cc')
            )
            ->where('fa.log_book_sent', false)
            ->groupBy('fa.customer_id', 'ai.motorbike_id', 'mb.reg_no', 'c.first_name', 'c.last_name', 'c.email', 'c.phone')
            ->havingRaw('cc = 1')
            ->get();

        foreach ($customers as $customer) {

            if ($this->shouldIgnoreEmail($customer->email)) {
                $this->error('Ignoring email: '.$customer->email);
                // add $customer->email to array_failed on each iteration
                $array_failed[] = $customer->email;

                continue;
            }

            try {
                $email = new InstalmentNotificationMail($customer);
                //  Mail::to($customer->email)
                //     ->cc('customerservice@neguinhomotors.co.uk')
                //      ->send($email);
                $this->info('Sent instalment notification to: '.$customer->fullname);
            } catch (RfcComplianceException $e) {
                $this->error('Failed to send email to: '.$customer->email.' | Error: '.$e->getMessage());
                $array_failed[] = $customer->email;

                continue;
            } catch (\Exception $e) {
                $this->error('Failed to send email to: '.$customer->email.' | Error: '.$e->getMessage());
                $array_failed[] = $customer->email;

                continue;
            }
        }
    }

    protected function shouldIgnoreEmail($email)
    {
        // $patterns = [
        //     '/\d+no@/',
        //    '/email\.ngm$/',
        //    '/email\.com-$/',
        //    '/\d+@/',
        //    '/-[a-zA-Z0-9]+@/'
        // ];

        // foreach ($patterns as $pattern) {
        //    if (preg_match($pattern, $email)) {
        //         return true;
        //      }
        //   }

        return false;
    }
}
