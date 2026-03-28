<div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-bg-info">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">{{ $stats['active_rentals'] }}</div>
                    <div class="text-uppercase small">Active Rentals</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">GBP {{ number_format($stats['weekly_revenue'], 2) }}</div>
                    <div class="text-uppercase small">Weekly Revenue</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">{{ $stats['due_payments'] }}</div>
                    <div class="text-uppercase small">Due Payments</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger">
                <div class="card-body text-center">
                    <div class="fs-2 fw-bold">GBP {{ number_format($stats['unpaid_invoices'], 2) }}</div>
                    <div class="text-uppercase small">Outstanding</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Search</label>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Booking ID, customer, email, phone, reg, make or model"
                        wire:model.live.debounce.300ms="search"
                    >
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="all">All active bookings</option>
                        <option value="payment_due">Payment due</option>
                        <option value="active">Fully up to date</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Rows per page</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Weekly Rent</th>
                        <th>Start</th>
                        <th>Next Due</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        @php
                            $nextDue = $row->next_unpaid_invoice_date ? \Illuminate\Support\Carbon::parse($row->next_unpaid_invoice_date) : null;
                            $licenseExpired = $row->license_expiry_date && \Illuminate\Support\Carbon::parse($row->license_expiry_date)->isPast();
                            $hasOutstanding = (float) $row->outstanding_amount > 0;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">#{{ $row->booking_id }}</div>
                                <div class="text-muted small">{{ $row->booking_state }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $row->first_name }} {{ $row->last_name }}</div>
                                <div class="small text-muted">{{ $row->phone ?: 'No phone' }}</div>
                                <div class="small text-muted">{{ $row->email ?: 'No email' }}</div>
                                @if ($licenseExpired)
                                    <span class="badge bg-danger mt-1">License expired</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $row->reg_no }}</div>
                                <div class="small text-muted">{{ $row->make }} {{ $row->model }}</div>
                            </td>
                            <td>GBP {{ number_format((float) $row->weekly_rent, 2) }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($row->item_start_date)->format('d M Y') }}</td>
                            <td>
                                @if ($nextDue)
                                    <span class="{{ $nextDue->isPast() || $nextDue->isToday() ? 'text-danger fw-semibold' : '' }}">
                                        {{ $nextDue->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">No unpaid invoice</span>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $hasOutstanding ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
                                    GBP {{ number_format((float) $row->outstanding_amount, 2) }}
                                </span>
                            </td>
                            <td>
                                @if ($hasOutstanding)
                                    <span class="badge bg-danger">Payment due</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('page.rental_operations.booking_details', ['bookingId' => $row->booking_id]) }}" class="btn btn-sm btn-primary">
                                        Details
                                    </a>
                                    <a href="{{ backpack_url('ngn-renting-booking/' . $row->booking_id . '/show') }}" class="btn btn-sm btn-outline-primary">
                                        Booking
                                    </a>
                                    <a href="{{ backpack_url('customer/' . $row->customer_id . '/show') }}" class="btn btn-sm btn-outline-secondary">
                                        Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No active rental rows matched the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rows->hasPages())
            <div class="card-footer">
                {{ $rows->links() }}
            </div>
        @endif
    </div>
</div>
