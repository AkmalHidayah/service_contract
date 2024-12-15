<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('LPJ Management') }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-6">
        <!-- Pencarian -->
        <div class="flex justify-between items-center mb-6">
            <input type="text" id="search" placeholder="Cari Notifikasi..." class="border border-gray-300 rounded px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-gray-300 w-full sm:w-1/3 shadow-sm">
        </div>

        <!-- Tabel LPJ Management -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-xs uppercase">
                        <th class="py-3 px-4 text-left border">Nomor Notifikasi</th>
                        <th class="py-3 px-4 text-left border">Tanggal Update</th>
                        <th class="py-3 px-4 text-left border">Nomor LPJ</th>
                        <th class="py-3 px-4 text-left border">Dokumen LPJ</th>
                        <th class="py-3 px-4 text-left border">Nomor PPL</th>
                        <th class="py-3 px-4 text-left border">Dokumen PPL</th>
                        <th class="py-3 px-4 text-center border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notification)
                        @php
                            $lpj = App\Models\Lpj::where('notification_number', $notification->notification_number)->first();
                            $has_lhpp = App\Models\Lhpp::where('notification_number', $notification->notification_number)->exists();
                        @endphp
                        @if($lpj || $has_lhpp)
                            <tr class="border-b">
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $notification->notification_number }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">{{ $lpj->update_date ?? now()->format('Y-m-d') }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    <input type="text" name="lpj_number" form="form-{{ $notification->notification_number }}" value="{{ $lpj->lpj_number ?? old('lpj_number') }}" class="w-full px-3 py-1 bg-gray-50 border border-gray-300 rounded text-xs shadow-sm focus:ring-2 focus:ring-blue-100">
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <label for="lpj_document_{{ $notification->notification_number }}" class="cursor-pointer bg-green-500 text-white px-4 py-2 rounded text-xs flex items-center space-x-1 hover:bg-green-600 transition duration-200">
                                            <i class="fas fa-upload"></i>
                                            <span>Upload</span>
                                        </label>
                                        <input id="lpj_document_{{ $notification->notification_number }}" type="file" name="lpj_document" form="form-{{ $notification->notification_number }}" class="hidden">
                                        @if($lpj && $lpj->lpj_document_path)
                                            <span class="text-xs text-blue-500 underline cursor-pointer" onclick="window.open('{{ Storage::url($lpj->lpj_document_path) }}', '_blank')">
                                                {{ basename($lpj->lpj_document_path) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500">Tidak ada file yang diunggah</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    <input type="text" name="ppl_number" form="form-{{ $notification->notification_number }}" 
                                        value="{{ $lpj->ppl_number ?? old('ppl_number') }}" 
                                        class="w-full px-3 py-1 bg-gray-50 border border-gray-300 rounded text-xs shadow-sm focus:ring-2 focus:ring-blue-100">
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <label for="ppl_document_{{ $notification->notification_number }}" 
                                            class="cursor-pointer bg-green-500 text-white px-4 py-2 rounded text-xs flex items-center space-x-1 hover:bg-green-600 transition duration-200">
                                            <i class="fas fa-upload"></i>
                                            <span>Upload</span>
                                        </label>
                                        <input id="ppl_document_{{ $notification->notification_number }}" type="file" name="ppl_document" 
                                            form="form-{{ $notification->notification_number }}" class="hidden">
                                        @if($lpj && $lpj->ppl_document_path)
                                            <a href="{{ Storage::url($lpj->ppl_document_path) }}" target="_blank" 
                                            class="text-blue-500 underline text-xs">
                                            {{ basename($lpj->ppl_document_path) }}
                                            </a>
                                        @else
                                            <span class="text-gray-500 text-xs">Belum ada</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form id="form-{{ $notification->notification_number }}" action="{{ route('lpj.update', $notification->notification_number) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-xs hover:bg-blue-600 transition duration-200">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: '{{ session("success") }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
</x-admin-layout>
