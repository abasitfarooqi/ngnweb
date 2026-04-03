<?php

declare(strict_types=1);

/**
 * Applies UniversalMailPayload::wrap() to simple build() mailables.
 * php scripts/apply-universal-mail-wrap.php
 */

$base = dirname(__DIR__);

$rows = <<<'CSV'
app/Mail/PaymentReminderNotification.php|Payment Reminder for MOT Booking|olders.emails.payment_reminder
app/Mail/InvoiceGenerationNotification.php|Invoice generated|olders.emails.invoice-generation
app/Mail/WeeklyClubTopupReportMailer.php|Weekly NGN Club report|olders.emails.cron-jobs.cron-job-weekly-ngn-club-report
app/Mail/PCNNotify.php|PCN notification|olders.emails.pcn-notify
app/Mail/PCNPoliceNotify.php|PCN police notification|olders.emails.pcn-notify-police
CSV;

foreach (explode("\n", trim($rows)) as $line) {
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    [$rel, $subj, $view] = explode('|', $line, 3);
    $path = $base.'/'.$rel;
    if (! is_file($path)) {
        fwrite(STDERR, "missing $path\n");
        continue;
    }
    $code = file_get_contents($path);
    $use = "use App\\Support\\UniversalMailPayload;\n";
    if (! str_contains($code, 'use App\\Support\\UniversalMailPayload;')) {
        $code = preg_replace('/(namespace [^;]+;\n)/', '$1'.$use, $code, 1) ?? $code;
    }
    $subjEsc = addslashes($subj);
    $newBuild = <<<PHP
    public function build()
    {
        return \$this->subject('{$subjEsc}')
            ->view('emails.templates.agreement-controller-universal')
            ->with(UniversalMailPayload::wrap(
                '{$view}',
                ['data' => \$this->data],
                '{$subjEsc}',
            ));
    }
PHP;
    if (! preg_match('/public function build\(\)\s*\{[\s\S]*?\n    \}/', $code, $m)) {
        fwrite(STDERR, "no build: $rel\n");
        continue;
    }
    $code = str_replace($m[0], $newBuild, $code);
    file_put_contents($path, $code);
    echo "ok $rel\n";
}
