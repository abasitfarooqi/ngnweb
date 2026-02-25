@extends(backpack_view('blank'))

@php
    if (backpack_theme_config('show_getting_started')) {
        $widgets['before_content'][] = [
            'type' => 'view',
            'view' => backpack_view('inc.getting_started'),
        ];
    } else {
        // Get real total club members count
        $totalClubMembers = \App\Models\ClubMember::count();

        // Get active rentals count (where end_date is null)
        $activeRentals = \App\Models\RentingBookingItem::whereNull('end_date')->where('is_posted', true)->count();

        // Get total regular customers count
        $totalCustomers = \App\Models\Customer::count();

        // Get total e-commerce customers count
        $totalEcommerceCustomers = \App\Models\CustomerAuth::count();

        // Get visitor statistics
        $today = \Carbon\Carbon::today();
        $yesterday = \Carbon\Carbon::yesterday();
        $dayBeforeYesterday = \Carbon\Carbon::yesterday()->subDay();

        $todayVisitors = \App\Models\ClubMemberPurchase::whereDate('date', $today)
            ->distinct('club_member_id')
            ->count('club_member_id');

        $yesterdayVisitors = \App\Models\ClubMemberPurchase::whereDate('date', $yesterday)
            ->distinct('club_member_id')
            ->count('club_member_id');

        $dayBeforeVisitors = \App\Models\ClubMemberPurchase::whereDate('date', $dayBeforeYesterday)
            ->distinct('club_member_id')
            ->count('club_member_id');

        Widget::add([
            'type' => 'div',
            'class' => 'row mb-2',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-12'],
                    'icon' => 'la la-users',
                    'color' => 'text-black',
                    'value' => 'CLUB MEMBERS VISITS',
                    'label' => '',
                ],
            ],
        ])->to('before_content');

        // Add visitor statistics row
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-calendar-minus',
                    'color' => 'info',
                    'value' => $dayBeforeVisitors,
                    'label' => $dayBeforeYesterday->format('D, M j') . ' Visits',
                    'link' => '/ngn-admin/ngn_club',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-calendar-day',
                    'color' => 'primary',
                    'value' => $yesterdayVisitors,
                    'label' => 'Yesterday\'s Visits',
                    'link' => '/ngn-admin/ngn_club',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-calendar-check',
                    'color' => 'success',
                    'value' => $todayVisitors,
                    'label' => 'Today\'s Visits',
                    'link' => '/ngn-admin/ngn_club',
                ],
            ],
        ])->to('before_content');

        // Get real total club members count
        $totalClubMembers = \App\Models\ClubMember::count();

        // Fetch total bikes with profile ID 1
        $ownedBikes = \App\Models\Motorbike::where('vehicle_profile_id', 1)->get();
        $totalOwnedBikes = $ownedBikes->count();

        // Get finance counts
        $activeFinance = \App\Models\FinanceApplication::where('is_cancelled', false)
            ->where('is_posted', true)
            ->where('log_book_sent', false)
            ->count();

        $terminatedFinance = \App\Models\FinanceApplication::where('is_cancelled', true)
            ->where('is_posted', true)
            ->count();

        $closedFinance = \App\Models\FinanceApplication::where('is_posted', true)
            ->where('log_book_sent', true)
            ->count();

        // Get repair statistics
        $totalRepairs = \App\Models\MotorbikeRepair::count();
        $completedRepairs = \App\Models\MotorbikeRepair::where('is_repaired', true)->count();
        $deliveredRepairs = \App\Models\MotorbikeRepair::where('is_returned', true)->count();

        // Get weekly and monthly bike sales statistics
        $currentWeekStart = \Carbon\Carbon::now()->startOfWeek();
        $currentWeekEnd = \Carbon\Carbon::now()->endOfWeek();
        $lastWeekStart = \Carbon\Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = \Carbon\Carbon::now()->subWeek()->endOfWeek();
        $currentMonthStart = \Carbon\Carbon::now()->startOfMonth();
        $currentMonthEnd = \Carbon\Carbon::now()->endOfMonth();
        $lastMonthStart = \Carbon\Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = \Carbon\Carbon::now()->subMonth()->endOfMonth();

        $currentWeekSales = \App\Models\FinanceApplication::where('is_posted', true)
            ->whereBetween('contract_date', [$currentWeekStart, $currentWeekEnd])
            ->count();

        $lastWeekSales = \App\Models\FinanceApplication::where('is_posted', true)
            ->whereBetween('contract_date', [$lastWeekStart, $lastWeekEnd])
            ->count();

        $currentMonthSales = \App\Models\FinanceApplication::where('is_posted', true)
            ->whereBetween('contract_date', [$currentMonthStart, $currentMonthEnd])
            ->count();

        $lastMonthSales = \App\Models\FinanceApplication::where('is_posted', true)
            ->whereBetween('contract_date', [$lastMonthStart, $lastMonthEnd])
            ->count();

        // Add weekly and monthly sales statistics row
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-calendar-check',
                    'color' => 'success',
                    'value' => $currentWeekSales,
                    'label' => 'Bikes Sold This Week',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-calendar-minus',
                    'color' => 'info',
                    'value' => $lastWeekSales,
                    'label' => 'Bikes Sold Last Week',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-calendar',
                    'color' => 'warning',
                    'value' => $currentMonthSales,
                    'label' => 'Bikes Sold This Month',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-calendar-alt',
                    'color' => 'primary',
                    'value' => $lastMonthSales,
                    'label' => 'Bikes Sold Last Month',
                ],
            ],
        ])->to('before_content');

        // Get bikes for sale data
        $bikesForSale = \App\Models\MotorbikesSale::join(
            'motorbikes',
            'motorbikes_sale.motorbike_id',
            '=',
            'motorbikes.id',
        )
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
            ->orderBy('motorbikes_sale.created_at', 'desc')
            ->get();

        // First row of stats
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-users',
                    'color' => 'primary',
                    'value' => $totalClubMembers,
                    'label' => 'NGN Club Members',
                    'link' => '/ngn-admin/ngn_club',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-motorcycle',
                    'color' => 'success',
                    'value' => $activeRentals,
                    'label' => 'Active Rentals',
                    'link' => '/ngn-admin/active_renting',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-id-card',
                    'color' => 'info',
                    'value' => $totalCustomers,
                    'label' => 'Regular Customers',
                    'link' => '/ngn-admin/customer',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-shopping-cart',
                    'color' => 'warning',
                    'value' => $totalEcommerceCustomers,
                    'label' => 'E-commerce Subscribers',
                    'link' => '/ngn-admin/ecommerce_customers',
                ],
            ],
        ])->to('before_content');

        // Second row for finance stats
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-money-bill',
                    'color' => 'success',
                    'value' => $activeFinance,
                    'label' => 'Active Finance',
                    'link' => '/ngn-admin/finance-application?log_book_sent=0',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-times-circle',
                    'color' => 'danger',
                    'value' => $terminatedFinance,
                    'label' => 'Terminated Finance',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-check-circle',
                    'color' => 'info',
                    'value' => $closedFinance,
                    'label' => 'Closed Finance',
                ],
            ],
        ])->to('before_content');

        // Third row for repair stats
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-wrench',
                    'color' => 'primary',
                    'value' => $totalRepairs,
                    'label' => 'Total Service Jobs',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-tools',
                    'color' => 'success',
                    'value' => $completedRepairs,
                    'label' => 'Repairs Completed',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-motorcycle',
                    'color' => 'info',
                    'value' => $deliveredRepairs,
                    'label' => 'Bikes Delivered',
                ],
            ],
        ])->to('before_content');

        // Widget for Owned Bikes
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-12'],
                    'icon' => 'la la-motorcycle',
                    'color' => 'primary',
                    'value' => $totalOwnedBikes,
                    'label' => 'Our Motorbikes',
                ],
            ],
        ])->to('before_content');

        // Get PCN statistics
        $totalPcnCases = \App\Models\PcnCase::count();
        $openPcnCases = \App\Models\PcnCase::where('isClosed', false)->count();
        $closedPcnCases = \App\Models\PcnCase::where('isClosed', true)->count();
        $policePcnCases = \App\Models\PcnCase::where('is_police', true)->count();

        // Add PCN Statistics Header
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-2',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-12'],
                    'icon' => 'la la-ticket',
                    'color' => 'text-black',
                    'value' => 'PCN CASES OVERVIEW',
                    'label' => 'PCN DASHBOARD AND STATS',
                    'link' => '/ngn-admin/pcn_page'
                ],
            ],
        ])->to('before_content');
        // Add PCN Statistics Row
        Widget::add([
            'type' => 'div',
            'class' => 'row mb-4',
            'content' => [
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-ticket-alt',
                    'color' => 'primary',
                    'value' => $totalPcnCases,
                    'label' => 'Total PCN Cases',
                    'link' => '/ngn-admin/pcn-case',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-exclamation-circle',
                    'color' => 'warning',
                    'value' => $openPcnCases,
                    'label' => 'Open Cases',
                    'link' => '/ngn-admin/pcn-case?has_been_appealed=1&isClosed=0',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-check-circle',
                    'color' => 'success',
                    'value' => $closedPcnCases,
                    'label' => 'Closed Cases',
                    'link' => '/ngn-admin/pcn-case?isClosed=1',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-shield',
                    'color' => 'info',
                    'value' => $policePcnCases,
                    'label' => 'Police PCN Cases',
                    'link' => '/ngn-admin/pcn-case?is_police=1',
                ],
            ],
        ])->to('before_content');

        // Add PCN chart
        Widget::add([
            'type' => 'chart',
            'controller' => \App\Http\Controllers\Admin\Charts\PcnCaseChartController::class,
            'wrapper' => ['class' => 'col-md-12'],
            'content' => [
                'header' => 'PCN Cases Trend',
                'body' => 'Monthly statistics for PCN cases',
            ],
        ])->to('before_content');

        // Add bikes for sale table
        Widget::add([
            'type' => 'card',
            'wrapper' => ['class' => 'col-md-12'],
            'class' => 'card mb-4',
            'content' => [
                'header' => '<h3 class="card-title">Bikes Available for Sale</h3>',
                'body' => view('components.bikes-for-sale-table', ['bikes' => $bikesForSale])->render(),
            ],
        ])->to('before_content');

        // Table for Owned Bikes
        Widget::add([
            'type' => 'card',
            'wrapper' => ['class' => 'col-md-12'],
            'class' => 'card mb-4',
            'content' => [
                'header' => '<h3 class="card-title">List of Our Motorbikes</h3>',
                'body' => view('components.owned-bikes-table', ['bikes' => $ownedBikes])->render(),
            ],
        ])->to('before_content');

        // Add charts in the first row
        Widget::add([
            'type' => 'chart',
            'controller' => \App\Http\Controllers\Admin\Charts\WeeklyUsersChartController::class,
            'wrapper' => ['class' => 'col-md-4'],
        ])->to('before_content');

        Widget::add([
            'type' => 'chart',
            'controller' => \App\Http\Controllers\Admin\Charts\RentingStatusChartController::class,
            'wrapper' => ['class' => 'col-md-4'],
        ])->to('before_content');
    }
@endphp

@section('content')
@endsection
