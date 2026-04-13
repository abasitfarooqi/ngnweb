<?php

namespace App\Console\Commands;

use App\Mail\ActiveRentingWeaklyMailer;
use App\Mail\AnnualBusiestDaysReportMail;
use App\Mail\DueInvoiceCustomerMail;
use App\Mail\DueInvoiceSummaryMail;
use App\Mail\InstalmentNotificationMail;
use App\Mail\InvoiceGenerationNotification;
use App\Mail\MailpitMigratedPreviewMail;
use App\Mail\MOTReminderEmail;
use App\Mail\NgnClubFestiveHoursMailer;
use App\Mail\QuarterlyVehicleVisitsReportMail;
use App\Mail\RentInvoiceReminderMail;
use App\Mail\WeeklyClubTopupReportMailer;
use App\Models\ClubMember;
use App\Support\UniversalMailPayload;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Sends one stubbed message per scheduled email path (Kernel schedule) to Mailpit for HTML checks.
 * Does not run full cron logic or query live recipients.
 */
class TestScheduledEmailSamplesToMailpit extends Command
{
    protected $signature = 'mail:test-scheduled-samples
                            {--to=preview@mailpit.local : Recipient address}
                            {--mailer=mailpit : Mailer name from config/mail.php}
                            {--list : List sample keys and exit}
                            {--only= : Comma-separated keys to send (default: all)}';

    protected $description = 'Send stub samples of scheduled email types to Mailpit (local/development only).';

