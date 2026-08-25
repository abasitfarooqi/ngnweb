<?php

namespace App\Services\Director;

use App\Models\BookingInvoice;
use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\ClubMemberRedeem;
use App\Models\ClubMemberSpending;
use App\Models\FinanceApplication;
use App\Models\MOTBooking;
use App\Models\NgnCompaignReferral;
use App\Models\NgnMotNotifier;
use App\Models\PcnCase;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use App\Models\RentingTransaction;
use App\Services\Renting\RentingReferralInvestigation;
use App\Support\FluxAdminAccess;
use App\Support\FluxAdminPageAccess;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

final class DirectorCommandCentre
{
    public const MODULES = ['overview', 'rentals', 'club', 'finance', 'mot', 'referrals', 'cash', 'pcn'];

    public function __construct(
        private Carbon $from,
        private Carbon $to,
        private string $module,
        private string $focus,
    ) {
        $this->module = in_array($module, self::MODULES, true) ? $module : 'overview';
    }

    /**
     * @param  array<string, string>  $filters
     */
    public static function make(array $filters): self
    {
        $from = self::parseDate($filters['from'] ?? '') ?? now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $to = self::parseDate($filters['to'] ?? '') ?? now()->endOfDay();
        if ($to->lt($from)) {
            $to = $from->copy()->endOfDay();
        }

        return new self($from, $to, (string) ($filters['module'] ?? 'overview'), (string) ($filters['focus'] ?? 'all'));
    }

    /**
     * @return array<string, mixed>
     */
    public function build(): array
    {
        $snapshot = $this->snapshot();
        $period = $this->period();
        $referrals = $this->rentalReferralMetrics();

        return [
            'from' => $this->from,
            'to' => $this->to,
            'module' => $this->module,
            'focus' => $this->focus,
            'snapshot' => $snapshot,
            'period' => $period,
            'referrals' => $referrals,
            'look' => $this->lookCloser($snapshot, $period, $referrals),
            'pages' => $this->pages(),
            'cards' => $this->cards($snapshot, $period, $referrals),
        ];
    }

    /**
     * Live position — not limited to the selected week.
     *
     * @return array<string, float|int>
     */
    public function snapshot(): array
    {
        $activeItems = $this->activeRentalItems();
        $bookingIds = $activeItems->pluck('booking_id')->unique()->filter()->values();
        $unpaid = $this->unpaidOnBookings($bookingIds);
        $financeActive = $this->tableReady('finance_applications')
            ? FinanceApplication::query()->activePaymentPlan()->where('is_posted', true)
            : null;

        return [
            'active_rentals' => $activeItems->count(),
            'weekly_rent' => (float) $activeItems->sum('weekly_rent'),
            'rental_unpaid' => (float) $unpaid['sum'],
            'rental_due_count' => (int) $unpaid['count'],
            'rental_deposits' => $this->tableReady('renting_bookings')
                ? (float) RentingBooking::query()->whereIn('id', $bookingIds)->sum('deposit')
                : 0.0,
            'finance_active' => $financeActive ? (int) $financeActive->count() : 0,
            'finance_weekly' => $financeActive ? (float) $financeActive->sum('weekly_instalment') : 0.0,
            'club_members' => $this->tableReady('club_members') ? (int) ClubMember::query()->count() : 0,
            'club_unpaid_spend' => $this->clubUnpaidSpend(),
            'mot_expired' => $this->motExpiredCount(),
            'mot_upcoming' => $this->motUpcomingCount(),
            'pcn_open' => class_exists(PcnCase::class) ? (int) PcnCase::openCount() : 0,
            'pcn_open_amount' => $this->pcnOpenAmount(),
        ];
    }

