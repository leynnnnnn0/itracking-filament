<x-layouts.pdf>
    <div class="table-header">
        <h1>{{ "Missing Equipment Report" }}</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Report id</th>
                <th>Equipment name</th>
                <th>Property Number</th>
                <th>Status</th>
                <th>Description</th>
                <th>Reported By</th>
                <th>Reported Date</th>
                <th>Is Condemned?</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->id }}</td>
                <td>{{ $report->equipment->name }}</td>
                <td>{{ $report->equipment->property_number }}</td>
                <td>{{ $report->status }}</td>
                <td>{{ $report->description ?? 'N/a'}}</td>
                <td>{{ $report->reported_by }}</td>
                <td>{{ $report->reported_date ? $report->reported_date->format('F d, Y') : 'N/A' }}</td>
                <td>{{ $report->is_condemend ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>