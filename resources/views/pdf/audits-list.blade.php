<x-layouts.pdf>
    <div class="table-header">
        <h1>Audits List</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Audit Id</th>
                <th>User</th>
                <th>Event</th>
                <th>Type</th>
                <th>Old values</th>
                <th>New values</th>
                <th>Created at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($model as $audit)
            <tr>
                <td>{{ $audit->id }}</td>
                <td>{{ $audit->user?->full_name ?? 'N/a' }}</td>
                <td>{{ $audit->event }}</td>
                <td>{{ Str::afterLast($audit->auditable_type, '\\') }}</td>
                <td>{{ json_encode($audit->old_values) }}</td>
                <td>{{ json_encode($audit->new_values) }}</td>
                <td>{{ $audit->created_at->format('F d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>