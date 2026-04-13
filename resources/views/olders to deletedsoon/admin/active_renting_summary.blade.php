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
                    <th>Booking Summary Link</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td>{{ $booking->customer }}</td>
                        <td>
                            <a href="{{ url('/admin/renting/bookings/' . $booking->id . '/summary_view') }}" target="_blank">
                                View summary
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
