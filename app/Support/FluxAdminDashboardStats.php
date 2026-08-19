<?php

namespace App\Support;

use App\Models\ClubMember;
use App\Models\ClubMemberPurchase;
use App\Models\Customer;
use App\Models\CustomerAuth;
use App\Models\FinanceApplication;
use App\Models\Motorbike;
use App\Models\MotorbikeRepair;
use App\Models\MotorbikesSale;
use App\Models\Motorcycle;
use App\Models\PcnCase;
use App\Models\RentalPayment;
use App\Models\RentingBooking;
use App\Models\RentingBookingItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FluxAdminDashboardStats
{
    public static function fluxOverview(): array
    {
        $stats = Cache::remember('flux-admin.dashboard.stats', now()->addMinutes(5), function () {
            $activeRentals = RentingBookingItem::whereNull('end_date')
                ->whereHas('booking', fn ($q) => $q->where('is_posted', true))
                ->where('is_posted', true)
                ->count();

            return [
                'total_vehicles' => TotalVehiclesQuery::count(),
                'total_motorbikes' => Motorbike::count(),
                'active_rentals' => $activeRentals,
                // Must match finance index ?status=active (posted, not cancelled, log book with NGN).
                'finance_applications' => FinanceApplication::activePaymentPlanListedCount(),
                'club_members' => ClubMember::count(),
            ];
        });

        // Always live — must match PCN index “Open” filter total.
        $stats['open_pcn_cases'] = PcnCase::openCount();

        return $stats;
    }

    public static function legacy(): array
    {
        return Cache::remember('flux-admin.dashboard.legacy', now()->addMinutes(5), function () {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $dayBeforeYesterday = Carbon::yesterday()->subDay();

            $todayVisitors = ClubMemberPurchase::whereDate('date', $today)
                ->distinct('club_member_id')
                ->count('club_member_id');
            $yesterdayVisitors = ClubMemberPurchase::whereDate('date', $yesterday)
                ->distinct('club_member_id')
                ->count('club_member_id');
            $dayBeforeVisitors = ClubMemberPurchase::whereDate('date', $dayBeforeYesterday)
                ->distinct('club_member_id')
                ->count('club_member_id');

            // Visit records: day one → today, and calendar month (1st → today)
            $allTimeVisits = ClubMemberPurchase::whereDate('date', '<=', $today)->count();
            $thisMonthVisits = ClubMemberPurchase::whereBetween('date', [
                Carbon::now()->startOfMonth()->toDateString(),
                $today->toDateString(),
            ])->count();

            $currentWeekStart = Carbon::now()->startOfWeek();
            $currentWeekEnd = Carbon::now()->endOfWeek();
            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
            $currentMonthStart = Carbon::now()->startOfMonth();
            $currentMonthEnd = Carbon::now()->endOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            $postedFinance = fn () => FinanceApplication::where('is_posted', true);

            return [
                'today' => $today,
                'total_club_members' => ClubMember::count(),
                'active_rentals' => RentingBookingItem::whereNull('end_date')->where('is_posted', true)->count(),
                'total_customers' => Customer::count(),
                'total_ecommerce_customers' => CustomerAuth::count(),
                'club_visits' => [
                    'day_before' => $dayBeforeVisitors,
                    'day_before_label' => $dayBeforeYesterday->format('D, M j'),
                    'yesterday' => $yesterdayVisitors,
                    'today' => $todayVisitors,
                    'all_time' => $allTimeVisits,
                    'this_month' => $thisMonthVisits,
                    'this_month_label' => Carbon::now()->format('M Y'),
                ],
                'sales' => [
                    'total' => $postedFinance()->count(),
                    'this_year' => $postedFinance()->whereBetween('contract_date', [
                        Carbon::now()->startOfYear(),
                        Carbon::now()->endOfYear(),
                    ])->count(),
                    'this_year_label' => Carbon::now()->format('Y'),
                    'this_week' => $postedFinance()->whereBetween('contract_date', [$currentWeekStart, $currentWeekEnd])->count(),
                    'last_week' => $postedFinance()->whereBetween('contract_date', [$lastWeekStart, $lastWeekEnd])->count(),
                    'this_month' => $postedFinance()->whereBetween('contract_date', [$currentMonthStart, $currentMonthEnd])->count(),
                    'last_month' => $postedFinance()->whereBetween('contract_date', [$lastMonthStart, $lastMonthEnd])->count(),
                ],
                'finance' => [
                    'active' => FinanceApplication::activePaymentPlanListedCount(),
                    'terminated' => FinanceApplication::where('is_cancelled', true)->where('is_posted', true)->count(),
                    'closed' => FinanceApplication::where('is_posted', true)->where('log_book_sent', true)->count(),
                ],
                'repairs' => [
                    'total' => MotorbikeRepair::count(),
                    'completed' => MotorbikeRepair::where('is_repaired', true)->count(),
                    'delivered' => MotorbikeRepair::where('is_returned', true)->count(),
                ],
                'bikes_for_sale' => MotorbikesSale::join('motorbikes', 'motorbikes_sale.motorbike_id', '=', 'motorbikes.id')
                    ->leftJoin('branches', 'motorbikes.branch_id', '=', 'branches.id')
                    ->where('motorbikes_sale.is_sold', false)
                    ->select([
                        'motorbikes.reg_no',
                        'motorbikes.make',
                        'motorbikes.model',
                        'motorbikes.year',
                        'motorbikes_sale.condition',
                        'motorbikes_sale.mileage',
                        'motorbikes_sale.price',
                        'motorbikes_sale.v5_available',
                        'branches.name as branch_name',
                    ])
                    ->orderByDesc('motorbikes_sale.created_at')
                    ->limit(50)
                    ->get(),
                'pcn' => [
                    'total' => PcnCase::count(),
                    'open' => PcnCase::openCount(),
                    'closed' => PcnCase::closed()->count(),
                    'police' => PcnCase::where('is_police', true)->count(),
                ],
                'pcn_chart' => self::pcnChartData(),
                'fleet_chart' => self::fleetChartData(),
                ...self::fleetAndPayments(),
            ];
        });
    }

    public static function fleetAndPayments(): array
    {
        $rentalpayments = RentalPayment::all()->where('outstanding', '>', 0);
        $rrpayments = DB::table('rental_payments')
            ->where('payment_type', 'rental')
            ->whereNull('deleted_at')
            ->sum('outstanding');
        $ddpayments = DB::table('rental_payments')
            ->where('payment_type', 'deposit')
            ->whereNull('deleted_at')
            ->sum('outstanding');

        $forRentCount = Motorcycle::where('availability', 'for rent')->count();
        $rentedCount = Motorcycle::where('availability', 'rented')->count();
        $forSaleCount = Motorcycle::where('availability', 'for sale')->count();
        $soldCount = Motorcycle::where('availability', 'sold')->count();
        $repairsCount = Motorcycle::where('availability', 'repairs')->count();
        $catBCount = Motorcycle::where('availability', 'cat b')->count();
        $claimInProgressCount = Motorcycle::where('availability', 'claim in progress')->count();
        $impoundedCount = Motorcycle::where('availability', 'impounded')->count();
        $accidentCount = Motorcycle::where('availability', 'accident')->count();
        $missingCount = Motorcycle::where('availability', 'missing')->count();
        $stolenCount = Motorcycle::where('availability', 'stolen')->count();

        return [
            'payment_count' => $rentalpayments->count(),
            'outstanding_total' => number_format((float) ($rrpayments + $ddpayments), 2, '.', ''),
            'outstanding_rentals' => $rrpayments,
            'outstanding_deposits' => $ddpayments,
            'fleet_counts' => [
                'for_rent' => $forRentCount,
                'rented' => $rentedCount,
                'for_sale' => $forSaleCount,
                'sold' => $soldCount,
                'repairs' => $repairsCount,
                'cat_b' => $catBCount,
                'claim' => $claimInProgressCount,
                'impounded' => $impoundedCount,
                'accident' => $accidentCount,
                'missing' => $missingCount,
                'stolen' => $stolenCount,
            ],
            'fleet_chart_values' => [
                $forRentCount, $rentedCount, $forSaleCount, $soldCount, $repairsCount,
                $catBCount, $claimInProgressCount, $impoundedCount, $accidentCount, $missingCount, $stolenCount,
            ],
            'tax_due' => Motorcycle::where('tax_due_date', '<', Carbon::now()->addDays(10))->orderBy('tax_due_date')->limit(10)->get(),
            'mot_due' => Motorcycle::where('mot_expiry_date', '<', Carbon::now()->addDays(10))->orderBy('mot_expiry_date')->limit(10)->get(),
        ];
    }

    public static function clearCache(): void
    {
        Cache::forget('flux-admin.dashboard.stats');
        Cache::forget('flux-admin.dashboard.legacy');
    }

    protected static function pcnChartData(): array
    {
        $rows = PcnCase::select(
            DB::raw('DATE_FORMAT(date_of_contravention, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN isClosed = 1 THEN 1 ELSE 0 END) as closed'),
            DB::raw('SUM(CASE WHEN isClosed = 0 THEN 1 ELSE 0 END) as open')
        )
            ->where('date_of_contravention', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $rows->pluck('month')->all(),
            'datasets' => [
                ['label' => 'Total cases', 'data' => $rows->pluck('total')->all(), 'borderColor' => 'rgb(75, 192, 192)', 'backgroundColor' => 'rgba(75, 192, 192, 0.1)'],
                ['label' => 'Closed', 'data' => $rows->pluck('closed')->all(), 'borderColor' => 'rgb(54, 162, 235)', 'backgroundColor' => 'rgba(54, 162, 235, 0.1)'],
                ['label' => 'Open', 'data' => $rows->pluck('open')->all(), 'borderColor' => 'rgb(255, 99, 132)', 'backgroundColor' => 'rgba(255, 99, 132, 0.1)'],
            ],
        ];
    }

    protected static function fleetChartData(): array
    {
        $query = "
            SELECT status, COUNT(id) AS count
            FROM (
                SELECT m.id, 'AVAILABLE' AS status
                FROM motorbikes m
                INNER JOIN renting_pricings rp ON rp.motorbike_id = m.id
                WHERE m.id NOT IN (SELECT motorbike_id FROM renting_booking_items WHERE end_date IS NULL)
                UNION ALL
                SELECT m.id, 'AVAILABLE' AS status
                FROM motorbikes m
                INNER JOIN motorbikes_sale ms ON ms.motorbike_id = m.id AND ms.is_sold = false
                UNION ALL
                SELECT m.id, 'AVAILABLE FOR REPAIR' AS status
                FROM motorbikes m
                INNER JOIN motorbikes_repair mr ON mr.motorbike_id = m.id AND mr.is_returned = false
                UNION ALL
                SELECT m.id, 'CATEGORY B' AS status
                FROM motorbikes m
                INNER JOIN motorbikes_cat_b mcb ON mcb.motorbike_id = m.id
                UNION ALL
                SELECT m.id, 'CLAIM' AS status
                FROM motorbikes m
                INNER JOIN claim_motorbikes cm ON cm.motorbike_id = m.id AND cm.is_returned = false
            ) AS unified
            GROUP BY status
        ";

        $rows = collect(DB::select($query));

        return [
            'labels' => $rows->pluck('status')->all(),
            'datasets' => [[
                'label' => 'In-house motorbikes',
                'data' => $rows->pluck('count')->all(),
                'backgroundColor' => ['rgb(70, 127, 208)', 'rgb(77, 189, 116)', 'rgb(96, 92, 168)', 'rgb(255, 193, 7)', 'rgb(255, 87, 34)'],
            ]],
        ];
    }
}
