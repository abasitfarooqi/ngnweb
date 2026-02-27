@extends(backpack_view('blank'))

@section('after_styles')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <style>
        /* Custom pagination styles */
        .custom-pagination {
            display: flex;
            gap: 5px;
            align-items: center;
            justify-content: center;
        }

        .custom-pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.4;
            border-radius: 0.2rem;
            min-width: 32px;
            text-align: center;
            border: 1px solid #dee2e6;
            color: #4e73df;
            text-decoration: none;
            background: white;
        }

        .custom-pagination .page-link:hover {
            background-color: #e9ecef;
        }

        .custom-pagination .active {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .custom-pagination .disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .custom-pagination .ellipsis {
            padding: 0.25rem 0.5rem;
            color: #6c757d;
        }
    </style>
@endsection

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">NGN CLUB STATISTICS</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Club Members Statistics</p>
    </section>
    
    <section class="content container-fluid animated fadeIn" bp-section="content">
        <!-- Searchable Club Members List -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Search Club Members</h5>
                        <div class="search-form">
                            <form action="{{ url(config('backpack.base.route_prefix') . '/ngn_club') }}" method="GET"
                                class="d-flex align-items-center gap-2">
                                <select name="search_field" class="form-select" style="width: 150px;">
                                    <option value="all" {{ $searchField === 'all' ? 'selected' : '' }}>All Fields
                                    </option>
                                    <option value="year" {{ $searchField === 'year' ? 'selected' : '' }}>Year</option>
                                    <option value="vrm" {{ $searchField === 'vrm' ? 'selected' : '' }}>VRM</option>
                                    <option value="full_name" {{ $searchField === 'full_name' ? 'selected' : '' }}>Name
                                    </option>
                                    <option value="make" {{ $searchField === 'make' ? 'selected' : '' }}>Make</option>
                                    <option value="model" {{ $searchField === 'model' ? 'selected' : '' }}>Model</option>
                                    <option value="phone" {{ $searchField === 'phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="email" {{ $searchField === 'email' ? 'selected' : '' }}>Email</option>
                                </select>
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" name="search" class="form-control" placeholder="Search..."
                                        value="{{ $search }}">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Member Details</th>
                                        <th>Vehicle Information</th>
                                        <th>Contact Info</th>
                                        <th>Visit Statistics</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($searchableMembers as $index => $member)
                                        <tr>
                                            <td>{{ ($searchableMembers->currentPage() - 1) * $searchableMembers->perPage() + $index + 1 }}
                                            </td>
                                            <td>{{ $member->full_name }}</td>
                                            <td>
                                                <div><strong>Year:</strong> {{ $member->year ?: 'N/A' }}</div>
                                                <div><strong>Make:</strong> {{ $member->make ?: 'N/A' }}</div>
                                                <div><strong>Model:</strong> {{ $member->model ?: 'N/A' }}</div>
                                                <div><strong>VRM:</strong> {{ $member->vrm ?: 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div><strong>Email:</strong> {{ $member->email }}</div>
                                                <div><strong>Phone:</strong> {{ $member->phone ?: 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div><strong>Total Visits:</strong> {{ $member->total_visits }}</div>
                                                <div><strong>Total Spent:</strong>
                                                    £{{ number_format($member->total_spent, 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                @if ($searchableMembers->hasPages())
                                    <div class="custom-pagination">
                                        @if ($searchableMembers->onFirstPage())
                                            <span class="page-link disabled">←</span>
                                        @else
                                            <a href="{{ $searchableMembers->previousPageUrl() }}&search={{ $search }}&search_field={{ $searchField }}"
                                                class="page-link">←</a>
                                        @endif

                                        @php
                                            $currentPage = $searchableMembers->currentPage();
                                            $lastPage = $searchableMembers->lastPage();
                                            $delta = 2;
                                        @endphp

                                        <a href="{{ $searchableMembers->url(1) }}&search={{ $search }}&search_field={{ $searchField }}"
                                            class="page-link {{ $currentPage == 1 ? 'active' : '' }}">1</a>

                                        @if ($currentPage > $delta + 2)
                                            <span class="ellipsis">...</span>
                                        @endif

                                        @foreach (range(max(2, $currentPage - $delta), min($lastPage - 1, $currentPage + $delta)) as $i)
                                            @if ($i > 1 && $i < $lastPage)
                                                <a href="{{ $searchableMembers->url($i) }}&search={{ $search }}&search_field={{ $searchField }}"
                                                    class="page-link {{ $currentPage == $i ? 'active' : '' }}">{{ $i }}</a>
                                            @endif
                                        @endforeach

                                        @if ($currentPage < $lastPage - ($delta + 1))
                                            <span class="ellipsis">...</span>
                                        @endif

                                        @if ($lastPage > 1)
                                            <a href="{{ $searchableMembers->url($lastPage) }}&search={{ $search }}&search_field={{ $searchField }}"
                                                class="page-link {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
                                        @endif

                                        @if ($searchableMembers->hasMorePages())
                                            <a href="{{ $searchableMembers->nextPageUrl() }}&search={{ $search }}&search_field={{ $searchField }}"
                                                class="page-link">→</a>
                                        @else
                                            <span class="page-link disabled">→</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Monthly Club Member Registration Statistics
                    </div>
                    <div class="card-body">
                        <canvas id="membersChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        Visit Statistics (Last 7 Days)
                    </div>
                    <div class="card-body">
                        <canvas id="branchVisitsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 20 Monthly Visitors -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Top 20 Visitors for {{ $selectedMonth }}</h5>
                        <div class="month-navigation">
                            <form action="{{ url(config('backpack.base.route_prefix') . '/ngn_club') }}" method="GET"
                                class="d-flex align-items-center">
                                <select name="month" class="form-select me-2" onchange="this.form.submit()">
                                    @foreach ($availableMonths as $month)
                                        <option value="{{ $month }}"
                                            {{ $month === $selectedMonth ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Member Name</th>
                                        <th>Contact Info</th>
                                        <th>Visit Count</th>
                                        <th>Total Spent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topVisitors as $index => $visitor)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $visitor->full_name }}</td>
                                            <td>
                                                <div>Email: {{ $visitor->email }}</div>
                                                <div>Phone: {{ $visitor->phone }}</div>
                                            </td>
                                            <td>{{ $visitor->visit_count }}</td>
                                            <td>£{{ number_format($visitor->total_spent, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                @if ($topVisitors->hasPages())
                                    <div class="custom-pagination">
                                        @if ($topVisitors->onFirstPage())
                                            <span class="page-link disabled">←</span>
                                        @else
                                            <a href="{{ $topVisitors->previousPageUrl() }}&month={{ $selectedMonth }}"
                                                class="page-link">←</a>
                                        @endif

                                        @php
                                            $currentPage = $topVisitors->currentPage();
                                            $lastPage = $topVisitors->lastPage();
                                            $delta = 2; // Number of pages to show before and after current page
                                        @endphp

                                        {{-- First Page --}}
                                        <a href="{{ $topVisitors->url(1) }}&month={{ $selectedMonth }}"
                                            class="page-link {{ $currentPage == 1 ? 'active' : '' }}">1</a>

                                        {{-- Left Ellipsis --}}
                                        @if ($currentPage > $delta + 2)
                                            <span class="ellipsis">...</span>
                                        @endif

                                        {{-- Page Numbers --}}
                                        @foreach (range(max(2, $currentPage - $delta), min($lastPage - 1, $currentPage + $delta)) as $i)
                                            @if ($i > 1 && $i < $lastPage)
                                                <a href="{{ $topVisitors->url($i) }}&month={{ $selectedMonth }}"
                                                    class="page-link {{ $currentPage == $i ? 'active' : '' }}">{{ $i }}</a>
                                            @endif
                                        @endforeach

                                        {{-- Right Ellipsis --}}
                                        @if ($currentPage < $lastPage - ($delta + 1))
                                            <span class="ellipsis">...</span>
                                        @endif

                                        {{-- Last Page --}}
                                        @if ($lastPage > 1)
                                            <a href="{{ $topVisitors->url($lastPage) }}&month={{ $selectedMonth }}"
                                                class="page-link {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
                                        @endif

                                        @if ($topVisitors->hasMorePages())
                                            <a href="{{ $topVisitors->nextPageUrl() }}&month={{ $selectedMonth }}"
                                                class="page-link">→</a>
                                        @else
                                            <span class="page-link disabled">→</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Club Members by Vehicle Year -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Club Members by Vehicle Year</h5>
                        <div class="year-navigation">
                            <form action="{{ url(config('backpack.base.route_prefix') . '/ngn_club') }}" method="GET"
                                class="d-flex align-items-center">
                                <select name="year" class="form-select me-2" onchange="this.form.submit()">
                                    <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>All Years
                                    </option>
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}"
                                            {{ $year === $selectedYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Member Details</th>
                                        <th>Vehicle Information</th>
                                        <th>Contact Info</th>
                                        <th>Visit Statistics</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($membersByYear as $index => $member)
                                        <tr>
                                            <td>{{ ($membersByYear->currentPage() - 1) * $membersByYear->perPage() + $index + 1 }}
                                            </td>
                                            <td>{{ $member->full_name }}</td>
                                            <td>
                                                <div><strong>Year:</strong> {{ $member->year ?: 'N/A' }}</div>
                                                <div><strong>Make:</strong> {{ $member->make ?: 'N/A' }}</div>
                                                <div><strong>Model:</strong> {{ $member->model ?: 'N/A' }}</div>
                                                <div><strong>VRM:</strong> {{ $member->vrm ?: 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div><strong>Email:</strong> {{ $member->email }}</div>
                                                <div><strong>Phone:</strong> {{ $member->phone ?: 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <div><strong>Total Visits:</strong> {{ $member->total_visits }}</div>
                                                <div><strong>Total Spent:</strong>
                                                    £{{ number_format($member->total_spent, 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                @if ($membersByYear->hasPages())
                                    <div class="custom-pagination">
                                        @if ($membersByYear->onFirstPage())
                                            <span class="page-link disabled">←</span>
                                        @else
                                            <a href="{{ $membersByYear->previousPageUrl() }}&year={{ $selectedYear }}"
                                                class="page-link">←</a>
                                        @endif

                                        @php
                                            $currentPage = $membersByYear->currentPage();
                                            $lastPage = $membersByYear->lastPage();
                                            $delta = 2;
                                        @endphp

                                        <a href="{{ $membersByYear->url(1) }}&year={{ $selectedYear }}"
                                            class="page-link {{ $currentPage == 1 ? 'active' : '' }}">1</a>

                                        @if ($currentPage > $delta + 2)
                                            <span class="ellipsis">...</span>
                                        @endif

                                        @foreach (range(max(2, $currentPage - $delta), min($lastPage - 1, $currentPage + $delta)) as $i)
                                            @if ($i > 1 && $i < $lastPage)
                                                <a href="{{ $membersByYear->url($i) }}&year={{ $selectedYear }}"
                                                    class="page-link {{ $currentPage == $i ? 'active' : '' }}">{{ $i }}</a>
                                            @endif
                                        @endforeach

                                        @if ($currentPage < $lastPage - ($delta + 1))
                                            <span class="ellipsis">...</span>
                                        @endif

                                        @if ($lastPage > 1)
                                            <a href="{{ $membersByYear->url($lastPage) }}&year={{ $selectedYear }}"
                                                class="page-link {{ $currentPage == $lastPage ? 'active' : '' }}">{{ $lastPage }}</a>
                                        @endif

                                        @if ($membersByYear->hasMorePages())
                                            <a href="{{ $membersByYear->nextPageUrl() }}&year={{ $selectedYear }}"
                                                class="page-link">→</a>
                                        @else
                                            <span class="page-link disabled">→</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('membersChart').getContext('2d');
            const labels = {!! $chartLabels !!};
            const currentMonth = '{{ $currentMonth }}';

            const backgroundColor = labels.map(month =>
                month === currentMonth ?
                'rgba(255, 99, 132, 0.5)' // Red color for current month
                :
                'rgba(54, 162, 235, 0.5)' // Blue color for other months
            );

            const borderColor = labels.map(month =>
                month === currentMonth ?
                'rgba(255, 99, 132, 1)' // Red border for current month
                :
                'rgba(54, 162, 235, 1)' // Blue border for other months
            );

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of New Members',
                        data: {!! $chartData !!},
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItems) {
                                    const month = tooltipItems[0].label;
                                    return month === currentMonth ?
                                        month + ' (Current Month)' :
                                        month;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            formatter: function(value) {
                                return value.toLocaleString();
                            },
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });

        // Branch visits chart
        document.addEventListener('DOMContentLoaded', function() {
            const dates = {!! $visitDates !!};
            const branchData = {!! $branchData !!};

            console.log('Dates:', dates);
            console.log('Branch Data:', branchData);

            // Calculate and log totals for each date
            dates.forEach((date, index) => {
                let total = 0;
                Object.values(branchData).forEach(branchCounts => {
                    total += branchCounts[index];
                });
                console.log(`Total for ${date}:`, total);
            });

            const colors = [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(153, 102, 255, 0.5)',
                'rgba(255, 159, 64, 0.5)'
            ];

            const datasets = Object.entries(branchData).map(([branch, data], index) => {
                console.log(`Branch ${branch} data:`, data);
                let sum = data.reduce((a, b) => a + b, 0);
                console.log(`Total for branch ${branch}:`, sum);
                return {
                    label: `${branch}`,
                    data: data,
                    backgroundColor: colors[index % colors.length],
                    borderColor: colors[index % colors.length].replace('0.5', '1'),
                    borderWidth: 1
                };
            });

            const visitsCtx = document.getElementById('branchVisitsChart').getContext('2d');
            new Chart(visitsCtx, {
                type: 'bar',
                data: {
                    labels: dates,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'center',
                            align: 'center',
                            formatter: function(value) {
                                return value > 0 ? value : '';
                            },
                            color: 'white',
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        });
    </script>
@endsection
