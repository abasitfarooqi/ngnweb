@extends('layouts.admin')

@section('content')
<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            
   
            <h3>Amend Booking Start Date</h3>
            <div class="mb-3">
                <input type="text" id="search-bar" class="form-control" placeholder="Search by Customer Name or Booking ID">
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>State</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $booking->id }}</td>
                            <td>{{ $booking->customer ? $booking->customer->first_name . ' ' . $booking->customer->last_name : 'No Customer' }}</td>
                            <td>
                                <input type="datetime-local" class="form-control form-control-sm start-date" value="{{ $booking->start_date->format('Y-m-d H:i') }}" data-booking-id="{{ $booking->id }}" disabled>
                            </td>
                            <td>{{ $booking->due_date }}</td>
                            <td>{{ $booking->state }}</td>
                            <td>
                                <button class="btn btn-sm btn-secondary edit-start-date" data-booking-id="{{ $booking->id }}">Edit</button>
                                <button class="btn btn-sm btn-primary update-start-date d-none" data-booking-id="{{ $booking->id }}">Update</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $('#search-bar').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $(document).on('click', '.edit-start-date', function() {
        var row = $(this).closest('tr');
        var dateInput = row.find('.start-date');
        var updateBtn = row.find('.update-start-date');
        dateInput.prop('disabled', false).focus();
        updateBtn.removeClass('d-none');
        $(this).addClass('d-none');
    });

    $(document).on('click', '.update-start-date', function() {
        const button = $(this);
        const row = button.closest('tr');
        const bookingId = button.data('booking-id');
        const newStartDate = row.find('.start-date').val();

        if (!newStartDate) {
            alert('Please select a start date');
            return;
        }

        $.ajax({
            url: '{{ route("admin.renting.bookings.updateStartDate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                booking_id: bookingId,
                new_start_date: newStartDate
            },
            success: function(response) {
                alert('Start date updated successfully');
                row.find('.start-date').prop('disabled', true);
                row.find('.update-start-date').addClass('d-none');
                row.find('.edit-start-date').removeClass('d-none');
            },
            error: function(xhr) {
                alert('Error updating start date: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });
});
</script>
@endsection 