    /**
     * Money and activity that landed inside the selected dates.
     *
     * @return array<string, float|int>
     */
    public function period(): array
    {
        $purchases = $this->clubPurchasesInPeriod();
        $spend = $this->clubSpendInPeriod();

        return [
            'rental_cash_in' => $this->sumIf('renting_transactions', fn () => (float) $this->between(RentingTransaction::query(), 'transaction_date')->sum('amount')),
            'rental_paid' => $this->sumIf('booking_invoices', fn () => (float) $this->paidInvoices()->sum('amount')),
            'rental_raised' => $this->sumIf('booking_invoices', fn () => (float) $this->between(BookingInvoice::query(), 'invoice_date')->sum('amount')),
            'rental_raised_unpaid' => $this->sumIf('booking_invoices', fn () => (float) $this->between(BookingInvoice::query()->where('is_paid', false), 'invoice_date')->sum('amount')),
            'club_visits' => (int) $purchases->unique('club_member_id')->count(),
            'club_sales' => (float) $purchases->sum('total'),
            'club_discount' => (float) $purchases->sum('discount'),
            'club_redeemed' => $this->sumIf('club_member_redeem', fn () => (float) $this->between(ClubMemberRedeem::query(), 'date')->sum('redeem_total')),
            'club_spend' => (float) $spend->sum('total'),
            'club_spend_paid' => (float) $spend->sum('paid_amount'),
            'club_referrals' => $this->sumIf('ngn_campaign_referrals', fn () => (int) $this->between(NgnCompaignReferral::query(), 'created_at')->count()),
            'club_referrals_ok' => $this->sumIf('ngn_campaign_referrals', fn () => (int) $this->between(NgnCompaignReferral::query()->where('validated', true), 'created_at')->count()),
            'finance_new' => $this->sumIf('finance_applications', fn () => (int) $this->between(FinanceApplication::query()->where('is_posted', true), 'contract_date')->count()),
            'finance_new_price' => $this->sumIf('finance_applications', fn () => (float) $this->between(FinanceApplication::query()->where('is_posted', true), 'contract_date')->sum('motorbike_price')),
            'finance_new_deposit' => $this->sumIf('finance_applications', fn () => (float) $this->between(FinanceApplication::query()->where('is_posted', true), 'contract_date')->sum('deposit')),
            'mot_bookings' => $this->sumIf('mot_bookings', fn () => (int) $this->between(MOTBooking::query(), 'date_of_appointment')->count()),
            'mot_paid' => $this->sumIf('mot_bookings', fn () => (int) $this->between(MOTBooking::query()->where('is_paid', true), 'date_of_appointment')->count()),
            'mot_done' => $this->sumIf('mot_bookings', fn () => (int) $this->between(MOTBooking::query()->where('status', MOTBooking::STATUS_COMPLETED), 'date_of_appointment')->count()),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    private function rentalReferralMetrics(): array
    {
        $metrics = RentingReferralInvestigation::make([
            'from' => $this->from->toDateString(),
            'to' => $this->to->toDateString(),
            'kind' => $this->module === 'referrals' && $this->focus === 'direct' ? 'direct' : ($this->module === 'referrals' && $this->focus === 'programme' ? 'programme' : 'all'),
        ])->metrics();

        return [
            'pounds_given' => (float) ($metrics['pounds_given'] ?? 0),
            'pounds_reversed' => (float) ($metrics['pounds_reversed'] ?? 0),
            'programme_weeks' => (int) ($metrics['programme_weeks'] ?? 0),
            'direct_weeks' => (int) ($metrics['direct_weeks'] ?? 0),
            'waiting_review' => (int) ($metrics['waiting_review'] ?? 0),
            'warnings' => (int) ($metrics['warnings'] ?? 0),
        ];
    }

    /**
     * @param  array<string, float|int>  $snapshot
     * @param  array<string, float|int>  $period
     * @param  array<string, float|int>  $referrals
     * @return list<array{label: string, value: string, hint: string, colour: string, href?: string}>
     */
    public function cards(array $snapshot, array $period, array $referrals): array
    {
        $cashIn = (float) $period['rental_cash_in'] + (float) $period['club_sales'] + (float) $period['finance_new_deposit'];
        $cashOut = (float) $period['club_discount'] + (float) $referrals['pounds_given'] + (float) $period['club_redeemed'];
        $pending = (float) $snapshot['rental_unpaid'] + (float) $snapshot['club_unpaid_spend'] + (float) $snapshot['pcn_open_amount'];

        $all = [
            'overview' => [
                ['label' => 'Coming in this period', 'value' => $this->money($cashIn), 'hint' => 'Rental cash + club sales + finance deposits.', 'colour' => 'green', 'href' => $this->url('flux-admin.active-rentals.index')],
                ['label' => 'Given away this period', 'value' => $this->money($cashOut), 'hint' => 'Club discount + redemptions + rental free weeks.', 'colour' => 'amber'],
                ['label' => 'Still pending now', 'value' => $this->money($pending), 'hint' => 'Unpaid rent + unpaid club spend + open PCNs.', 'colour' => 'red', 'href' => $this->url('flux-admin.rental-due-payments.index')],
                ['label' => 'Active rentals', 'value' => number_format((int) $snapshot['active_rentals']), 'hint' => $this->money((float) $snapshot['weekly_rent']).' a week if collected.', 'colour' => 'blue', 'href' => $this->url('flux-admin.rentals.index')],
                ['label' => 'Active payment plans', 'value' => number_format((int) $snapshot['finance_active']), 'hint' => $this->money((float) $snapshot['finance_weekly']).' a week on the books.', 'colour' => 'purple', 'href' => $this->url('flux-admin.finance.index', ['status' => 'active'])],
                ['label' => 'Open PCN value', 'value' => $this->money((float) $snapshot['pcn_open_amount']), 'hint' => number_format((int) $snapshot['pcn_open']).' open cases.', 'colour' => 'pink', 'href' => $this->url('flux-admin.pcn.index', ['status' => 'open'])],
            ],
            'rentals' => [
                ['label' => 'Active rentals', 'value' => number_format((int) $snapshot['active_rentals']), 'hint' => 'Posted items with no end date.', 'colour' => 'green', 'href' => $this->url('flux-admin.active-rentals.index')],
                ['label' => 'Weekly book', 'value' => $this->money((float) $snapshot['weekly_rent']), 'hint' => 'What the live fleet should pay each week.', 'colour' => 'blue'],
                ['label' => 'Cash taken', 'value' => $this->money((float) $period['rental_cash_in']), 'hint' => 'Renting transactions in this period.', 'colour' => 'green'],
                ['label' => 'Invoices paid', 'value' => $this->money((float) $period['rental_paid']), 'hint' => 'Marked paid in this period.', 'colour' => 'indigo'],
                ['label' => 'Still unpaid', 'value' => $this->money((float) $snapshot['rental_unpaid']), 'hint' => number_format((int) $snapshot['rental_due_count']).' due invoices on live rentals.', 'colour' => 'red', 'href' => $this->url('flux-admin.rental-due-payments.index')],
                ['label' => 'Raised unpaid here', 'value' => $this->money((float) $period['rental_raised_unpaid']), 'hint' => 'Invoices dated in this period that are still unpaid.', 'colour' => 'amber'],
            ],
            'club' => [
                ['label' => 'Members', 'value' => number_format((int) $snapshot['club_members']), 'hint' => 'Club list as it stands now.', 'colour' => 'pink', 'href' => $this->url('flux-admin.club.index')],
                ['label' => 'Sales', 'value' => $this->money((float) $period['club_sales']), 'hint' => number_format((int) $period['club_visits']).' visits in this period.', 'colour' => 'green', 'href' => $this->url('flux-admin.club-purchases.index')],
                ['label' => 'Discount given', 'value' => $this->money((float) $period['club_discount']), 'hint' => 'Club purchase discounts in this period.', 'colour' => 'amber'],
                ['label' => 'Redeemed', 'value' => $this->money((float) $period['club_redeemed']), 'hint' => 'Balance paid back to members.', 'colour' => 'indigo', 'href' => $this->url('flux-admin.club-redemptions.index')],
                ['label' => '0% spend booked', 'value' => $this->money((float) $period['club_spend']), 'hint' => $this->money((float) $period['club_spend_paid']).' already paid.', 'colour' => 'blue', 'href' => $this->url('flux-admin.club-spending.index')],
                ['label' => 'Unpaid spend now', 'value' => $this->money((float) $snapshot['club_unpaid_spend']), 'hint' => 'Still outstanding on 0% spendings.', 'colour' => 'red'],
            ],
            'finance' => [
                ['label' => 'Active plans', 'value' => number_format((int) $snapshot['finance_active']), 'hint' => 'Posted, not cancelled, log book still with NGN.', 'colour' => 'purple', 'href' => $this->url('flux-admin.finance.index', ['status' => 'active'])],
                ['label' => 'Weekly incoming', 'value' => $this->money((float) $snapshot['finance_weekly']), 'hint' => 'Sum of weekly instalments on those plans.', 'colour' => 'green'],
                ['label' => 'New contracts', 'value' => number_format((int) $period['finance_new']), 'hint' => 'Posted in this period.', 'colour' => 'blue'],
                ['label' => 'Bike value sold', 'value' => $this->money((float) $period['finance_new_price']), 'hint' => 'Motorbike price on those contracts.', 'colour' => 'indigo'],
                ['label' => 'Deposits taken', 'value' => $this->money((float) $period['finance_new_deposit']), 'hint' => 'Deposits on contracts posted in this period.', 'colour' => 'green'],
            ],
            'mot' => [
                ['label' => 'Expired MOT', 'value' => number_format((int) $snapshot['mot_expired']), 'hint' => 'Notifier list past due date.', 'colour' => 'red', 'href' => $this->url('flux-admin.mot-stats.index')],
                ['label' => 'Due in 30 days', 'value' => number_format((int) $snapshot['mot_upcoming']), 'hint' => 'Notifier list approaching expiry.', 'colour' => 'amber'],
                ['label' => 'Bookings', 'value' => number_format((int) $period['mot_bookings']), 'hint' => 'MOT appointments in this period.', 'colour' => 'blue', 'href' => $this->url('flux-admin.mot-bookings.index')],
                ['label' => 'Paid bookings', 'value' => number_format((int) $period['mot_paid']), 'hint' => number_format((int) $period['mot_done']).' marked completed.', 'colour' => 'green'],
            ],
            'referrals' => [
                ['label' => 'Rental £ given', 'value' => $this->money((float) $referrals['pounds_given']), 'hint' => 'Free weeks posted in this period.', 'colour' => 'green', 'href' => $this->url('flux-admin.rental-referral-investigation.index')],
                ['label' => '£ reversed', 'value' => $this->money((float) $referrals['pounds_reversed']), 'hint' => 'Gifted week later unpaid.', 'colour' => 'red'],
                ['label' => 'Programme weeks', 'value' => number_format((int) $referrals['programme_weeks']), 'hint' => number_format((int) $referrals['direct_weeks']).' staff direct.', 'colour' => 'blue'],
                ['label' => 'Waiting for staff', 'value' => number_format((int) $referrals['waiting_review']), 'hint' => 'Friend paid a week. Approve or refuse.', 'colour' => 'amber'],
                ['label' => 'Club referrals', 'value' => number_format((int) $period['club_referrals']), 'hint' => number_format((int) $period['club_referrals_ok']).' validated.', 'colour' => 'pink'],
                ['label' => 'Need a look', 'value' => number_format((int) $referrals['warnings']), 'hint' => 'Duplicate or already rented.', 'colour' => 'red'],
            ],
            'cash' => [
                ['label' => 'Rental cash in', 'value' => $this->money((float) $period['rental_cash_in']), 'hint' => 'Transactions dated in this period.', 'colour' => 'green'],
                ['label' => 'Club sales', 'value' => $this->money((float) $period['club_sales']), 'hint' => 'Purchases in this period.', 'colour' => 'green'],
                ['label' => 'Finance deposits', 'value' => $this->money((float) $period['finance_new_deposit']), 'hint' => 'On contracts posted in this period.', 'colour' => 'purple'],
                ['label' => 'Discount + free weeks', 'value' => $this->money((float) $period['club_discount'] + (float) $referrals['pounds_given']), 'hint' => 'Money not taken.', 'colour' => 'amber'],
                ['label' => 'Rent still unpaid', 'value' => $this->money((float) $snapshot['rental_unpaid']), 'hint' => 'Due invoices on live rentals.', 'colour' => 'red', 'href' => $this->url('flux-admin.rental-due-payments.index')],
                ['label' => 'Club spend unpaid', 'value' => $this->money((float) $snapshot['club_unpaid_spend']), 'hint' => '0% spend still outstanding.', 'colour' => 'red'],
            ],
            'pcn' => [
                ['label' => 'Open cases', 'value' => number_format((int) $snapshot['pcn_open']), 'hint' => 'Live PCN workload.', 'colour' => 'amber', 'href' => $this->url('flux-admin.pcn.index', ['status' => 'open'])],
                ['label' => 'Open full amount', 'value' => $this->money((float) $snapshot['pcn_open_amount']), 'hint' => 'Sum of full amounts on open cases.', 'colour' => 'red'],
            ],
        ];

        $cards = $all[$this->module] ?? $all['overview'];

        return $this->filterCards($cards);
    }

    /**
     * @param  list<array{label: string, value: string, hint: string, colour: string, href?: string}>  $cards
     * @return list<array{label: string, value: string, hint: string, colour: string, href?: string}>
     */
    private function filterCards(array $cards): array
    {
        $focus = $this->focus;
        if ($focus === '' || $focus === 'all') {
            return $cards;
        }

        $needles = match ($this->module.'.'.$focus) {
            'rentals.cash' => ['Cash taken', 'Invoices paid', 'Weekly book'],
            'rentals.unpaid' => ['Still unpaid', 'Raised unpaid here'],
            'club.discounts' => ['Discount given', 'Redeemed'],
            'club.spend' => ['0% spend booked', 'Unpaid spend now'],
            'club.referrals' => ['Members'],
            'finance.active' => ['Active plans', 'Weekly incoming'],
            'finance.new' => ['New contracts', 'Bike value sold', 'Deposits taken'],
            'mot.expired' => ['Expired MOT', 'Due in 30 days'],
            'mot.bookings' => ['Bookings', 'Paid bookings'],
            'referrals.programme', 'referrals.direct', 'referrals.rentals' => ['Rental £ given', '£ reversed', 'Programme weeks', 'Waiting for staff', 'Need a look'],
            'referrals.club' => ['Club referrals'],
            'cash.in' => ['Rental cash in', 'Club sales', 'Finance deposits'],
            'cash.out' => ['Discount + free weeks'],
            'cash.pending' => ['Rent still unpaid', 'Club spend unpaid'],
            default => null,
        };

        if ($needles === null) {
            return $cards;
        }

        return array_values(array_filter($cards, fn (array $card) => in_array($card['label'], $needles, true)));
    }

    /**
     * @return list<array{label: string, hint: string, url: string}>
     */
    public function pages(): array
    {
        $sets = [
            'overview' => [
                ['Director rental referrals', 'Programme, staff direct, cash given.', 'flux-admin.rental-referral-investigation.index'],
                ['Weekly chase report', 'Who staff spoke to and what is still unpaid.', 'flux-admin.rental-weekly-follow-up-report.index'],
                ['Active rentals', 'Live fleet, weekly book, arrears.', 'flux-admin.active-rentals.index'],
                ['Due rent WhatsApp', 'Overdue invoices to chase.', 'flux-admin.rental-due-payments.index'],
                ['Payment plans', 'Active finance contracts.', 'flux-admin.finance.index', ['status' => 'active']],
                ['Club purchases', 'Sales and discounts.', 'flux-admin.club-purchases.index'],
                ['MOT stats', 'Expired and upcoming MOTs.', 'flux-admin.mot-stats.index'],
                ['PCN cases', 'Open penalty work.', 'flux-admin.pcn.index', ['status' => 'open']],
            ],
            'rentals' => [
                ['Rentals home', 'Every rental desk page.', 'flux-admin.rental-operations.index'],
                ['Active bookings', 'Filters and outstanding.', 'flux-admin.rentals.index'],
                ['Active rentals overview', 'Weekly book and unpaid totals.', 'flux-admin.active-rentals.index'],
                ['Due payments', 'WhatsApp chase list.', 'flux-admin.rental-due-payments.index'],
                ['Weekly chase report', 'Director follow-up snapshot.', 'flux-admin.rental-weekly-follow-up-report.index'],
                ['Ended with pendings', 'Closed rentals that still owe.', 'flux-admin.ended-with-pendings.index'],
                ['Rental referrals', 'Staff list of friends and gifts.', 'flux-admin.rental-referrals.index'],
            ],
            'club' => [
                ['Club members', 'Full club list.', 'flux-admin.club.index'],
                ['Purchases', 'Sales, discount, POS.', 'flux-admin.club-purchases.index'],
                ['0% spending', 'Credit spendings.', 'flux-admin.club-spending.index'],
                ['Spending payments', 'Money collected on 0% spend.', 'flux-admin.club-spending-payments.index'],
                ['Redemptions', 'Discount paid back.', 'flux-admin.club-redemptions.index'],
            ],
            'finance' => [
                ['Payment plans', 'All contracts.', 'flux-admin.finance.index'],
                ['Active plans', 'Still collecting.', 'flux-admin.finance.index', ['status' => 'active']],
                ['Judopay MIT', 'Incoming card collections.', 'flux-admin.judopay.mit-dashboard'],
                ['Weekly MIT queue', 'This week’s collections.', 'flux-admin.judopay.weekly-mit-queue'],
            ],
            'mot' => [
                ['MOT home', 'Bookings and checker.', 'flux-admin.mot.index'],
                ['MOT bookings', 'Diary.', 'flux-admin.mot-bookings.index'],
                ['MOT stats', 'Notifier expiry list.', 'flux-admin.mot-stats.index'],
                ['Compliance preview', 'MOT / tax / insurance.', 'flux-admin.motorbike-compliance.preview'],
            ],
            'referrals' => [
                ['Rental investigation', 'Director filters on programme and direct.', 'flux-admin.rental-referral-investigation.index'],
                ['Staff rental referrals', 'Day-to-day list.', 'flux-admin.rental-referrals.index'],
                ['Club members', 'Club referral sit on the member, not a staff list.', 'flux-admin.club.index'],
            ],
            'cash' => [
                ['Due rent', 'Unpaid weekly invoices.', 'flux-admin.rental-due-payments.index'],
                ['Active rentals', 'Weekly book versus unpaid.', 'flux-admin.active-rentals.index'],
                ['Club spending', 'Unpaid 0% spend.', 'flux-admin.club-spending.index'],
                ['Ended pendings', 'Closed rentals still owing.', 'flux-admin.ended-with-pendings.index'],
                ['Referral cash given', 'Free weeks posted.', 'flux-admin.rental-referral-investigation.index'],
            ],
            'pcn' => [
                ['PCN home', 'Module dashboard.', 'flux-admin.modules.show', ['module' => 'pcn']],
                ['Open cases', 'Work still live.', 'flux-admin.pcn.index', ['status' => 'open']],
            ],
        ];

        $out = [];
        foreach ($sets[$this->module] ?? $sets['overview'] as $row) {
            $params = $row[3] ?? [];
            $url = $this->url($row[2], is_array($params) ? $params : []);
            if ($url === null) {
                continue;
            }
            $out[] = ['label' => $row[0], 'hint' => $row[1], 'url' => $url];
        }

        return $out;
    }

    /**
     * @param  array<string, float|int>  $snapshot
     * @param  array<string, float|int>  $period
     * @param  array<string, float|int>  $referrals
     * @return list<array{title: string, meta: string, url: ?string}>
     */
    private function lookCloser(array $snapshot, array $period, array $referrals): array
    {
        unset($period, $referrals);

        return match ($this->module) {
            'rentals', 'cash', 'overview' => $this->topUnpaidRentals(),
            'club' => $this->focus === 'referrals' ? $this->recentClubReferrals() : $this->topClubDiscounts(),
            'finance' => $this->newFinanceContracts(),
            'mot' => $this->nextMotExpiry(),
            'referrals' => $this->recentClubReferrals(),
            'pcn' => [['title' => number_format((int) $snapshot['pcn_open']).' open PCNs', 'meta' => $this->money((float) $snapshot['pcn_open_amount']).' full amount still open.', 'url' => $this->url('flux-admin.pcn.index', ['status' => 'open'])]],
            default => [],
        };
    }

    /**
     * @return list<array{title: string, meta: string, url: ?string}>
     */
    private function topUnpaidRentals(): array
    {
        if (! $this->tableReady('booking_invoices') || ! $this->tableReady('renting_bookings')) {
            return [];
        }

        $rows = BookingInvoice::query()
            ->selectRaw('booking_id, SUM(amount) as outstanding')
            ->where('is_paid', false)
            ->whereDate('invoice_date', '<=', now()->toDateString())
            ->groupBy('booking_id')
            ->orderByDesc('outstanding')
            ->limit(8)
            ->get();

        $bookings = RentingBooking::query()
            ->with('customer')
            ->whereIn('id', $rows->pluck('booking_id'))
            ->get()
            ->keyBy('id');

        return $rows->map(function ($row) use ($bookings) {
            $booking = $bookings->get((int) $row->booking_id);
            $name = trim((string) (($booking?->customer?->first_name ?? '').' '.($booking?->customer?->last_name ?? '')));

            return [
                'title' => 'Rental #'.$row->booking_id.($name !== '' ? ' · '.$name : ''),
                'meta' => $this->money((float) $row->outstanding).' unpaid and due.',
                'url' => $this->url('flux-admin.rentals.show', ['booking' => (int) $row->booking_id]),
            ];
        })->all();
    }

    /**
     * @return list<array{title: string, meta: string, url: ?string}>
     */
    private function topClubDiscounts(): array
    {
        if (! $this->tableReady('club_member_purchases')) {
            return [];
        }

        return $this->between(ClubMemberPurchase::query()->with('clubMember'), 'date')
            ->orderByDesc('discount')
            ->limit(8)
            ->get()
            ->map(fn (ClubMemberPurchase $row) => [
                'title' => $row->clubMember?->full_name ?: 'Club purchase #'.$row->id,
                'meta' => $this->money((float) $row->discount).' off a '.$this->money((float) $row->total).' sale · '.($row->date ? Carbon::parse($row->date)->format('d M Y') : '—'),
                'url' => $this->url('flux-admin.club-purchases.index'),
            ])
            ->all();
    }

    /**
     * @return list<array{title: string, meta: string, url: ?string}>
     */
    private function newFinanceContracts(): array
    {
        if (! $this->tableReady('finance_applications')) {
            return [];
        }

        return $this->between(FinanceApplication::query()->where('is_posted', true)->with('customer'), 'contract_date')
            ->orderByDesc('contract_date')
            ->limit(8)
            ->get()
            ->map(fn (FinanceApplication $row) => [
                'title' => 'Plan #'.$row->id,
                'meta' => $this->money((float) $row->weekly_instalment).' a week · deposit '.$this->money((float) $row->deposit),
                'url' => $this->url('flux-admin.finance.show', ['application' => $row->id]),
            ])
            ->all();
    }

    /**
     * @return list<array{title: string, meta: string, url: ?string}>
     */
    private function nextMotExpiry(): array
    {
        if (! $this->tableReady('ngn_mot_notifier')) {
            return [];
        }

        return NgnMotNotifier::query()
            ->whereNotNull('mot_due_date')
            ->orderBy('mot_due_date')
            ->limit(8)
            ->get()
            ->map(fn (NgnMotNotifier $row) => [
                'title' => strtoupper((string) $row->motorbike_reg).' · '.($row->customer_name ?: 'MOT'),
                'meta' => 'Due '.($row->mot_due_date ? Carbon::parse($row->mot_due_date)->format('d M Y') : '—'),
                'url' => $this->url('flux-admin.mot-stats.index'),
            ])
            ->all();
    }

    /**
     * @return list<array{title: string, meta: string, url: ?string}>
     */
    private function recentClubReferrals(): array
    {
        if (! $this->tableReady('ngn_campaign_referrals')) {
            return [];
        }

        return $this->between(NgnCompaignReferral::query()->with('referrer'), 'created_at')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (NgnCompaignReferral $row) => [
                'title' => ($row->referrer?->full_name ?: 'Club member').' → '.($row->referred_full_name ?: $row->referred_phone),
                'meta' => ($row->validated ? 'Validated' : 'Not validated').' · '.$row->created_at?->format('d M Y'),
                'url' => $this->url('flux-admin.club.index'),
            ])
            ->all();
    }

    private function activeRentalItems(): Collection
    {
        if (! $this->tableReady('renting_booking_items') || ! $this->tableReady('renting_bookings')) {
            return collect();
        }

        return RentingBookingItem::query()
            ->whereNull('end_date')
            ->where('is_posted', true)
            ->whereHas('booking', fn ($q) => $q->where('is_posted', true))
            ->get(['id', 'booking_id', 'weekly_rent']);
    }

    /**
     * @param  Collection<int, mixed>  $bookingIds
     * @return array{sum: float, count: int}
     */
    private function unpaidOnBookings(Collection $bookingIds): array
    {
        if ($bookingIds->isEmpty() || ! $this->tableReady('booking_invoices')) {
            return ['sum' => 0.0, 'count' => 0];
        }

        $query = BookingInvoice::query()
            ->whereIn('booking_id', $bookingIds)
            ->where('is_paid', false)
            ->whereDate('invoice_date', '<=', now()->toDateString());

        return [
            'sum' => (float) (clone $query)->sum('amount'),
            'count' => (int) (clone $query)->count(),
        ];
    }

    private function paidInvoices()
    {
        $query = BookingInvoice::query()->where('is_paid', true);

        return $query->where(function ($inner) {
            $inner->where(function ($paid) {
                $paid->whereNotNull('paid_date')
                    ->whereDate('paid_date', '>=', $this->from->toDateString())
                    ->whereDate('paid_date', '<=', $this->to->toDateString());
            })->orWhere(function ($fallback) {
                $fallback->whereNull('paid_date')
                    ->whereDate('invoice_date', '>=', $this->from->toDateString())
                    ->whereDate('invoice_date', '<=', $this->to->toDateString());
            });
        });
    }

    private function clubPurchasesInPeriod(): Collection
    {
        if (! $this->tableReady('club_member_purchases')) {
            return collect();
        }

        return $this->between(ClubMemberPurchase::query(), 'date')->get(['id', 'club_member_id', 'total', 'discount']);
    }

    private function clubSpendInPeriod(): Collection
    {
        if (! $this->tableReady('club_member_spendings')) {
            return collect();
        }

        return $this->between(ClubMemberSpending::query(), 'date')->get(['id', 'total', 'paid_amount']);
    }

    private function clubUnpaidSpend(): float
    {
        if (! $this->tableReady('club_member_spendings')) {
            return 0.0;
        }

        return (float) ClubMemberSpending::query()
            ->selectRaw('COALESCE(SUM(total - COALESCE(paid_amount, 0)), 0) as unpaid')
            ->value('unpaid');
    }

    private function motExpiredCount(): int
    {
        if (! $this->tableReady('ngn_mot_notifier')) {
            return 0;
        }

        return (int) NgnMotNotifier::query()->whereDate('mot_due_date', '<', now())->count();
    }

    private function motUpcomingCount(): int
    {
        if (! $this->tableReady('ngn_mot_notifier')) {
            return 0;
        }

        return (int) NgnMotNotifier::query()->whereBetween('mot_due_date', [now()->toDateString(), now()->addDays(30)->toDateString()])->count();
    }

    private function pcnOpenAmount(): float
    {
        if (! class_exists(PcnCase::class) || ! $this->tableReady('pcn_cases')) {
            return 0.0;
        }

        return (float) PcnCase::open()->sum('full_amount');
    }

    private function between($query, string $column)
    {
        return $query
            ->whereDate($column, '>=', $this->from->toDateString())
            ->whereDate($column, '<=', $this->to->toDateString());
    }

    private function tableReady(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function sumIf(string $table, callable $callback): int|float
    {
        if (! $this->tableReady($table)) {
            return 0;
        }

        return $callback();
    }

    private function money(float $amount): string
    {
        return '£'.number_format($amount, 2);
    }

    /** @param  array<string, mixed>  $params */
    private function url(string $route, mixed $params = []): ?string
    {
        if (! Route::has($route)) {
            return null;
        }

        $module = is_array($params) ? ($params['module'] ?? null) : null;
        if (! FluxAdminPageAccess::allows(FluxAdminAccess::user(), $route, $module)) {
            return null;
        }

        try {
            return route($route, $params);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function parseDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value, config('app.timezone'))->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
