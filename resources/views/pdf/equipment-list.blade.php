<x-layouts.pdf>
    <div class="table-header">
        <h1>Equipment List</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
        @if($accountablePerson && !$responsiblePerson)
        <p>Accounting Officer: {{ $accountablePerson }}</p>
        @endif
        @if($responsiblePerson && !$accountablePerson)
        <p>Responsible Person: {{ $responsiblePerson }}</p>
        @endif
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                @if(!$accountablePerson)
                <th>Accountable Officer</th>
                @endif
                @if(!$responsiblePerson)
                <th>Responsible Person</th>
                @endif
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
            </tr>
        </thead>
        <tbody>
            @foreach ($equipments as $equipment)
            <tr>
                <td>{{ $equipment->id }}</td>
                <td>{{ $equipment->name ?? 'N/a' }}</td>
                @if(!$accountablePerson)
                <td>{{ $equipment->accountable_officer->full_name ?? 'N/a' }}</td>
                @endif
                @if(!$responsiblePerson)
                <td>{{ $equipment->personnel->full_name ?? 'N/a' }}</td>
                @endif
                <td>{{ $equipment->organization_unit->name  ?? 'N/a'}}</td>
                <td>{{ $equipment->operating_unit_project->name ?? 'N/a' }}</td>
                <td>{{ $equipment->property_number ?? 'N/a' }}</td>
                <td>{{ $equipment->quantity ?? 'N/a' }}</td>
                <td>{{ $equipment->quantity_available ?? 'N/a' }}</td>
                <td>{{ $equipment->quantity_missing ?? 'N/a' }}</td>
                <td>{{ $equipment->quantity_condemned ?? 'N/a' }}</td>
                <td>{{ $equipment->unit ?? 'N/a' }}</td>
                <td>{{ $equipment->description ?? 'N/a' }}</td>
                <td>{{ $equipment->date_acquired ? Carbon\Carbon::parse($equipment->date_acquired)->format('F d, Y') : 'N/a' }}</td>
                <td>{{ $equipment->fund->name ?? 'N/a' }}</td>
                <td>{{ $equipment->estimated_useful_time ? 'Until ' .  Carbon\Carbon::parse($equipment->estimated_useful_time)->format('F Y') : 'N/a'}}</td>
                <td>{{ number_format($equipment->unit_price, 2) ?? 'N/a'}} </td>
                <td>{{ number_format($equipment->total_amount, 2) ?? 'N/a'}} </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>