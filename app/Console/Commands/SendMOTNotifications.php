<?php

namespace App\Console\Commands;

use App\Mail\MOTReminderEmail;
use App\Models\Motorbike;
use App\Models\NgnMotNotifier;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMOTNotifications extends Command
{
    protected $signature = 'mot:send-notifications';

    protected $description = 'Sends notifications to customers about their upcoming or expired MOTs.';

    public function handle()
    {
        $this->info('Checking for MOT notifications to send...');

        // Step 1: Force all MOT valid entries to NEVER SEND
        $validMotNotifiers = NgnMotNotifier::where('mot_status', 'MOT valid')->get();
        foreach ($validMotNotifiers as $notifier) {
            $notifier->mot_notify_email = false;
            $notifier->mot_email_sent_expired = true;
            $notifier->mot_is_notified_10 = true;
            $notifier->mot_email_sent_10 = true;
            $notifier->mot_is_notified_30 = true;
            $notifier->mot_email_sent_30 = true;
            $notifier->save();

            Log::info("Notifier ID {$notifier->id} ({$notifier->motorbike_reg}) marked as NEVER SEND (MOT valid).");
        }

        // Step 2: Fetch all remaining notifiers eligible for email
        $notifiers = NgnMotNotifier::where('mot_notify_email', true)->get();

        foreach ($notifiers as $notifier) {

            // Skip if MOT due date missing
            if (! $notifier->mot_due_date) {
                Log::warning("Skipping notifier ID {$notifier->id} because MOT due date is missing.");

                continue;
            }

            $shouldSendEmail = false;
            $emailStage = null;
            $today = Carbon::now()->startOfDay();

            // 1. Expired MOT: only send **once**, 14 days after expiry
            if ($notifier->mot_status === 'Expired'
                && ! $notifier->mot_email_sent_expired
                && Carbon::parse($notifier->mot_due_date)->addDays(14)->startOfDay()->lte($today)) {
                $shouldSendEmail = true;
                $emailStage = 'expired';
            }
            // 2. 10-day reminder (once lifetime)
            elseif ($notifier->mot_is_notified_10 && ! $notifier->mot_email_sent_10) {
                $shouldSendEmail = true;
                $emailStage = '10';
            }
            // 3. 30-day reminder (once lifetime)
            elseif ($notifier->mot_is_notified_30 && ! $notifier->mot_email_sent_30) {
                $shouldSendEmail = true;
                $emailStage = '30';
            }

            if (! $shouldSendEmail) {
                continue;
            }

            // Fetch motorbike details
            $motorbike = Motorbike::where('reg_no', $notifier->motorbike_reg)->first();

            $emailData = [
                'customer_name' => $notifier->customer_name,
                'customer_email' => $notifier->customer_email,
                'mot_due_date' => $notifier->mot_due_date,
                'tax_due_date' => $notifier->tax_due_date,
                'insurance_due_date' => $notifier->insurance_due_date,
                'motorbike_reg' => $notifier->motorbike_reg,
                'motorbike_model' => $motorbike ? $motorbike->model : null,
                'motorbike_make' => $motorbike ? $motorbike->make : null,
                'motorbike_year' => $motorbike ? $motorbike->year : null,
                'motorbike_id' => $motorbike ? $motorbike->id : null,
                'email_stage' => $emailStage,
            ];

            Log::info("Sending MOT notification ({$emailStage}) to: {$notifier->customer_email}", $emailData);

            // Send email
            Mail::mailer('bulk')->to($notifier->customer_email)->send(new MOTReminderEmail($emailData));
            Mail::mailer('bulk')->to('support@neguinhomotors.co.uk')->send(new MOTReminderEmail($emailData));

            // Mark email as sent (lifetime)
            switch ($emailStage) {
                case '30':
                    $notifier->mot_email_sent_30 = true;
                    break;
                case '10':
                    $notifier->mot_email_sent_10 = true;
                    break;
                case 'expired':
                    $notifier->mot_email_sent_expired = true; // ✅ ensures it never sends again
                    break;
            }

            // Update last email date
            $notifier->mot_last_email_notification_date = now();
            $notifier->save();

            Log::info("Marked {$emailStage} email as sent for notifier ID: {$notifier->id}");
        }

        $this->info('Notifications processed and logged.');
    }
}
