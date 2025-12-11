@extends('layouts.main')
@section('title', 'Preview File FTP')

@section('content')
<div class="p-4">
    <h2 class="text-lg font-semibold mb-3">Preview File: {{ $filename }}</h2>

    <div class="bg-white shadow p-4 rounded">

        @if($ext === 'pdf')
            {{-- PDF Preview --}}
            <iframe src="data:application/pdf;base64,{{ base64_encode($content) }}"
                    class="w-full" style="height: 700px;"></iframe>

        @elseif(in_array($ext, ['txt','csv','log','json','xml']))
            {{-- Text Preview --}}
            <div class="overflow-x-auto">
                <pre class="whitespace-pre bg-gray-100 p-3 rounded border font-mono text-sm">
                    {{ $content }}
                </pre>
            </div>

        @else
            {{-- File tidak bisa preview --}}
            <p>Tipe file <b>{{ $ext }}</b> tidak dapat dipreview.</p>
            <a href="{{ route('admin.ftp.download', $filename) }}"
                class="px-3 py-2 bg-blue-500 text-white rounded">Download</a>
        @endif

    </div>
</div>
@endsection
