<?php

namespace App\Console\Commands;

use App\Mail\ContactSubmission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailpitCommand extends Command
{
    protected $signature = 'mail:test-mailpit
                            {--mailable=plain : plain, contact (universal-wrapped HTML)}
                            {--to=preview@mailpit.local : Recipient address (Mailpit accepts any)}
                            {--mailer=mailpit : Mailer name from config/mail.php}';

    protected $description = 'Send a test email to Mailpit for local HTML checks (local or development only).';

    public function handle(): int
    {
        if (! in_array(app()->environment(), ['local', 'development'], true)) {
            $this->error('Refused: use APP_ENV=local or development for this command.');

            return self::FAILURE;
        }

        $mailerName = (string) $this->option('mailer');
        $to = (string) $this->option('to');
        $kind = strtolower((string) $this->option('mailable'));

        if (! array_key_exists($mailerName, config('mail.mailers', []))) {
            $this->error("Unknown mailer [{$mailerName}]. Check config/mail.php.");

            return self::FAILURE;
        }

        try {
            if ($kind === 'contact') {
                Mail::mailer($mailerName)->to($to)->send(new ContactSubmission(
                    senderName: 'Mailpit preview',
                    senderEmail: 'preview@mailpit.local',
                    phone: '020 0000 0000',
                    topic: 'Mailpit test',
                    messageBody: 'This is a sample contact submission sent via php artisan mail:test-mailpit.',
                    branchName: 'Catford',
                ));
            } else {
                Mail::mailer($mailerName)->raw(
                    "Mailpit connectivity test\n\nTime: ".now()->toIso8601String(),
                    function ($message) use ($to): void {
                        $message->to($to)->subject('[NGN local] Mailpit plain text test');
                    }
                );
            }
        } catch (\Throwable $e) {
            $this->error('Send failed: '.$e->getMessage());
            $this->line('Is Mailpit running? SMTP usually 127.0.0.1:1025, web UI often http://127.0.0.1:8025');

            return self::FAILURE;
        }

        $this->info("Sent to {$to} using mailer [{$mailerName}] (mailable={$kind}).");
        $this->line('Open Mailpit in your browser (default http://127.0.0.1:8025) to view the message.');

        return self::SUCCESS;
    }
}
