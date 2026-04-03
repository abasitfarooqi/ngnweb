@extends(backpack_view('blank'))

@push('after_styles')
    <style>
        .small-box .inner h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 10px 0;
            white-space: nowrap;
            padding: 0;
        }

        .small-box .inner p {
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .small-box {
            padding: 20px;
            border-radius: 0.25rem;
            transition: transform 0.3s;
        }

        .small-box:hover {
            transform: translateY(-2px);
        }

        /* Swap VRM and Make/Model styles */
        .vehicle-info .reg-no {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
        }

        .vehicle-info .make-model {
            font-size: 0.875rem;
            color: #718096;
        }

        @@media (max-width: 767.98px) {
            .col-md-3 {
                width: 50%;
            }

            .small-box .inner h3 {
                font-size: 1.8rem;
            }

            .small-box .inner p {
                font-size: 0.9rem;
            }
        }
    </style>
@endpush

@section('content')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none"
        bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">Active Rentals</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Overview of all active rentals</p>
    </section>

    <section class="content container-fluid animated fadeIn" bp-section="content">
        <!-- Stats Cards Row -->
        <div class="row mb-4">

            <div class="col-md-3">
                <div class="small-box bg-info">
                    <div class="inner text-center">
                        <h3>{{ $stats['active_rentals'] }}</h3>
                        <p>Active Rentals</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success">
                    <div class="inner text-center">
                        <h3>£{{ number_format($stats['weekly_revenue'], 0) }}</h3>
                        <p>Weekly Revenue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning">
                    <div class="inner text-center">
                        <h3>£{{ number_format($stats['unpaid_invoices'], 0) }}</h3>
                        <p>Unpaid Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner text-center">
                        <h3>{{ $stats['due_payments'] }}</h3>
                        <p>Due Payments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Rentals Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Active Rentals</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm">
                                <input type="text" id="tableSearch" class="form-control float-right"
                                    placeholder="Search...">
                                <select id="statusFilter" class="form-control float-right ml-2">
                                    <option value="">All</option>
                                    <option value="payment_due">Payment Due</option>
                                    <option value="active">Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Motorcycle</th>
                                    <th>Weekly Rent</th>
                                    <th>Start Date</th>
                                    <th>Next Due Date</th>
                                    <th>Deposit</th>
                                    <th>Outstanding</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeBookings as $booking)
                                    @foreach ($booking->rentingBookingItems->whereNull('end_date') as $item)
                                        @php
                                            // Get the next unpaid invoice (including future)
                                            $nextUnpaidInvoice = $booking->bookingInvoices
                                                ->where('is_paid', false)
                                                ->sortBy('invoice_date')
                                                ->first();

                                            // Get current or past due unpaid invoice
                                            $currentDueInvoice = $booking->bookingInvoices
                                                ->where('is_paid', false)
                                                ->where('invoice_date', '<=', now())
                                                ->sortByDesc('invoice_date')
                                                ->first();

                                            // Calculate outstanding amount (excluding future invoices)
                                            $outstandingAmount = $booking->bookingInvoices
                                                ->where('is_paid', false)
                                                ->where('invoice_date', '<=', now())
                                                ->sum('amount');
                                        @endphp

                                        <tr class="{{ $currentDueInvoice ? 'payment_due' : ($outstandingAmount > 0 ? 'outstanding' : 'active') }}">
                                            <td>{{ $booking->id }}</td>
                                            <td>
                                                <strong>{{ $booking->customer->full_name }}</strong><br>
                                                <small class="text-muted">{{ $booking->customer->phone }}</small>
                                                @if ($booking->customer->license_expiry_date && $booking->customer->license_expiry_date <= now())
                                                    <br><span class="badge badge-danger">License Expired</span>
                                                @endif
                                            </td>
                                            <td class="vehicle-info">
                                                <div class="reg-no">{{ $item->motorbike->reg_no }}</div>
                                                <div class="make-model">{{ $item->motorbike->make }}
                                                    {{ $item->motorbike->model }}</div>
                                            </td>
                                            <td>£{{ number_format($item->weekly_rent, 2) }}</td>
                                            <td>{{ $item->start_date->format('d M Y') }}</td>
                                            <td>
                                                @if ($nextUnpaidInvoice)
                                                    <span
                                                        class="{{ $nextUnpaidInvoice->invoice_date <= now() ? 'text-danger' : '' }}">
                                                        {{ $nextUnpaidInvoice->invoice_date->format('d M Y') }}
                                                        @if ($nextUnpaidInvoice->invoice_date > now())
                                                            <small class="text-muted">(Future)</small>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="text-muted">No Invoice Due</span>
                                                @endif
                                            </td>
                                            <td>£{{ number_format($booking->deposit, 0) }}</td>
                                            <td>
                                                <span
                                                    class="{{ $outstandingAmount > 0 ? 'text-danger' : 'text-success' }}">
                                                    £{{ number_format($outstandingAmount, 0) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($currentDueInvoice)
                                                    <span class="badge badge-danger">Payment Due</span>
                                                @elseif($outstandingAmount > 0)
                                                    <span class="badge badge-warning">Outstanding Balance</span>
                                                @else
                                                    <span class="badge badge-success">Active</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('after_scripts')
        <script>
            $(document).ready(function() {
                $("#tableSearch").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("table tbody tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });

                $("#statusFilter").on("change", function() {
                    var selectedValue = $(this).val();
                    $("table tbody tr").show(); // Show all rows initially
                    if (selectedValue === "payment_due") {
                        $("table tbody tr:not(.payment_due)").hide(); // Hide non-payment due rows
                    } else if (selectedValue === "active") {
                        $("table tbody tr:not(.active)").hide(); // Hide non-active rows
                    }
                });
            });
        </script>
    @endpush
@endsection