    public function handle(): int
    {
        if (! in_array(app()->environment(), ['local', 'development'], true)) {
            $this->error('Refused: use APP_ENV=local or development.');

            return self::FAILURE;
        }

        $mailer = (string) $this->option('mailer');
        $to = (string) $this->option('to');

        if (! array_key_exists($mailer, config('mail.mailers', []))) {
            $this->error("Unknown mailer [{$mailer}]. Check config/mail.php.");

            return self::FAILURE;
        }

        $samples = $this->sampleFactories();
        if ($this->option('list')) {
            $this->info('Scheduled email sample keys (use --only=key1,key2):');
            foreach (array_keys($samples) as $key) {
                $this->line('  '.$key);
            }
            $this->newLine();
            $this->comment('For every migrated Blade under livewire/agreements/migrated/emails, also run:');
            $this->line('  php artisan mail:test-mailpit --mailable=all-migrated --mailer='.$mailer.' --to='.$to);

            return self::SUCCESS;
        }

        $only = array_filter(array_map('trim', explode(',', (string) $this->option('only'))));
        if ($only !== []) {
            $samples = array_intersect_key($samples, array_flip($only));
            if ($samples === []) {
                $this->error('No matching keys. Use --list.');

                return self::FAILURE;
            }
        }

        $ok = 0;
        $failed = [];
        foreach ($samples as $key => $callback) {
            try {
                $callback($mailer, $to);
                $this->line("<info>OK</info> {$key}");
                $ok++;
            } catch (Throwable $e) {
                $failed[$key] = $e->getMessage();
                $this->error("FAIL {$key}: ".$e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Sent {$ok} message(s) to {$to} via [{$mailer}]. Open Mailpit (e.g. http://127.0.0.1:8025).");
        if ($failed !== []) {
            foreach ($failed as $k => $msg) {
                $this->warn("  {$k}: {$msg}");
            }

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array<string, callable(string $mailer, string $to): void>
     */
    private function sampleFactories(): array
    {
        return [
            'mit_weekly_opening' => function (string $mailer, string $to): void {
                $summary = ['expected' => 199.99];
                $expectedItems = [[
                    'vrm' => 'AB12 CDE',
                    'customer_name' => 'Preview Customer',
                    'customer_phone' => '020 0000 0000',
                    'amount' => 99.5,
                    'billing_frequency' => 'weekly',
                    'mit_fire_date' => now()->addDay()->format('D, M d, Y H:i'),
                    'status' => 'generated',
                ]];
                $payload = UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.mit-weekly-opening-report',
                    [
                        'summary' => $summary,
                        'expectedItems' => $expectedItems,
                        'weekStart' => Carbon::now()->startOfWeek()->format('l, M d, Y'),
                        'weekEnd' => Carbon::now()->endOfWeek()->format('l, M d, Y'),
                    ],
                    ['title' => '[Mailpit sample] Weekly MIT opening report'],
                );
                Mail::mailer($mailer)->to($to)->send(new MailpitMigratedPreviewMail(
                    $payload,
                    '[Mailpit sample] Weekly MIT opening report',
                ));
            },

            'pcn_email_job_t1' => function (string $mailer, string $to): void {
                $payload = UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.pcn.t1',
                    [
                        'data' => (object) [
                            'full_name' => 'Preview Customer',
                            'reg_no' => 'AB12 CDE',
                            'pcn_number' => 'PCN-MAILPIT-001',
                        ],
                    ],
                    ['title' => '[Mailpit sample] PCN T1'],
                );
                Mail::mailer($mailer)->to($to)->send(new MailpitMigratedPreviewMail(
                    $payload,
                    '[Mailpit sample] PCN email job (T1 style)',
                ));
            },

            'renting_invoice_generation' => function (string $mailer, string $to): void {
                Mail::mailer($mailer)->to($to)->send(new InvoiceGenerationNotification([
                    'email' => [$to],
                    'totalProcessed' => 12,
                    'newInvoices' => 3,
                ]));
            },

            'instalment_notification' => function (string $mailer, string $to): void {
                $row = (object) [
                    'fullname' => 'Preview Rider',
                    'regno' => 'XY99 ZZZ',
                    'motorbike_id' => 1,
                    'email' => $to,
                ];
                Mail::mailer($mailer)->to($to)->send(new InstalmentNotificationMail($row));
            },

            'renting_invoice_reminder' => function (string $mailer, string $to): void {
                $booking = (object) [
                    'fullname' => 'Preview Tenant',
                    'reg_no' => 'AA11 BBB',
                    'email' => $to,
                ];
                Mail::mailer($mailer)->to($to)->send(new RentInvoiceReminderMail($booking));
            },

            'due_invoices_customer_and_summary' => function (string $mailer, string $to): void {
                $row = [
                    'booking_no' => 1001,
                    'customer_name' => 'Preview Customer',
                    'customer_email' => $to,
                    'vin_number' => 'VINMAILPIT1',
                    'registration_number' => 'AB12 CDE',
                    'weekly_rent' => '45.00',
                    'invoice_date' => now()->addDay()->toDateString(),
                ];
                Mail::mailer($mailer)->to($to)->send(new DueInvoiceCustomerMail($row));
                Mail::mailer($mailer)->to($to)->send(new DueInvoiceSummaryMail(collect([$row])));
            },

            'admin_weekly_renting_report' => function (string $mailer, string $to): void {
                Mail::mailer($mailer)->to($to)->send(new ActiveRentingWeaklyMailer([
                    'active_bookings' => new Collection,
                    'stats' => [
                        'active_rentals' => 0,
                        'weekly_revenue' => 0,
                        'due_payments' => 0,
                        'total_deposits' => 0,
                        'unpaid_invoices' => 0,
                    ],
                ]));
            },

            'admin_weekly_club_topup' => function (string $mailer, string $to): void {
                Mail::mailer($mailer)->to($to)->send(new WeeklyClubTopupReportMailer([
                    'active_bookings' => new Collection,
                ]));
            },

            'mot_send_notifications' => function (string $mailer, string $to): void {
                $emailData = [
                    'customer_name' => 'Preview Rider',
                    'customer_email' => $to,
                    'mot_due_date' => now()->addDays(10)->toDateString(),
                    'tax_due_date' => now()->addMonth()->toDateString(),
                    'insurance_due_date' => null,
                    'motorbike_reg' => 'AB12 CDE',
                    'motorbike_model' => 'Sample',
                    'motorbike_make' => 'Honda',
                    'motorbike_year' => 2020,
                    'motorbike_id' => 1,
                    'email_stage' => '30',
                ];
                Mail::mailer($mailer)->to($to)->send(new MOTReminderEmail($emailData));
            },

            'monthly_sales_report' => function (string $mailer, string $to): void {
                $rows = collect([
                    [
                        'date' => now()->format('d-m-Y H:i'),
                        'reg_no' => 'AB12 CDE',
                        'motorbike_id' => 1,
                        'status' => 'Sold',
                        'user' => 'preview.user',
                        'buyer_name' => 'Buyer One',
                        'buyer_phone' => '07000 000000',
                        'buyer_email' => $to,
                        'buyer_address' => '1 Preview Street',
                    ],
                ]);
                $payload = UniversalMailPayload::fromLegacyEmailView(
                    'livewire.agreements.migrated.emails.monthly_sales_report',
                    ['data' => $rows],
                    ['title' => '[Mailpit sample] Monthly motorbike sales report'],
                );
                Mail::mailer($mailer)->to($to)->send(new MailpitMigratedPreviewMail(
                    $payload,
                    '[Mailpit sample] Monthly motorbike sales report',
                ));
            },

            'annual_busiest_days' => function (string $mailer, string $to): void {
                $emailData = [
                    'yearStart' => Carbon::now()->subYear()->toDateString(),
                    'yearEnd' => Carbon::now()->toDateString(),
                    'allDaysReport' => [
                        (object) ['day_name' => 'Monday', 'total_visits' => 10],
                        (object) ['day_name' => 'Saturday', 'total_visits' => 42],
                    ],
                    'allMonthsReport' => [
                        (object) ['month_key' => '2026-01', 'month_name' => 'January 2026', 'total_visits' => 100],
                    ],
                    'dayReportByBranch' => [
                        'CATFORD' => [
                            'branch_name' => 'CATFORD',
                            'data' => [(object) ['day_name' => 'Saturday', 'total_visits' => 25]],
                        ],
                    ],
                    'monthReportByBranch' => [
                        'CATFORD' => [
                            'branch_name' => 'CATFORD',
                            'data' => [(object) ['month_key' => '2026-01', 'month_name' => 'January 2026', 'total_visits' => 80]],
                        ],
                    ],
                ];
                Mail::mailer($mailer)->to($to)->send(new AnnualBusiestDaysReportMail($emailData));
            },

            'quarterly_vehicle_visits' => function (string $mailer, string $to): void {
                $emailData = [
                    'mostVisited' => [
                        (object) ['make' => 'Honda (120)', 'make_only' => 'Honda', 'model' => 'CBR', 'total_visits' => 85],
                    ],
                    'leastVisited' => [
                        (object) ['make' => 'Yamaha (15)', 'make_only' => 'Yamaha', 'model' => 'MT-07', 'total_visits' => 8],
                    ],
                    'mostRepeatedModelYear' => [],
                    'leastRepeatedModelYear' => [],
                    'year' => (int) now()->format('Y'),
                    'periodStartFormatted' => now()->subMonths(3)->format('d M Y'),
                    'periodEndFormatted' => now()->format('d M Y'),
                ];
                Mail::mailer($mailer)->to($to)->send(new QuarterlyVehicleVisitsReportMail($emailData));
            },

            'ngn_club_festive_hours' => function (string $mailer, string $to): void {
                $member = new ClubMember([
                    'full_name' => 'Preview Club Member',
                    'email' => $to,
                ]);
                Mail::mailer($mailer)->to($to)->send(new NgnClubFestiveHoursMailer($member));
            },
        ];
    }
}
