<x-layouts.pdf>
    <div class="table-header">
        <h1>Users List</h1>
        <p>Generated on: {{ date('F d, Y') }}</p>
    </div>
    <table class="print-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->middle_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->phone_number }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.pdf>