<x-layouts.pdf>
    <div class="table-header">
        <h1>{{ "List of Supplies" }}</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Report Id</th>
                <th>Report Type</th>
                <th>Supply</th>
                <th>Supply Id</th>
                <th>Quantity</th>
                <th>Reconciled Quantity</th>
                <th>Date</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>{{ $report->action }}</td>
                <td>{{ $report->supply->description }}</td>
                <td>{{ $report->supply->id }}</td>
                <td>{{ $report->quantity }}</td>
                <td>{{ $report->quantity_returned }}</td>
                <td>{{ $report->date_acquired }}</td>
                <td>{{ $report->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>