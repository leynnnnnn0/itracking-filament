<x-layouts.pdf>
    <h4 class="center">Borrowed Equipments Log</h4>
    <h5 class="center">As of {{ Carbon\Carbon::today()->format('F d, Y') }}</h5>
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
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>