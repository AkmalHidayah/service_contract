<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>
    <!-- Search Bar -->
    <div class="flex justify-between mb-4">
        <input type="text" id="search" placeholder="Cari Nomor Notifikasi..." 
            class="border border-gray-300 rounded px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-gray-300 w-full sm:w-1/3">
    </div>
            <!-- Tampilkan Pagination -->
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
        <!-- Tabel Data -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table  id="notificationTable" class="min-w-full text-gray-800 text-sm">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Nomor Notifikasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Detail Pekerjaan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Catatan User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($notifications as $index => $notification)
                        @if($notification->isAbnormalAvailable && $notification->isScopeOfWorkAvailable && $notification->isGambarTeknikAvailable)
                            <tr class="{{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-100 transition duration-150">
                                <!-- Kolom pertama dengan Nomor Notifikasi -->
                                <td class="px-4 py-3 whitespace-nowrap text-xs font-medium text-gray-600 notification-number">
                                {{ $notification->notification_number }}
                            </td>

                                <!-- Kolom kedua dengan Detail Pekerjaan dalam bentuk grid -->
                                <td class="px-4 py-3">
                                    <div class="grid grid-cols-2 gap-y-4">
                                        <div class="col-span-2 border-b border-gray-300 pb-2 mb-2">
                                            <span class="font-semibold text-gray-700">Nama Pekerjaan:</span> {{ $notification->job_name }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-700">Unit Kerja:</span> {{ $notification->unit_work }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-700">Input Date:</span> {{ $notification->input_date }}
                                        </div>
                                        <div class="col-span-2 mt-2 border-b border-gray-300 pb-2">
                                            <span class="font-semibold text-gray-700">Priority:</span> 
                                            <form action="{{ route('notifications.updatePriority', ['notification_number' => $notification->notification_number]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <div class="flex items-center space-x-2">
                                                    <select name="priority" id="priority-{{ $notification->notification_number }}" class="priority-select px-2 py-1 rounded bg-gray-100 border-gray-300 text-gray-600 text-xs focus:ring-2 focus:ring-gray-300">
                                                        <option value="Urgently" {{ $notification->priority == 'Urgently' ? 'selected' : '' }}>Urgently</option>
                                                        <option value="Hard" {{ $notification->priority == 'Hard' ? 'selected' : '' }}>Hard</option>
                                                        <option value="Medium" {{ $notification->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                                        <option value="Low" {{ $notification->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                                    </select>
                                                    <button type="submit" class="bg-gray-500 text-white px-3 py-1 rounded text-xs hover:bg-gray-600 transition duration-150">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-span-2 flex flex-wrap gap-2 mt-2">
                                            <a href="{{ route('abnormal.view', ['notificationNumber' => $notification->notification_number]) }}" class="bg-red-400 text-white px-2 py-1 rounded-lg hover:bg-red-500 text-xs transition-all duration-200 ease-in-out" target="_blank">Abnormalitas <i class="fas fa-file-pdf text-sm"></i></a>
                                            <a href="{{ route('scopeofwork.view', ['notificationNumber' => $notification->notification_number]) }}" class="bg-green-400 text-white px-2 py-1 rounded-lg hover:bg-green-500 text-xs transition-all duration-200 ease-in-out" target="_blank">Scope Of Work <i class="fas fa-file-pdf text-sm"></i></a>
                                            <a href="{{ route('view-dokumen', ['notificationNumber' => $notification->notification_number]) }}" class="bg-blue-400 text-white px-2 py-1 rounded-lg hover:bg-blue-500 text-xs transition-all duration-200 ease-in-out" target="_blank">Gambar Teknik <i class="fas fa-file-pdf text-sm"></i></a>
                                        </div>
                                    </div>
                                    <!-- Kondisi untuk Tombol Lihat/Buat SPK -->
                                    @if($notification->priority == 'Urgently')
                                        <div class="mt-2 flex space-x-2">
                                            @php
                                                $spk = \App\Models\SPK::where('notification_number', $notification->notification_number)->first();
                                            @endphp

                                            @if ($spk)
                                                <a href="{{ route('spk.show', ['notification_number' => $notification->notification_number]) }}" class="bg-yellow-400 text-white px-3 py-1 rounded text-xs hover:bg-yellow-500 transition duration-150 flex items-center space-x-1" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                    <span>Lihat Initial Work</span>
                                                </a>
                                            @else
                                                <a href="{{ route('spk.create', ['notificationNumber' => $notification->notification_number]) }}" class="bg-yellow-400 text-white px-3 py-1 rounded text-xs hover:bg-orange-500 transition duration-150 flex items-center space-x-1">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span>Buat Initial Work</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <!-- Kolom ketiga dengan aksi tambahan -->
                                <td class="px-4 py-3 whitespace-nowrap text-right align-bottom">
                                    <form action="{{ route('notifications.update', ['notification_number' => $notification->notification_number]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-end justify-end space-x-2">
                                            <select name="status" id="status-{{ $notification->notification_number }}" class="status-select px-2 py-1 rounded bg-gray-100 border-gray-300 text-yellow-600 text-xs focus:ring-2 focus:ring-yellow-300">
                                                <option value="Pending" {{ $notification->status == 'Pending' ? 'selected' : '' }} class="text-yellow-600">Pending</option>
                                                <option value="Approved" {{ $notification->status == 'Approved' ? 'selected' : '' }} class="text-green-600">Approved</option>
                                            </select>
                                            <textarea name="catatan" placeholder="Catatan" rows="2" class="px-2 py-1 rounded bg-gray-100 border-gray-300 text-gray-600 text-xs focus:ring-2 focus:ring-gray-300 resize-none" style="width: 100px; height: 40px;"></textarea>
                                            <button type="submit" class="bg-gray-500 text-white px-2 py-1 rounded-full hover:bg-gray-600 transition duration-150">Save
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
@if (session('success_priority'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success_priority') }}',
            confirmButtonColor: '#606f7b',
            confirmButtonText: 'OK'
        });
    </script>
@endif

@if (session('success_status'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success_status') }}',
            confirmButtonColor: '#606f7b',
            confirmButtonText: 'OK'
        });
    </script>
@endif
<script>
 document.getElementById('search').addEventListener('keyup', function () {
            const query = this.value.toLowerCase(); // Ambil input pencarian dan ubah ke huruf kecil
            const rows = document.querySelectorAll('#notificationTable tbody tr'); // Semua baris dalam tabel

            rows.forEach(row => {
                const notificationNumber = row.querySelector('.notification-number').textContent.toLowerCase();
                if (notificationNumber.includes(query)) {
                    row.style.display = ''; // Tampilkan baris jika cocok
                } else {
                    row.style.display = 'none'; // Sembunyikan baris jika tidak cocok
                }
            });
        });
</script>
<script src="{{ asset('js/adminnotifications.js') }}"></script>
</x-admin-layout>
