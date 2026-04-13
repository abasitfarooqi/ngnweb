<table class="table table-striped">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Total Stock</th>
            <th>Branches with Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product['Product Name'] }}</td>
                <td>{{ $product['SKU'] }}</td>
                <td>{{ $product['Category'] }}</td>
                <td>{{ $product['Brand'] }}</td>
                <td>{{ $product['Total Stock'] }}</td>
                <td>
                    @foreach ($product['Branches with Stock'] as $branch)
                        <div>{{ $branch['branch'] }}: {{ $branch['stock'] }}</div>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>