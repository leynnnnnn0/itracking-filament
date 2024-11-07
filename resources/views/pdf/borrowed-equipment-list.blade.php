<x-layouts.pdf>
    <div class="table-header">
        <h1>Borrowed Equipments Log</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Equipment Name</th>
                <th>Property Number</th>
                <th>Borrower First Name</th>
                <th>Borrower Last Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Returned Date</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowedEquipments as $borrowedEquipment)
            <tr>
                <td>{{ $borrowedEquipment->id }}</td>
                <td>{{ $borrowedEquipment->equipment->name }}</td>
                <td>{{ $borrowedEquipment->equipment->property_number }}</td>
                <td>{{ $borrowedEquipment->borrower_first_name }}</td>
                <td>{{ $borrowedEquipment->borrower_last_name }}</td>
                <td>{{ $borrowedEquipment->borrower_phone_number }}</td>
                <td>{{ $borrowedEquipment->borrower_email }}</td>
                <td>{{ $borrowedEquipment->start_date->format('M d, Y') }}</td>
                <td>{{ $borrowedEquipment->end_date->format('M d, Y') }}</td>
                <td>{{ $borrowedEquipment->returned_date ? $borrowedEquipment->returned_date->format('M d, Y') : 'Not yet returned' }}</td>
                <td>{{ Str::replace('_', ' ', Str::title(App\BorrowStatus::from($borrowedEquipment->status)->name)) }}</td>
                <td>{{ $borrowedEquipment->remarks }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>