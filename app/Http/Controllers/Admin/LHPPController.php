<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LHPP;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Http;

class LHPPController extends Controller
{
    public function index()
    {
        // Ambil data LHPP dengan pagination
        $lhpps = LHPP::paginate(10); // Ambil 10 data per halaman
        
        // Kirim data ke view
        return view('admin.lhpp.index', compact('lhpps'));
    }
    
    /**
     * Menampilkan form LHPP
     */
    public function create()
    {
        // Ambil notifikasi yang belum ada di tabel 'lhpp'
        $notifications = Notification::whereNotIn('notification_number', function ($query) {
            $query->select('notification_number')->from('lhpp');
        })->get();

        // Kirim variabel $notifications ke view
        return view('admin.lhpp.create', compact('notifications'));
    }
    

    /**
     * Menyimpan data dari form LHPP
     */
    public function store(Request $request)
    {
        // Validasi input form
        $validated = $request->validate([
            'notification_number' => 'required|string|max:255',
            'nomor_order' => 'required|string|max:255',
            'description_notifikasi' => 'nullable|string',
            'purchase_order_number' => 'required|string|max:255',
            'unit_kerja' => 'required|string|max:255',
            'tanggal_selesai' => 'required|date',
            'waktu_pengerjaan' => 'required|integer',

            // Validasi array untuk material
            'material_description' => 'required|array',
            'material_volume' => 'required|array',
            'material_harga_satuan' => 'required|array',
            'material_jumlah' => 'required|array',

            // Validasi array untuk consumable
            'consumable_description' => 'required|array',
            'consumable_volume' => 'required|array',
            'consumable_harga_satuan' => 'required|array',
            'consumable_jumlah' => 'required|array',

            // Validasi array untuk upah kerja
            'upah_description' => 'required|array',
            'upah_volume' => 'required|array',
            'upah_harga_satuan' => 'required|array',
            'upah_jumlah' => 'required|array',

            // Subtotal dan total
            'material_subtotal' => 'required|numeric',
            'consumable_subtotal' => 'required|numeric',
            'upah_subtotal' => 'required|numeric',
            'total_biaya' => 'required|numeric',

            // Kontrak PKM
            'kontrak_pkm' => 'required|string|in:Fabrikasi,Konstruksi,Pengerjaan Mesin',
        ]);

        try {
            // Simpan data ke database
            LHPP::create([
                'notification_number' => $validated['notification_number'],
                'nomor_order' => $validated['nomor_order'],
                'description_notifikasi' => $validated['description_notifikasi'],
                'purchase_order_number' => $validated['purchase_order_number'],
                'unit_kerja' => $validated['unit_kerja'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'waktu_pengerjaan' => $validated['waktu_pengerjaan'],

                // Material data
                'material_description' => $validated['material_description'],
                'material_volume' => $validated['material_volume'],
                'material_harga_satuan' => $validated['material_harga_satuan'],
                'material_jumlah' => $validated['material_jumlah'],

                // Consumable data
                'consumable_description' => $validated['consumable_description'],
                'consumable_volume' => $validated['consumable_volume'],
                'consumable_harga_satuan' => $validated['consumable_harga_satuan'],
                'consumable_jumlah' => $validated['consumable_jumlah'],

                // Upah data
                'upah_description' => $validated['upah_description'],
                'upah_volume' => $validated['upah_volume'],
                'upah_harga_satuan' => $validated['upah_harga_satuan'],
                'upah_jumlah' => $validated['upah_jumlah'],

                // Subtotal dan total
                'material_subtotal' => $validated['material_subtotal'],
                'consumable_subtotal' => $validated['consumable_subtotal'],
                'upah_subtotal' => $validated['upah_subtotal'],
                'total_biaya' => $validated['total_biaya'],

                // Kontrak PKM
                'kontrak_pkm' => $validated['kontrak_pkm'],
            ]);
                
            // Kirim notifikasi WhatsApp ke Manager dengan unit_work "Workshop & Construction"
            $managers = User::where('unit_work', 'Workshop & Construction')
                            ->where('jabatan', 'Manager')
                            ->get();

            foreach ($managers as $manager) {
                try {
                    $message = "Permintaan Approval Pembuatan LHPP:\nNomor Notifikasi: {$validated['notification_number']}\nDeskripsi: {$validated['description_notifikasi']}\nUnit Kerja: {$validated['unit_kerja']}\n\nSilakan login untuk melihat detailnya:\nhttps://sectionofworkshop.com/approval/lhpp";

                    Http::withHeaders([
                        'Authorization' => 'KBTe2RszCgc6aWhYapcv' // API key Fonnte Anda
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $manager->whatsapp_number,
                        'message' => $message,
                    ]);

                    \Log::info("WhatsApp notification sent to Manager: " . $manager->whatsapp_number);
                } catch (\Exception $e) {
                    \Log::error("Gagal mengirim WhatsApp ke {$manager->whatsapp_number}: " . $e->getMessage());
                }
            }

            return redirect()->route('lhpp.index')->with('success', 'Data LHPP berhasil disimpan.');
        } catch (\Exception $e) {
            \Log::error("Error saving LHPP: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data LHPP.')->withInput();
        }
    }

    public function getPurchaseOrder($notificationNumber)
    {
        $notification = Notification::where('notification_number', $notificationNumber)->with('purchaseOrder')->first();
        
        if ($notification && $notification->purchaseOrder) {
            return response()->json(['purchase_order_number' => $notification->purchaseOrder->purchase_order_number]);
        }
    
        return response()->json(['purchase_order_number' => '-']);
    }

    public function calculateWorkDuration($notificationNumber, $tanggalSelesai)
    {
        $notification = Notification::where('notification_number', $notificationNumber)->with('purchaseOrder')->first();
        
        if ($notification && $notification->purchaseOrder && $notification->purchaseOrder->update_date) {
            $updateDate = $notification->purchaseOrder->update_date;
            $selesai = new \DateTime($tanggalSelesai);
            $updateDate = new \DateTime($updateDate);

            // Hitung selisih hari antara update PO dan tanggal selesai
            $diff = $updateDate->diff($selesai)->days;

            return response()->json(['waktu_pengerjaan' => $diff]);
        }

        return response()->json(['waktu_pengerjaan' => 0]);
    }

    public function show($id)
    {
        $lhpp = LHPP::findOrFail($id);
        
        // Cek apakah field adalah string sebelum json_decode
        $lhpp->material_description = is_string($lhpp->material_description) ? json_decode($lhpp->material_description) : $lhpp->material_description;
        $lhpp->material_volume = is_string($lhpp->material_volume) ? json_decode($lhpp->material_volume) : $lhpp->material_volume;
        $lhpp->material_harga_satuan = is_string($lhpp->material_harga_satuan) ? json_decode($lhpp->material_harga_satuan) : $lhpp->material_harga_satuan;
        $lhpp->material_jumlah = is_string($lhpp->material_jumlah) ? json_decode($lhpp->material_jumlah) : $lhpp->material_jumlah;
    
        $lhpp->consumable_description = is_string($lhpp->consumable_description) ? json_decode($lhpp->consumable_description) : $lhpp->consumable_description;
        $lhpp->consumable_volume = is_string($lhpp->consumable_volume) ? json_decode($lhpp->consumable_volume) : $lhpp->consumable_volume;
        $lhpp->consumable_harga_satuan = is_string($lhpp->consumable_harga_satuan) ? json_decode($lhpp->consumable_harga_satuan) : $lhpp->consumable_harga_satuan;
        $lhpp->consumable_jumlah = is_string($lhpp->consumable_jumlah) ? json_decode($lhpp->consumable_jumlah) : $lhpp->consumable_jumlah;
    
        $lhpp->upah_description = is_string($lhpp->upah_description) ? json_decode($lhpp->upah_description) : $lhpp->upah_description;
        $lhpp->upah_volume = is_string($lhpp->upah_volume) ? json_decode($lhpp->upah_volume) : $lhpp->upah_volume;
        $lhpp->upah_harga_satuan = is_string($lhpp->upah_harga_satuan) ? json_decode($lhpp->upah_harga_satuan) : $lhpp->upah_harga_satuan;
        $lhpp->upah_jumlah = is_string($lhpp->upah_jumlah) ? json_decode($lhpp->upah_jumlah) : $lhpp->upah_jumlah;
    
        return view('admin.lhpp.show', compact('lhpp'));
    }
    public function edit($id)
{
    // Ambil data LHPP berdasarkan id/notification_number
    $lhpp = LHPP::findOrFail($id);

    // Decode JSON jika field berisi string JSON
    $lhpp->material_description = is_string($lhpp->material_description) ? json_decode($lhpp->material_description) : $lhpp->material_description;
    $lhpp->material_volume = is_string($lhpp->material_volume) ? json_decode($lhpp->material_volume) : $lhpp->material_volume;
    $lhpp->material_harga_satuan = is_string($lhpp->material_harga_satuan) ? json_decode($lhpp->material_harga_satuan) : $lhpp->material_harga_satuan;
    $lhpp->material_jumlah = is_string($lhpp->material_jumlah) ? json_decode($lhpp->material_jumlah) : $lhpp->material_jumlah;

    // Kirim data ke view
    return view('admin.lhpp.edit', compact('lhpp'));
}

public function update(Request $request, $id)
{
    // Validasi input form
    $validated = $request->validate([
        'nomor_order' => 'required|string|max:255',
        'description_notifikasi' => 'nullable|string',
        'purchase_order_number' => 'required|string|max:255',
        'unit_kerja' => 'required|string|max:255',
        'tanggal_selesai' => 'required|date',
        'waktu_pengerjaan' => 'required|integer',

        // Validasi array untuk material
        'material_description' => 'required|array',
        'material_volume' => 'required|array',
        'material_harga_satuan' => 'required|array',
        'material_jumlah' => 'required|array',

        // Validasi array untuk consumable
        'consumable_description' => 'required|array',
        'consumable_volume' => 'required|array',
        'consumable_harga_satuan' => 'required|array',
        'consumable_jumlah' => 'required|array',

        // Validasi array untuk upah kerja
        'upah_description' => 'required|array',
        'upah_volume' => 'required|array',
        'upah_harga_satuan' => 'required|array',
        'upah_jumlah' => 'required|array',

        // Subtotal dan total
        'material_subtotal' => 'required|numeric',
        'consumable_subtotal' => 'required|numeric',
        'upah_subtotal' => 'required|numeric',
        'total_biaya' => 'required|numeric',
    ]);

    // Cari data LHPP yang akan di-update
    $lhpp = LHPP::findOrFail($id);

    // Update data dengan input baru
    $lhpp->update($validated);

    // Redirect ke halaman index setelah update
    return redirect()->route('lhpp.index')->with('success', 'Data LHPP berhasil diperbarui.');
}
public function destroy($notification_number)
{
    // Cari data berdasarkan notification_number
    $lhpp = LHPP::findOrFail($notification_number);

    // Hapus data
    $lhpp->delete();

    // Redirect kembali ke halaman index dengan pesan sukses
    return redirect()->route('lhpp.index')->with('success', 'Data berhasil dihapus.');
}

    
}
