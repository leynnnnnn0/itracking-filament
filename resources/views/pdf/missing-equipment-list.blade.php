<x-layouts.pdf>
    <div class="table-header">
        <h1>{{ "Missing Equipment Report" }}</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Equipment Id</th>
                <th>Name</th>
                <th>Accountable Officer</th>
                <th>Responsible Person</th>
                <th>Property Number</th>
                <th>Report id</th>
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
                <td>{{ $report->equipment->id }}</td>
                <td>{{ $report->equipment->name ?? 'N/a' }}</td>
                <td>{{ $report->equipment->accountable_officer->full_name ?? 'N/a' }}</td>
                <td>{{ $report->equipment->personnel->full_name ?? 'N/a' }}</td>
                <td>{{ $report->equipment->property_number ?? 'N/a' }}</td>
                <td>{{ $report->id }}</td>
                <td>{{ $report->status }}</td>
                <td>{{ $report->description ?? 'N/a'}}</td>
                <td>{{ $report->reported_by }}</td>
                <td>{{ $report->reported_date ? $report->reported_date->format('F d, Y') : 'N/A' }}</td>
                <td>{{ $report->is_condemned ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>