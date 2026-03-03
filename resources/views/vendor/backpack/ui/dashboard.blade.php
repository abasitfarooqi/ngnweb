@extends(backpack_view('blank'))

@section('header')
    <div class="container-fluid">
        <h1 class="h3 mb-0">
            {{ \Carbon\Carbon::parse($toDay ?? now())->format('d/m/Y') }}
            @if (optional(backpack_auth()->user())->first_name)
                — {{ backpack_auth()->user()->first_name }},
            @endif
            {{ __('Welcome to the NGN Administration Dashboard') }}
        </h1>
    </div>
@endsection

@php
    if (backpack_theme_config('show_getting_started')) {
        \Backpack\CRUD\app\Library\Widget::add([
            'type' => 'view',
            'view' => backpack_view('inc.getting_started'),
        ])->to('before_content');
    } else {
        $totalClubMembers = \App\Models\ClubMember::count();
        $activeRentals = \App\Models\RentingBookingItem::whereNull('end_date')->where('is_posted', true)->count();
        $totalCustomers = \App\Models\Customer::count();
        $totalEcommerceCustomers = \App\Models\CustomerAuth::count();

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

        \Backpack\CRUD\app\Library\Widget::add([
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

        \Backpack\CRUD\app\Library\Widget::add([
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
                    'link' => backpack_url('ngn_club'),
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-calendar-day',
                    'color' => 'primary',
                    'value' => $yesterdayVisitors,
                    'label' => "Yesterday's Visits",
                    'link' => backpack_url('ngn_club'),
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-4'],
                    'icon' => 'la la-calendar-check',
                    'color' => 'success',
                    'value' => $todayVisitors,
                    'label' => "Today's Visits",
                    'link' => backpack_url('ngn_club'),
                ],
            ],
        ])->to('before_content');

        $ownedBikes = \App\Models\Motorbike::where('vehicle_profile_id', 1)->get();
        $totalOwnedBikes = $ownedBikes->count();

        $activeFinance = \App\Models\FinanceApplication::where('is_cancelled', false)
            ->where('is_posted', true)
            ->where(function ($q) { $q->where('log_book_sent', false)->orWhereNull('log_book_sent'); })
            ->count();
        $terminatedFinance = \App\Models\FinanceApplication::where('is_cancelled', true)->where('is_posted', true)->count();
        $closedFinance = \App\Models\FinanceApplication::where('is_posted', true)
            ->where(function ($q) { $q->where('log_book_sent', true); })
            ->count();

        $totalRepairs = \App\Models\MotorbikeRepair::count();
        $completedRepairs = \App\Models\MotorbikeRepair::where('is_repaired', true)->count();
        $deliveredRepairs = \App\Models\MotorbikeRepair::where('is_returned', true)->count();

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

        \Backpack\CRUD\app\Library\Widget::add([
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

        $bikesForSale = \App\Models\MotorbikesSale::join('motorbikes', 'motorbikes_sale.motorbike_id', '=', 'motorbikes.id')
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

        \Backpack\CRUD\app\Library\Widget::add([
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
                    'link' => backpack_url('club-member'),
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-motorcycle',
                    'color' => 'success',
                    'value' => $activeRentals,
                    'label' => 'Active Rentals',
                    'link' => backpack_url('active_renting'),
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-id-card',
                    'color' => 'info',
                    'value' => $totalCustomers,
                    'label' => 'Regular Customers',
                    'link' => backpack_url('customer'),
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-shopping-cart',
                    'color' => 'warning',
                    'value' => $totalEcommerceCustomers,
                    'label' => 'E-commerce Subscribers',
                    'link' => backpack_url('customer'),
                ],
            ],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
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
                    'link' => backpack_url('finance-application').'?log_book_sent=0',
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

        \Backpack\CRUD\app\Library\Widget::add([
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

        \Backpack\CRUD\app\Library\Widget::add([
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

        $totalPcnCases = \App\Models\PcnCase::count();
        $openPcnCases = \App\Models\PcnCase::where('isClosed', false)->count();
        $closedPcnCases = \App\Models\PcnCase::where('isClosed', true)->count();
        $policePcnCases = \App\Models\PcnCase::where('is_police', true)->count();

        \Backpack\CRUD\app\Library\Widget::add([
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
                    'link' => backpack_url('pcn_page'),
                ],
            ],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
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
                    'link' => backpack_url('pcn-case'),
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-exclamation-circle',
                    'color' => 'warning',
                    'value' => $openPcnCases,
                    'label' => 'Open Cases',
                    'link' => backpack_url('pcn-case').'?has_been_appealed=1&isClosed=0',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-check-circle',
                    'color' => 'success',
                    'value' => $closedPcnCases,
                    'label' => 'Closed Cases',
                    'link' => backpack_url('pcn-case').'?isClosed=1',
                ],
                [
                    'type' => 'view',
                    'view' => 'components.stat-card',
                    'wrapper' => ['class' => 'col-md-3'],
                    'icon' => 'la la-shield',
                    'color' => 'info',
                    'value' => $policePcnCases,
                    'label' => 'Police PCN Cases',
                    'link' => backpack_url('pcn-case').'?is_police=1',
                ],
            ],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
            'type' => 'chart',
            'controller' => \App\Http\Controllers\Admin\Charts\PcnCaseChartController::class,
            'wrapper' => ['class' => 'col-md-12'],
            'content' => [
                'header' => 'PCN Cases Trend',
                'body' => 'Monthly statistics for PCN cases',
            ],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
            'type' => 'card',
            'wrapper' => ['class' => 'col-md-12'],
            'class' => 'card mb-4',
            'content' => [
                'header' => '<h3 class="card-title">Bikes Available for Sale</h3>',
                'body' => view('components.bikes-for-sale-table', ['bikes' => $bikesForSale])->render(),
            ],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
            'type' => 'card',
            'wrapper' => ['class' => 'col-md-12'],
            'class' => 'card mb-4',
            'content' => [
                'header' => '<h3 class="card-title">List of Our Motorbikes</h3>',
                'body' => view('components.owned-bikes-table', ['bikes' => $ownedBikes])->render(),
            ],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
            'type' => 'chart',
            'controller' => \App\Http\Controllers\Admin\Charts\WeeklyUsersChartController::class,
            'wrapper' => ['class' => 'col-md-4'],
        ])->to('before_content');

        \Backpack\CRUD\app\Library\Widget::add([
            'type' => 'chart',
            'controller' => \App\Http\Controllers\Admin\Charts\RentingStatusChartController::class,
            'wrapper' => ['class' => 'col-md-4'],
        ])->to('before_content');
    }
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <h2 class="h4 mb-3"><strong>{{ $count ?? 0 }} Rental Payments</strong></h2>
            <table class="table table-bordered table-sm mb-4">
                <thead>
                    <tr>
                        <th>Outstanding</th>
                        <th>Rentals</th>
                        <th>Deposits</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-danger"><strong>£{{ $rpayments ?? '0.00' }}</strong></td>
                        <td class="text-danger"><a href="{{ backpack_url('rentalpayments') }}">£{{ $rrpayments ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('outstanding-deposits') }}">£{{ $ddpayments ?? 0 }}</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <canvas id="ngn-rentals-chart" height="100"></canvas>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="table table-bordered table-sm table-responsive">
                <thead>
                    <tr>
                        <th>For Rent</th>
                        <th>Rented</th>
                        <th>For Sale</th>
                        <th>Sold</th>
                        <th>Repairs</th>
                        <th>Cat B</th>
                        <th>Claim in Progress</th>
                        <th>Impounded</th>
                        <th>Accident</th>
                        <th>Missing</th>
                        <th>Stolen</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-danger"><a href="{{ backpack_url('is-for-rent') }}">{{ $forRentCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('is-rented') }}">{{ $rentedCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('is-for-sale') }}">{{ $forSaleCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('is-sold') }}">{{ $soldCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('is-for-repairs') }}">{{ $repairsCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('cat-b') }}">{{ $catBCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('claim-in-progress') }}">{{ $claimInProgressCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('impounded') }}">{{ $impoundedCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('accident') }}">{{ $accidentCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('missing') }}">{{ $missingCount ?? 0 }}</a></td>
                        <td class="text-danger"><a href="{{ backpack_url('stolen') }}">{{ $stolenCount ?? 0 }}</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h3 class="h5"><strong>TAX &amp; MOT Due Soon</strong></h3>
            <div class="row">
                <div class="col-md-6">
                    <h4 class="h6">TAX</h4>
                    @if (isset($taxDue) && $taxDue->count() > 0)
                        <ul class="list-unstyled small">
                            @foreach ($taxDue->take(10) as $m)
                                <li>{{ $m->registration ?? $m->id }} — {{ $m->tax_due_date ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="small text-muted">None in next 10 days.</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h4 class="h6">MOT</h4>
                    @if (isset($motDue) && $motDue->count() > 0)
                        <ul class="list-unstyled small">
                            @foreach ($motDue->take(10) as $m)
                                <li>{{ $m->registration ?? $m->id }} — {{ $m->mot_expiry_date ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="small text-muted">None in next 10 days.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            var rentaldata = @json(isset($rentaldata) ? $rentaldata : array_fill(0, 11, 0));
            var ctx = document.getElementById('ngn-rentals-chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['For Rent', 'Rented', 'For Sale', 'Sold', 'Repairs', 'Cat B', 'CIP', 'Impounded', 'Accident', 'Missing', 'Stolen'],
                        datasets: [{ label: 'NGN Motorcycle Fleet Stats', data: rentaldata, borderWidth: 1 }]
                    },
                    options: { scales: { y: { beginAtZero: true } } }
                });
            }
        })();
    </script>
@endpush
