<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hpp1;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\KuotaAnggaranOA;


class InputHPPController extends Controller
{
    public function index()
{
    // Mengambil data HPP dengan pagination dan urutkan berdasarkan 'created_at' dari terbaru ke paling lama
    $hpp = Hpp1::orderBy('created_at', 'desc')->paginate(5); 
     // Ambil total HPP berdasarkan unit kerja
     $unitKerjaHppData = Hpp1::selectRaw('requesting_unit, SUM(total_amount) as total')
     ->groupBy('requesting_unit')
     ->orderBy('total', 'desc')
     ->take(10) // Top 10 unit kerja 
     ->get();
    
    // Mengirim data paginated ke view
    return view('admin.inputhpp.index', compact('hpp','unitKerjaHppData')); 
}
public function destroy($notification_number)
{
    // Mencari data HPP berdasarkan notification_number
    $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();

    try {
        // Menghapus data HPP
        $hpp->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.inputhpp.index')->with('success', 'Dokumen HPP berhasil dihapus.');
    } catch (\Exception $e) {
        // Handle error saat penghapusan
        return redirect()->route('admin.inputhpp.index')->with('error', 'Gagal menghapus dokumen HPP: ' . $e->getMessage());
    }
}
public function edit($notification_number)
{
    // Cari data HPP berdasarkan notification_number
    $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();

    // Decode JSON untuk data array
    $hpp->uraian_pekerjaan = json_decode($hpp->uraian_pekerjaan, true);
    $hpp->jenis_material = json_decode($hpp->jenis_material, true);
    $hpp->qty = json_decode($hpp->qty, true);
    $hpp->satuan = json_decode($hpp->satuan, true);
    $hpp->volume_satuan = json_decode($hpp->volume_satuan, true);
    $hpp->jumlah_volume_satuan = json_decode($hpp->jumlah_volume_satuan, true);
    $hpp->harga_material = json_decode($hpp->harga_material, true);
    $hpp->harga_consumable = json_decode($hpp->harga_consumable, true);
    $hpp->harga_upah = json_decode($hpp->harga_upah, true);
    $hpp->jumlah_harga_material = json_decode($hpp->jumlah_harga_material, true);
    $hpp->jumlah_harga_consumable = json_decode($hpp->jumlah_harga_consumable, true);
    $hpp->jumlah_harga_upah = json_decode($hpp->jumlah_harga_upah, true);
    $hpp->harga_total = json_decode($hpp->harga_total, true);
    $hpp->keterangan = json_decode($hpp->keterangan, true);

    // Kirim data ke view edit
    return view('admin.inputhpp.edit', compact('hpp'));
}


public function createHpp1()
{
    // Ambil notifikasi yang belum memiliki HPP dengan source_form createhpp1
    $notifications = Notification::whereNotIn('notification_number', function($query) {
        $query->select('notification_number')->from('hpp1');
    })->get();

    $source_form = 'createhpp1';

    // Ambil Outline Agreement yang aktif berdasarkan tanggal saat ini
    $currentDate = now()->format('Y-m-d');
    $currentOA = KuotaAnggaranOA::where('periode_kontrak_start', '<=', $currentDate)
                ->where('periode_kontrak_end', '>=', $currentDate)
                ->first();

    // Kirim data ke view, termasuk Outline Agreement yang aktif
    return view('admin.inputhpp.createhpp1', compact('notifications', 'source_form', 'currentOA'));
}

    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'notification_number' => 'required|string|max:255',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);
    // Periksa apakah notification_number sudah ada
    $existingHpp = Hpp1::where('notification_number', $request->notification_number)->first();

    if ($existingHpp) {
        // Jika notification_number sudah ada, return dengan alert
        return redirect()->back()->with('error', 'Notifikasi Tersebut Telah dibuatkan HPP Silahkan Kembali Ke Halaman Notifikasi.');
    }
    
        // Proses dan simpan data ke dalam tabel hpps
        $hpp = new Hpp1();
        $hpp->source_form = $request->input('source_form', '');
        $hpp->notification_number = $request->input('notification_number', '-');
        $hpp->cost_centre = $request->input('cost_centre', '-');
        $hpp->description = $request->input('description', '-');
        $hpp->usage_plan = $request->input('usage_plan', '-');
        $hpp->completion_target = $request->input('completion_target', '-');
        $hpp->requesting_unit = $request->input('requesting_unit', '-');
        $hpp->controlling_unit = $request->input('controlling_unit', '-');
        $hpp->outline_agreement = $request->input('outline_agreement', '-');
    
        // Uraian pekerjaan dan sub-uraian pekerjaan
        $hpp->uraian_pekerjaan = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('uraian_pekerjaan', ["-"])));
    
        $hpp->jenis_material = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('jenis_material', ["-"])));
    
        // Lanjutkan dengan pola yang sama untuk semua kolom JSON
        $hpp->qty = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('qty', ["-"])));
    
        $hpp->satuan = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('satuan', ["-"])));
    
        $hpp->volume_satuan = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('volume_satuan', ["-"])));
    
        $hpp->jumlah_volume_satuan = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('jumlah_volume_satuan', ["-"])));
    
        $hpp->harga_material = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('harga_material', ["-"])));
    
        $hpp->harga_consumable = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('harga_consumable', ["-"])));
    
        $hpp->harga_upah = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('harga_upah', ["-"])));
    
        $hpp->jumlah_harga_material = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('jumlah_harga_material', ["-"])));
    
        $hpp->jumlah_harga_consumable = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('jumlah_harga_consumable', ["-"])));
    
        $hpp->jumlah_harga_upah = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('jumlah_harga_upah', ["-"])));
    
        $hpp->harga_total = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('harga_total', ["-"])));
    
        $hpp->keterangan = json_encode(array_map(function($value) {
            return $value ?: '-';
        }, $request->input('keterangan', ["-"])));
    
        $hpp->total_amount = $request->input('total_amount', 0);
    
        $hpp->save();
        $managers = User::where('unit_work', 'Workshop & Construction')
        ->where('jabatan', 'Manager')
        ->get();

        foreach ($managers as $manager) {
        try {
        Http::withHeaders([
            'Authorization' => 'KBTe2RszCgc6aWhYapcv' // API key Fonnte Anda
        ])->post('https://api.fonnte.com/send', [
            'target' => $manager->whatsapp_number,
            'message' => "Permintaan Approval Pekerjaan HPP:\nNomor Notifikasi: {$hpp->notification_number}\nUnit Kerja: {$hpp->controlling_unit}\nDeskripsi: {$hpp->description}\n\nSilakan login untuk melihat detailnya:\nhttps://sectionofworkshop.com/approval/hpp",
        ]);

        \Log::info("WhatsApp notification sent to Manager: " . $manager->whatsapp_number);
        } catch (\Exception $e) {
        \Log::error("Gagal mengirim WhatsApp ke {$manager->whatsapp_number}: " . $e->getMessage());
        }
        }
  
        return redirect()->route('admin.inputhpp.index')->with('success', 'HPP berhasil dibuat.');
    }
    public function update(Request $request, $notification_number)
{
    // Validasi input
    $request->validate([
        'description' => 'required|string',
        'requesting_unit' => 'required|string',
        'uraian_pekerjaan' => 'required|array',
        'jenis_material' => 'required|array',
        'qty' => 'required|array',
        'satuan' => 'required|array',
        'volume_satuan' => 'required|array',
        'jumlah_volume_satuan' => 'required|array',
        'harga_material' => 'required|array',
        'harga_consumable' => 'required|array',
        'harga_upah' => 'required|array',
        'harga_total' => 'required|array',
        'keterangan' => 'nullable|array',
        'total_amount' => 'required|numeric',
    ]);

    // Cari data HPP berdasarkan notification_number
    $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();

    // Update data
    $hpp->description = $request->description;
    $hpp->requesting_unit = $request->requesting_unit;
    $hpp->uraian_pekerjaan = json_encode($request->uraian_pekerjaan);
    $hpp->jenis_material = json_encode($request->jenis_material);
    $hpp->qty = json_encode($request->qty);
    $hpp->satuan = json_encode($request->satuan);
    $hpp->volume_satuan = json_encode($request->volume_satuan);
    $hpp->jumlah_volume_satuan = json_encode($request->jumlah_volume_satuan);
    $hpp->harga_material = json_encode($request->harga_material);
    $hpp->harga_consumable = json_encode($request->harga_consumable);
    $hpp->harga_upah = json_encode($request->harga_upah);
    $hpp->harga_total = json_encode($request->harga_total);
    $hpp->keterangan = json_encode($request->keterangan);
    $hpp->total_amount = $request->total_amount;

    $hpp->save();

    return redirect()->route('admin.inputhpp.index')->with('success', 'Data berhasil diperbarui.');
}


    public function viewHpp1($notification_number)
    {
        $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();
    
        // Decode JSON fields into arrays
        $hpp->uraian_pekerjaan = json_decode($hpp->uraian_pekerjaan, true);
        $hpp->jenis_material = json_decode($hpp->jenis_material, true);
        $hpp->qty = json_decode($hpp->qty, true);
        $hpp->satuan = json_decode($hpp->satuan, true);
        $hpp->volume_satuan = json_decode($hpp->volume_satuan, true);
        $hpp->jumlah_volume_satuan = json_decode($hpp->jumlah_volume_satuan, true);
        $hpp->harga_material = json_decode($hpp->harga_material, true);
        $hpp->harga_consumable = json_decode($hpp->harga_consumable, true);
        $hpp->harga_upah = json_decode($hpp->harga_upah, true);
        $hpp->jumlah_harga_material = json_decode($hpp->jumlah_harga_material, true);
        $hpp->jumlah_harga_consumable = json_decode($hpp->jumlah_harga_consumable, true);
        $hpp->jumlah_harga_upah = json_decode($hpp->jumlah_harga_upah, true);
        $hpp->harga_total = json_decode($hpp->harga_total, true);
        $hpp->keterangan = json_decode($hpp->keterangan, true);
    
        return view('admin.inputhpp.viewhpp1', compact('hpp'));
    }
    
// public function destroy($notification_number)
// {
//     // Mencari data HPP berdasarkan notification_number
//     $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();

//     // Menghapus data HPP
//     $hpp->delete();

//     // Redirect dengan pesan sukses
//     return redirect()->route('admin.inputhpp.index')->with('success', 'Dokumen HPP berhasil dihapus.');
// }

}
