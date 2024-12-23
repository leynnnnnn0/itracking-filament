<x-layouts.pdf>
    <div class="table-header">
        <h1>Personnel List</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Office Phone</th>
                <th>Office Email</th>
                <th>Department</th>
                <th>Office</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model as $personnel)
            <tr>
                <td>{{ $personnel->full_name }}</td>
                <td>{{ $personnel->office_phone }}</td>
                <td>{{ $personnel->office_email }}</td>
                <td>{{ $personnel->department->name }}</td>
                <td>{{ $personnel->office->name }}</td>
                <td>{{ $personnel->start_date->format('F d, Y') }}</td>
                <td>{{ $personnel->end_date ? $personnel->end_date->format('F d, Y') : 'N/A' }}</td>
                <td>{{ $personnel->remarks ?? 'No remarks' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>