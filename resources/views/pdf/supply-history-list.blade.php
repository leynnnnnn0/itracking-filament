<x-layouts.pdf>
    <div class="table-header">
        <h1>Supplies History</h1>
        <p>As of {{ Carbon\Carbon::today()->format('F d, Y')}}</p>

    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Used</th>
                <th>Added</th>
                <th>Total</th>
                <th>Expiry Date</th>
                <th>Is Consumable?</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supplies as $supply)
            <tr>
                <td>{{ $supply->supply->description }}</td>
                <td>{{ $supply->quantity }}</td>
                <td>{{ $supply->used }}</td>
                <td>{{ $supply->added }}</td>
                <td>{{ $supply->total }}</td>
                <td>{{ $supply->expiry_date?->format('F d, Y') ?? 'N/A' }}</td>
                <td>{{ $supply->is_consumable ? 'Yes' : 'No' }}</td>
                <td>{{ $supply->created_at?->format('F d, Y') ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>