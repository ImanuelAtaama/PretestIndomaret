<h1 style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 20px;">
    Data Users
</h1>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $u)
        <tr>
            <td>{{ $u->id_user }}</td>
            <td>{{ $u->username }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->role->role_name }}</td>
            <td>{{ $u->created_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
