<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Anggaran') }}
        </h2>
    </x-slot>

    <!-- Pencarian dan Kontrol Pagination -->
    <div class="flex justify-between items-center mb-4 p-4 bg-gray-100 rounded-lg shadow-sm">
        <form method="GET" action="{{ route('admin.verifikasianggaran.index') }}" class="flex w-full sm:w-2/3 space-x-2">
            <input 
                type="text" 
                name="search" 
                placeholder="Cari Nomor Notifikasi..." 
                value="{{ request('search') }}"
                class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-full"
            >
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm transition duration-150">
                Cari
            </button>
        </form>
        <form method="GET" action="{{ route('admin.verifikasianggaran.index') }}">
            <select 
                name="entries" 
                onchange="this.form.submit()" 
                class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
            >
                <option value="5" {{ request('entries') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('entries') == 10 ? 'selected' : '' }}>10</option>
                <option value="15" {{ request('entries') == 15 ? 'selected' : '' }}>15</option>
            </select>
        </form>
    </div>

    <!-- Daftar Notifikasi -->
    <div class="space-y-4 p-4">
        @forelse($notifications as $notification)
            @if($notification->isAbnormalAvailable && $notification->isScopeOfWorkAvailable && $notification->isGambarTeknikAvailable && $notification->isHppAvailable)
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <!-- Nomor Notifikasi dan Tanggal -->
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-800 font-semibold">
                            Nomor Notifikasi: {{ $notification->notification_number }}
                        </span>
                        <span class="text-gray-500 text-xs">
                            Update Date: {{ $notification->update_date ? $notification->update_date->format('Y-m-d') : '-' }}
                        </span>
                    </div>

                    <!-- Links untuk Dokumen -->
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($notification->isAbnormalAvailable)
                            <a href="{{ route('abnormal.view', ['notificationNumber' => $notification->notification_number]) }}" 
                                class="flex items-center space-x-1 bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs font-medium transition duration-150">
                                <i class="fas fa-file-alt"></i> <span>Abnormalitas</span>
                            </a>
                        @endif
                        @if($notification->isScopeOfWorkAvailable)
                            <a href="{{ route('scopeofwork.view', ['notificationNumber' => $notification->notification_number]) }}" 
                                class="flex items-center space-x-1 bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs font-medium transition duration-150">
                                <i class="fas fa-file-alt"></i> <span>Scope of Work</span>
                            </a>
                        @endif
                        @if($notification->isGambarTeknikAvailable)
                            <a href="{{ route('view-dokumen', ['notificationNumber' => $notification->notification_number]) }}" 
                                class="flex items-center space-x-1 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs font-medium transition duration-150">
                                <i class="fas fa-file-alt"></i> <span>Gambar Teknik</span>
                            </a>
                        @endif
                         <!-- Link untuk HPP berdasarkan Source Form -->
                        @if($notification->isHppAvailable)
                            @if($notification->source_form === 'createhpp1')
                                <a href="{{ route('admin.inputhpp.view_hpp1', ['notification_number' => $notification->notification_number]) }}" 
                                    class="flex items-center space-x-1 bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs font-medium transition duration-150" 
                                    target="_blank">
                                    <i class="fas fa-file-alt"></i> <span>HPP</span>
                                </a>
                            @elseif($notification->source_form === 'createhpp2')
                                <a href="{{ route('admin.inputhpp.view_hpp2', ['notification_number' => $notification->notification_number]) }}" 
                                    class="flex items-center space-x-1 bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-xs font-medium transition duration-150" 
                                    target="_blank">
                                    <i class="fas fa-file-alt"></i> <span>HPP</span>
                                </a>
                            @elseif($notification->source_form === 'createhpp3')
                                <a href="{{ route('admin.inputhpp.view_hpp3', ['notification_number' => $notification->notification_number]) }}" 
                                    class="flex items-center space-x-1 bg-green-500 text-white px-3 py-1 rounded hover:bg-green-700 text-xs font-medium transition duration-150" 
                                    target="_blank">
                                    <i class="fas fa-file-alt"></i> <span>HPP</span>
                                </a>
                            @endif
                        @endif
                    </div>

                    <!-- Total Anggaran dan Status -->
                    <div class="flex justify-between items-center">
                        <div class="text-gray-700 font-semibold">
                            Total Anggaran: Rp{{ number_format($notification->total_amount, 2, ',', '.') }}
                        </div>
                        <form 
                            action="{{ route('notifications.updateStatusAnggaran', ['notification_number' => $notification->notification_number]) }}" 
                            method="POST" 
                            class="flex items-center space-x-1"
                        >
                            @csrf
                            @method('PATCH')
                            <select 
                                name="status_anggaran" 
                                class="px-2 py-1 rounded text-white text-xs font-medium focus:ring-2 focus:ring-blue-300
                                {{ $notification->status_anggaran === 'Tersedia' ? 'bg-green-500' : 'bg-red-500' }}"
                            >
                                <option value="Tersedia" {{ $notification->status_anggaran === 'Tersedia' ? 'selected' : '' }}>
                                    Anggaran Tersedia
                                </option>
                                <option value="Tidak Tersedia" {{ $notification->status_anggaran === 'Tidak Tersedia' ? 'selected' : '' }}>
                                    Anggaran Tidak Tersedia
                                </option>
                            </select>
                            <button 
                                type="submit" 
                                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition duration-150"
                            >
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-gray-600 text-center p-4">
                    Tidak ada data yang memenuhi syarat untuk ditampilkan.
                </div>
            @endif
        @empty
            <p class="text-center text-gray-500">Tidak ada data notifikasi ditemukan.</p>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 px-4">
        {{ $notifications->appends(request()->query())->links() }}
    </div>

    <script>
        document.querySelectorAll('select[name="status_anggaran"]').forEach(function(selectElement) {
            selectElement.addEventListener('change', function() {
                this.classList.toggle('bg-green-500', this.value === 'Tersedia');
                this.classList.toggle('bg-red-500', this.value === 'Tidak Tersedia');
            });
        });
    </script>
</x-admin-layout>
