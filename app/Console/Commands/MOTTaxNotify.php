<?php

namespace App\Console\Commands;

use App\Http\Controllers\MotorbikeController;
use App\Mail\MOTTaxNotificationMail;
use App\Models\Motorbike;
use Carbon\Carbon; // / This needs to be created
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MOTTaxNotify extends Command
{
    protected $signature = 'app:mot-tax-notify';

    protected $description = 'MOT / Tax Notification. Subscriber Service';

    public function handle()
    {
        // Corrected SQL execution
        $subscribers = DB::select('
            SELECT
                first_name,
                last_name,
                email,
                reg_no,
                phone,
                notify_email,
                notify_phone
            FROM veh_notifications
            WHERE enable = 1
        ');

        $new_veh_reg = [];
        $invalid_vrm = [];

        foreach ($subscribers as $subscriber) {
            // \Log::info('Subscriber: ' . json_encode($subscriber));

            // Corrected property access and method usage
            if (! Motorbike::isRegNoExists(strtoupper(str_replace(' ', '', $subscriber->reg_no)))) {
                $new_veh_reg[] = $subscriber->reg_no;
            }
        }

        // Insert new vehicle / reg_no
        foreach ($new_veh_reg as $reg_no) {
            if (! MotorbikeController::CheckAndInsert($reg_no)) {
                $invalid_vrm[] = $reg_no;
            }
        }

        // remove $invalid_vrm from $subscribers
        foreach ($invalid_vrm as $reg_no) {
            foreach ($subscribers as $key => $subscriber) {
                if ($subscriber->reg_no == $reg_no) {
                    unset($subscribers[$key]);
                }
            }
        }

        $notificationCount = 0;
        $newRegistrationsCount = count($new_veh_reg);
        $invalidRegistrationsCount = count($invalid_vrm);

        $used_motorbike = Motorbike::join('motorbikes_sale', 'motorbikes.id', '=', 'motorbikes_sale.motorbike_id')

            ->select('motorbikes.*', 'motorbikes_sale.condition', 'motorbikes_sale.is_sold', 'motorbikes_sale.mileage', 'motorbikes_sale.price', 'motorbikes_sale.engine as sale_engine', 'motorbikes_sale.suspension', 'motorbikes_sale.brakes', 'motorbikes_sale.belt', 'motorbikes_sale.electrical', 'motorbikes_sale.tires', 'motorbikes_sale.note')
            ->where('is_sold', '=', 0)
            ->orderBy('motorbikes_sale.price', 'asc')
            ->get();

        foreach ($subscribers as $subscriber) {

            $mot_due_date = MotorbikeController::getMotTaxExpiryDate($subscriber->reg_no, 'mot');
            $tax_due_date = MotorbikeController::getMotTaxExpiryDate($subscriber->reg_no, 'tax');

            $mot_expiry_date = $mot_due_date ? Carbon::parse($mot_due_date) : null;
            $tax_expiry_date = $tax_due_date ? Carbon::parse($tax_due_date) : null;

            $today = Carbon::now();

            $mot_diff = $mot_expiry_date ? $today->diffInDays($mot_expiry_date, false) : PHP_INT_MAX;
            $tax_diff = $tax_expiry_date ? $today->diffInDays($tax_expiry_date, false) : PHP_INT_MAX;

            $classification = '';
            $template = '';
            $time_frame = 0;

            if ($mot_diff <= 10 && $tax_diff <= 10) {
                $classification = 'MOT-TAX';
                $time_frame = 10;
            } elseif ($mot_diff <= 10) {
                $classification = 'MOT';
                $time_frame = 10;
            } elseif ($tax_diff <= 10) {
                $classification = 'TAX';
                $time_frame = 10;
            } elseif ($mot_diff <= 15 && $tax_diff <= 15) {
                $classification = 'MOT-TAX';
                $time_frame = 15;
            } elseif ($mot_diff <= 15) {
                $classification = 'MOT';
                $time_frame = 15;
            } elseif ($tax_diff <= 15) {
                $classification = 'TAX';
                $time_frame = 15;
            } elseif ($mot_diff <= 30 && $tax_diff <= 30) {
                $classification = 'MOT-TAX';
                $time_frame = 30;
            } elseif ($mot_diff <= 30) {
                $classification = 'MOT';
                $time_frame = 30;
            } elseif ($tax_diff <= 30) {
                $classification = 'TAX';
                $time_frame = 30;
            }

            if ($classification !== '') {
                $template = "emails.{$classification}-{$time_frame}days";
                $subjectLine = "{$classification} Expiry ";
                $mailable = new MOTTaxNotificationMail($subscriber, $template, $subjectLine, $used_motorbike);

                \Log::info('Sending email to: '.$subscriber->email);

                Mail::to($subscriber->email)->send($mailable);

                \Log::info('Reg No: '.$subscriber->reg_no." Classification: $classification, Time Frame: $time_frame days, Notify: true");
                $notificationCount++;
            }
        }

        \Log::info('Notification Count: '.$notificationCount);
        \Log::info('New Registrations Count: '.$newRegistrationsCount);
        \Log::info('Invalid Registrations Count: '.$invalidRegistrationsCount);
    }
}
