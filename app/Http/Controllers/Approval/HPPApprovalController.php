<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hpp1;
use App\Models\User;
use Illuminate\Support\Facades\Http;



class HPPApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
    
        // Cek apakah user dari unit_work "Workshop & Construction"
        if ($user->unit_work === 'Workshop & Construction') {
            // Ambil semua dokumen HPP untuk unit "Workshop & Construction"
            $hppDocuments = Hpp1::with([
                'managerSignatureRequestingUser', 
                'generalManagerRequestingUser', 
                'seniorManagerRequestingUser',
                'managerSignatureUser',
                'seniorManagerSignatureUser',
                'generalManagerSignatureUser',
                'directorSignatureUser'
            ])
            ->orderByRaw("CASE 
                WHEN manager_signature_user_id IS NULL THEN 0 
                WHEN senior_manager_signature_user_id IS NULL THEN 1 
                WHEN general_manager_signature_user_id IS NULL THEN 2 
                WHEN director_signature_user_id IS NULL THEN 3 
                ELSE 4 
            END")
            ->get();
        } else {
            // Ambil dokumen HPP berdasarkan requesting_unit yang cocok dengan unit_work user
            $hppDocuments = Hpp1::with([
                'managerSignatureRequestingUser', 
                'generalManagerRequestingUser', 
                'seniorManagerRequestingUser',
                'managerSignatureUser',
                'seniorManagerSignatureUser',
                'generalManagerSignatureUser',
                'directorSignatureUser'
            ])
            ->where('requesting_unit', $user->unit_work)
            ->orderByRaw("CASE 
                WHEN manager_signature_requesting_user_id IS NULL THEN 0 
                WHEN senior_manager_signature_requesting_user_id IS NULL THEN 1 
                WHEN general_manager_signature_requesting_user_id IS NULL THEN 2 
                ELSE 3 
            END")
            ->get();
        }
    
        // Kirim data HPP ke view
        return view('approval.hpp.index', compact('hppDocuments'));
    }
    public function viewHpp1($notification_number)
    {
        $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();
        return view('approval.hpp.viewhpp1', compact('hpp'));
    }
    
    public function viewHpp2($notification_number)
    {
        $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();
        return view('approval.hpp.viewhpp2', compact('hpp'));
    }
    
    public function viewHpp3($notification_number)
    {
        $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();
        return view('approval.hpp.viewhpp3', compact('hpp'));
    }
    
        
    public function saveSignature(Request $request, $signType, $notificationNumber)
    {
        try {
            \Log::info('Received signType: ' . $signType);
            \Log::info('Notification Number: ' . $notificationNumber);
            \Log::info('Signature: ' . $request->tanda_tangan);
    
            $request->validate([
                'tanda_tangan' => 'required',
            ]);
    
            $hpp = Hpp1::where('notification_number', $notificationNumber)->firstOrFail();
    
            if ($signType == 'manager') {
                $hpp->manager_signature = $request->tanda_tangan;
                $hpp->manager_signature_user_id = auth()->user()->id;
                $hpp->save();
    
                // Notify Senior Manager of "Workshop & Construction"
                $this->notifyNextRole('Senior Manager', 'Workshop & Construction', $hpp);
            } elseif ($signType == 'senior_manager') {
                $hpp->senior_manager_signature = $request->tanda_tangan;
                $hpp->senior_manager_signature_user_id = auth()->user()->id;
                $hpp->save();
    
                // Notify Manager of Requesting Unit
                $this->notifyNextRole('Manager', $hpp->requesting_unit, $hpp);
            } elseif ($signType == 'manager_requesting') {
                $hpp->manager_signature_requesting_unit = $request->tanda_tangan;
                $hpp->manager_signature_requesting_user_id = auth()->user()->id;
                $hpp->save();
    
                // Notify Senior Manager of Requesting Unit
                $this->notifyNextRole('Senior Manager', $hpp->requesting_unit, $hpp);
            } elseif ($signType == 'senior_manager_requesting') {
                $hpp->senior_manager_signature_requesting_unit = $request->tanda_tangan;
                $hpp->senior_manager_signature_requesting_user_id = auth()->user()->id;
                $hpp->save();
    
                // Notify General Manager of "Workshop & Construction"
                $this->notifyNextRole('General Manager', 'Workshop & Construction', $hpp);
            } elseif ($signType == 'general_manager') {
                $hpp->general_manager_signature = $request->tanda_tangan;
                $hpp->general_manager_signature_user_id = auth()->user()->id;
                $hpp->save();
    
                // Notify General Manager of Requesting Unit
                $this->notifyNextRole('General Manager', $hpp->requesting_unit, $hpp);
            } elseif ($signType == 'general_manager_requesting') {
                $hpp->general_manager_signature_requesting_unit = $request->tanda_tangan;
                $hpp->general_manager_signature_requesting_user_id = auth()->user()->id;
                $hpp->save();
    
                // Notify Director
                $this->notifyNextRole('Director', null, $hpp);
            } elseif ($signType == 'director') {
                $hpp->director_signature = $request->tanda_tangan;
                $hpp->director_signature_user_id = auth()->user()->id;
                $hpp->save();
    
                \Log::info('All approvals completed for Notification Number: ' . $notificationNumber);
            }
    
            return response()->json(['message' => 'Signature saved successfully!'], 200);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Error saving signature', 'details' => $e->getMessage()], 500);
        }
    }
    
    protected function notifyNextRole($role, $unitWork, $hpp)
    {
        $query = User::where('jabatan', $role);
        if ($unitWork) {
            $query->where('unit_work', $unitWork);
        }
        $users = $query->get();
    
        foreach ($users as $user) {
            $message = "Permintaan Approval Pembuatan HPP :\nNotification Number: {$hpp->notification_number}\nDescription: {$hpp->description}\nPlease log in to review:\nhttps://sectionofworkshop.com/approval/hpp";
    
            Http::withHeaders([
                'Authorization' => 'KBTe2RszCgc6aWhYapcv'
            ])->post('https://api.fonnte.com/send', [
                'target' => $user->whatsapp_number,
                'message' => $message,
            ]);
    
            \Log::info("WhatsApp notification sent to {$role}: " . $user->whatsapp_number);
        }
    }
    

    public function reject(Request $request, $signType, $notificationNumber)
    {
        // Validasi alasan penolakan
        $request->validate([
            'reason' => 'required|string',
        ]);

        $hpp = Hpp1::where('notification_number', $notificationNumber)->firstOrFail();

        // Simpan alasan penolakan dan tandai dokumen ditolak
        $hpp->rejection_reason = $request->reason;

        if ($signType === 'manager') {
            $hpp->manager_signature = 'rejected';
        } elseif ($signType === 'senior_manager') {
            $hpp->senior_manager_signature = 'rejected';
        } elseif ($signType === 'general_manager') {
            $hpp->general_manager_signature = 'rejected';
        } elseif ($signType === 'director') {
            $hpp->director_signature = 'rejected';
        } elseif ($signType === 'manager_requesting') {
            $hpp->manager_signature_requesting_unit = 'rejected';
        } elseif ($signType === 'senior_manager_requesting') {
            $hpp->senior_manager_signature_requesting_unit = 'rejected';
        } elseif ($signType === 'general_manager_requesting') {
            $hpp->general_manager_signature_requesting_unit = 'rejected';
        }

        $hpp->save();

        return response()->json(['message' => 'Document rejected successfully!'], 200);
    }
    public function saveNotes(Request $request, $notification_number, $type)
    {
        $hpp = Hpp1::where('notification_number', $notification_number)->firstOrFail();
        
        if ($type == 'controlling') {
            $existingNotes = $hpp->controlling_notes ? json_decode($hpp->controlling_notes, true) : [];
            $newNotes = $request->input('controlling_notes');
    
            // Tambahkan catatan baru dengan informasi user_id
            foreach (array_filter($newNotes) as $note) {
                $existingNotes[] = [
                    'note' => $note,
                    'user_id' => auth()->user()->id, // Simpan ID user yang menambahkan catatan
                ];
            }
    
            $hpp->controlling_notes = json_encode($existingNotes);
        } elseif ($type == 'requesting') {
            $existingNotes = $hpp->requesting_notes ? json_decode($hpp->requesting_notes, true) : [];
            $newNotes = $request->input('requesting_notes');
    
            // Tambahkan catatan baru dengan informasi user_id
            foreach (array_filter($newNotes) as $note) {
                $existingNotes[] = [
                    'note' => $note,
                    'user_id' => auth()->user()->id, // Simpan ID user yang menambahkan catatan
                ];
            }
    
            $hpp->requesting_notes = json_encode($existingNotes);
        }
        
        $hpp->save();
    
        return redirect()->back()->with('success', 'Catatan berhasil disimpan.');
    }
    public function getOldSignature($signType, $notificationNumber)
    {
        // Logging untuk debug
        \Log::info('Sign Type: ' . $signType);
        \Log::info('Notification Number: ' . $notificationNumber);
    
        // Cari dokumen berdasarkan nomor notifikasi
        $hpp = Hpp1::where('notification_number', $notificationNumber)->first();
    
        if (!$hpp) {
            \Log::error('Dokumen tidak ditemukan: ' . $notificationNumber);
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }
    
        $signature = null;
        $userId = null;
    
        // Tentukan kolom tanda tangan dan user_id berdasarkan tipe tanda tangan
        switch ($signType) {
            case 'manager':
                $signature = $hpp->manager_signature;
                $userId = $hpp->manager_signature_user_id;
                break;
            case 'senior_manager':
                $signature = $hpp->senior_manager_signature;
                $userId = $hpp->senior_manager_signature_user_id;
                break;
            case 'general_manager':
                $signature = $hpp->general_manager_signature;
                $userId = $hpp->general_manager_signature_user_id;
                break;
            case 'director':
                $signature = $hpp->director_signature;
                $userId = $hpp->director_signature_user_id;
                break;
            case 'manager_requesting':
                $signature = $hpp->manager_signature_requesting_unit;
                $userId = $hpp->manager_signature_requesting_user_id;
                break;
            case 'senior_manager_requesting':
                $signature = $hpp->senior_manager_signature_requesting_unit;
                $userId = $hpp->senior_manager_signature_requesting_user_id;
                break;
            case 'general_manager_requesting':
                $signature = $hpp->general_manager_signature_requesting_unit;
                $userId = $hpp->general_manager_signature_requesting_user_id;
                break;
            default:
                return response()->json(['message' => 'Tipe tanda tangan tidak valid'], 400);
        }
    
        // Jika tanda tangan ditemukan, kirimkan respon
        if ($signature && $userId) {
            $user = User::find($userId); // Ambil informasi user yang bertanda tangan
            return response()->json([
                'signature' => $signature,
                'user' => $user ? $user->only(['id', 'name', 'jabatan']) : null,
            ], 200);
        } else {
            return response()->json(['message' => 'Tanda tangan lama tidak ditemukan'], 404);
        }
    }
    
    

}
