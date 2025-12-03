@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
<div class="mt-32 text-center">
    <h2 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->username }}!</h2>
    <p>Anda login sebagai {{ auth()->user()->role->role_name }}.</p>
</div>
@endsection
