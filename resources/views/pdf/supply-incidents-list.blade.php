<x-layouts.pdf>
    <Until: class="table-header">
        <h1>Supplies Incident List</h1>
        <p>As of {{ Carbon\Carbon::today()->format('F d, Y')}}</p>
        </div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Incident id</th>
                    <th>Supply id</th>
                    <th>Supply</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Incident Date</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($model as $incident)
                <tr>
                    <td>{{ $incident->id }}</td>
                    <td>{{ $incident->supply->id }}</td>
                    <td>{{ $incident->supply->description }}</td>
                    <td>{{ $incident->type }}</td>
                    <td>{{ $incident->quantity }}</td>
                    <td>{{ $incident->incident_date->format('F d, Y') }}</td>
                    <td>{{ $incident->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
</x-layouts.pdf>