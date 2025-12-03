@extends('layouts.main')
@section('title', 'Master User')

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
@endif

{{-- Error untuk id_role --}}
@error('id_role')
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ $message }}
    </div>
@enderror

<div class="mb-4 flex space-x-2 items-end">
    {{-- Username Autocomplete --}}
    <div class="relative w-64">
        <input type="text"
                id="username_filter"
                class="border p-2 rounded w-full"
                placeholder="Ketik username...">

        <!-- Dropdown -->
        <div id="username_dropdown"
            class="absolute top-full left-0 right-0 bg-white border rounded mt-1 hidden max-h-48 overflow-auto shadow-lg z-50">
        </div>
    </div>

    {{-- Tanggal range --}}
    <div>
        <label>Tanggal</label>
        <input type="text" id="date_filter" class="border p-2 rounded" placeholder="Pilih tanggal...">
    </div>

    <button id="filterBtn" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    <button id="csvBtn" class="bg-green-600 text-white px-4 py-2 rounded">Download CSV</button>
    <button id="pdfBtn" class="bg-red-600 text-white px-4 py-2 rounded">Download PDF</button>
</div>

<div id="download-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Download File</h3>

        <!-- FILENAME READONLY -->
        <input type="text" id="download-filename"
                class="border p-2 w-full mb-4 bg-gray-200 cursor-not-allowed"
                readonly>

        <!-- WHERE OPTION -->
        <label class="block mb-2 font-semibold">Where:</label>
        <select id="download-where" class="border p-2 w-full mb-4 rounded">
            <option value="download">Download</option>
            <option value="newtab">Open in New Window</option>
        </select>

        <div class="flex justify-end space-x-2">
            <button onclick="closeDownloadModal()" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</button>
            <button onclick="confirmDownload()" class="px-4 py-2 bg-blue-500 text-white rounded">OK</button>
        </div>
    </div>
</div>

<div class="flex justify-end gap-3 mb-4">
    <button onclick="openCsvModal()" class="bg-purple-600 text-white px-4 py-2 rounded mb-4">
    Upload CSV
    </button>
    <button onclick="openModal('create')" class="bg-green-500 text-white px-4 py-2 rounded mb-4">Tambah User</button>
</div>

<!-- Modal Upload CSV -->
<div id="csv-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 w-96 rounded">
        <h3 class="text-xl font-semibold mb-4">Upload CSV User</h3>
        <form id="csv-upload-form" method="POST" enctype="multipart/form-data" action="{{ route('admin.master_user.import') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 font-semibold">File CSV</label>
                <input type="file" name="file" accept=".csv" required class="border p-2 w-full rounded">
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeCsvModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                    Batal
                </button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>


<table class="w-full bg-white shadow-md rounded">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Username</th>
            <th class="p-2">Email</th>
            <th class="p-2">Role</th>
            <th class="p-2">created_at</th>
            <th class="p-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td class="p-2 text-center">{{ $user->username }}</td>
            <td class="p-2 text-center">{{ $user->email }}</td>
            <td class="p-2 text-center">{{ $user->role->role_name }}</td>
            <td class="p-2 text-center">{{ $user->created_at }}</td>
            <td class="p-2 text-center">
                <button onclick="openModal('update', {{ $user->id_user }}, '{{ $user->username }}', '{{ $user->email }}', {{ $user->id_role }})" class="bg-blue-500 text-white px-2 py-1 rounded">Edit</button>
                <form method="POST" action="{{ route('admin.master_user.destroy', $user->id_user) }}" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if ($users->hasPages())
<div class="flex items-center justify-center gap-4 mt-4 text-gray-700">

    {{-- Previous --}}
    @if ($users->onFirstPage())
        <span class="px-4 py-2 border rounded text-gray-400 cursor-not-allowed">
            ← Previous
        </span>
    @else
        <a href="{{ $users->previousPageUrl() }}"
           class="px-4 py-2 border rounded hover:bg-gray-100 transition">
            ← Previous
        </a>
    @endif

    {{-- Page info --}}
    <span class="font-medium">
        Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
    </span>

    {{-- Next --}}
    @if ($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}"
            class="px-4 py-2 border rounded hover:bg-gray-100 transition">
            Next →
        </a>
    @else
        <span class="px-4 py-2 border rounded text-gray-400 cursor-not-allowed">
            Next →
        </span>
    @endif

</div>
@endif


