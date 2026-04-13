@extends('layouts.admin')

@section('content')
    <div class="main-content">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="text-primary">Booking Details</h2>
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="ngn_customer_id" class="form-label">Customer</label>
                                        <select id="ngn_customer_id" name="customer_id" class="form-select">
                                            <option value="">All Customers</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ngn_motorbike_id" class="form-label">Motorbike</label>
                                        <select id="ngn_motorbike_id" name="motorbike_id" class="form-select">
                                            <option value="">All Motorbikes</option>
                                            @foreach($motorbikes as $bike)
                                                <option value="{{ $bike->id }}">{{ $bike->reg_no }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ngn_westatus" class="form-label">Status</label>
                                        <select id="ngn_westatus" name="westatus" class="form-select">
                                            <option value="">All Statuses</option>
                                            <option value="ONGOING">Ongoing</option>
                                            <option value="ENDED">Ended</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <label for="ngn_start_date" class="form-label">Start Date</label>
                                        <input type="date" id="ngn_start_date" name="start_date" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ngn_end_date" class="form-label">End Date</label>
                                        <input type="date" id="ngn_end_date" name="end_date" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="ngn_state" class="form-label">Booking State</label>
                                        <select id="ngn_state" name="state" class="form-select">
                                            <option value="">All States</option>
                                            <option value="Completed & Issued">Completed & Issued</option>
                                            <option value="Completed">Completed</option>
                                            <option value="DRAFT">DRAFT</option>
                                            <option value="Awaiting Documents & Payment">Awaiting Documents & Payment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" id="ngn_filterBtn" class="btn btn-primary me-2">Apply Filters</button>
                                    <button type="button" id="ngn_resetBtn" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered  table-hover">
                                    <thead class="table-danger">
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Customer Name</th>
                                            <th>Booking Date</th>
                                            <th>Next Due Date</th>
                                            <th>Status</th>
                                            <th>Motorbike Reg No</th>
                                            <th>Weekly Rent</th>
                                            <th>End Date</th>
                                            <th>Customer Phone</th>
                                            <th>Customer Email</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ngn_bookingData">
                                        @foreach($bookingHistory as $booking)
                                            <tr>
                                                <td>{{ $booking->BOOKING_ID }}</td>
                                                <td>{{ $booking->FIRST_NAME }} {{ $booking->LAST_NAME }}</td>
                                                <td>{{ $booking->BOOKING_DATE }}</td>
                                                <td>{{ $booking->NEXT_DUE_DATE }}</td>
                                                <td>{{ $booking->RBSTATE }}</td>
                                                <td>{{ $booking->REG_NO }}</td>
                                                <td>{{ $booking->WEEKLY_RENT }}</td>
                                                <td>{{ $booking->END_DATE ?? 'N/A' }}</td>
                                                <td>{{ $booking->PHONE }}</td>
                                                <td>{{ $booking->EMAIL }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function applyFilters() {
            const customer_id = $('#ngn_customer_id').val();
            const motorbike_id = $('#ngn_motorbike_id').val();
            const status = $('#ngn_westatus').val();
            const state = $('#ngn_state').val();
            const start_date = $('#ngn_start_date').val();
            const end_date = $('#ngn_end_date').val();

            $.ajax({
                url: "{{ route('admin.renting.bookings.history') }}",
                type: "GET",
                data: {
                    customer_id: customer_id,
                    motorbike_id: motorbike_id,
                    westatus: status,
                    state: state,
                    start_date: start_date,
                    end_date: end_date,
                },
                success: function(data) {
                    let rows = '';
                    data.forEach(function(booking) {
                        rows += `<tr>
                            <td>${booking.BOOKING_ID}</td>
                            <td>${booking.FIRST_NAME} ${booking.LAST_NAME}</td>
                            <td>${booking.BOOKING_DATE}</td>
                            <td>${booking.NEXT_DUE_DATE}</td>
                            <td>${booking.RBSTATE}</td>
                            <td>${booking.REG_NO}</td>
                            <td>${booking.WEEKLY_RENT}</td>
                            <td>${booking.END_DATE ?? 'N/A'}</td>
                            <td>${booking.PHONE}</td>
                            <td>${booking.EMAIL}</td>
                        </tr>`;
                    });
                    $('#ngn_bookingData').html(rows);
                },
                error: function(xhr, status, error) {
                    alert('An error occurred. Please try again.');
                }
            });
        }

        $('#ngn_customer_id, #ngn_motorbike_id, #ngn_westatus, #ngn_state, #ngn_start_date, #ngn_end_date').on('change keyup', function() {
            applyFilters();
        });

        $('#ngn_resetBtn').on('click', function() {
            $('#ngn_customer_id, #ngn_motorbike_id, #ngn_westatus, #ngn_state, #ngn_start_date, #ngn_end_date').val('');
            applyFilters();
        });
    </script>

    <style>
        /* Custom styles */
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .btn-primary {
            background-color: #9d141d;
            border: none;
        }
        .btn-primary:hover {
            background-color: #ac111b;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        *{
            color: black;
        }
    </style>
@endsection
