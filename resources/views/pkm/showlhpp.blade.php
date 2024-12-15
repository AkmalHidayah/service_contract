<x-document>
    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border border-black">
                <!-- Bagian Header -->
                <div class="mb-4">
                    <h1 class="text-2xl font-bold uppercase pb-2">Laporan Hasil Penyelesaian Pekerjaan (LHPP)</h1>
                    <div class="border-b-2 border-black w-1/2"></div> <!-- Lebar garis disesuaikan menjadi w-1/2 -->
                </div>

                <!-- Bagian Informasi -->
                <div class="mb-4">
                    <table class="min-w-full border-collapse table-auto">
                        <tbody>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">NOMOR NOTIFIKASI</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->notification_number }}</td>
                            </tr>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">NOMOR ORDER</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->nomor_order }}</td>
                            </tr>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">DESCRIPTION NOTIFIKASI</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->description_notifikasi }}</td>
                            </tr>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">PURCHASING ORDER</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->purchase_order_number }}</td>
                            </tr>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">UNIT KERJA PEMINTA (USER)</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->unit_kerja }}</td>
                            </tr>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">TANGGAL SELESAI PEKERJAAN</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->tanggal_selesai }}</td>
                            </tr>
                            <tr>
                                <td class="pr-1 py-1 text-left font-semibold w-1/4">WAKTU PENGERJAAN</td>
                                <td class="px-1 py-1 text-left w-3/4">: {{ $lhpp->waktu_pengerjaan }} Hari</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
