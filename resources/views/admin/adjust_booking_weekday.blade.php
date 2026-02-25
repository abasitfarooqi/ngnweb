@extends(backpack_view('blank'))

@section('content')
<div class="row">
    <div class="col">
        <h1>Adjust Booking Weekday</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Current Start Date</th>
                    <th>Change Weekday</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>{{ $booking->customer }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->start_date)->format('l, d M Y') }}</td>
                        <td>
                            <form method="POST" action="{{ url('ngn-admin/adjust-booking-weekday/'.$booking->id) }}" class="d-flex align-items-center" style="gap: 0.5rem;">
                                @csrf
                                @method('PUT')
                                <select name="new_weekday" class="form-control" style="min-width:180px;width: auto; display: inline-block;">
                                    @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $index => $day)
                                        <option value="{{ $index }}"
                                            {{ $index == 0 ? 'disabled' : '' }}
                                            {{ \Carbon\Carbon::parse($booking->start_date)->dayOfWeek == $index ? 'selected' : '' }}>
                                            {{ $day }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary" style="margin-left: 0.5rem;">Update</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