<!-- Modal Pop-up Master User -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h3 id="modal-title" class="text-xl mb-4">Tambah User</h3>
        <form id="user-form" method="POST">
            @csrf
            <input type="hidden" name="_method" id="method" value="POST">
            <input type="hidden" name="id_user" id="id_user">

            <div class="mb-4">
                <label>Username</label>
                <input type="text" name="username" id="username" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email" id="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label>Role</label>
                <select name="id_role" id="id_role" class="w-full p-2 border rounded" required>
                    @foreach($roles as $role)
                    <option value="{{ $role->id_role }}">{{ $role->role_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeModal()" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="text-white text-lg">
        <svg class="animate-spin h-8 w-8 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        Mengirim email, harap tunggu...
    </div>
</div>


<script>
    let validUsername = false;

    function openCsvModal(){
        document.getElementById('csv-modal').classList.remove('hidden');
    }

    function closeCsvModal(){
        document.getElementById('csv-modal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const usernameInput = document.getElementById('username_filter');
        const usernameDropdown = document.getElementById('username_dropdown');

        usernameInput.addEventListener('input', function() {
            const query = this.value.trim();
            validUsername = false;  // reset setiap kali mengetik

            if (query.length === 0) {
                usernameDropdown.classList.add('hidden');
                return;
            }

            fetch(`/admin/users/autocomplete?username=${query}`)
                .then(res => res.json())
                .then(data => {
                    usernameDropdown.innerHTML = '';

                    if (data.length === 0) {
                        usernameDropdown.innerHTML = '<div class="p-2 text-gray-500">Tidak ada data</div>';
                    } else {
                        data.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'p-2 hover:bg-gray-200 cursor-pointer';
                            div.textContent = user.username;

                            div.addEventListener('click', function () {
                                usernameInput.value = user.username;
                                validUsername = true;  // username valid hanya jika user klik dropdown
                                usernameDropdown.classList.add('hidden');
                            });

                            usernameDropdown.appendChild(div);
                        });
                    }

                    usernameDropdown.classList.remove('hidden');
                });
        });

    // Klik luar menutup dropdown
    document.addEventListener('click', function(e) {
        if (!usernameDropdown.contains(e.target) && e.target !== usernameInput) {
            usernameDropdown.classList.add('hidden');
        }
    });

        // Datepicker (gunakan flatpickr atau HTML5 date range)
        flatpickr("#date_filter", {
            mode: "range",
            dateFormat: "Y-m-d",  // hanya tanggal
            enableTime: false     // matikan jam
        });

        // Filter button
        document.getElementById('filterBtn').addEventListener('click', function(){
            const username = document.getElementById('username_filter').value;
            const dateRange = document.getElementById('date_filter').value;

            // Jika user isi username tapi tidak pilih dari dropdown → blok
            if (username && !validUsername) {
                alert("Pilih username dari daftar autocomplete, bukan ketik manual.");
                return;
            }

            let url = `/admin/master_user?`;
            if(username) url += `username=${encodeURIComponent(username)}&`;
            if(dateRange) url += `date=${encodeURIComponent(dateRange)}`;

            window.location.href = url;
        });
    });

    let downloadType = ''; // csv atau pdf

    document.getElementById('csvBtn').addEventListener('click', () => openDownloadModal('csv'));
    document.getElementById('pdfBtn').addEventListener('click', () => openDownloadModal('pdf'));

    function openDownloadModal(type){
        downloadType = type;

        const dateRange = document.getElementById('date_filter').value;
        let filename = "";

        // Jika ada date range: format YYYY-MM-DD_to_YYYY-MM-DD
        if (dateRange.includes(" to ")) {
            const [from, to] = dateRange.split(" to ");
            filename = `${type}_${from}_to_${to}`;
        } else {
            // Tidak ada range → pakai timestamp sekarang
            const now = new Date();
            const formatted =
                now.getFullYear() + "-" +
                String(now.getMonth() + 1).padStart(2, '0') + "-" +
                String(now.getDate()).padStart(2, '0') + "_" +
                String(now.getHours()).padStart(2, '0') + "-" +
                String(now.getMinutes()).padStart(2, '0') + "-" +
                String(now.getSeconds()).padStart(2, '0');

            filename = `${type}_${formatted}`;
        }

        // Set value ke input
        document.getElementById('download-filename').value = filename;
        document.getElementById('download-modal').classList.remove('hidden');
    }


    function closeDownloadModal(){
        document.getElementById('download-modal').classList.add('hidden');
    }

    function confirmDownload() {
        const filename = document.getElementById('download-filename').value;
        const where = document.getElementById('download-where').value;

        const params = new URLSearchParams(window.location.search);
        params.append('filename', filename);

        const url = `/admin/master_user/export/${downloadType}?${params}`;

        if (where === "download") {
            // download biasa
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            link.remove();
        }
        else if (where === "newtab") {
            params.append('open', 1);
            window.open(`/admin/master_user/export/${downloadType}?${params.toString()}`, "_blank");
        }

        closeDownloadModal();
    }

    function openModal(type, id = null, username = '', email = '', id_role = '') {
        document.getElementById('modal').classList.remove('hidden');
        if (type === 'create') {
            document.getElementById('modal-title').textContent = 'Tambah User';
            document.getElementById('user-form').action = '{{ route("admin.master_user.store") }}';
            document.getElementById('method').value = 'POST';
            document.getElementById('id_user').value = '';
            document.getElementById('username').value = '';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
            document.getElementById('id_role').value = '';
        } else {
            document.getElementById('modal-title').textContent = 'Edit User';
            document.getElementById('user-form').action = '/admin/master_user/' + id; // route update manual
            document.getElementById('method').value = 'PUT';
            document.getElementById('id_user').value = id;
            document.getElementById('username').value = username;
            document.getElementById('email').value = email;
            document.getElementById('password').value = ''; // kosongkan password jika tidak diubah
            document.getElementById('id_role').value = id_role;
        }
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }

    document.getElementById('user-form').addEventListener('submit', function() {
        // tampilkan overlay loading
        document.getElementById('loading-overlay').classList.remove('hidden');
    });

</script>
@endsection
