@extends('layouts.main')
@section('title', 'Dashboard')


@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <a href="{{ route('admin.master_user.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
        <h3 class="text-xl font-bold">Master User</h3>
        <p>Kelola pengguna</p>
    </a>
    <a href="{{ route('admin.master_role.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
        <h3 class="text-xl font-bold">Master Role</h3>
        <p>Kelola peran</p>
    </a>
    <a href="{{ route('admin.ftp.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg">
        <h3 class="text-xl font-bold">File Transfer Protocol</h3>
        <p>Kelola FIle</p>
    </a>
</div>
@endsection
