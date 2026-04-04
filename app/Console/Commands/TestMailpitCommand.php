<?php

namespace App\Console\Commands;

use App\Mail\ContactSubmission;
use App\Mail\MailpitMigratedPreviewMail;
use App\Support\MailpitMigratedPreviewData;
use App\Support\UniversalMailPayload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use InvalidArgumentException;
use Throwable;

class TestMailpitCommand extends Command
{
    protected $signature = 'mail:test-mailpit
                            {--mailable=plain : plain, contact, migrated, all-migrated, list-migrated}
                            {--view= : Migrated template: relative to livewire.agreements.migrated.emails (e.g. pcn.t1 or ecommerce/order-confirmed)}
                            {--subject= : Optional subject override for migrated preview(s)}
                            {--stop-on-failure : With all-migrated: stop on first send/render error}
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

        if ($kind === 'list-migrated') {
            foreach (MailpitMigratedPreviewData::discoverRelativeViews() as $line) {
                $this->line($line);
            }
            $this->info('Use: php artisan mail:test-mailpit --mailable=migrated --view=RELATIVE_FROM_LIST');
            $this->info('Or:   php artisan mail:test-mailpit --mailable=all-migrated');

            return self::SUCCESS;
        }

        if ($kind === 'all-migrated') {
            return $this->sendAllMigratedToMailpit($mailerName, $to);
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
            } elseif ($kind === 'migrated') {
                $relative = $this->normaliseMigratedViewOption((string) $this->option('view'));
                if ($relative === '') {
                    $this->error('Migrated preview requires --view= (see --mailable=list-migrated).');

                    return self::FAILURE;
                }

                $subjectBase = trim((string) $this->option('subject'));
                $subject = $subjectBase !== '' ? $subjectBase : '[NGN Mailpit] '.$relative;

                $this->sendOneMigratedPreview($relative, $to, $mailerName, $subject);
            } else {
                Mail::mailer($mailerName)->raw(
                    "Mailpit connectivity test\n\nTime: ".now()->toIso8601String(),
                    function ($message) use ($to): void {
                        $message->to($to)->subject('[NGN local] Mailpit plain text test');
                    }
                );
            }
        } catch (Throwable $e) {
            $this->error('Send failed: '.$e->getMessage());
            $this->line($e->getFile().':'.$e->getLine());
            $this->line('Is Mailpit running? SMTP usually 127.0.0.1:1025, web UI often http://127.0.0.1:8025');
            $this->newLine();
            $this->warn('If this template needs extra variables, extend App\\Support\\MailpitMigratedPreviewData::shapeForRelativeView() or coreStubs().');

            return self::FAILURE;
        }

        $this->info("Sent to {$to} using mailer [{$mailerName}] (mailable={$kind}).");
        $this->line('Open Mailpit in your browser (default http://127.0.0.1:8025) to view the message.');

        return self::SUCCESS;
    }

    private function sendAllMigratedToMailpit(string $mailerName, string $to): int
    {
        $views = MailpitMigratedPreviewData::discoverRelativeViews();
        $total = count($views);
        if ($total === 0) {
            $this->warn('No migrated email views found under resources/views/livewire/agreements/migrated/emails.');

            return self::SUCCESS;
        }

        $subjectBase = trim((string) $this->option('subject'));
        $stopOnFailure = (bool) $this->option('stop-on-failure');
        $ok = 0;
        /** @var array<string, string> $failed */
        $failed = [];

        foreach ($views as $i => $relative) {
            $n = $i + 1;
            $fullView = MailpitMigratedPreviewData::fullViewName($relative);
            if (! View::exists($fullView)) {
                $failed[$relative] = 'view not registered';
                $this->warn("[{$n}/{$total}] skip (missing view): {$relative}");
                if ($stopOnFailure) {
                    return self::FAILURE;
                }

                continue;
            }

            if ($subjectBase !== '') {
                $subject = "[NGN Mailpit {$n}/{$total}] {$subjectBase} — {$relative}";
            } else {
                $subject = "[NGN Mailpit {$n}/{$total}] {$relative}";
            }

            try {
                $this->sendOneMigratedPreview($relative, $to, $mailerName, $subject);
                $ok++;
                $this->line("[{$n}/{$total}] sent: {$relative}");
            } catch (Throwable $e) {
                $failed[$relative] = $e->getMessage();
                $this->error("[{$n}/{$total}] failed: {$relative} — ".$e->getMessage());
                if ($stopOnFailure) {
                    return self::FAILURE;
                }
            }
        }

        $this->newLine();
        $this->info("Mailpit batch complete: {$ok} sent, ".count($failed)." failed, {$total} total.");
        if ($failed !== []) {
            $this->newLine();
            foreach ($failed as $path => $message) {
                $this->line("  - {$path}: {$message}");
            }
            $this->newLine();
            $this->warn('Extend App\\Support\\MailpitMigratedPreviewData for missing stub variables.');
        }
        $this->line('Open Mailpit (e.g. http://127.0.0.1:8025) and sort by date to review.');

        return count($failed) > 0 && $ok === 0 ? self::FAILURE : self::SUCCESS;
    }

    private function sendOneMigratedPreview(string $relative, string $to, string $mailerName, string $subject): void
    {
        $fullView = MailpitMigratedPreviewData::fullViewName($relative);
        if (! View::exists($fullView)) {
            throw new InvalidArgumentException("View not found: {$fullView}");
        }

        $viewData = MailpitMigratedPreviewData::viewDataFor($relative);
        $mailData = UniversalMailPayload::fromLegacyEmailView(
            $fullView,
            $viewData,
            [
                'title' => $subject,
                'greetingName' => 'there',
            ],
        );

        Mail::mailer($mailerName)->to($to)->send(new MailpitMigratedPreviewMail(
            universalMailData: $mailData,
            subjectLine: $subject,
        ));
    }

    private function normaliseMigratedViewOption(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        $prefix = MailpitMigratedPreviewData::VIEW_PREFIX;
        if (str_starts_with($raw, $prefix)) {
            $raw = substr($raw, strlen($prefix));
        }

        return str_replace(['/', '\\'], '.', $raw);
    }
}
