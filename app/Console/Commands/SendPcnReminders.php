<?php

namespace App\Console\Commands;

use App\Mail\PcnUnpaidFollowUpReminderMail;
use App\Models\Communication;
use App\Models\PcnCase;
use App\Services\Communications\CommunicationSchema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SendPcnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-pcn-reminders {--dry-run : Show eligible cases without emailing or storing notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email staff about unpaid PCNs exactly 12 days after contravention';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = now()->subDays(12)->toDateString();
        $cases = PcnCase::with(['customer', 'motorbike', 'updates'])
            ->open()
            ->whereDate('date_of_contravention', $date)
            ->whereDoesntHave('updates', fn ($q) => $q->where('is_appealed', true))
            ->whereDoesntHave('updates', fn ($q) => $q->where('is_paid_by_keeper', true))
            ->get();

        foreach ($cases as $case) {
            $key = 'pcn.unpaid_follow_up_reminder';
            $correlation = $case->id.':'.$date;
            if ($this->option('dry-run')) {
                $this->line(sprintf(
                    'Eligible: PCN %s (case #%d), customer %s, date %s',
                    $case->pcn_number,
                    $case->id,
                    trim(($case->customer?->first_name ?? '').' '.($case->customer?->last_name ?? '')) ?: 'not linked',
                    $date,
                ));
                continue;
            }
            if (app(CommunicationSchema::class)->ready() && Communication::query()->where('communication_key', $key)->where('correlation_id', $correlation)->exists()) {
                continue;
            }

            $mail = new PcnUnpaidFollowUpReminderMail($case);
            Mail::to('catford@neguinhomotors.co.uk')->send($mail);

            if (Schema::hasTable('communications')) {
                $communication = Communication::query()->create([
                    'uuid' => (string) Str::uuid(),
                    'communication_key' => $key,
                    'recipient_email' => 'catford@neguinhomotors.co.uk',
                    'subject' => 'PCN customer payment follow-up: '.$case->pcn_number,
                    'title' => 'PCN payment reminder',
                    'preview' => 'Follow up with the customer to pay an outstanding PCN amount.',
                    'content_html' => $mail->render(),
                    'content_text' => 'Please ask the customer to follow up and pay PCN '.$case->pcn_number.'.',
                    'structured_content' => ['pcn_case_id' => $case->id, 'date_of_contravention' => $date, 'reminder_day' => 12],
                    'payload_snapshot' => ['pcn_case_id' => $case->id],
                    'policy_snapshot' => ['email_enabled' => true, 'internal_inbox_enabled' => true, 'staff_copy_enabled' => true],
                    'source_type' => self::class,
                    'source_id' => $case->id,
                    'correlation_id' => $correlation,
                    'priority' => 'important',
                    'category' => 'reminders',
                ]);
                $communication->deliveries()->create([
                    'channel' => 'email', 'status' => 'sent', 'provider' => config('mail.default'),
                    'queued_at' => now(), 'sent_at' => now(),
                ]);
                $communication->deliveries()->create([
                    'channel' => 'internal_inbox', 'status' => 'delivered',
                    'queued_at' => now(), 'sent_at' => now(), 'delivered_at' => now(),
                ]);
            }
            $this->info('Reminder sent for PCN '.$case->pcn_number);
        }

        $this->info('Processed '.$cases->count().' PCN reminder candidate(s).');
    }
}
