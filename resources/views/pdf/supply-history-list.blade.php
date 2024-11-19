<x-layouts.pdf>
    <Until: class="table-header">
        <h1>Supplies History</h1>
        @if($from && $until)
        <p>From: {{ Carbon\Carbon::parse($from)->format('F d, Y')}} Until: {{ Carbon\Carbon::parse($until)->format('F d, Y')}}</p>
        @elseif($from)
        <p>From: {{ Carbon\Carbon::parse($from)->format('F d, Y')}} Until: {{ Carbon\Carbon::today()->format('F d, Y')}}</p>
        @elseif($until)
        <p>Until: {{ Carbon\Carbon::parse($until)->format('F d, Y')}}</p>
        @else
        <p>As of {{ Carbon\Carbon::today()->format('F d, Y')}}</p>
        @endif

        </div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Used</th>
                    <th>Added</th>
                    <th>Missing</th>
                    <th>Expired</th>
                    <th>Total</th>
                    <th>Expiry Date</th>
                    <th>Is Consumable?</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supplies as $supply)
                <tr>
                    <td>{{ $supply->supply->id }}</td>
                    <td>{{ $supply->supply->description }}</td>
                    <td>{{ implode(',', $supply->supply->categories->pluck('name')->toArray())}}</td>
                    <td>{{ $supply->quantity }}</td>
                    <td>{{ $supply->used }}</td>
                    <td>{{ $supply->added }}</td>
                    <td>{{ $supply->missing }}</td>
                    <td>{{ $supply->expired }}</td>
                    <td>{{ $supply->total }}</td>
                    <td>{{ $supply->supply->expiry_date?->format('F d, Y') ?? 'N/A' }}</td>
                    <td>{{ $supply->is_consumable ? 'Yes' : 'No' }}</td>
                    <td>{{ $supply->created_at?->format('F d, Y') ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
</x-layouts.pdf>