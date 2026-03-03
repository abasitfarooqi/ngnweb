<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class NgnClubController
 *
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NgnClubController extends Controller
{
    public function index()
    {
        DB::enableQueryLog();  // Add this at the start

        $memberStats = DB::table('club_members')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(1) as count'))
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month')
            ->get();

        $labels = $memberStats->pluck('month')->toJson();
        $data = $memberStats->pluck('count')->toJson();
        $currentMonth = date('Y-m');

        // Get current month for top visitors default view
        $selectedMonth = request('month', date('Y-m'));

        // Get top 20 visitors for the selected month
        $topVisitors = DB::table('club_member_purchases as cmp')
            ->join('club_members as cm', 'cm.id', '=', 'cmp.club_member_id')
            ->select([
                'cm.id',
                'cm.full_name',
                'cm.email',
                'cm.phone',
                DB::raw('COUNT(cmp.id) as visit_count'),
                DB::raw('SUM(cmp.total) as total_spent'),
            ])
            ->whereRaw('DATE_FORMAT(cmp.date, "%Y-%m") = ?', [$selectedMonth])
            ->groupBy('cm.id', 'cm.full_name', 'cm.email', 'cm.phone')
            ->orderByDesc('visit_count')
            ->paginate(10);

        // Get available months for navigation
        $availableMonths = DB::table('club_member_purchases')
            ->select(DB::raw('DISTINCT DATE_FORMAT(date, "%Y-%m") as month'))
            ->orderByDesc('month')
            ->pluck('month');

        // Get selected year for members list (default to 'All')
        $selectedYear = request('year', 'all');

        // Get all unique years from club_members
        $availableYears = DB::table('club_members')
            ->select('year')
            ->whereNotNull('year')
            ->where('year', '!=', '')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Get members by year with their visit counts
        $membersByYearQuery = DB::table('club_members as cm')
            ->select([
                'cm.id',
                'cm.full_name',
                'cm.email',
                'cm.phone',
                'cm.year',
                'cm.make',
                'cm.model',
                'cm.vrm',
                DB::raw('COUNT(DISTINCT cmp.id) as total_visits'),
                DB::raw('COALESCE(SUM(cmp.total), 0) as total_spent'),
            ])
            ->leftJoin('club_member_purchases as cmp', 'cm.id', '=', 'cmp.club_member_id');

        if ($selectedYear !== 'all') {
            $membersByYearQuery->where('cm.year', $selectedYear);
        }

        $membersByYear = $membersByYearQuery
            ->groupBy('cm.id', 'cm.full_name', 'cm.email', 'cm.phone', 'cm.year', 'cm.make', 'cm.model', 'cm.vrm')
            ->orderByDesc('total_visits')
            ->paginate(5);

        // Search functionality for club members
        $search = request()->get('search', '');
        $searchField = request()->get('search_field', 'all');

        $searchableMembers = DB::table('club_members as cm')
            ->select([
                'cm.id',
                'cm.full_name',
                'cm.email',
                'cm.phone',
                'cm.year',
                'cm.make',
                'cm.model',
                'cm.vrm',
                DB::raw('COUNT(DISTINCT cmp.id) as total_visits'),
                DB::raw('COALESCE(SUM(cmp.total), 0) as total_spent'),
            ])
            ->leftJoin('club_member_purchases as cmp', 'cm.id', '=', 'cmp.club_member_id')
            ->when($search, function ($query) use ($search, $searchField) {
                if ($searchField === 'all') {
                    return $query->where(function ($q) use ($search) {
                        $q->where('cm.full_name', 'LIKE', "%{$search}%")
                            ->orWhere('cm.email', 'LIKE', "%{$search}%")
                            ->orWhere('cm.phone', 'LIKE', "%{$search}%")
                            ->orWhere('cm.year', 'LIKE', "%{$search}%")
                            ->orWhere('cm.make', 'LIKE', "%{$search}%")
                            ->orWhere('cm.model', 'LIKE', "%{$search}%")
                            ->orWhere('cm.vrm', 'LIKE', "%{$search}%");
                    });
                } else {
                    return $query->where("cm.{$searchField}", 'LIKE', "%{$search}%");
                }
            })
            ->groupBy('cm.id', 'cm.full_name', 'cm.email', 'cm.phone', 'cm.year', 'cm.make', 'cm.model', 'cm.vrm')
            ->orderByDesc('total_visits')
            ->paginate(5);

        $lastWeekVisits = DB::table('club_member_purchases as cmp')
            ->select([
                DB::raw('DATE_FORMAT(cmp.date, "%Y-%m-%d") as visit_date'),
                'cmp.branch_id',
                DB::raw('COUNT(1) as visit_count'),
            ])
            ->where('cmp.date', '>=', Carbon::now()->subDays(7))
            ->whereNotNull('cmp.branch_id')
            ->groupBy(DB::raw('DATE_FORMAT(cmp.date, "%Y-%m-%d")'), 'cmp.branch_id')
            ->orderBy('visit_date')
            ->orderBy('cmp.branch_id')
            ->get();

        $dates = $lastWeekVisits->pluck('visit_date')->unique()->sort()->values();
        $branches = $lastWeekVisits->pluck('branch_id')->unique()->sort()->values();

        // Let's verify the total for specific date
        $totalForDate = $lastWeekVisits
            ->where('visit_date', '2025-02-12')
            ->sum('visit_count');

        $branchData = [];
        foreach ($branches as $branch) {
            $branchVisits = [];
            foreach ($dates as $date) {
                $visits = $lastWeekVisits
                    ->where('branch_id', $branch)
                    ->where('visit_date', $date)
                    ->first();

                $count = $visits ? intval($visits->visit_count) : 0;

                $branchVisits[] = $count;
            }
            $branchData[$branch] = $branchVisits;
        }

        // Verify final data structure
        foreach ($dates as $index => $date) {
            $total = 0;
            $individualCounts = [];
            foreach ($branches as $branch) {
                $total += $branchData[$branch][$index];
                $individualCounts[] = [$branch => $branchData[$branch][$index]];
            }
        }

        return view('olders.admin.ngn_club', [
            'title' => 'Ngn Club',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'NgnClub' => false,
            ],
            'page' => 'resources/views/admin/ngn_club.blade.php',
            'controller' => 'app/Http/Controllers/Admin/NgnClubController.php',
            'chartLabels' => $labels,
            'chartData' => $data,
            'currentMonth' => $currentMonth,
            'visitDates' => $dates->toJson(),
            'branchData' => json_encode($branchData),
            'topVisitors' => $topVisitors,
            'selectedMonth' => $selectedMonth,
            'availableMonths' => $availableMonths,
            'membersByYear' => $membersByYear,
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'searchableMembers' => $searchableMembers,
            'search' => $search,
            'searchField' => $searchField,
        ]);
    }
}
