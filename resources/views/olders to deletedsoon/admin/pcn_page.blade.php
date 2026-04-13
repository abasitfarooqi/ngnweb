@extends(backpack_view('blank'))

@section('after_styles')
    <style>
        .stat-card {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .text-white .stat-label {
            color: rgba(255, 255, 255, 0.9) !important;
        }

        .chart-container {
            position: relative;
            margin-bottom: 30px;
            padding: 20px;
            /* background: white; */
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .amount-details {
            font-size: 14px;
            margin-top: auto;
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-details {
            font-size: 14px;
            margin-top: 8px;
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-highlight {
            font-weight: bold;
            color: #ffc107;
        }
    </style>
@endsection

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">PCN Statistics Dashboard <a href="#PcnListContainer" class="btn btn-sm btn-primary">PCN List (Shortcut)</a></h1>
    </section>

    <section class="content container-fluid animated fadeIn" bp-section="content">
        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3 d-flex">
                
                    <div class="stat-card bg-primary text-white w-100">
                        <a href="/ngn-admin/pcn-case" style="text-decoration: none; color: inherit;"><div class="stat-number">{{ $totalCases }}</div></a>
                        <a href="/ngn-admin/pcn-case" style="text-decoration: none; color: inherit;"><div class="stat-label">Total PCN Cases</div></a>
                    </div>
                
            </div>
            <div class="col-md-3 d-flex">
                
                    <div class="stat-card bg-warning text-white w-100">

                        <a href="/ngn-admin/pcn-case?isClosed=0" style="text-decoration: none; color: inherit;"><div class="stat-number">{{ $openCases }}</div></a>
                        <a href="/ngn-admin/pcn-case?isClosed=0" style="text-decoration: none; color: inherit;"><div class="stat-label">Open Cases</div></a>
                        <a href="/ngn-admin/pcn-case?isClosed=0" style="text-decoration: none; color: inherit;"><div class="stat-details">
                        Outstanding: £{{ number_format($totalFullAmount, 2) }}
                        <br>
                        Under Appeal: {{ $appealedCases }} cases
                    </div></a>
                </div>
            </div>
            <div class="col-md-3 d-flex">
                
                    <div class="stat-card bg-info text-white w-100">
                    <a href="/ngn-admin/pcn-case?has_been_appealed=1&isClosed=0" style="text-decoration: none; color: inherit;"><div class="stat-number">{{ $appealedCases }}</div></a>
                    <a href="/ngn-admin/pcn-case?has_been_appealed=1&isClosed=0" style="text-decoration: none; color: inherit;"><div class="stat-label">Open Cases Under Appeal</div></a>
                    <a href="/ngn-admin/pcn-case?has_been_appealed=1&isClosed=0" style="text-decoration: none; color: inherit;">
                        <div class="stat-details">
                            Police: {{ $appealedStats['police'] }} | Regular: {{ $appealedStats['regular'] }}
                        </div>
                    </a>
                </div>
                </a>
            </div>
            <div class="col-md-3 d-flex">
                
                    <div class="stat-card bg-danger text-white w-100">
                        <a href="/ngn-admin/pcn-case?isClosed=1" style="text-decoration: none; color: inherit;"><div class="stat-number">{{ $cancelledCases }}</div></a>
                        <a href="/ngn-admin/pcn-case?isClosed=1" style="text-decoration: none; color: inherit;"><div class="stat-label">Cancelled Cases</div></a>
                        <a href="/ngn-admin/pcn-case?isClosed=1" style="text-decoration: none; color: inherit;"><div class="stat-details">
                            Police: {{ $cancelledStats['police'] }} | Regular: {{ $cancelledStats['regular'] }}
                        </div></a>
                </div>
                
            </div>
        </div>

        <!-- Status Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card bg-light">
                    <div class="stat-number text-danger">£{{ number_format($totalFullAmount, 2) }}</div>
                    <div class="stat-label">Outstanding Full Amount</div>
                    <div class="stat-details text-muted">
                        For {{ $openCases }} open cases
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-light">
                    <div class="stat-number text-warning">£{{ number_format($totalReducedAmount, 2) }}</div>
                    <div class="stat-label">Outstanding Reduced Amount</div>
                    <div class="stat-details text-muted">
                        For {{ $openCases }} open cases
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-light">
                    <div class="stat-number text-success">{{ $closedCases }}</div>
                    <div class="stat-label">Successfully Closed Cases</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Monthly Trend Chart -->
            <div class="col-md-8">
                <div class="chart-container">
                    <h5>Monthly PCN Cases Trend</h5>
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <!-- Case Status Pie Chart -->
            <div class="col-md-4">
                <div class="chart-container">
                    <h5>Case Status Distribution</h5>
                    <canvas id="statusPieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Additional Charts -->
        <div class="row">
            <div class="col-md-3">
                <div class="chart-container">
                    <h5>Police vs Regular PCN Distribution</h5>
                    <canvas id="policeChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5>Outstanding Amount Comparison</h5>
                    <canvas id="amountChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 10 Vehicles with Most PCNs -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5>Top 10 Vehicles with Most Open PCNs</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Registration Number</th>
                                    <th>Customer Name</th>
                                    <th>Number of Open PCNs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($topVehicles as $index => $vehicle)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $vehicle->motorbike->reg_no ?? 'N/A' }}</td>
                                        <td>
                                            @if ($vehicle->customer)
                                                {{ $vehicle->customer->first_name }} {{ $vehicle->customer->last_name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $vehicle->pcn_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PCN List Table -->
        <div class="row mt-4" id="PcnListContainer">
            <div class="col-12">
                <div class="chart-container">
                    <h5>Penalty Charge Notice Customer List</h5>
                    <div class="d-flex justify-content-start mb-2">
                        <form id="sortForm" method="GET" action="{{ route('page.pcn_page.index') }}">
                            <div class="form-inline">
                                <label for="sort_order" class="mr-2">Sort by Created Date:</label>
                                <select name="sort_order" id="sort_order" class="form-control mr-2">
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                                </select>
                                <!-- <button type="button" class="btn btn-primary" id="sortButton">Sort</button> -->
                            </div>
                        </form>
                    </div>
                    <div id="pcnListContainer" class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Customer Name</th>
                                    <th>Registration Number</th>
                                    <th>PCN Number</th>
                                    <th>Amount (£)</th>
                                    <th>WhatsApp Sent</th>
                                    <th>Last Reminder Sent At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="pcnListBody">
                                @include('livewire.agreements.migrated.admin.partials.pcn_list_body', ['pcnList' => $pcnList])
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('after_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trend Chart
            const monthlyData = @json($monthlyStats);
            new Chart(document.getElementById('monthlyTrendChart'), {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Total Cases',
                        data: monthlyData.map(item => item.total),
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }, {
                        label: 'Closed Cases',
                        data: monthlyData.map(item => item.closed),
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1
                    }, {
                        label: 'Open Cases',
                        data: monthlyData.map(item => item.open),
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Status Pie Chart with Appeals
            new Chart(document.getElementById('statusPieChart'), {
                type: 'pie',
                data: {
                    labels: ['Open (Not Appealed)', 'Open (Under Appeal)', 'Closed', 'Cancelled'],
                    datasets: [{
                        data: [
                            {{ $openCases - $appealedCases }},
                            {{ $appealedCases }},
                            {{ $closedCases }},
                            {{ $cancelledCases }}
                        ],
                        backgroundColor: [
                            'rgb(255, 205, 86)',
                            'rgb(54, 162, 235)',
                            'rgb(75, 192, 192)',
                            'rgb(255, 99, 132)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Police vs Regular Chart
            new Chart(document.getElementById('policeChart'), {
                type: 'pie',
                data: {
                    labels: ['Police PCN', 'Regular PCN'],
                    datasets: [{
                        data: [{{ $policeStats['police'] }}, {{ $policeStats['regular'] }}],
                        backgroundColor: ['rgb(54, 162, 235)', 'rgb(255, 99, 132)']
                    }]
                },
                options: {
                    responsive: true
                }
            });

            // Amount Comparison Chart
            new Chart(document.getElementById('amountChart'), {
                type: 'bar',
                data: {
                    labels: ['Police PCN', 'Regular PCN'],
                    datasets: [{
                        label: 'Outstanding Amount (£)',
                        data: [
                            {{ $outstandingAmounts['police'] }},
                            {{ $outstandingAmounts['regular'] }}
                        ],
                        backgroundColor: ['rgb(54, 162, 235)', 'rgb(255, 99, 132)']
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '£' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return '£' + tooltipItem.yLabel.toLocaleString();
                            }
                        }
                    }
                }
            });

            // AJAX sorting
            $('#sort_order').on('change', function() {
                const sortOrder = $(this).val();
                $.ajax({
                    url: '{{ route('page.pcn_page.index') }}',
                    type: 'GET',
                    data: { sort_order: sortOrder },
                    success: function(response) {
                        $('#pcnListBody').html(response.html);
                    },
                    error: function(xhr) {
                        console.error('Error fetching sorted data:', xhr);
                    }
                });
            });
        });
    </script>
@endsection
