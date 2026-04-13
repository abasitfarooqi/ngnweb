<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Registration</th>
                <th>Make</th>
                <th>Model</th>
                <th>Year</th>
                <th>Condition</th>
                <th>Mileage</th>
                <th>Price</th>
                <th>V5</th>
                <th>Branch</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bikes as $bike)
                <tr>
                    <td class="font-weight-bold">{{ strtoupper($bike->reg_no) }}</td>
                    <td>{{ $bike->make }}</td>
                    <td>{{ $bike->model }}</td>
                    <td>{{ $bike->year }}</td>
                    <td>
                        <span class="badge badge-{{ $bike->condition === 'new' ? 'success' : 'info' }}">
                            {{ ucfirst($bike->condition) }}
                        </span>
                    </td>
                    <td>{{ number_format($bike->mileage) }} mi</td>
                    <td class="font-weight-bold text-success">£{{ number_format($bike->price, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $bike->v5_available ? 'success' : 'warning' }}">
                            {{ $bike->v5_available ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>{{ $bike->branch_name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
