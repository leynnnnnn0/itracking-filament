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
                <th>Organization Unit</th>
                <th>Operating Unit Project</th>
                <th>Property Number</th>
                <th>Quantity</th>
                <th>Quantity Available</th>
                <th>Quantity Missing</th>
                <th>Quantity Condemned</th>
                <th>Unit</th>
                <th>Description</th>
                <th>Date Acquired</th>
                <th>Fund</th>
                <th>Estimated Useful Time</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
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
                <td>{{ $report->equipment->organization_unit->name  ?? 'N/a'}}</td>
                <td>{{ $report->equipment->operating_unit_project->name ?? 'N/a' }}</td>
                <td>{{ $report->equipment->property_number ?? 'N/a' }}</td>
                <td>{{ $report->equipment->quantity ?? 'N/a' }}</td>
                <td>{{ $report->equipment->quantity_available ?? 'N/a' }}</td>
                <td>{{ $report->equipment->quantity_missing ?? 'N/a' }}</td>
                <td>{{ $report->equipment->quantity_condemned ?? 'N/a' }}</td>
                <td>{{ $report->equipment->unit ?? 'N/a' }}</td>
                <td>{{ $report->equipment->description ?? 'N/a' }}</td>
                <td>{{ $report->equipment->date_acquired ? Carbon\Carbon::parse($report->equipment->date_acquired)->format('F d, Y') : 'N/a' }}</td>
                <td>{{ $report->equipment->fund->name ?? 'N/a' }}</td>
                <td>{{ $report->equipment->estimated_useful_time ? 'Until ' .  Carbon\Carbon::parse($report->equipment->estimated_useful_time)->format('F Y') : 'N/a'}}</td>
                <td>{{ number_format($report->equipment->unit_price, 2) ?? 'N/a'}} </td>
                <td>{{ number_format($report->equipment->total_amount, 2) ?? 'N/a'}} </td>
                <td>{{ $report->id }}</td>
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