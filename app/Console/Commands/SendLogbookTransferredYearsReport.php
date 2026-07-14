<?php

namespace App\Console\Commands;

use App\Mail\LogbookTransferredYearsReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLogbookTransferredYearsReport extends Command
{
    protected $signature = 'app:send-logbook-transferred-years-report
                            {--dry-run : Show row counts only, do not send email}';

    protected $description = 'One-off email report: vehicles with logbook transferred in 2022–2025 (customer, seller, transfer date/time)';

    public function handle(): int
    {
        $years = [2022, 2023, 2024, 2025];

        $rows = DB::select("
            SELECT
                fa.id AS application_id,
                fa.logbook_transfer_date,
                m.reg_no,
                m.make,
                m.model,
                m.vin_number,
                c.first_name AS customer_first_name,
                c.last_name AS customer_last_name,
                c.email AS customer_email,
                c.phone AS customer_phone,
                fa.sold_by,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS staff_from_user
            FROM finance_applications fa
            INNER JOIN application_items ai ON ai.application_id = fa.id AND ai.motorbike_id IS NOT NULL
            INNER JOIN motorbikes m ON m.id = ai.motorbike_id
            LEFT JOIN customers c ON c.id = fa.customer_id
            LEFT JOIN users u ON u.id = fa.user_id
            WHERE fa.log_book_sent = 1
              AND fa.logbook_transfer_date IS NOT NULL
              AND YEAR(fa.logbook_transfer_date) IN (?, ?, ?, ?)
            ORDER BY fa.logbook_transfer_date ASC, fa.id ASC, m.reg_no ASC
        ", $years);

        $byYear = [];
        foreach ($years as $y) {
            $byYear[$y] = [];
        }

        foreach ($rows as $row) {
            $dt = Carbon::parse($row->logbook_transfer_date);
            $y = (int) $dt->year;
            if (! isset($byYear[$y])) {
                $byYear[$y] = [];
            }
            $soldBy = trim((string) ($row->sold_by ?? ''));
            $staffUser = trim((string) ($row->staff_from_user ?? ''));
            $row->seller_display = $soldBy !== '' ? $soldBy : ($staffUser !== '' ? $staffUser : '—');
            $row->transfer_month_label = $dt->format('F Y');
            $row->transfer_datetime_label = $dt->format('d/m/Y H:i');
            $row->customer_display = trim(
                trim((string) ($row->customer_first_name ?? '')).' '.trim((string) ($row->customer_last_name ?? ''))
            );
            if ($row->customer_display === '') {
                $row->customer_display = '—';
            }
            $byYear[$y][] = $row;
        }

        $totals = [];
        foreach ($years as $y) {
            $totals[$y] = count($byYear[$y]);
        }

        $emailData = [
            'years' => $years,
            'byYear' => $byYear,
            'totals' => $totals,
            'grandTotal' => count($rows),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        if ($this->option('dry-run')) {
            foreach ($years as $y) {
                $this->line("{$y}: {$totals[$y]} row(s)");
            }
            $this->line("Total: {$emailData['grandTotal']}");

            return self::SUCCESS;
        }

        $recipients = config('reports.internal_report_recipients');
        if ($recipients === []) {
            $this->error('No report recipients configured. Set INTERNAL_REPORT_EMAILS in .env (comma-separated).');

            return self::FAILURE;
        }

        try {
            Mail::to($recipients)->send(new LogbookTransferredYearsReportMail($emailData));
            Log::info('Logbook transferred years report emailed.', ['to' => $recipients, 'rows' => $emailData['grandTotal']]);
            $this->info('Sent logbook transferred report to: '.implode(', ', $recipients));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Logbook transferred years report failed: '.$e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
