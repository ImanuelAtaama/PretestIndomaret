<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>
<body class="bg-gray-100 font-sans">
    <!-- Overlay untuk mobile -->
    <div class="fixed inset-0 bg-black text-white flex-col justify-center items-center text-center px-6 hidden z-[99999]" id="mobile-blocker">
        <h1 class="text-2xl font-bold mb-4">Silakan Buka di Desktop</h1>
        <p class="text-lg opacity-80">Aplikasi ini hanya dapat diakses menggunakan perangkat komputer.</p>
    </div>

    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col">
        <!-- Header -->
        @if (!Request::is('login'))
        <header class="bg-white shadow-md p-4 flex justify-between items-center">
            <!-- Nama halaman -->
            <h1 class="text-xl font-bold text-gray-800">@yield('title', 'Pretest Indomaret')</h1>

            <!-- Tombol titik tiga -->
            <button @click="sidebarOpen = true" class="text-gray-700 text-2xl font-bold focus:outline-none">⋮</button>
        </header>
        @endif

        <!-- Sidebar overlay -->
        <div x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
            @click="sidebarOpen = false"></div>

        <!-- Sidebar slide-in dari kanan -->
        <aside x-show="sidebarOpen"
            x-transition:enter="transition transform duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 w-64 h-full bg-white shadow-lg z-50 flex flex-col">

            <div class="p-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-bold">Menu</h2>
                <button @click="sidebarOpen = false" class="text-gray-700 text-xl font-bold">&times;</button>
            </div>

            <nav class="flex-1 p-4 overflow-y-auto">
                <ul class="flex flex-col space-y-2">
                    <!-- Semua user bisa akses dashboard -->
                    @if(Auth::check())
                        <li>
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Dashboard</a>
                        </li>
                    @endif

                    @if(Auth::check() && Auth::user()->id_role == 1)
                        <!-- Hanya admin -->
                        <li>
                            <a href="{{ route('admin.master_user.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Master User</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.master_role.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Master Role</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.ftp.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100">File Transfer Protocol</a>
                        </li>
                    @endif

                    @if(Auth::check())
                        <!-- Logout -->
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 rounded hover:bg-gray-100">Logout</button>
                            </form>
                        </li>
                    @endif
                </ul>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 container mx-auto p-6">
            @yield('content')
        </main>
    </div>
    <script>
    function checkScreen() {
        const blocker = document.getElementById('mobile-blocker');

        if (window.innerWidth < 1024) {
            blocker.classList.remove('hidden');
            blocker.classList.add('flex');
        } else {
            blocker.classList.add('hidden');
            blocker.classList.remove('flex');
        }
    }

    checkScreen();
    window.addEventListener('resize', checkScreen);
    </script>
</body>
</html>
