<?php

namespace App\Console\Commands;

use App\Mail\ContractsPendingLogbookReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendContractsPendingLogbookReport extends Command
{
    protected $signature = 'app:send-contracts-pending-logbook-report
                            {--dry-run : Show counts per contract month only, do not send email}';

    protected $description = 'One-off email report: posted contracts, not cancelled, logbook not transferred — one table per contract month';

    public function handle(): int
    {
        $rows = DB::select("
            SELECT
                fa.id AS application_id,
                fa.contract_date,
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
            WHERE fa.is_posted = 1
              AND (fa.is_cancelled = 0 OR fa.is_cancelled IS NULL)
              AND (fa.log_book_sent = 0 OR fa.log_book_sent IS NULL)
              AND fa.contract_date IS NOT NULL
            ORDER BY fa.contract_date ASC, fa.id ASC, m.reg_no ASC
        ");

        $byMonth = [];
        foreach ($rows as $row) {
            $dt = Carbon::parse($row->contract_date);
            $key = $dt->format('Y-m');
            if (! isset($byMonth[$key])) {
                $byMonth[$key] = [];
            }
            $soldBy = trim((string) ($row->sold_by ?? ''));
            $staffUser = trim((string) ($row->staff_from_user ?? ''));
            $row->seller_display = $soldBy !== '' ? $soldBy : ($staffUser !== '' ? $staffUser : '—');
            $row->contract_month_label = $dt->format('F Y');
            $row->contract_datetime_label = $dt->format('d/m/Y H:i');
            $row->customer_display = trim(
                trim((string) ($row->customer_first_name ?? '')).' '.trim((string) ($row->customer_last_name ?? ''))
            );
            if ($row->customer_display === '') {
                $row->customer_display = '—';
            }
            $byMonth[$key][] = $row;
        }

        $monthKeys = array_keys($byMonth);
        sort($monthKeys);

        $totals = [];
        foreach ($monthKeys as $key) {
            $totals[$key] = count($byMonth[$key]);
        }

        $emailData = [
            'monthKeys' => $monthKeys,
            'byMonth' => $byMonth,
            'totals' => $totals,
            'grandTotal' => count($rows),
            'generatedAt' => now()->format('d/m/Y H:i'),
        ];

        if ($this->option('dry-run')) {
            foreach ($monthKeys as $key) {
                $label = Carbon::createFromFormat('Y-m', $key)->format('F Y');
                $this->line("{$label} ({$key}): {$totals[$key]} row(s)");
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
            Mail::to($recipients)->send(new ContractsPendingLogbookReportMail($emailData));
            Log::info('Contracts pending logbook report emailed.', ['to' => $recipients, 'rows' => $emailData['grandTotal']]);
            $this->info('Sent contracts pending logbook report to: '.implode(', ', $recipients));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Contracts pending logbook report failed: '.$e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
