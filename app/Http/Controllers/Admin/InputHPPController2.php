<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hpp1;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use App\Models\KuotaAnggaranOA;

class InputHPPController2 extends Controller

{
    public function createHpp2()
    {
        // Ambil notifikasi yang belum memiliki HPP dengan source_form createhpp1
        $notifications = Notification::whereNotIn('notification_number', function($query) {
            $query->select('notification_number')->from('hpp1');
        })->get();
    
        $source_form = 'createhpp2';
    
        // Ambil Outline Agreement yang aktif berdasarkan tanggal saat ini
        $currentDate = now()->format('Y-m-d');
        $currentOA = KuotaAnggaranOA::where('periode_kontrak_start', '<=', $currentDate)
                    ->where('periode_kontrak_end', '>=', $currentDate)
                    ->first();
    
        // Kirim data ke view, termasuk Outline Agreement yang aktif
        return view('admin.inputhpp.createhpp2', compact('notifications', 'source_form', 'currentOA'));
    }
    
    
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'notification_number' => 'required|string|max:255',
            // Tambahkan validasi lain sesuai kebutuhan
        ]);

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
            'message' => "Permintaan Approval HPP:\nNomor Notifikasi: {$hpp->notification_number}\nUnit Kerja: {$hpp->controlling_unit}\nDeskripsi: {$hpp->description}\n\nSilakan login untuk melihat detailnya:\nhttps://bengkelmesin.com/hpp",
        ]);

        \Log::info("WhatsApp notification sent to Manager: " . $manager->whatsapp_number);
        } catch (\Exception $e) {
        \Log::error("Gagal mengirim WhatsApp ke {$manager->whatsapp_number}: " . $e->getMessage());
        }
        }
  
        return redirect()->route('admin.inputhpp.index')->with('success', 'HPP berhasil dibuat.');
    }

    public function viewHpp2($notification_number)
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

        return view('admin.inputhpp.viewhpp2', compact('hpp'));
    }
}
