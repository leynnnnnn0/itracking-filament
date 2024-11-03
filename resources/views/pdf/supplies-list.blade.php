<x-layouts.pdf>
    <div class="table-header">
        <h1>{{ "List of Supplies" }}</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Description</th>
                <th>Categories</th>
                <th>Quantity</th>
                <th>Used</th>
                <th>Total</th>
                <th>Expiry Date</th>
                <th>Is Consumable?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
            <tr>
                <td>{{ $supply->id }}</td>
                <td>{{ $supply->description }}</td>
                <td>{{ implode(' / ', $supply->categories->map(function ($item) {
                            return $item->name;
                    })->toArray())}}</td>
                <td>{{ $supply->quantity }}</td>
                <td>{{ $supply->used }}</td>
                <td>{{ $supply->total }}</td>
                <td>{{ $supply->expiry_date?->format('F d, Y') ?? 'N/a' }}</td>
                <td>{{ $supply->is_consumable ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>