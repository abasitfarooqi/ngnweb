<!-- File: resources/views/components/owned-bikes-table.blade.php -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Registration Number</th>
            <th>Model</th>
            <th>Year</th>
            <th>Color</th>
            <th>Rental Status</th> <!-- New column for rental status -->
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bikes as $bike)
            <tr>
                <td>{{ $bike->reg_no }}</td>
                <td>{{ $bike->model }}</td>
                <td>{{ $bike->year }}</td>
                <td>{{ $bike->color }}</td>
                <td>
                    @php
                        // Check if the bike is currently rented
                        $isRented = \App\Models\RentingBookingItem::where('motorbike_id', $bike->id)
                            ->whereNull('end_date') // Active rentals have no end date
                            ->exists();
                    @endphp
                    {{ $isRented ? 'Active' : 'Not Active' }} <!-- Display rental status -->
                </td>
                <td>
                    <a href="{{ route('motorbikes.show', $bike->id) }}" class="btn btn-info">View</a>
                    <a href="{{ route('motorbikes.edit', $bike->id) }}" class="btn btn-warning">Edit</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>