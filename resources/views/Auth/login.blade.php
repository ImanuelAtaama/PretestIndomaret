@extends('layouts.main')

@section('content')
<div class="mt-32  max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
    @if ($errors->any())
        <div class="mb-4 text-red-500 text-center">
            {{ $errors->first() }}
        </div>
    @endif
    <h2 class="text-2xl font-bold mb-4">Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700">Username</label>
            <input type="text" name="username" class="w-full p-2 border rounded" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" class="w-full p-2 border rounded" required>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Login</button>
    </form>
</div>
@endsection