<!-- Bagian Tabel Material -->
<div class="overflow-x-auto">
    <table class="min-w-full bg-white border border-black mb-6">
        <thead class="bg-amber-100">
            <tr>
                <th colspan="5" class="px-4 py-2 text-left border border-black">NO. A. ACTUAL PEMAKAIAN MATERIAL</th>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left border border-black">No</th>
                <th class="px-4 py-2 text-left border border-black">Material Description</th> <!-- Tambahkan kolom baru -->
                <th class="px-4 py-2 text-left border border-black">Volume (Kg)</th>
                <th class="px-4 py-2 text-left border border-black">Harga Satuan (Rp)</th>
                <th class="px-4 py-2 text-left border border-black">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMaterial = 0; @endphp
            @foreach($lhpp->material_description as $key => $desc)
                <tr>
                    <td class="px-4 py-2 border border-black">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border border-black">{{ $desc }}</td>
                    <td class="px-4 py-2 border border-black">{{ $lhpp->material_volume[$key] }}</td>
                    <td class="px-4 py-2 border border-black">{{ number_format($lhpp->material_harga_satuan[$key], 2, ',', '.') }}</td>
                    <td class="px-4 py-2 border border-black">{{ number_format($lhpp->material_jumlah[$key], 2, ',', '.') }}</td>
                </tr>
                @php $totalMaterial += $lhpp->material_jumlah[$key]; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="px-4 py-2 text-right font-bold border border-black">SUB TOTAL ( A )</td>
                <td colspan="2" class="px-4 py-2 border border-black">Rp {{ number_format($totalMaterial, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Bagian Tabel Consumable -->
    <table class="min-w-full bg-white border border-black mb-6">
        <thead class="bg-amber-100">
            <tr>
                <th colspan="5" class="px-4 py-2 text-left border border-black">NO. B. ACTUAL PEMAKAIAN CONSUMABLE</th>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left border border-black">No</th>
                <th class="px-4 py-2 text-left border border-black">Consumable Description</th> <!-- Tambahkan kolom baru -->
                <th class="px-4 py-2 text-left border border-black">Volume (Jam/Kg)</th>
                <th class="px-4 py-2 text-left border border-black">Harga Satuan (Rp)</th>
                <th class="px-4 py-2 text-left border border-black">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalConsumable = 0; @endphp
            @foreach($lhpp->consumable_description as $key => $desc)
                <tr>
                    <td class="px-4 py-2 border border-black">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border border-black">{{ $desc }}</td>
                    <td class="px-4 py-2 border border-black">{{ $lhpp->consumable_volume[$key] }}</td>
                    <td class="px-4 py-2 border border-black">{{ number_format($lhpp->consumable_harga_satuan[$key], 2, ',', '.') }}</td>
                    <td class="px-4 py-2 border border-black">{{ number_format($lhpp->consumable_jumlah[$key], 2, ',', '.') }}</td>
                </tr>
                @php $totalConsumable += $lhpp->consumable_jumlah[$key]; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="px-4 py-2 text-right font-bold border border-black">SUB TOTAL ( B )</td>
                <td colspan="2" class="px-4 py-2 border border-black">Rp {{ number_format($totalConsumable, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Bagian Tabel Biaya Upah Kerja -->
    <table class="min-w-full bg-white border border-black">
        <thead class="bg-amber-100">
            <tr>
                <th colspan="5" class="px-4 py-2 text-left border border-black">NO. C. ACTUAL BIAYA UPAH KERJA</th>
            </tr>
            <tr>
                <th class="px-4 py-2 text-left border border-black">No</th>
                <th class="px-4 py-2 text-left border border-black">Upah Kerja Description</th> <!-- Tambahkan kolom baru -->
                <th class="px-4 py-2 text-left border border-black">Volume (Jam/Kg)</th>
                <th class="px-4 py-2 text-left border border-black">Harga Satuan (Rp)</th>
                <th class="px-4 py-2 text-left border border-black">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalUpah = 0; @endphp
            @foreach($lhpp->upah_description as $key => $desc)
                <tr>
                    <td class="px-4 py-2 border border-black">{{ $key + 1 }}</td>
                    <td class="px-4 py-2 border border-black">{{ $desc }}</td>
                    <td class="px-4 py-2 border border-black">{{ $lhpp->upah_volume[$key] }}</td>
                    <td class="px-4 py-2 border border-black">{{ number_format($lhpp->upah_harga_satuan[$key], 2, ',', '.') }}</td>
                    <td class="px-4 py-2 border border-black">{{ number_format($lhpp->upah_jumlah[$key], 2, ',', '.') }}</td>
                </tr>
                @php $totalUpah += $lhpp->upah_jumlah[$key]; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="px-4 py-2 text-right font-bold border border-black">SUB TOTAL ( C )</td>
                <td colspan="2" class="px-4 py-2 border border-black">Rp {{ number_format($totalUpah, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="px-4 py-2 text-right font-bold border border-black">TOTAL ACTUAL BIAYA ( A + B + C )</td>
                <td colspan="2" class="px-4 py-2 border border-black">Rp {{ number_format($totalMaterial + $totalConsumable + $totalUpah, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</div>


   <!-- Tabel Hasil Quality Control dan Unit -->
<div class="mt-8 border border-black">
    <table class="w-full table-auto text-left border-collapse">
        <thead class="font-bold">
            <tr>
                <th class="border border-black p-2">HASIL QUALITY CONTROL</th>
                <th class="border border-black p-2">UNIT KERJA PEMINTA</th>
                <th class="border border-black p-2">UNIT WORKSHOP</th>
                <th class="border border-black p-2">PT. PKM</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-black p-2">
                    <div class="flex justify-between">
                        <div>APPROVE</div>
                    </div>
                    <div class="border-t border-black mt-2"></div> <!-- Garis antara approve dan reject -->
                    <div class="mt-2">REJECT</div>
                </td>
                <td class="border border-black text-center p-2">(MANAGER USER)</td>
                <td class="border border-black text-center p-2">HERWANTO.S</td>
                <td class="border border-black text-center p-2">MANAGER PKM</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Bagian Catatan dan Tindakan -->
<div class="mt-8">
    <div class="grid grid-cols-2 gap-6">
        <div class="border border-black px-2 py-4"> <!-- Border melingkupi seluruh bagian -->
            <p class="font-semibold">Catatan User:</p>
            <p>-</p> <!-- Konten catatan -->
        </div>
        <div class="border border-black px-2 py-4"> <!-- Border melingkupi seluruh bagian -->
            <p class="font-semibold">Catatan Unit Workshop:</p>
            <p>-</p> <!-- Konten catatan -->
        </div>
    </div>
</div>


</x-document>