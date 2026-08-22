<?php

namespace App\Services\Renting;

use App\Mail\RentingWeeklyUpdateReportMail;
use App\Models\BookingInvoice;
use App\Models\RentingWeeklyUpdate;
use App\Support\AgreementPdfViewAssets;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RentingWeeklyUpdateReportService
{
    public const DIRECTOR_EMAIL = 'thiago@neguinhomotors.co.uk';

    public const CC_EMAIL = 'customerservice@neguinhomotors.co.uk';

    public const STORAGE_DIR = 'renting-weekly-update-reports';

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function completedPeriod(?Carbon $at = null): array
    {
        $at = ($at ?? now())->copy()->timezone(config('app.timezone'));
        $thisSaturday = $at->copy()->startOfWeek(Carbon::MONDAY)->addDays(5)->setTime(15, 45, 0);
        $end = $at->gte($thisSaturday) ? $thisSaturday : $thisSaturday->copy()->subWeek();
        $start = $end->copy()->startOfWeek(Carbon::MONDAY)->setTime(9, 0, 0);

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function openPeriod(?Carbon $at = null): array
    {
        $at = ($at ?? now())->copy()->timezone(config('app.timezone'));
        $start = $at->copy()->startOfWeek(Carbon::MONDAY)->setTime(9, 0, 0);
        $end = $start->copy()->addDays(5)->setTime(15, 45, 0);

        return [$start, $end];
    }

    /**
     * @return list<array{key: string, label: string, start: string, end: string}>
     */
    public function recentPeriods(int $count = 8): array
    {
        $periods = [];
        [$openStart, $openEnd] = $this->openPeriod();
        if (now()->lt($openEnd)) {
            $periods[] = [
                'key' => $this->periodKey($openStart, $openEnd),
                'label' => $openStart->format('d M Y H:i').' → '.$openEnd->format('d M Y H:i').' (in progress)',
                'start' => $openStart->toDateTimeString(),
                'end' => $openEnd->toDateTimeString(),
            ];
        }

        [$start, $end] = $this->completedPeriod();

        for ($i = 0; $i < $count; $i++) {
            $periods[] = [
                'key' => $this->periodKey($start, $end),
                'label' => $start->format('d M Y H:i').' → '.$end->format('d M Y H:i'),
                'start' => $start->toDateTimeString(),
                'end' => $end->toDateTimeString(),
            ];
            $end = $end->copy()->subWeek();
            $start = $end->copy()->startOfWeek(Carbon::MONDAY)->setTime(9, 0, 0);
        }

        return $periods;
    }

    public function periodKey(Carbon $start, Carbon $end): string
    {
        return $start->format('Y-m-d_Hi').'__'.$end->format('Y-m-d_Hi');
    }

    /**
     * @return array<string, mixed>
     */
    public function build(Carbon $start, Carbon $end): array
    {
        $entries = $this->tablesReady()
            ? $this->entryRows($start, $end)
            : collect();
        $accounts = $this->unpaidAccounts($entries);

        return [
            'start' => $start,
            'end' => $end,
            'generated_at' => now(),
            'entries' => $accounts->pluck('notes')->flatten(1)->values(),
            'accounts' => $accounts,
            'email_accounts' => $this->emailAccounts($accounts),
            'intro' => $this->intro($start, $end, $accounts),
            'summary' => $this->summary($accounts),
        ];
    }

    public function pdfBinary(array $report): string
    {
        return Pdf::loadView('pdf.renting-weekly-update-report', [
            'report' => $report,
            'logoSrc' => AgreementPdfViewAssets::composerVariables()['agreementPdfLogoSrc'],
        ])
            ->setPaper('a4', 'portrait')
            ->output();
    }

    public function storePdf(array $report): string
    {
        $path = self::STORAGE_DIR.'/'.$this->periodKey($report['start'], $report['end']).'.pdf';
        Storage::disk('local')->put($path, $this->pdfBinary($report));

        return $path;
    }

    public function send(array $report, ?string $storedPath = null): string
    {
        $path = $storedPath ?: $this->storePdf($report);

        Mail::to(self::DIRECTOR_EMAIL)
            ->cc(self::CC_EMAIL)
            ->send(new RentingWeeklyUpdateReportMail($report, $path));

        return $path;
    }

    public function filename(array $report): string
    {
        return 'rental-weekly-follow-up-'.$report['start']->format('Ymd').'-'.$report['end']->format('Ymd').'.pdf';
    }

    public function tablesReady(): bool
    {
        return Schema::hasTable('renting_weekly_updates');
    }

    /** @return Collection<int, array<string, mixed>> */
    private function entryRows(Carbon $start, Carbon $end): Collection
    {
        return RentingWeeklyUpdate::query()
            ->with([
                'user:id,first_name,last_name',
                'invoice:id,booking_id,invoice_date,amount,is_paid',
                'booking.customer:id,first_name,last_name,phone,email',
                'booking.rentingBookingItems.motorbike:id,reg_no',
            ])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->map(fn (RentingWeeklyUpdate $update) => $this->mapEntry($update))
            ->values();
    }

    /** @return array<string, mixed> */
    private function mapEntry(RentingWeeklyUpdate $update): array
    {
        $customer = $update->booking?->customer;
        $customerName = trim(($customer?->first_name.' '.$customer?->last_name) ?: '');

        return [
            'id' => (int) $update->id,
            'noted_at' => $update->created_at,
            'date' => $update->created_at?->format('d M Y') ?: '—',
            'time' => $update->created_at?->format('H:i') ?: '—',
            'staff' => trim(($update->user?->first_name.' '.$update->user?->last_name) ?: '') ?: '—',
            'staff_id' => $update->user_id ? (int) $update->user_id : null,
            'customer' => $customerName !== '' ? $customerName : '—',
            'customer_id' => $customer?->id ? (int) $customer->id : null,
            'phone' => $customer?->phone ?: '—',
            'email' => $customer?->email ?: '—',
            'registration' => $this->registration($update),
            'booking_id' => (int) $update->booking_id,
            'invoice_id' => $update->invoice_id ? (int) $update->invoice_id : null,
            'invoice_date' => $update->invoice?->invoice_date?->format('d M Y'),
            'invoice_amount' => $update->invoice ? (float) $update->invoice->amount : null,
            'invoice_paid' => (bool) ($update->invoice?->is_paid),
            'weekly_rent' => $this->weeklyRent($update),
            'note' => (string) $update->note,
        ];
    }

    private function weeklyRent(RentingWeeklyUpdate $update): ?float
    {
        $rent = $update->booking?->rentingBookingItems?->first()?->weekly_rent;

        return $rent !== null ? (float) $rent : null;
    }

    private function registration(RentingWeeklyUpdate $update): string
    {
        $items = $update->booking?->rentingBookingItems;
        if (! $items) {
            return '—';
        }

        $reg = $items->first(fn ($item) => filled($item->motorbike?->reg_no))?->motorbike?->reg_no;

        return $reg ? strtoupper((string) $reg) : '—';
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $entries
     * @return Collection<int, array<string, mixed>>
     */
    private function unpaidAccounts(Collection $entries): Collection
    {
        $unpaid = $this->unpaidInvoices($entries->pluck('booking_id')->unique()->filter()->values());

        return $entries
            ->groupBy('booking_id')
            ->filter(fn (Collection $rows, $bookingId) => $unpaid->has((int) $bookingId))
            ->map(function (Collection $rows) use ($unpaid) {
                $first = $rows->first();
                $invoices = $unpaid->get((int) $first['booking_id']);
                $notes = $rows->sortBy(fn (array $row) => optional($row['noted_at'])->timestamp ?? 0)->values();
                $invoiceRows = $invoices->map(function (BookingInvoice $invoice) use ($notes) {
                    return [
                        'id' => (int) $invoice->id,
                        'date' => $invoice->invoice_date?->format('d M Y') ?: '—',
                        'amount' => (float) $invoice->amount,
                        'notes' => $notes->where('invoice_id', (int) $invoice->id)->values(),
                    ];
                })->values();

                return [
                    'customer' => $first['customer'],
                    'customer_id' => $first['customer_id'],
                    'phone' => $first['phone'],
                    'email' => $first['email'] ?? '—',
                    'registration' => $first['registration'],
                    'booking_id' => $first['booking_id'],
                    'weekly_rent' => $first['weekly_rent'],
                    'notes' => $notes,
                    'rental_notes' => $notes->filter(fn (array $row) => empty($row['invoice_id']))->values(),
                    'invoices' => $invoiceRows,
                    'unpaid_invoices' => $invoices->count(),
                    'outstanding' => (float) $invoices->sum('amount'),
                    'oldest_due' => optional($invoices->sortBy('invoice_date')->first()?->invoice_date)->format('d M Y') ?: '—',
                    'staff' => $notes->pluck('staff')->unique()->filter(fn ($name) => $name !== '—')->values(),
                    'story' => $this->accountStory($first, $notes, $invoices),
                ];
            })
            ->sortByDesc('outstanding')
            ->values();
    }

    /**
     * @param  Collection<int, int>  $bookingIds
     * @return Collection<int, Collection<int, BookingInvoice>>
     */
    private function unpaidInvoices(Collection $bookingIds): Collection
    {
        if ($bookingIds->isEmpty() || ! Schema::hasTable('booking_invoices')) {
            return collect();
        }

        return BookingInvoice::query()
            ->whereIn('booking_id', $bookingIds)
            ->where('is_paid', false)
            ->whereDate('invoice_date', '<=', now()->toDateString())
            ->orderBy('invoice_date')
            ->get(['id', 'booking_id', 'amount', 'invoice_date'])
            ->groupBy('booking_id');
    }

    /**
     * @param  array<string, mixed>  $first
     * @param  Collection<int, array<string, mixed>>  $notes
     * @param  Collection<int, BookingInvoice>  $invoices
     */
    private function accountStory(array $first, Collection $notes, Collection $invoices): string
    {
        $name = $first['customer'] !== '—' ? $first['customer'] : 'This customer';
        $bike = $first['registration'] !== '—' ? ' on '.$first['registration'] : '';
        $rent = $first['weekly_rent'] ? ' Weekly rent is £'.number_format((float) $first['weekly_rent'], 2).'.' : '';
        $invoiceBits = $invoices->map(function (BookingInvoice $invoice) {
            return 'invoice #'.$invoice->id.' dated '.($invoice->invoice_date?->format('d M Y') ?: 'an unknown date').' for £'.number_format((float) $invoice->amount, 2);
        });
        $owing = $invoices->count() === 1
            ? $name.' still owes '.$invoiceBits->first().'.'
            : $name.' still owes '.$invoices->count().' weekly invoices ('.$invoiceBits->implode('; ').'), totalling £'.number_format((float) $invoices->sum('amount'), 2).'.';

        $staff = $notes->pluck('staff')->unique()->filter(fn ($name) => $name !== '—')->values();
        $who = $staff->count() === 1
            ? $staff->first().' followed this up'
            : ($staff->isNotEmpty() ? $staff->implode(' and ').' followed this up' : 'Staff followed this up');
        $times = $notes->count() === 1 ? 'once this week' : $notes->count().' times this week';

        return $name.' is on rental #'.$first['booking_id'].$bike.'.'.$rent.' '.$owing.' '.$who.' '.$times.'.';
    }

    /**
     * Email lists only bookings (and invoices) that have notes this week.
     *
     * @param  Collection<int, array<string, mixed>>  $accounts
     * @return Collection<int, array<string, mixed>>
     */
    private function emailAccounts(Collection $accounts): Collection
    {
        return $accounts
            ->map(function (array $account) {
                $invoices = collect($account['invoices'])
                    ->filter(fn (array $invoice) => collect($invoice['notes'])->isNotEmpty())
                    ->values();

                if ($invoices->isEmpty() && collect($account['rental_notes'])->isEmpty()) {
                    return null;
                }

                $account['invoices'] = $invoices;

                return $account;
            })
            ->filter()
            ->values();
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $accounts
     */
    private function intro(Carbon $start, Carbon $end, Collection $accounts): string
    {
        $customers = $accounts->pluck('customer_id')->unique()->count();
        $notes = $accounts->sum(fn (array $row) => $row['notes']->count());
        $money = (float) $accounts->sum('outstanding');
        $staff = $accounts->pluck('staff')->flatten()->unique()->filter()->values();

        if ($customers === 0) {
            return 'Between '.$start->format('l j F').' and '.$end->format('l j F').', staff did not record any weekly updates against customers who still have unpaid rent. History on each account has been left as it is.';
        }

        $who = $staff->count() === 1
            ? $staff->first().' did this work'
            : ($staff->isNotEmpty() ? $staff->implode(', ').' shared this work' : 'The rentals desk did this work');

        return 'This is the rentals chase report for '.$start->format('l j F').' to '.$end->format('l j F').'. '
            .$who.'. Staff recorded '.$notes.' follow-up '.($notes === 1 ? 'note' : 'notes').' on '.$customers.' '
            .($customers === 1 ? 'customer who still has' : 'customers who still have').' unpaid weekly rent, totalling £'.number_format($money, 2).'. '
            .'Each account below explains who we spoke about, what is still unpaid, and what staff wrote after speaking to the customer. '
            .'Nothing has been removed from the live history.';
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $accounts
     * @return array<string, mixed>
     */
    private function summary(Collection $accounts): array
    {
        $notes = $accounts->pluck('notes')->flatten(1);
        $byStaff = $notes
            ->groupBy(fn (array $row) => $row['staff_id'] ?: 0)
            ->map(fn (Collection $rows) => [
                'name' => (string) ($rows->first()['staff'] ?? '—'),
                'count' => $rows->count(),
            ])
            ->sortByDesc('count')
            ->values();

        return [
            'customers' => $accounts->pluck('customer_id')->unique()->count(),
            'entries' => $notes->count(),
            'by_staff' => $byStaff,
            'multiple_contacts' => $accounts->filter(fn (array $row) => $row['notes']->count() > 1)->values(),
            'arrears' => $accounts,
        ];
    }
}
