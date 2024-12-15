<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SPK;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SPKNotification;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Http;


class SPKApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        // Cek apakah user dari unit_work "Workshop & Construction"
        if ($user->unit_work !== 'Workshop & Construction') {
            // Jika bukan, kembalikan halaman kosong atau pesan error
            return view('approval.spk.index', ['spks' => []]);
        }
    
        // Ambil dokumen SPK berdasarkan status penandatanganan
        $spks = SPK::where(function ($query) use ($user) {
                    if ($user->jabatan === 'Manager') {
                        // Tampilkan semua dokumen, baik yang sudah ditandatangani atau belum
                        $query->whereNull('manager_signature')
                              ->orWhereNotNull('manager_signature');
                    } elseif ($user->jabatan === 'Senior Manager') {
                        // Tampilkan semua dokumen, baik yang sudah ditandatangani atau belum
                        $query->whereNotNull('manager_signature')
                              ->orWhereNotNull('senior_manager_signature');
                    }
                })
                ->orderByRaw("CASE 
                    WHEN manager_signature IS NULL THEN 0 
                    WHEN senior_manager_signature IS NULL THEN 1 
                    ELSE 2 
                END")
                ->get();
    
        // Kirim data SPK ke view
        return view('approval.spk.index', compact('spks'));
    }
    
    public function saveSignature(Request $request, $signType, $nomorSpk)
    {
        try {
            \Log::info('Received signType: ' . $signType);
            \Log::info('SPK Number: ' . $nomorSpk);
            \Log::info('Signature: ' . $request->tanda_tangan);
    
            $request->validate([
                'tanda_tangan' => 'required',
            ]);
    
            // Ambil data SPK berdasarkan nomor SPK
            $spk = SPK::where('nomor_spk', $nomorSpk)->firstOrFail();
    
            if ($signType === 'manager') {
                $spk->manager_signature = $request->tanda_tangan;
                $spk->save();
    
                // Kirim notifikasi WhatsApp ke Senior Manager setelah Manager menandatangani
                $seniorManagers = User::where('unit_work', 'Workshop & Construction')
                                      ->where('jabatan', 'Senior Manager')
                                      ->get();
    
                foreach ($seniorManagers as $seniorManager) {
                    $message = "Permintaan Approval Dokumen SPK:\nNomor SPK: {$spk->nomor_spk}\nPerihal: {$spk->perihal}\nTanggal SPK: {$spk->tanggal_spk}\nUnit Kerja: {$spk->unit_work}\n\nSilakan login untuk melihat dan menandatangani dokumen:\nhttps://sectionofworkshop.com/approval/spk";
    
                    Http::withHeaders([
                        'Authorization' => 'KBTe2RszCgc6aWhYapcv' // API key Fonnte Anda
                    ])->post('https://api.fonnte.com/send', [
                        'target' => $seniorManager->whatsapp_number,
                        'message' => $message,
                    ]);
    
                    \Log::info('WhatsApp notification sent to Senior Manager: ' . $seniorManager->whatsapp_number);
                }
            } elseif ($signType === 'senior_manager') {
                $spk->senior_manager_signature = $request->tanda_tangan;
                $spk->save();
            }
    
            return response()->json(['message' => 'Signature saved successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan tanda tangan', 'details' => $e->getMessage()], 500);
        }
    }
}    
