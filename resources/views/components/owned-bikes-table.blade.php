<table class="table table-striped">
    <thead>
        <tr>
            <th>Registration Number</th>
            <th>Model</th>
            <th>Year</th>
            <th>Color</th>
            <th>Rental Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bikes as $bike)
            <tr>
                <td>{{ $bike->reg_no ?? '' }}</td>
                <td>{{ $bike->model ?? '' }}</td>
                <td>{{ $bike->year ?? '' }}</td>
                <td>{{ $bike->color ?? '—' }}</td>
                <td>
                    @php
                        $isRented = \App\Models\RentingBookingItem::where('motorbike_id', $bike->id)
                            ->whereNull('end_date')
                            ->exists();
                    @endphp
                    {{ $isRented ? 'Active' : 'Not Active' }}
                </td>
                <td>
                    <a href="{{ backpack_url('motorbikes/'.$bike->id.'/edit') }}" class="btn btn-sm btn-info">Edit</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
