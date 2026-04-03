<h4>Total Stock Available: {{ $totalAvailableStock }}</h4>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Branch</th>
            <th>Available Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($branchStocks as $branchId => $branch)
            <tr>
                <td>{{ $branch['branch'] }}</td>
                <td>{{ $branch['stock'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Branch</th>
            <th>Transaction Date</th>
            <th>Stock In</th>
            <th>Stock Out</th>
            <th>Transaction Type</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stockMovements as $movement)
            <tr>
                <td>{{ $movement->branch->name }}</td>
                <td>{{ $movement->transaction_date }}</td>
                <td>{{ $movement->in }}</td>
                <td>{{ $movement->out }}</td>
                <td>{{ $movement->transaction_type }}</td>
                <td>{{ $movement->remarks }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">No Stock Movements</td>
            </tr>
        @endforelse
    </tbody>
</table>

