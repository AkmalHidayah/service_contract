<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\ApprovalMiddleware;
use App\Http\Middleware\PkmMiddleware;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\Abnormal\AbnormalController;
use App\Http\Controllers\Abnormalitas\AbnormalitasController;
use App\Http\Controllers\ScopeOfWork\ScopeOfWorkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GambarTeknikController;
use App\Http\Controllers\Approval\ApprovalController;
use App\Http\Controllers\Admin\InputHPPController;
use App\Http\Controllers\Admin\InputHPPController2;
use App\Http\Controllers\Admin\InputHPPController3;
use App\Http\Controllers\Admin\SPKController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\LhppController;
use App\Http\Controllers\PKMDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Approval\SPKApprovalController;
use App\Http\Controllers\Approval\HPPApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Cek apakah pengguna sudah login
    if (auth()->check()) {
        return view('welcome'); // Tetap di halaman welcome meskipun login
    }
    return view('welcome'); // Tetap di halaman welcome jika belum login
})->name('welcome');


// Redirect to login if accessing root
Route::get('/login', function () {
    return redirect('login');
});

// Group routes that require authentication and email verification
Route::middleware(['auth', 'verified'])->group(function () {

// Dashboard route
Route::get('/dashboard', [DashboardUserController::class, 'index'])->name('dashboard');
// LHPP detail (accessible by any authenticated user)
Route::get('lhpp/{notification_number}', [App\Http\Controllers\Admin\LHPPController::class, 'show'])->name('lhpp.show');

// Notifikasi routes
Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifikasi', [NotificationController::class, 'store'])->name('notifications.store');
Route::get('/notifikasi/{notification_number}/edit', [NotificationController::class, 'edit'])->name('notifications.edit');
Route::patch('/notifikasi/{notification_number}', [NotificationController::class, 'update'])->name('notifications.update');
Route::delete('/notifikasi/{notification_number}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
Route::patch('/notifikasi/{notification_number}/priority', [NotificationController::class, 'updatePriority'])->name('notifications.updatePriority');
Route::patch('/admin/verifikasianggaran/update-status-anggaran/{notification_number}', [NotificationController::class, 'updateStatusAnggaran'])->name('notifications.updateStatusAnggaran');
Route::get('/notifikasi/{notification_number}', [NotificationController::class, 'show'])->name('notifications.show');

// Untuk Index di AbnormalitasController
Route::get('/abnormalitas', [App\Http\Controllers\Abnormalitas\AbnormalitasController::class, 'index'])->name('abnormalitas.index');

// Abnormal Routes
Route::get('/abnormal/create/{notificationNumber}', [App\Http\Controllers\Abnormal\AbnormalController::class, 'create'])->name('abnormal.create');
Route::post('/abnormal/store', [App\Http\Controllers\Abnormal\AbnormalController::class, 'store'])->name('abnormal.store');
Route::get('/abnormal/{notificationNumber}/edit', [App\Http\Controllers\Abnormal\AbnormalController::class, 'edit'])->name('abnormal.edit');
Route::patch('/abnormal/{notificationNumber}', [AbnormalController::class, 'update'])->name('abnormal.update');
Route::get('/abnormal/{notificationNumber}/view', [AbnormalController::class, 'show'])->name('abnormal.view');

// Scope of Work routes
Route::get('/scopeofwork', [ScopeOfWorkController::class, 'index'])->name('scopeofwork.index');
Route::get('/scopeofwork/create/{notificationNumber}', [ScopeOfWorkController::class, 'create'])->name('scopeofwork.create');
Route::post('/scopeofwork/store', [ScopeOfWorkController::class, 'store'])->name('scopeofwork.store');
Route::get('/scopeofwork/{notificationNumber}/edit', [ScopeOfWorkController::class, 'edit'])->name('scopeofwork.edit');
Route::patch('/scopeofwork/{notificationNumber}', [ScopeOfWorkController::class, 'update'])->name('scopeofwork.update');
Route::post('/save-signature', [ScopeOfWorkController::class, 'saveSignature'])->name('save.signature');
Route::get('/scopeofwork/{notificationNumber}/view', [ScopeOfWorkController::class, 'show'])->name('scopeofwork.view');


// Rute untuk upload dokumen
Route::post('/upload-dokumen', [GambarTeknikController::class, 'uploadDokumen'])->name('upload-dokumen');
// Rute untuk melihat dokumen
Route::get('/gambarteknik/{notificationNumber}/view', [GambarTeknikController::class, 'viewDokumen'])->name('view-dokumen');
// Rute untuk menghapus dokumen
Route::delete('/hapus-dokumen/{notificationNumber}', [GambarTeknikController::class, 'hapusDokumen'])->name('hapus-dokumen');


// Profile routes
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Authentication routes
require __DIR__.'/auth.php';

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

});

// Admin dashboard route
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/get-years', [HomeController::class, 'getYears']);
    Route::get('/admin/realisasi-biaya', [HomeController::class, 'realisasiBiaya']);
    Route::get('/admin/notifikasi', [HomeController::class, 'notifikasi'])->name('notifikasi.index');
    Route::get('/admin/verifikasianggaran', [HomeController::class, 'verifikasiAnggaran'])->name('admin.verifikasianggaran.index');
    Route::get('/admin/purchaserequest', [HomeController::class, 'purchaseRequest'])->name('admin.purchaserequest');
    Route::get('/admin/updateoa', [HomeController::class, 'updateOA'])->name('admin.updateoa');
    Route::post('/admin/updateoa', [HomeController::class, 'storeOA'])->name('admin.storeOA');
    Route::post('/admin/lpj/{notification_number}', [HomeController::class, 'updateLpj'])->name('lpj.update');
});
// Route untuk SPK
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Route untuk halaman pembuatan SPK, sekarang menerima nomor notifikasi (notification_number) sebagai parameter
    Route::get('/spk/create/{notificationNumber}', [SPKController::class, 'create'])->name('spk.create');
    
    // Route untuk menyimpan data SPK
    Route::post('/spk/store', [SPKController::class, 'store'])->name('spk.store');
    
    // Route untuk melihat detail SPK berdasarkan nomor notifikasi
    Route::get('/spk/{notification_number}', [SPKController::class, 'show'])->name('spk.show');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function() {
    Route::get('inputhpp', [InputHPPController::class, 'index'])->name('inputhpp.index');
    Route::get('inputhpp/create-hpp1', [InputHPPController::class, 'createHpp1'])->name('inputhpp.create_hpp1');
    Route::post('inputhpp/store', [InputHPPController::class, 'store'])->name('inputhpp.store'); 
    Route::get('inputhpp/create-hpp2', [InputHPPController2::class, 'createHpp2'])->name('inputhpp.create_hpp2');
    Route::get('inputhpp/create-hpp3', [InputHPPController3::class, 'createHpp3'])->name('inputhpp.create_hpp3');
    Route::get('inputhpp/view-hpp1/{notification_number}', [InputHPPController::class, 'viewHpp1'])->name('admin.inputhpp.view_hpp1');
    Route::get('inputhpp/view-hpp2/{notification_number}', [InputHPPController2::class, 'viewHpp2'])->name('admin.inputhpp.view_hpp2');
    Route::get('inputhpp/view-hpp3/{notification_number}', [InputHPPController3::class, 'viewHpp3'])->name('admin.inputhpp.view_hpp3');
    Route::delete('inputhpp/{notification_number}', [InputHPPController::class, 'destroy'])->name('inputhpp.destroy');
    Route::get('inputhpp/edit/{notification_number}', [InputHPPController::class, 'edit'])->name('inputhpp.edit');
    Route::put('inputhpp/update/{notification_number}', [InputHPPController::class, 'update'])->name('inputhpp.update');  
});


Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/purchaseorder', [PurchaseOrderController::class, 'index'])->name('admin.purchaseorder');
    Route::post('purchaseorder/{notification_number}', [PurchaseOrderController::class, 'update'])->name('purchaseorder.update');
});

