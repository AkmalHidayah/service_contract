<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form LHPP') }}
        </h2>
    </x-slot>
    <!-- Tombol Kembali -->
    <a href="{{ route('lhpp.index') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
    <i class="fas fa-arrow-left mr-2">Kembali</i>
            </a>

                    <div class="py-12">
                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                            <form action="{{ route('lhpp.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="notifikasi" class="block text-sm font-medium text-gray-700">Notifikasi</label>
                        <select id="notifikasi" name="notification_number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="" disabled selected>Pilih Notifikasi</option>
                            @foreach($notifications as $notification)
                                <option value="{{ $notification->notification_number }}" data-unit-work="{{ $notification->unit_work }}">
                                    {{ $notification->notification_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <div>
                            <label for="nomor_order" class="block text-sm font-medium text-gray-700">Nomor Order</label>
                            <input type="text" name="nomor_order" id="nomor_order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="description_notifikasi" class="block text-sm font-medium text-gray-700">Deskripsi Notifikasi</label>
                            <textarea name="description_notifikasi" id="description_notifikasi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div>
                            <label for="purchase_order_number" class="block text-sm font-medium text-gray-700">Purchasing Order</label>
                            <input type="text" name="purchase_order_number" id="purchase_order_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="unit_kerja" class="block text-sm font-medium text-gray-700">Unit Kerja Peminta</label>
                        <input type="text" name="unit_kerja" id="unit_kerja" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                    </div>
                        <div>
                        <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai Pekerjaan</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" onchange="calculateWorkDuration()">
                        </div>
                    </div>

                    <div class="mb-4">
                    <label for="waktu_pengerjaan" class="block text-sm font-medium text-gray-700">Waktu Pengerjaan (Hari)</label>
                    <input type="number" name="waktu_pengerjaan" id="waktu_pengerjaan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                    </div>

                     <!-- A. Actual Pemakaian Material -->
                     <h3 class="font-semibold text-lg mb-2">A. Actual Pemakaian Material</h3>
                    <div id="material-section">
                        <div class="grid grid-cols-4 gap-4 mb-4">
                            <div>
                                <label for="material_description_1" class="block text-sm font-medium text-gray-700">Actual Pemakaian Material</label>
                                <input type="text" name="material_description[]" id="material_description_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="material_volume_1" class="block text-sm font-medium text-gray-700">Volume (Kg)</label>
                                <input type="text" name="material_volume[]" id="material_volume_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="material_harga_satuan_1" class="block text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
                                <input type="text" name="material_harga_satuan[]" id="material_harga_satuan_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="material_jumlah_1" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="text" name="material_jumlah[]" id="material_jumlah_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-material-row" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4">Tambah Row Material</button>

                    <!-- Subtotal A -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div class="col-span-3 text-right font-semibold">SUB TOTAL (A)</div>
                        <div>
                            <input type="text" name="material_subtotal" id="material_subtotal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                        </div>
                    </div>
                    <!-- B. Actual Pemakaian Consumable -->
                    <h3 class="font-semibold text-lg mb-2">B. Actual Pemakaian Consumable</h3>
                    <div id="consumable-section">
                        <div class="grid grid-cols-4 gap-4 mb-4">
                            <div>
                                <label for="consumable_description_1" class="block text-sm font-medium text-gray-700">Actual Pemakaian Consumable</label>
                                <input type="text" name="consumable_description[]" id="consumable_description_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="consumable_volume_1" class="block text-sm font-medium text-gray-700">Volume (Jam/Kg)</label>
                                <input type="text" name="consumable_volume[]" id="consumable_volume_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="consumable_harga_satuan_1" class="block text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
                                <input type="text" name="consumable_harga_satuan[]" id="consumable_harga_satuan_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="consumable_jumlah_1" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="text" name="consumable_jumlah[]" id="consumable_jumlah_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-consumable-row" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4">Tambah Row Consumable</button>

                    <!-- Subtotal B -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div class="col-span-3 text-right font-semibold">SUB TOTAL (B)</div>
                        <div>
                            <input type="text" name="consumable_subtotal" id="consumable_subtotal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                        </div>
                    </div>


                     <!-- C. Actual Biaya Upah Kerja -->
                    <h3 class="font-semibold text-lg mb-2">C. Actual Biaya Upah Kerja</h3>
                    <div id="upah-section">
                        <div class="grid grid-cols-4 gap-4 mb-4">
                            <div>
                                <label for="upah_description_1" class="block text-sm font-medium text-gray-700">Actual Biaya Upah Kerja</label>
                                <input type="text" name="upah_description[]" id="upah_description_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="upah_volume_1" class="block text-sm font-medium text-gray-700">Volume (Jam/Kg)</label>
                                <input type="text" name="upah_volume[]" id="upah_volume_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="upah_harga_satuan_1" class="block text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
                                <input type="text" name="upah_harga_satuan[]" id="upah_harga_satuan_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="upah_jumlah_1" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="text" name="upah_jumlah[]" id="upah_jumlah_1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-upah-row" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4">Tambah Row Upah</button>

                    <!-- Subtotal C -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div class="col-span-3 text-right font-semibold">SUB TOTAL (C)</div>
                        <div>
                            <input type="text" name="upah_subtotal" id="upah_subtotal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                        </div>
                    </div>
                    <!-- Total Keseluruhan -->
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <div class="col-span-3 text-right font-semibold">TOTAL ACTUAL BIAYA (A + B + C)</div>
                        <div>
                            <input type="text" name="total_biaya" id="total_biaya" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" readonly>
                        </div>
                    </div>
                        <!-- Kontrak PKM -->
                        <div>
                            <h3 class="font-semibold text-lg mb-2">Kontrak PKM</h3>
                            <div class="mb-4">
                                <label for="kontrak_pkm" class="block text-sm font-medium text-gray-700">Pilih Kontrak PKM</label>
                                <select id="kontrak_pkm" name="kontrak_pkm" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="" disabled selected>Pilih salah satu</option> <!-- Default placeholder -->
                                    <option value="Fabrikasi">Fabrikasi</option>
                                    <option value="Konstruksi">Konstruksi</option>
                                    <option value="Pengerjaan Mesin">Pengerjaan Mesin</option>
                                </select>
                            </div>
                        </div>

                    <!-- Tombol Submit -->
                    <div class="text-right">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function calculateWorkDuration() {
        // Tanggal update dari Purchase Order (harus di-passing dari backend)
        let updateDate = "{{ $notification->purchaseOrder->update_date ?? null }}";
        let endDate = document.getElementById('tanggal_selesai').value;

        if (updateDate && endDate) {
            // Konversi ke format tanggal
            let start = new Date(updateDate);
            let end = new Date(endDate);

            // Hitung selisih waktu dalam milisecond
            let timeDifference = end - start;

            // Konversi ke hari
            let daysDifference = timeDifference / (1000 * 60 * 60 * 24);

            // Isi field waktu pengerjaan
            document.getElementById('waktu_pengerjaan').value = Math.round(daysDifference);
        } else {
            document.getElementById('waktu_pengerjaan').value = 0;
        }
    }

    document.getElementById('notifikasi').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const unitWork = selectedOption.getAttribute('data-unit-work');

        // Isi otomatis field Unit Kerja
        document.getElementById('unit_kerja').value = unitWork ? unitWork : '';
    });
</script>
    <script src="{{ asset('js/lhpp.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // SweetAlert untuk pesan sukses
    @if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'OK'
    });
    @endif

    // SweetAlert untuk pesan error
    @if(session('error'))
    Swal.fire({
        title: 'Gagal!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'OK'
    });
    @endif
</script>
</x-admin-layout>
