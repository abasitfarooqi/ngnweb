<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $booking['customer'] }}</td>
                    <td>{{ $booking['booking_date'] }}</td>
                    <td>
                        <span
                            class="badge badge-{{ $booking['status'] === 'Active'
                                ? 'success'
                                : ($booking['status'] === 'Pending'
                                    ? 'warning'
                                    : ($booking['status'] === 'Completed'
                                        ? 'info'
                                        : 'danger')) }}">
                            {{ $booking['status'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