// Routes untuk LHPP
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('lhpp', [App\Http\Controllers\Admin\LHPPController::class, 'index'])->name('lhpp.index');
    Route::get('lhpp/create', [App\Http\Controllers\Admin\LHPPController::class, 'create'])->name('lhpp.create');
    Route::post('lhpp', [App\Http\Controllers\Admin\LHPPController::class, 'store'])->name('lhpp.store');
    Route::get('lhpp/get-purchase-order/{notificationNumber}', [App\Http\Controllers\Admin\LHPPController::class, 'getPurchaseOrder'])->name('lhpp.get-purchase-order');
    Route::get('lhpp/get-work-duration/{notificationNumber}', [App\Http\Controllers\Admin\LHPPController::class, 'getWorkDuration'])->name('lhpp.get-work-duration');
    Route::get('lhpp/{notification_number}', [App\Http\Controllers\Admin\LHPPController::class, 'show'])->name('admin.lhpp.show');  // Nama route diubah
    Route::get('lhpp/{id}/edit', [App\Http\Controllers\Admin\LHPPController::class, 'edit'])->name('lhpp.edit');
    Route::put('lhpp/{id}', [App\Http\Controllers\Admin\LHPPController::class, 'update'])->name('lhpp.update');
    Route::delete('lhpp/{notification_number}', [App\Http\Controllers\Admin\LHPPController::class, 'destroy'])->name('lhpp.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/lpj', [HomeController::class, 'lpj'])->name('admin.lpj');
    Route::post('/admin/lpj/{notification_number}', [HomeController::class, 'updateLpj'])->name('lpj.update');
});

//Route PKM

