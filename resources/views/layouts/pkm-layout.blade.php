<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Scripts -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-BJr1-sId.css') }}">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Styles -->
    <style>
        /* Untuk hover di dropdown */
        .dropdown-menu:hover {
            background-color: #fb923c;
        }

        /* Sidebar */
        aside {
            background-image: linear-gradient(to bottom, #f97316, #fb923c);
        }

        /* Hover sidebar navigation */
        .sidebar-link:hover {
            background-color: #ea580c;
        }

        /* Aktif state sidebar */
        .sidebar-link-active {
            background-color: #d97706;
        }

        /* Warna navbar */
        nav.bg-orange-500 {
            background-color: #ea580c;
        }

        /* Warna tombol logout */
        .btn-logout {
            background-color: #f97316;
            color: white;
        }

        .btn-logout:hover {
            background-color: #ea580c;
        }

        /* Customization for icons and text */
        .icon {
            font-size: 1.25rem;
            transition: transform 0.2s;
        }

        .icon:hover {
            transform: scale(1.2);
        }

        .nav-text {
            font-weight: bold;
        }

        .nav-text:hover {
            color: #ffffff;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ open: false }" class="relative min-h-screen flex bg-gray-200">
        <!-- Sidebar -->
        <aside class="fixed top-0 left-0 h-full z-10 text-orange-100 w-56 px-2 py-4 shadow-lg">
            <div class="flex items-center justify-between px-2">
                <div class="flex items-center space-x-2">
                    <a href="#">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
                    </a>
                    <div>
                        <span class="text-2xl font-extrabold text-red-700 block">PKM</span>
                        <span class="text-xl font-extrabold text-white block">Dashboard</span>
                    </div>
                </div>
            </div>
            <nav class="mt-5">
                <!-- Navigation Links -->
                <a href="{{ route('pkm.dashboard') }}" class="block px-4 py-2 mt-2 text-sm font-semibold text-white sidebar-link {{ request()->routeIs('pkm.dashboard') ? 'sidebar-link-active' : '' }} rounded-lg flex items-center">
                    <i class="fas fa-tachometer-alt icon mr-2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="{{ route('pkm.jobwaiting') }}" class="block px-4 py-2 mt-2 text-sm font-semibold text-white sidebar-link {{ request()->routeIs('pkm.jobwaiting') ? 'sidebar-link-active' : '' }} rounded-lg flex items-center">
                    <i class="fa fa-bell icon mr-2"></i>
                    <span class="nav-text">Job Waiting</span>
                </a>
                <a href="{{ route('pkm.laporan') }}" class="block px-4 py-2 mt-2 text-sm font-semibold text-white sidebar-link {{ request()->routeIs('pkm.laporan') ? 'sidebar-link-active' : '' }} rounded-lg flex items-center">
                    <i class="fas fa-tasks icon mr-2"></i>
                    <span class="nav-text">Laporan</span>
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="ml-56 flex-1 flex flex-col overflow-y-auto bg-orange-100">
            <!-- Top Navigation -->
            <nav class="bg-orange-500 shadow-lg">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="relative flex items-center justify-between h-16">
                        <div class="flex items-center">
                            <!-- Logo di sebelah kiri -->
                            <img src="{{ asset('images/pkm.png') }}" alt="PKM Logo" class="h-10 w-auto mr-2">
                            <!-- Teks di sebelah kiri -->
                            <div class="flex flex-col text-white">
                                <span class="font-bold text-lg">PT. Prima Karya Manuggal</span>
                                <span class="text-sm">Management Section</span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Dropdown Profil -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <i class="fas fa-user-circle text-xl mr-2 text-gray-600"></i>
                                        <div>Welcome {{ Auth::user()->name }}</div>
                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div> 
                </div>
            </nav>

            <!-- Main content -->
            <main class="flex-1 p-5 bg-gray-100 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
    <script src="{{ asset('build/assets/app-CH09qwMe.js') }}" defer></script>
</body>
</html>
