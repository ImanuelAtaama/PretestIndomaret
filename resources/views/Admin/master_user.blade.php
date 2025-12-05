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

@error('file')
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ $message }}
    </div>
@enderror

<div class="mb-4 flex space-x-2 items-end">

    {{-- Username Autocomplete --}}
    <div class="relative w-64">
        <input type="text" id="username_filter" class="border p-2 rounded w-full" placeholder="Ketik username...">
        <div id="username_dropdown"
            class="absolute top-full left-0 right-0 bg-white border rounded mt-1 hidden max-h-48 overflow-auto shadow-lg z-50">
        </div>
    </div>

    {{-- Tanggal --}}
    <div>
        <label>Tanggal</label>
        <input type="text" id="date_filter" class="border p-2 rounded" placeholder="Pilih tanggal...">
    </div>

    <button id="filterBtn" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
    <button id="csvBtn" class="bg-green-600 text-white px-4 py-2 rounded">Download CSV</button>
    <button id="pdfBtn" class="bg-red-600 text-white px-4 py-2 rounded">Download PDF</button>
</div>

{{-- Modal Download --}}
<div id="download-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Download File</h3>

        <input type="text" id="download-filename"
                class="border p-2 w-full mb-4 bg-gray-200 cursor-not-allowed"
                readonly>

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

{{-- Modal Upload CSV --}}
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

{{-- TABLE --}}
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
                <button onclick="openModal('update', {{ $user->id_user }}, '{{ $user->username }}', '{{ $user->email }}', {{ $user->id_role }})"
                        class="bg-blue-500 text-white px-2 py-1 rounded">
                    Edit
                </button>

                <form method="POST" action="{{ route('admin.master_user.destroy', $user->id_user) }}" class="delete-form" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Pagination --}}
@if ($users->hasPages())
<div class="flex items-center justify-center gap-4 mt-4 text-gray-700">
    @if ($users->onFirstPage())
        <span class="px-4 py-2 border rounded text-gray-400 cursor-not-allowed">← Previous</span>
    @else
        <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 border rounded hover:bg-gray-100 transition">← Previous</a>
    @endif

    <span class="font-medium">
        Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
    </span>

    @if ($users->hasMorePages())
        <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 border rounded hover:bg-gray-100 transition">Next →</a>
    @else
        <span class="px-4 py-2 border rounded text-gray-400 cursor-not-allowed">Next →</span>
    @endif
</div>
@endif

{{-- Modal CRUD --}}
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

{{-- Loading Overlay --}}
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

let validUsername = false;
let downloadType = "";

function showLoading(){
    document.getElementById('loading-overlay').classList.remove('hidden');
}
function hideLoading(){
    document.getElementById('loading-overlay').classList.add('hidden');
}

function openCsvModal(){ document.getElementById('csv-modal').classList.remove('hidden'); }
function closeCsvModal(){ document.getElementById('csv-modal').classList.add('hidden'); }

document.addEventListener('DOMContentLoaded', function() {

    /* ======================= AUTOCOMPLETE ======================= */
    const usernameInput = document.getElementById('username_filter');
    const usernameDropdown = document.getElementById('username_dropdown');

    usernameInput.addEventListener('input', function() {
        const query = this.value.trim();
        validUsername = false;

        if (!query.length) {
            usernameDropdown.classList.add('hidden');
            return;
        }

        fetch(`/admin/users/autocomplete?username=${query}`)
            .then(res => res.json())
            .then(data => {
                usernameDropdown.innerHTML = '';

                data.length
                    ? data.forEach(u => {
                        const div = document.createElement('div');
                        div.className = 'p-2 hover:bg-gray-200 cursor-pointer';
                        div.textContent = u.username;
                        div.onclick = () => {
                            usernameInput.value = u.username;
                            validUsername = true;
                            usernameDropdown.classList.add('hidden');
                        };
                        usernameDropdown.appendChild(div);
                    })
                    : usernameDropdown.innerHTML = '<div class="p-2 text-gray-500">Tidak ada data</div>';

                usernameDropdown.classList.remove('hidden');
            });
    });

    document.addEventListener('click', e => {
        if (!usernameDropdown.contains(e.target) && e.target !== usernameInput) {
            usernameDropdown.classList.add('hidden');
        }
    });

    /* ======================= DATE RANGE ======================= */
    flatpickr("#date_filter", {
        mode: "range",
        dateFormat: "Y-m-d",
        enableTime: false
    });

    /* ======================= FILTER ======================= */
    document.getElementById('filterBtn').onclick = () => {
        const username = usernameInput.value;
        const date = document.getElementById('date_filter').value;

        if (username && !validUsername) {
            return alert("Pilih username dari dropdown!");
        }

        let url = `/admin/master_user?`;
        if (username) url += `username=${encodeURIComponent(username)}&`;
        if (date) url += `date=${encodeURIComponent(date)}`;

        window.location.href = url;
    };

    /* ======================= UPLOAD CSV LOADING ======================= */
    document.getElementById('csv-upload-form').onsubmit = () => {
        showLoading();
    };

});

//download csv & pdf
document.getElementById('csvBtn').onclick = () => openDownloadModal('csv');
document.getElementById('pdfBtn').onclick = () => openDownloadModal('pdf');

function openDownloadModal(type){
    downloadType = type;

    const now = new Date();
    const pad = n => n.toString().padStart(2, '0');

    // Format: YYYYMMDDHHMMSS
    const formattedDate =
        `${now.getFullYear()}${pad(now.getMonth()+1)}${pad(now.getDate())}` +
        `${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}`;

    // Hasil: csv_20251205144415  atau  pdf_20251205144415
    const filename = `${type}_${formattedDate}`;

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

    let url = `/admin/master_user/export/${downloadType}?${params}`;

    if (where === "download") {
        window.location.href = url;
    } else {
        params.append('open', 1);
        window.open(`/admin/master_user/export/${downloadType}?${params}`, "_blank");
    }

    closeDownloadModal();
}

//create & update
function openModal(type, id = null, username = '', email = '', id_role = ''){
    document.getElementById('modal').classList.remove('hidden');

    if (type === 'create') {
        document.getElementById('modal-title').innerText = "Tambah User";
        document.getElementById('user-form').action = '{{ route("admin.master_user.store") }}';
        document.getElementById('method').value = 'POST';

        document.getElementById('username').value = "";
        document.getElementById('email').value = "";
        document.getElementById('password').value = "";
        document.getElementById('id_role').value = "";
    } else {
        document.getElementById('modal-title').innerText = "Edit User";
        document.getElementById('user-form').action = `/admin/master_user/${id}`;
        document.getElementById('method').value = 'PUT';

        document.getElementById('username').value = username;
        document.getElementById('email').value = email;
        document.getElementById('password').value = "";
        document.getElementById('id_role').value = id_role;
    }
}

function closeModal(){
    document.getElementById('modal').classList.add('hidden');
}

document.getElementById('user-form').onsubmit = () => {
    showLoading();
};

//delete
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', () => {
        showLoading();
    });
});

</script>
@endsection
