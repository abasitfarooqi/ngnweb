<?php

namespace App\Console\Commands;

use App\Mail\SoldNewMotorbikesYearsReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSoldNewMotorbikesYearsReport extends Command
{
    protected $signature = 'app:send-sold-new-motorbikes-years-report
                            {--dry-run : Show row counts only, do not send email}';

    protected $description = 'One-off email report: motorbikes_sale listings marked sold (is_sold), by calendar year of sale / listing update';

    public function handle(): int
    {
        $startYear = 2022;
        $endYear = (int) now()->year;
        $years = range($startYear, max($startYear, $endYear));
        $placeholders = implode(', ', array_fill(0, count($years), '?'));

        $rows = DB::select("
            SELECT
                ms.id AS listing_id,
                ms.motorbike_id,
                ms.price AS listing_price,
                ms.condition AS sale_condition,
                ms.mileage,
                m.reg_no,
                m.make,
                m.model,
                m.year AS model_year,
                m.color,
                m.vin_number,
                sold.customer_name AS sold_customer_name,
                sold.phone_number AS sold_customer_phone,
                sold.sold_price AS sold_transaction_price,
                ms.buyer_name,
                ms.buyer_phone,
                ms.buyer_email,
                COALESCE(sold.created_at, ms.updated_at, ms.created_at) AS sold_record_at,
                TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS staff_from_user
            FROM motorbikes_sale ms
            INNER JOIN motorbikes m ON m.id = ms.motorbike_id
            LEFT JOIN motorbikes_sold sold ON sold.listing_id = ms.id
            LEFT JOIN users u ON u.id = ms.user_id
            WHERE ms.is_sold = 1
              AND YEAR(COALESCE(sold.created_at, ms.updated_at, ms.created_at)) IN ({$placeholders})
            ORDER BY sold_record_at ASC, ms.id ASC
        ", $years);

        $byYear = [];
        foreach ($years as $y) {
            $byYear[$y] = [];
        }

        foreach ($rows as $row) {
            $dt = Carbon::parse($row->sold_record_at);
            $y = (int) $dt->year;
            if (! isset($byYear[$y])) {
                $byYear[$y] = [];
            }
            $staffUser = trim((string) ($row->staff_from_user ?? ''));
            $row->staff_display = $staffUser !== '' ? $staffUser : '—';
            $row->sold_month_label = $dt->format('F Y');
            $row->sold_datetime_label = $dt->format('d/m/Y H:i');

            $nameSold = trim((string) ($row->sold_customer_name ?? ''));
            $nameBuyer = trim((string) ($row->buyer_name ?? ''));
            $row->buyer_display = $nameSold !== '' ? $nameSold : ($nameBuyer !== '' ? $nameBuyer : '—');

            $phoneSold = trim((string) ($row->sold_customer_phone ?? ''));
            $phoneBuyer = trim((string) ($row->buyer_phone ?? ''));
            $row->phone_display = $phoneSold !== '' ? $phoneSold : ($phoneBuyer !== '' ? $phoneBuyer : '—');

            $row->email_display = trim((string) ($row->buyer_email ?? '')) !== ''
                ? trim((string) $row->buyer_email)
                : '—';

            $fmt = static fn ($v) => $v !== null && $v !== '' ? '£'.number_format((float) $v, 2) : '—';
            $row->list_price_display = $fmt($row->listing_price);
            $row->sold_price_display = $fmt($row->sold_transaction_price);

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
            'yearRangeLabel' => min($years).'–'.max($years),
            'yearNote' => "Year groups use the calendar year of the motorbikes_sold row's created time when present; otherwise the sale listing's updated or created time.",
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
            Mail::to($recipients)->send(new SoldNewMotorbikesYearsReportMail($emailData));
            Log::info('Sold motorbikes (sale listings) years report emailed.', ['to' => $recipients, 'rows' => $emailData['grandTotal']]);
            $this->info('Sent sold motorbikes report to: '.implode(', ', $recipients));

            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Sold motorbikes years report failed: '.$e->getMessage());
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
