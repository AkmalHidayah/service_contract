<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LHPP;
use App\Models\User;
use Illuminate\Support\Facades\Http; // Untuk API Fonnte
use Illuminate\Support\Facades\Log; // Untuk logging

class LHPPApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ambil dokumen LHPP berdasarkan unit kerja user dan logika berjenjang
        if ($user->unit_work === 'Workshop & Construction') {
            // Manager Workshop
            $lhppDocuments = LHPP::orderByRaw("CASE 
                    WHEN manager_signature_user_id IS NULL THEN 0 
                    WHEN manager_signature_requesting_user_id IS NULL THEN 1 
                    WHEN manager_pkm_signature_user_id IS NULL THEN 2 
                    ELSE 3 
                END")->get();
        } elseif (in_array($user->unit_work, ['Fabrikasi', 'Konstruksi', 'Pengerjaan Mesin'])) {
            // Manager PKM
            $lhppDocuments = LHPP::where(function ($query) use ($user) {
                $query->whereNotNull('manager_signature_requesting') // Manager Peminta sudah tanda tangan
                      ->whereNull('manager_pkm_signature') // Manager PKM belum tanda tangan
                      ->where('kontrak_pkm', $user->unit_work); // Kontrak PKM cocok dengan unit kerja user
            })
            ->orWhere(function ($query) {
                $query->whereNotNull('manager_signature') // Manager Workshop sudah tanda tangan
                      ->whereNull('manager_signature_requesting'); // Manager Peminta belum tanda tangan
            })
            ->orWhereNotNull('manager_pkm_signature') // Semua tanda tangan selesai
            ->orderByRaw("CASE 
                WHEN manager_signature_user_id IS NULL THEN 0 
                WHEN manager_signature_requesting_user_id IS NULL THEN 1 
                WHEN manager_pkm_signature_user_id IS NULL THEN 2 
                ELSE 3 
            END")
            ->get();
        } else {
            // Manager Peminta
            $lhppDocuments = LHPP::where('unit_kerja', $user->unit_work)
                ->where(function ($query) {
                    $query->whereNotNull('manager_signature') // Manager Workshop sudah tanda tangan
                          ->whereNull('manager_signature_requesting'); // Manager Peminta belum tanda tangan
                })
                ->orWhereNotNull('manager_signature_requesting') // Tanda tangan Peminta selesai
                ->orWhereNotNull('manager_pkm_signature') // Semua tanda tangan selesai
                ->orderByRaw("CASE 
                    WHEN manager_signature_user_id IS NULL THEN 0 
                    WHEN manager_signature_requesting_user_id IS NULL THEN 1 
                    WHEN manager_pkm_signature_user_id IS NULL THEN 2 
                    ELSE 3 
                END")
                ->get();
        }

        // Kirim data LHPP ke view
        return view('approval.lhpp.index', compact('lhppDocuments'));
    }
    
    public function saveSignature(Request $request, $signType, $notificationNumber)
    {
        try {
            $request->validate([
                'tanda_tangan' => 'required',
            ]);

            // Ambil dokumen LHPP berdasarkan nomor notifikasi
            $lhpp = LHPP::where('notification_number', $notificationNumber)->firstOrFail();

            // Simpan tanda tangan sesuai tipe
            if ($signType === 'manager') {
                $lhpp->manager_signature = $request->tanda_tangan;
                $lhpp->manager_signature_user_id = auth()->user()->id;

                // Kirim notifikasi WhatsApp ke Manager Peminta
                $this->sendWhatsAppNotification($lhpp, 'manager_requesting');
            } elseif ($signType === 'manager_requesting') {
                $lhpp->manager_signature_requesting = $request->tanda_tangan;
                $lhpp->manager_signature_requesting_user_id = auth()->user()->id;

                // Kirim notifikasi WhatsApp ke Manager PKM
                $this->sendWhatsAppNotification($lhpp, 'manager_pkm');
            } elseif ($signType === 'manager_pkm') {
                $lhpp->manager_pkm_signature = $request->tanda_tangan;
                $lhpp->manager_pkm_signature_user_id = auth()->user()->id;
            }

            // Simpan ke database
            $lhpp->save();

            return response()->json(['message' => 'Signature saved successfully!'], 200);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => 'Failed to save signature'], 500);
        }
    }

    private function sendWhatsAppNotification($lhpp, $signType)
    {
        $role = 'Manager'; // Jabatan tetap sebagai "Manager"
        $unitWork = $signType === 'manager_requesting' ? $lhpp->unit_kerja : $lhpp->kontrak_pkm;
    
        Log::info("Sending WhatsApp Notification to $role in Unit Work: $unitWork");
    
        $managers = User::where('jabatan', $role)
                        ->where('unit_work', $unitWork)
                        ->get();
    
        if ($managers->isEmpty()) {
            Log::warning("No managers found for role $role in unit $unitWork.");
            return; // Jika tidak ada Manager, hentikan proses
        }
    
        foreach ($managers as $manager) {
            try {
                $message = "Permintaan Approval Dokumen LHPP:\nNomor Notifikasi: {$lhpp->notification_number}\nDeskripsi: {$lhpp->description_notifikasi}\nUnit Kerja: {$lhpp->unit_kerja}\n\nSilakan login untuk melihat detailnya:\nhttps://sectionofworkshop.com/approval/lhpp";
    
                $response = Http::withHeaders([
                    'Authorization' => 'KBTe2RszCgc6aWhYapcv',
                ])->post('https://api.fonnte.com/send', [
                    'target' => $manager->whatsapp_number,
                    'message' => $message,
                ]);
    
                Log::info("Fonnte Response for {$manager->whatsapp_number}: ", $response->json());
            } catch (\Exception $e) {
                Log::error("Failed to send WhatsApp to {$manager->whatsapp_number}: " . $e->getMessage());
            }
        }
    }
    
     public function reject(Request $request, $signType, $notificationNumber)
    {
        // Validasi alasan penolakan
        $request->validate([
            'reason' => 'required|string',
        ]);
    
        // Cari dokumen LHPP berdasarkan nomor notifikasi
        $lhpp = LHPP::where('notification_number', $notificationNumber)->firstOrFail();
    
        // Simpan alasan penolakan dan tandai dokumen ditolak
        $lhpp->rejection_reason = $request->reason;
    
        // Update status reject berdasarkan tipe tanda tangan
        if ($signType === 'manager') {
            $lhpp->manager_signature = 'rejected';
            $lhpp->manager_signature_user_id = null;
        } elseif ($signType === 'manager_requesting') {
            $lhpp->manager_signature_requesting = 'rejected';
            $lhpp->manager_signature_requesting_user_id = null;
        } elseif ($signType === 'manager_pkm') {
            $lhpp->manager_pkm_signature = 'rejected';
            $lhpp->manager_pkm_signature_user_id = null;
        }
    
        // Simpan perubahan di database
        $lhpp->save();
    
        return response()->json(['message' => 'Document rejected successfully!'], 200);
    }

    public function saveNotes(Request $request, $notification_number, $type)
    {
        $lhpp = LHPP::where('notification_number', $notification_number)->firstOrFail();
        
        // Debug: Log isi request dan isi kolom sebelum di-update
        \Log::info("Incoming Request", $request->all());
        \Log::info("Existing Controlling Notes", [$lhpp->controlling_notes]);
        \Log::info("Existing Requesting Notes", [$lhpp->requesting_notes]);
    
        if ($type == 'controlling') {
            $existingNotes = $lhpp->controlling_notes ? json_decode($lhpp->controlling_notes, true) : [];
            $newNotes = $request->input('controlling_notes');
            
            foreach (array_filter($newNotes) as $note) {
                $existingNotes[] = [
                    'note' => $note,
                    'user_id' => auth()->user()->id,
                ];
            }
            $lhpp->controlling_notes = json_encode($existingNotes);
        } elseif ($type == 'requesting') {
            $existingNotes = $lhpp->requesting_notes ? json_decode($lhpp->requesting_notes, true) : [];
            $newNotes = $request->input('requesting_notes');
            
            foreach (array_filter($newNotes) as $note) {
                $existingNotes[] = [
                    'note' => $note,
                    'user_id' => auth()->user()->id,
                ];
            }
            $lhpp->requesting_notes = json_encode($existingNotes);
        }
        
        // Debug: Log nilai setelah diperbarui
        \Log::info("Updated Controlling Notes", [$lhpp->controlling_notes]);
        \Log::info("Updated Requesting Notes", [$lhpp->requesting_notes]);
    
        $lhpp->save();
        
        return redirect()->back()->with('success', 'Catatan berhasil disimpan.');
    }
    public function updateStatus(Request $request, $notification_number)
{
    // Validasi request untuk status approval
    $request->validate([
        'status_approve' => 'required|in:Approved,Rejected'
    ]);

    // Cari dokumen LHPP berdasarkan nomor notifikasi
    $lhpp = LHPP::where('notification_number', $notification_number)->firstOrFail();

    // Update status approval
    $lhpp->status_approve = $request->status_approve;
    $lhpp->save();

    // Redirect dengan pesan sukses
    return redirect()->back()->with('success', 'Status approval berhasil diperbarui.');
}

public function show($notification_number)
{
    $lhpp = LHPP::where('notification_number', $notification_number)->firstOrFail();
    return view('approval.lhpp.show', compact('lhpp'));
}


}
