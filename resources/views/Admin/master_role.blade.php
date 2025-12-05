@extends('layouts.main')
@section('title', 'Master Role')

@section('content')
{{-- Flash Messages --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif
<div class="flex justify-end mb-4">
    <button onclick="openModal('create')" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Tambah Role</button>
</div>

<table class="w-full bg-white shadow-md rounded">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Role Name</th>
            <th class="p-2">Deskripsi</th>
            <th class="p-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
        <tr>
            <td class="p-2 text-center">{{ $role->role_name }}</td>
            <td class="p-2 text-center">{{ $role->Deskripsi }}</td>
            <td class="p-2 text-center">
                @if($role->id_role != 1)
                    <button onclick="openModal('update', {{ $role->id_role }}, '{{ $role->role_name }}', '{{ $role->Deskripsi }}')" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                    <form method="POST" action="{{ route('admin.master_role.destroy', $role->id_role) }}" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                    </form>
                @else
                    <button onclick="openModal('update', {{ $role->id_role }}, '{{ $role->role_name }}', '{{ $role->Deskripsi }}')" class="bg-blue-500 text-white px-2 py-1 rounded">Edit Deskripsi</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<style>
    /* Sembunyikan tulisan "Showing x to y..." pada pagination */
    nav > div:first-child {
        display: none !important;
    }
</style>

<div class="mt-4">
    {{ $roles->links() }}
</div>

<!-- Modal Pop-up Master Role -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h3 id="modal-title" class="text-xl mb-4">Tambah Role</h3>
        <form id="role-form" method="POST">
            @csrf
            <input type="hidden" name="_method" id="method" value="POST">
            <input type="hidden" name="id_role" id="id_role">

            <div class="mb-4">
                <label>Role Name</label>
                <input type="text" name="role_name" id="role_name" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label>Deskripsi</label>
                <textarea name="Deskripsi" id="Deskripsi" class="w-full p-2 border rounded" required></textarea>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeModal()" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="text-white text-lg">
        <svg class="animate-spin h-8 w-8 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Memproses, harap tunggu...
    </div>
</div>

<script>
    function openModal(type, id = null, role_name = '', Deskripsi = '') {
        document.getElementById('modal').classList.remove('hidden');
        if (type === 'create') {
            document.getElementById('modal-title').textContent = 'Tambah Role';
            document.getElementById('role-form').action = '{{ route("admin.master_role.store") }}';
            document.getElementById('method').value = 'POST';
            document.getElementById('id_role').value = '';
            document.getElementById('role_name').value = '';
            document.getElementById('Deskripsi').value = '';
        } else {
            document.getElementById('modal-title').textContent = 'Edit Role';
            document.getElementById('role-form').action = '{{ route("admin.master_role.update", ":id") }}'.replace(':id', id);
            document.getElementById('method').value = 'PUT';
            document.getElementById('id_role').value = id;
            document.getElementById('role_name').value = role_name;
            document.getElementById('Deskripsi').value = Deskripsi;
        }
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    function showLoading() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    }

    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('hidden');
    }

    document.getElementById('role-form').onsubmit = () => {
        showLoading();
    };

    document.querySelectorAll('form[method="POST"]').forEach(form => {
        form.addEventListener('submit', () => {
            showLoading();
        });
    });
</script>
@endsection
