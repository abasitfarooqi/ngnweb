<?php

namespace App\Console\Commands;

use App\Mail\PcnJobEmail;
use App\Mail\PcnJobEmailFail;
use App\Models\Customer;
use App\Models\PcnEmailJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PcnEmailJobCommand extends Command
{
    /*
        - Template 1 (T1)
            > First Time only. Due to Que database is empty, suggests to initiate first time email
              which run once only. (Data will be collected of 3 months criteria not paid, and case
              not closed).
              (Recipients: NGN, Customer)
        - Template 2 (T2)
            > If Customer not paid in 48hrs (Created at date will be used).
              (Recipients: NGN, Customer)
        - Template 3 (T3)
            > if Customer not paid, and NGN not appealed. Criteria: contravention date + 10 days.
              (Recipients: NGN)
        - Template 4 (T4)
            > if Customer not paid, and NGN not appealed. Criteria: contravention date + 13 days.
              (Recipients: NGN)
        - Template 5 (T5)
            > Alert: Appealed date + 12 days.
              (Recipients: NGN)
        - Tempalte 6 (T6)
            >  Alert: Appealed date + 14 days.
               (Recipients: NGN)
        - Template 7 (T7)
            >  If Customer Paid it overrides all, T1….T6. and remove PCN from alert schedule.
               (Recipients: NGN)
    */

    protected $signature = 'app:pcn-email-job';

    protected $description = 'PCN Email Job';

    private $t_one = false;

    public function handle()
    {
        $pcnemailjobs_count = PcnEmailJob::count();

        if ($pcnemailjobs_count === 0) {

            $t_one = true;

            $pcn_cases = DB::table('pcn_cases as pc')
                ->where('pc.isClosed', false)
                ->whereBetween('pc.created_at', [Carbon::now()->subMonths(2), Carbon::now()])
                // ->whereBetween('pc.created_at', [Carbon::now()->subHours(380), Carbon::now()])
                // ->whereBetween('pc.created_at', [Carbon::now()->subDays(20), Carbon::now()])
                ->get();

            // Insert into pcn_email_jobs
            foreach ($pcn_cases as $pcn_case) {
                PcnEmailJob::create([
                    'customer_id' => $pcn_case->customer_id,
                    'motorbike_id' => $pcn_case->motorbike_id,
                    'case_id' => $pcn_case->id,
                    'is_sent' => false,
                    'force_stop' => false,
                    'user_id' => $pcn_case->user_id,
                    'template_code' => 'T1',
                ]);
            }
        } else {
            $t_one = false;
        }

        if ($t_one === true) {
            $pcn_email_jobs = DB::table('pcn_email_jobs as pej')
                ->where('pej.template_code', 'T1')
                ->where('pej.is_sent', false)
                ->where('pej.force_stop', false)
                ->get();

            $ignore_emails_list = [];

            foreach ($pcn_email_jobs as $pcn_email_job) {
                Log::info('Email Sent for T1');

                $pcn_email_job->pcn_number = $pcn_number = DB::table('pcn_cases')->where('id', $pcn_email_job->case_id)->value('pcn_number');

                $pcn_email_job->reg_no = DB::table('motorbikes')->where('id', $pcn_email_job->motorbike_id)->value('reg_no');

                $pcn_email_job->full_name = Customer::where('id', $pcn_email_job->customer_id)->value('first_name').' '.Customer::where('id', $pcn_email_job->customer_id)->value('last_name');

                $pcn_customer_email = Customer::where('id', $pcn_email_job->customer_id)->value('email');

                if ($this->shouldIgnoreEmail($pcn_customer_email)) {
                    $ignore_emails_list[] = [
                        'email' => $pcn_customer_email,
                        'pcn_number' => $pcn_number,
                        'reg_no' => $pcn_email_job->reg_no,
                        'full_name' => $pcn_email_job->full_name,
                    ];
                    PcnEmailJob::where('id', $pcn_email_job->id)->update(['is_sent' => false]);
                    PcnEmailJob::where('id', $pcn_email_job->id)->update(['force_stop' => true]);

                    continue;
                } else {
                    // update pcn_email_jobs is_sent = true
                    PcnEmailJob::where('id', $pcn_email_job->id)->update(['is_sent' => true]);
                    PcnEmailJob::where('id', $pcn_email_job->id)->update(['force_stop' => true]);
                }

                // Mail::to($pcn_customer_email)
                //     ->send(new PcnJobEmail($pcn_email_job, 't1'));
            }

            if (count($ignore_emails_list) > 0) {
                Mail::to('customerservice@neguinhomotors.co.uk')->cc('admin@neguinhomotors.co.uk')->send(new PcnJobEmailFail($ignore_emails_list, 't1'));
            }
        } elseif ($t_one == false) {

            // T2 only works on 00:00 each day.
            // $pcn_cases = DB::table('pcn_cases as pc')
            //     ->select('pc.id as caseid', 'pc.*')
            //     ->join('customers as c', 'c.id', '=', 'pc.customer_id')
            //     ->where('pc.isClosed', false)
            //     ->where(function ($query) {
            //         $query->whereBetween('pc.created_at', [
            //             Carbon::now()->subHours(57),
            //             Carbon::now()->subHours(38),
            //         ]);
            //     })
            //     ->whereNotIn('pc.id', function ($query) {
            //         $query->select('case_id')
            //             ->from('pcn_email_jobs as pej')
            //             ->where('pej.template_code', 'T2');
            //     })
            //     ->orderBy('pc.date_of_contravention', 'desc')
            //     ->get();
        
            $pcn_cases = DB::table('pcn_cases as pc')
            ->join('customers as c', 'c.id', '=', 'pc.customer_id')
            ->leftJoin('pcn_case_updates as pcu', function($join) {
                $join->on('pcu.case_id', '=', 'pc.id')
                    ->whereRaw('pcu.update_date = (SELECT MAX(update_date) FROM pcn_case_updates WHERE case_id = pc.id)');
            })
            ->select('pc.id as caseid', 'pc.*', 'pcu.is_appealed', 'pcu.is_paid_by_owner', 'pcu.is_paid_by_keeper', 'pcu.is_transferred', 'pcu.is_cancled')
            ->where('pc.isClosed', false)
            ->whereBetween('pc.created_at', [Carbon::now()->subHours(57), Carbon::now()->subHours(38)])
            // Only send if Appealed checkbox is UNCHECKED
            ->where(function ($query) {
                $query->whereNull('pcu.is_appealed')
                    ->orWhere('pcu.is_appealed', false);
            })
            // Skip if Paid by NGN (owner)
            ->where(function ($query) {
                $query->whereNull('pcu.is_paid_by_owner')
                    ->orWhere('pcu.is_paid_by_owner', false);
            })
            // Skip if Paid by Hirer
            ->where(function ($query) {
                $query->whereNull('pcu.is_paid_by_keeper')
                    ->orWhere('pcu.is_paid_by_keeper', false);
            })
            // Skip if Transferred
            ->where(function ($query) {
                $query->whereNull('pcu.is_transferred')
                    ->orWhere('pcu.is_transferred', false);
            })
            // Skip if Cancelled
            ->where(function ($query) {
                $query->whereNull('pcu.is_cancled')
                    ->orWhere('pcu.is_cancled', false);
            })
            // Skip if already sent
            ->whereNotIn('pc.id', function ($query) {
                $query->select('case_id')
                    ->from('pcn_email_jobs as pej')
                    ->where('pej.template_code', 'T2');
            })
            ->orderBy('pc.date_of_contravention', 'desc')
            ->get();


            foreach ($pcn_cases as $pcn_case) {
                PcnEmailJob::create([
                    'customer_id' => $pcn_case->customer_id,
                    'motorbike_id' => $pcn_case->motorbike_id,
                    'case_id' => $pcn_case->caseid,
                    'is_sent' => false,
                    'force_stop' => false,
                    'user_id' => $pcn_case->user_id,
                    'template_code' => 'T2',
                ]);
            }

            // T2 Template + Email Called
            $pcn_email_jobs = DB::table('pcn_email_jobs as pej')
                ->where('pej.template_code', 'T2')
                ->where('pej.is_sent', false)
                ->where('pej.force_stop', false)
                ->get();

            $ignore_emails_list = [];

            foreach ($pcn_email_jobs as $pcn_email_job) {

                $pcn_email_job->pcn_number = $pcn_number = DB::table('pcn_cases')->where('id', $pcn_email_job->case_id)->value('pcn_number');

                $pcn_email_job->reg_no = DB::table('motorbikes')->where('id', $pcn_email_job->motorbike_id)->value('reg_no');

                $pcn_email_job->full_name = Customer::where('id', $pcn_email_job->customer_id)->value('first_name').' '.Customer::where('id', $pcn_email_job->customer_id)->value('last_name');

                $pcn_customer_email = Customer::where('id', $pcn_email_job->customer_id)->value('email');

                
                // --- Fetch and attach date_of_letter_issued
                $pcn_email_job->date_of_letter_issued = DB::table('pcn_cases')
                ->where('id', $pcn_email_job->case_id)
                ->value('date_of_letter_issued');

                if ($this->shouldIgnoreEmail($pcn_customer_email)) {
                    $ignore_emails_list[] = [
                        'email' => $pcn_customer_email,
                        'pcn_number' => $pcn_number,
                        'reg_no' => $pcn_email_job->reg_no,
                        'full_name' => $pcn_email_job->full_name,
                    ];
                    PcnEmailJob::where('id', $pcn_email_job->id)
                        ->update([
                            'is_sent' => false,
                            'force_stop' => true,
                        ]);

                    continue;
                } else {
                    // update pcn_email_jobs is_sent = true
                    PcnEmailJob::where('id', $pcn_email_job->id)
                        ->update([
                            'is_sent' => true,
                            'force_stop' => true,
                        ]);
                }

                Mail::to($pcn_customer_email)
                    ->cc('customerservice@neguinhomotors.co.uk')
                    ->send(new PcnJobEmail($pcn_email_job, 't2'));
            }

            if (count($ignore_emails_list) > 0) {
                Mail::to('customerservice@neguinhomotors.co.uk')->cc('admin@neguinhomotors.co.uk')->send(new PcnJobEmailFail($ignore_emails_list, 't2'));
            }

            // T3 Applies
            $firstQuery = DB::table('pcn_cases as pc')
                ->leftJoin('pcn_case_updates as pcu', 'pcu.case_id', '=', 'pc.id')
                ->select('pc.id', 'pc.pcn_number', 'pc.date_of_contravention', 'pc.motorbike_id', 'pc.customer_id', 'pc.isClosed', 'pc.full_amount', 'pc.reduced_amount', 'pc.user_id')
                ->where('pc.isClosed', false)
                ->where('pc.date_of_contravention', '>', Carbon::now()->subDays(9));

            $secondQuery = DB::table('pcn_cases as pc')
                ->join('pcn_case_updates as pcu', 'pcu.case_id', '=', 'pc.id')
                ->select('pc.id', 'pc.pcn_number', 'pc.date_of_contravention', 'pc.motorbike_id', 'pc.customer_id', 'pc.isClosed', 'pc.full_amount', 'pc.reduced_amount', 'pc.user_id')
                ->where('pc.isClosed', false)
                ->where('pcu.is_appealed', false)
                ->where('pc.date_of_contravention', '>', Carbon::now()->subDays(9));

            $pcn_cases = $firstQuery->union($secondQuery)->get();

            foreach ($pcn_cases as $pcn_case) {
                $pcnEmailJob = PcnEmailJob::firstOrCreate(
                    ['case_id' => $pcn_case->id, 'template_code' => 'T3'],
                    [
                        'customer_id' => $pcn_case->customer_id,
                        'motorbike_id' => $pcn_case->motorbike_id,
                        'is_sent' => false,
                        'force_stop' => false,
                        'user_id' => $pcn_case->user_id,
                        'template_code' => 'T3',
                    ]
                );
            }

            // T3 Template //
            $pcn_email_jobs = DB::table('pcn_email_jobs as pej')
                ->where('pej.template_code', 'T3')
                ->where('pej.is_sent', false)
                ->where('pej.force_stop', false)
                ->get();

            $ignore_emails_list = [];

            foreach ($pcn_email_jobs as $pcn_email_job) {

                $pcn_email_job = (object) $pcn_email_job;

                $pcn_email_job->pcn_number = DB::table('pcn_cases')->where('id', $pcn_email_job->case_id)->value('pcn_number');
                $pcn_email_job->reg_no = DB::table('motorbikes')->where('id', $pcn_email_job->motorbike_id)->value('reg_no');
                $pcn_email_job->full_name = Customer::where('id', $pcn_email_job->customer_id)->value('first_name').' '.Customer::where('id', $pcn_email_job->customer_id)->value('last_name');
                $pcn_email_job->pcn_customer_email = Customer::where('id', $pcn_email_job->customer_id)->value('email');

                Log::info(__FILE__.' at line '.__LINE__.'Updated PCN Email Job: ', (array) $pcn_email_job);

                $updated_pcn_email_jobs[] = $pcn_email_job;
            }

            if (isset($updated_pcn_email_jobs) && count($updated_pcn_email_jobs) > 0) {

                //    Mail::to('customerservice@neguinhomotors.co.uk')
                // ->cc('customerservice@neguinhomotors.co.uk')
                //        ->send(new PcnJobEmail($updated_pcn_email_jobs, 't3'));

                foreach ($pcn_email_jobs as $pcn_email_job) {

                    PcnEmailJob::where('id', $pcn_email_job->id)
                        ->update([
                            'is_sent' => true,
                            'force_stop' => true,
                        ]);
                }
            }
        }

        // T4 Applies - Start
        $firstQuery = DB::table('pcn_cases as pc')
            ->leftJoin('pcn_case_updates as pcu', 'pcu.case_id', '=', 'pc.id')
            ->select('pc.id', 'pc.pcn_number', 'pc.date_of_contravention', 'pc.motorbike_id', 'pc.customer_id', 'pc.isClosed', 'pc.full_amount', 'pc.reduced_amount', 'pc.user_id')
            ->where('pc.isClosed', false)
            ->where('pc.date_of_contravention', '>', Carbon::now()->subDays(7));

        $secondQuery = DB::table('pcn_cases as pc')
            ->join('pcn_case_updates as pcu', 'pcu.case_id', '=', 'pc.id')
            ->select('pc.id', 'pc.pcn_number', 'pc.date_of_contravention', 'pc.motorbike_id', 'pc.customer_id', 'pc.isClosed', 'pc.full_amount', 'pc.reduced_amount', 'pc.user_id')
            ->where('pc.isClosed', false)
            ->where('pcu.is_appealed', false)
            ->where('pc.date_of_contravention', '>', Carbon::now()->subDays(7));

        $pcn_cases = $firstQuery->union($secondQuery)->get();

        foreach ($pcn_cases as $pcn_case) {
            $pcnEmailJob = PcnEmailJob::firstOrCreate(
                ['case_id' => $pcn_case->id, 'template_code' => 'T4'],
                [
                    'customer_id' => $pcn_case->customer_id,
                    'motorbike_id' => $pcn_case->motorbike_id,
                    'is_sent' => false,
                    'force_stop' => false,
                    'user_id' => $pcn_case->user_id,
                    'template_code' => 'T4',
                ]
            );
        }

        // T4 Template //
        $pcn_email_jobs = DB::table('pcn_email_jobs as pej')
            ->where('pej.template_code', 'T4')
            ->where('pej.is_sent', false)
            ->where('pej.force_stop', false)
            ->get();

        $ignore_emails_list = [];

        foreach ($pcn_email_jobs as $pcn_email_job) {

            $pcn_email_job = (object) $pcn_email_job;

            $pcn_email_job->pcn_number = DB::table('pcn_cases')->where('id', $pcn_email_job->case_id)->value('pcn_number');
            $pcn_email_job->reg_no = DB::table('motorbikes')->where('id', $pcn_email_job->motorbike_id)->value('reg_no');
            $pcn_email_job->full_name = Customer::where('id', $pcn_email_job->customer_id)->value('first_name').' '.Customer::where('id', $pcn_email_job->customer_id)->value('last_name');
            $pcn_email_job->pcn_customer_email = Customer::where('id', $pcn_email_job->customer_id)->value('email');

            Log::info('Updated PCN Email Job: ', (array) $pcn_email_job);

            $updated_pcn_email_jobs[] = $pcn_email_job;
        }

        if (isset($updated_pcn_email_jobs) && count($updated_pcn_email_jobs) > 0) {

            //     Mail::to('customerservice@neguinhomotors.co.uk')
            //         ->send(new PcnJobEmail($updated_pcn_email_jobs, 't4'));

            foreach ($pcn_email_jobs as $pcn_email_job) {
                PcnEmailJob::where('id', $pcn_email_job->id)
                    ->update([
                        'is_sent' => true,
                        'force_stop' => true,
                    ]);
            }
        }

        // T5 Applies - Start
        Log::info('T5 Applies');
        $pcn_cases = DB::table('pcn_case_updates as pcnu')
            ->select(
                'pcnu.case_id',
                'pc.customer_id',
                'pc.user_id',
                'pc.motorbike_id',
                'pc.pcn_number'
            )
            ->join(DB::raw('(SELECT case_id, MIN(update_date) AS first_appeal_date
                         FROM pcn_case_updates
                         WHERE is_appealed = true AND is_cancled = false
                         GROUP BY case_id) as first_appeals'), function ($join) {
                $join->on('pcnu.case_id', '=', 'first_appeals.case_id')
                    ->on('pcnu.update_date', '=', 'first_appeals.first_appeal_date');
            })
            ->join('pcn_cases as pc', 'pc.id', '=', 'pcnu.case_id')
            ->where('pc.isClosed', 0)
            ->where('pcnu.is_appealed', 1)
            ->whereBetween('pcnu.update_date', [now()->subDays(14), now()->subDays(12)])
            ->get();

        Log::info(__FILE__.' at line '.__LINE__.$pcn_cases->count());

        foreach ($pcn_cases as $pcn_case) {
            $pcnEmailJob = PcnEmailJob::firstOrCreate(
                ['case_id' => $pcn_case->case_id, 'template_code' => 'T5'],
                [
                    'customer_id' => $pcn_case->customer_id,
                    'motorbike_id' => $pcn_case->motorbike_id,
                    'is_sent' => false,
                    'force_stop' => false,
                    'user_id' => 93,
                    'template_code' => 'T5',
                ]
            );
        }

        // T5 Template //
        $pcn_email_jobs = DB::table('pcn_email_jobs as pej')
            ->where('pej.template_code', 'T5')
            ->where('pej.is_sent', false)
            ->where('pej.force_stop', false)
            ->get();

        $ignore_emails_list = [];

        foreach ($pcn_email_jobs as $pcn_email_job) {

            $pcn_email_job = (object) $pcn_email_job;

            $pcn_email_job->pcn_number = DB::table('pcn_cases')->where('id', $pcn_email_job->case_id)->value('pcn_number');
            $pcn_email_job->reg_no = DB::table('motorbikes')->where('id', $pcn_email_job->motorbike_id)->value('reg_no');
            $pcn_email_job->full_name = Customer::where('id', $pcn_email_job->customer_id)->value('first_name').' '.Customer::where('id', $pcn_email_job->customer_id)->value('last_name');
            $pcn_email_job->pcn_customer_email = Customer::where('id', $pcn_email_job->customer_id)->value('email');

            Log::info('Updated PCN Email Job: ', (array) $pcn_email_job);

            $updated_pcn_email_jobs[] = $pcn_email_job;
        }

        if (isset($updated_pcn_email_jobs) && count($updated_pcn_email_jobs) > 0) {

            //      Mail::to('customerservice@neguinhomotors.co.uk')
            //          ->send(new PcnJobEmail($updated_pcn_email_jobs, 't5'));
        }

        //
        //
        // T6 Applies - Start

        $pcn_cases = DB::table('pcn_case_updates as pcnu')
            ->select(
                'pcnu.case_id',
                'pc.customer_id',
                'pc.user_id',
                'pc.motorbike_id',
                'pc.pcn_number'
            )
            ->join(DB::raw('(SELECT case_id, MIN(update_date) AS first_appeal_date
                         FROM pcn_case_updates
                         WHERE is_appealed = true AND is_cancled = false
                         GROUP BY case_id) as first_appeals'), function ($join) {
                $join->on('pcnu.case_id', '=', 'first_appeals.case_id')
                    ->on('pcnu.update_date', '=', 'first_appeals.first_appeal_date');
            })
            ->join('pcn_cases as pc', 'pc.id', '=', 'pcnu.case_id')
            ->where('pc.isClosed', 0)
            ->where('pcnu.is_appealed', 1)
            ->whereBetween('pcnu.update_date', [now()->subDays(16), now()->subDays(14)])
            ->get();

        Log::info('Count of pcn_cases: '.$pcn_cases->count().' in '.__FILE__.' at line '.__LINE__);

        Log::info($pcn_cases);

        foreach ($pcn_cases as $pcn_case) {

            $pcnEmailJob = PcnEmailJob::firstOrCreate(

                ['case_id' => $pcn_case->case_id, 'template_code' => 'T5'],
                [
                    'customer_id' => $pcn_case->customer_id,
                    'motorbike_id' => $pcn_case->motorbike_id,
                    'is_sent' => false,
                    'force_stop' => false,
                    'user_id' => 93,
                    'template_code' => 'T5',
                ]
            );
        }

        // T6 Template //
        $pcn_email_jobs = DB::table('pcn_email_jobs as pej')
            ->where('pej.template_code', 'T5')
            ->where('pej.is_sent', false)
            ->where('pej.force_stop', false)
            ->get();

        Log::info(gettype($pcn_email_jobs).$pcn_cases->count().' in '.__FILE__.' at line '.__LINE__);

        $ignore_emails_list = [];

        foreach ($pcn_email_jobs as $pcn_email_job) {

            $pcn_email_job = (object) $pcn_email_job;

            $pcn_email_job->pcn_number = DB::table('pcn_cases')->where('id', $pcn_email_job->case_id)->value('pcn_number');
            $pcn_email_job->reg_no = DB::table('motorbikes')->where('id', $pcn_email_job->motorbike_id)->value('reg_no');
            $pcn_email_job->full_name = Customer::where('id', $pcn_email_job->customer_id)->value('first_name').' '.Customer::where('id', $pcn_email_job->customer_id)->value('last_name');
            $pcn_email_job->pcn_customer_email = Customer::where('id', $pcn_email_job->customer_id)->value('email');

            Log::info('Updated PCN Email Job: ', (array) $pcn_email_job);

            $updated_pcn_email_jobs[] = $pcn_email_job;
        }

        if (isset($updated_pcn_email_jobs) && count($updated_pcn_email_jobs) > 0) {

            //     Mail::to('customerservice@neguinhomotors.co.uk')
            //         ->send(new PcnJobEmail($updated_pcn_email_jobs, 't6'));
        }
    }

    protected function shouldIgnoreEmail($email)
    {
        $patterns = [
            '/\d+no@/',
            '/email\.ngm$/',
            '/email\.com-$/',
            '/\d+@/',
            '/-[a-zA-Z0-9]+@/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $email)) {

                Log::info('Email Ignored: '.$email.true.' in '.__FILE__.' at line '.__LINE__);

                return true;
            }
        }

        Log::info('Email not ignore: '.$email.false.__FILE__.' at line '.__LINE__);

        return false;
    }
}
