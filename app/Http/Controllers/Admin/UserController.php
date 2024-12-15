<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Fungsi untuk menampilkan daftar pengguna dalam bentuk tabel berdasarkan usertype
    public function index()
    {
        // Mengelompokkan pengguna berdasarkan usertype
        $admins = User::where('usertype', 'admin')->get();
        $pkms = User::where('usertype', 'pkm')->get();
        $approvals = User::where('usertype', 'approval')->get();
        $users = User::where('usertype', 'user')->get();

        // Return view dengan data yang sudah dikelompokkan
        return view('admin.user.index', compact('admins', 'pkms', 'approvals', 'users'));
    }

    // Fungsi untuk menampilkan form edit user
    public function edit($id)
    {
        // Mengambil data user berdasarkan ID
        $user = User::findOrFail($id);
        
        // Mengembalikan data dalam format JSON
        return response()->json($user);
    }
    
    
    // Fungsi untuk memperbarui data user
    public function update(Request $request, $id)
    {
        // Validasi data yang di-submit
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'usertype' => 'required|string',
            'departemen' => 'nullable|string',
            'unit_work' => 'nullable|string',
            'seksi' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'whatsapp_number' => 'nullable|string', // Tambahkan validasi untuk whatsapp_number
        ]);
    
        // Mengambil user berdasarkan ID
        $user = User::findOrFail($id);
    
        // Memperbarui data user
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'usertype' => $request->input('usertype'),
            'departemen' => $request->input('departemen'),
            'unit_work' => $request->input('unit_work'),
            'seksi' => $request->input('seksi'),
            'jabatan' => $request->input('jabatan'),
            'whatsapp_number' => $request->input('whatsapp_number'), // Update whatsapp_number
        ]);
    
        // Redirect kembali ke halaman daftar user dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }
    
    
    // Fungsi untuk menghapus user
    public function destroy($id)
    {
        // Cari user berdasarkan ID
        $user = User::findOrFail($id);

        // Hapus user dari database
        $user->delete();

        // Redirect kembali ke halaman daftar user dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
