@extends('layouts.main')
@section('title', 'File Transfer Protocol')

@section('content')
@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Tampilkan success --}}
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="p-6">
    {{-- Upload CSV Button dan Form --}}
    <div class="flex justify-end mb-4">
        <!-- Tombol yang membuka modal upload -->
        <button
            onclick="document.getElementById('uploadModal').classList.remove('hidden')"
            class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Upload CSV
        </button>
    </div>

    {{-- Upload Modal --}}
    <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded p-6 w-96">
            <h2 class="text-lg font-semibold mb-4">Upload CSV ke FTP</h2>

            <form id="csv-upload-form" action="{{ route('admin.ftp.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".csv" required
                    class="mb-4 w-full border rounded px-3 py-2">

                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="px-4 py-2 rounded border hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Upload</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel File FTP --}}
    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2 font-semibold">Nama File</th>
                    <th class="px-4 py-2 font-semibold w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($files as $file)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ basename($file) }}</td>
                        <td class="px-4 py-2 ">
                            <a href="{{ route('admin.ftp.delete', basename($file)) }}"
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 delete-file">
                                Hapus
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-gray-500 text-center">Tidak ada file.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
    const form = document.getElementById('csv-upload-form');
    const overlay = document.getElementById('loading-overlay');

    function showLoading() {
        overlay.classList.remove('hidden');
    }

    form.onsubmit = function(e) {
        e.preventDefault(); // hentikan submit default
        showLoading();      // tampilkan overlay
        form.submit();      // submit form setelah overlay muncul
    }

    // Delete file dengan loading overlay
    document.querySelectorAll('.delete-file').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // hentikan link default
            if(confirm('Hapus file ini?')) {
                showLoading();          // tampilkan overlay
                window.location.href = this.href; // lanjut ke URL delete
            }
        });
    });

    // Klik di luar modal untuk tutup
    window.onclick = function(event) {
        const modal = document.getElementById('uploadModal');
        if(event.target == modal) {
            modal.classList.add('hidden');
        }
    }
</script>
@endsection