Route::middleware(['auth', PkmMiddleware::class])
    ->prefix('pkm')
    ->name('pkm.')
    ->group(function () {
        Route::get('/dashboard', [PKMDashboardController::class, 'index'])->name('dashboard');
        Route::get('/jobwaiting', [PKMDashboardController::class, 'jobWaiting'])->name('jobwaiting');
        Route::get('/laporan', [PKMDashboardController::class, 'laporan'])->name('laporan');
        Route::get('/notification/{notification_number}', [PKMDashboardController::class, 'notificationDetail'])->name('notification.detail');
        Route::get('/lhpp/{notification_number}', [PKMDashboardController::class, 'showLHPP'])->name('lhpp.show');
    });
    Route::post('/jobwaiting/update-progress/{notification_number}', [PKMDashboardController::class, 'updateProgress'])->name('jobwaiting.updateProgress');
    Route::get('inputhpp/view-hpp1/{notification_number}', [InputHPPController::class, 'viewHpp1'])->name('admin.inputhpp.view_hpp1');
    Route::get('inputhpp/view-hpp2/{notification_number}', [InputHPPController2::class, 'viewHpp2'])->name('admin.inputhpp.view_hpp2');
    Route::get('inputhpp/view-hpp3/{notification_number}', [InputHPPController3::class, 'viewHpp3'])->name('admin.inputhpp.view_hpp3');
    Route::get('/spk/{notificationNumber}', [SPKController::class, 'view'])->name('spk.view');
    Route::get('/gambar-teknik/{notificationNumber}', [GambarTeknikController::class, 'view'])->name('gambar_teknik.view');
//});

// Routes for Abnormal Approval
Route::middleware(['auth'])->group(function () {
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/sign/{signType}/{notificationNumber}', [ApprovalController::class, 'saveSignature'])->name('approval.saveSignature');
    Route::get('/approval/get-old-signature/{signType}/{notificationNumber}', [ApprovalController::class, 'getOldSignature']);
    
});
// Routes for SPK Approval
Route::middleware(['auth'])->group(function () {
    Route::get('/approval/spk', [SPKApprovalController::class, 'index'])->name('approval.spk.index'); // Mengarahkan ke rute yang benar
    Route::post('/approval/spk/sign/{signType}/{nomorSpk}', [SPKApprovalController::class, 'saveSignature'])->name('approval.spk.saveSignature');
});
// Routes for HPP Approval
Route::middleware(['auth'])->group(function () {
    Route::get('/approval/hpp', [App\Http\Controllers\Approval\HPPApprovalController::class, 'index'])->name('approval.hpp.index');
    Route::post('/approval/hpp/sign/{signType}/{notificationNumber}', [App\Http\Controllers\Approval\HPPApprovalController::class, 'saveSignature'])->name('approval.hpp.saveSignature');
    Route::post('/approval/hpp/reject/{signType}/{notificationNumber}', [App\Http\Controllers\Approval\HPPApprovalController::class, 'reject'])->name('approval.hpp.reject');
    Route::post('/approval/hpp/notes/{notification_number}/{type}', [App\Http\Controllers\Approval\HPPApprovalController::class, 'saveNotes'])->name('approval.hpp.saveNotes');
    Route::get('/approval/hpp/get-old-signature/{signType}/{notificationNumber}', [HPPApprovalController::class, 'getOldSignature'])
    ->name('approval.hpp.getOldSignature');
});
Route::middleware(['auth'])->prefix('approval/hpp')->name('approval.hpp.')->group(function () {
    Route::get('view-hpp1/{notification_number}', [App\Http\Controllers\Approval\HPPApprovalController::class, 'viewHpp1'])->name('view_hpp1');
    Route::get('view-hpp2/{notification_number}', [App\Http\Controllers\Approval\HPPApprovalController::class, 'viewHpp2'])->name('view_hpp2');
    Route::get('view-hpp3/{notification_number}', [App\Http\Controllers\Approval\HPPApprovalController::class, 'viewHpp3'])->name('view_hpp3');
});


// Routes for LHPP Approval
Route::middleware(['auth'])->group(function () {
    Route::get('/approval/lhpp', [App\Http\Controllers\Approval\LHPPApprovalController::class, 'index'])->name('approval.lhpp.index');
    Route::post('/approval/lhpp/sign/{signType}/{notificationNumber}', [App\Http\Controllers\Approval\LHPPApprovalController::class, 'saveSignature'])->name('approval.lhpp.saveSignature');
    Route::post('/approval/lhpp/notes/{notification_number}/{type}', [App\Http\Controllers\Approval\LHPPApprovalController::class, 'saveNotes'])->name('approval.lhpp.saveNotes');
    Route::put('/approval/lhpp/status/{notification_number}', [App\Http\Controllers\Approval\LHPPApprovalController::class, 'updateStatus'])->name('approval.lhpp.updateStatus');
    Route::post('/approval/lhpp/reject/{signType}/{notificationNumber}', [App\Http\Controllers\Approval\LHPPApprovalController::class, 'reject'])->name('approval.lhpp.reject');
    Route::get('/approval/lhpp/show/{notification_number}', [App\Http\Controllers\Approval\LHPPApprovalController::class, 'show'])->name('approval.lhpp.show');

});

// Route::get('/send-wa', function() {
//     $response = Http::withHeaders([
//         'Authorization' => '3GBUnGXz7gPbP5AJKA4a'
//     ])->post('https://api.fonnte.com/send', [
//         'target' => '083150898767',
//         'message' => 'Ini Pesan Laravel'
//     ]);

//     dd(json_decode($response, true));
// });





