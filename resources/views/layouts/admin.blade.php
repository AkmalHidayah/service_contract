<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts (Remove the existing Figtree link) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Scripts -->
        <link rel="stylesheet" href="{{ asset('build/assets/app-BJr1-sId.css') }}">

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ open: false }" class="relative min-h-screen flex bg-gray-200">
       <!-- Sidebar -->
<aside class="fixed top-0 left-0 h-full z-10 text-blue-100 w-56 px-2 py-4 shadow-lg" style="background-color: #2f3e80;">
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center space-x-2">
            <a href="#">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-200" />
            </a>
            <div>
                <span class="text-2xl font-extrabold text-gray-200 block">BMS</span>
                <span class="text-xl font-extrabold text-white block">Dashboard</span>
            </div>
        </div>
    </div>
    <nav class="mt-5">
    <!-- Dashboard Icon (tachometer-alt updated) -->
    <a href="{{ route('admin.dashboard') }}" 
       class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.dashboard') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
       @click="open = !open" x-data="{ clicked: false }" 
       @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-chart-pie mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>Dashboard</span>
    </a>

    <!-- Notifikasi Icon (bell updated) -->
    <a href="{{ route('notifikasi.index') }}" 
       class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('notifikasi.index') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
       @click="open = !open" x-data="{ clicked: false }" 
       @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-envelope-open-text mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>Notifikasi</span>
    </a>

    <!-- Input HPP Icon (file-alt updated) -->
    <a href="{{ route('admin.inputhpp.index') }}" 
       class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.inputhpp.index') || request()->routeIs('admin.inputhpp.create_hpp1') || request()->routeIs('admin.inputhpp.create_hpp2') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
       @click="open = !open" x-data="{ clicked: false }" 
       @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-pencil-alt mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>Create Hpp</span>
    </a>

    <!-- Verifikasi Anggaran Icon (money-check updated) -->
    <a href="{{ route('admin.verifikasianggaran.index') }}" 
       class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.verifikasianggaran.index') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
       @click="open = !open" x-data="{ clicked: false }" 
       @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-money-check-alt mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>Verifikasi Anggaran</span>
    </a>

    <!-- PR / PO Icon (shopping-cart updated) -->
    <a href="{{ route('admin.purchaseorder') }}" 
       class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.purchaseorder') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
       @click="open = !open" x-data="{ clicked: false }" 
       @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-tasks mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>PR / PO</span>
    </a>

    <!-- LHPP Icon (tasks updated) -->
    <a href="{{ route('lhpp.index') }}" 
       class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('lhpp.*') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
       @click="open = !open" x-data="{ clicked: false }" 
       @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-file-alt mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>LHPP</span>
    </a>

    <!-- LPJ Icon (folder-open updated) -->
    <a href="{{ route('admin.lpj') }}" 
    class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.lpj') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
    @click="open = !open" x-data="{ clicked: false }" 
    @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-folder-open mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>LPJ/PPL</span>
    </a>

    <!-- Kuota Anggaran & OA Icon (clipboard-list) -->
    <a href="{{ route('admin.updateoa') }}" 
        class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.updateoa') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
        @click="open = !open" x-data="{ clicked: false }" 
        @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
        <i class="fas fa-clipboard-list mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
        <span>Kuota Anggaran & OA</span>  
    </a>



    <!-- USer Icon (wrench updated) -->
    <a href="{{ route('admin.users.index') }}" 
   class="block px-4 py-2 mt-2 text-sm font-semibold text-white {{ request()->routeIs('admin.users.index') ? 'bg-blue-800' : 'hover:bg-blue-700' }} rounded-lg flex items-center group"
   @click="open = !open" x-data="{ clicked: false }" 
   @click="clicked = !clicked" :class="{ 'scale-110': clicked }">
    <i class="fas fa-user-circle mr-2 text-lg group-hover:text-blue-300 transition-transform transform group-hover:scale-110"></i>
    <span>User Panel</span>
</a>
</nav>

</aside>

       <!-- Main content -->
<div class="ml-56 flex-1 flex flex-col overflow-y-auto bg-gray-100">
    <!-- Top Navigation -->
    <nav style="background-color: #2f3e80;" class="shadow-lg">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="relative flex items-center justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo SIG di sebelah kiri -->
                    <img src="{{ asset('images/logo-sig.png') }}" alt="SIG Logo" class="h-10 w-auto mr-2">
                    <!-- Logo Semen Tonasa di sebelah kiri -->
                    <img src="{{ asset('images/logo-st2.png') }}" alt="Semen Tonasa Logo" class="h-10 w-auto mr-2">
                    <!-- Text Section -->
                    <div class="flex flex-col text-white">
                        <span class="font-bold text-lg">SECTION OF WORKSHOP</span>
                        <span class="text-sm">Dept. Of Project Management & Main Support</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Icon Lonceng Notifikasi -->
                    <div class="relative">
                        <button class="text-white focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            <!-- Badge Notifikasi -->
                            <span class="absolute top-0 right-0 inline-block w-3 h-3 bg-red-500 text-white text-xs font-semibold rounded-full"></span>
                        </button>
                    </div>

                    <!-- Dropdown Profil di Sebelah Kanan -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-500 dark:text-gray-400 bg-white dark:bg-blue-900 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <!-- Icon Profile -->
                                <i class="fas fa-user-circle text-xl mr-2 text-blue-500"></i>
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
