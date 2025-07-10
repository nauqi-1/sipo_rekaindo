<?php

// internal API
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemoController;
use App\Http\Controllers\Api\ArsipApiController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\MemoApiController;
use App\Http\Controllers\Api\UndanganApiController;
use App\Http\Controllers\Api\RisalahApiController;
use App\Http\Controllers\Api\KirimApiController;
use App\Http\Controllers\Api\UserManageApiController;
use App\Http\Controllers\Api\PerusahaanApiController;
use App\Http\Controllers\Api\LaporanApiController;
use App\Http\Controllers\Api\ProfileApiController;

// eksternal API
use App\Http\Controllers\CetakPDFController;

// Public routes

Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
   // validasi token
    Route::get('/checkToken', [AuthController::class, 'checkToken']);

    // Roles
    Route::get('/roles', [UserController::class, 'showRole']);

    // Profil Pengguna
    Route::get('/profile', [ProfileApiController::class, 'profileDetails']);

    // User Manage
    Route::get('/users', [UserManageApiController::class, 'index']);
    Route::delete('delete/user/{id}', [UserManageApiController::class, 'destroy']);

    // Memo
    Route::get('/memo', [MemoApiController::class, 'index']);
    Route::get('/memo/superadmin', [MemoApiController::class, 'superadmin']);
    Route::get('/memo/manager', [MemoApiController::class, 'manager']);
    Route::delete('/memo/{id}', [MemoApiController::class, 'destroy']);
    Route::put('/memo/updateStatus/{id}', [MemoApiController::class, 'updateStatus']);
    Route::get('/memo-admin-b', [MemoApiController::class, 'getPendingForAdminB']);
    Route::get('/memo/{id}', [MemoApiController::class, 'show']);
    Route::get('memo/view-pdf/{id_memo}', [MemoApiController::class, 'ViewMemoPDF']);
    Route::get('/preview/memo/{id_memo}', [CetakPDFController::class, 'viewmemoPDF']);

   // Undangan
    Route::get('/undangan', [UndanganApiController::class, 'index']);
    Route::get('/undangan/superadmin', [UndanganApiController::class, 'superadmin']);
    Route::get('/undangan/manager', [KirimApiController::class, 'Undangan']);
    Route::put('/undangan/updateStatus/{id}', [UndanganApiController::class, 'updateDocumentStatus']);
    Route::delete('/undangan/{id}', [UndanganApiController::class, 'destroy']);
    Route::get('undangan/view-pdf/{id}', [UndanganApiController::class, 'ViewUndanganPDF']);
    Route::get('/preview/undangan/{id_undangan}', [CetakPDFController::class, 'viewundanganPDF']);


    // Kirim Dokumen
        Route::get('/kirim/{id}', [KirimApiController::class, 'index']);
    // Mengambil data undangan di manager yg sudah dikirim
        Route::get('/kirim/manager/{id}', [KirimApiController::class, 'viewManager']);
    // Untuk mengirim dokumen via API (POST)
        Route::post('/kirim/send', [KirimApiController::class, 'sendDocument']);
    // routes/api.php
    

    // User Manage
        Route::get('/users', [UserManageApiController::class, 'index']);
        Route::delete('delete/user/{id}', [UserManageApiController::class, 'destroy']);

    // Dashboard    
    Route::get('/dashboard/jumlahMemo', [MemoApiController::class, 'jumlahMemo']);
    Route::get('/dashboard/jumlahUndangan', [UndanganApiController::class, 'jumlahUndangan']);
    Route::get('/dashboard/jumlahRisalah', [RisalahApiController::class, 'jumlahRisalah']);


    // Risalah
    Route::get('/risalah', [RisalahApiController::class, 'index']);
    Route::get('/risalah/superadmin', [RisalahApiController::class, 'superadmin']);
    Route::get('/risalah/manager', [KirimApiController::class, 'Risalah']);
    Route::delete('/risalah/{id}', [RisalahApiController::class, 'destroy']);
    Route::put('/risalah/updateStatus/{id}', [RisalahApiController::class, 'updateStatus']);
    Route::get('risalah/view-pdf/{id_risalah}', [RisalahApiController::class, 'ViewRisalahPDF']);
    Route::get('/preview/risalah/{id_risalah}', [CetakPDFController::class, 'viewrisalahPDF']);
    Route::get('/detail/risalah/{id_risalah}', [RisalahApiController::class, 'show']);

    // Notifikasi
    Route::get('/', [NotifikasiController::class, 'index']);
    Route::put('/{id}/read', [NotifikasiController::class, 'markAsRead']);
    Route::put('/read-all', [NotifikasiController::class, 'markAllAsRead']);
    Route::get('/unread-count', [NotifikasiController::class, 'unreadCount']);

    // Data Perusahaan
    Route::get('/perusahaan', [PerusahaanApiController::class, 'index']);

    // Laporan
    Route::get('/laporan/getMemos', [LaporanApiController::class, 'getMemos']);
    Route::get('/laporan/getUndangans', [LaporanApiController::class, 'getUndangans']);
    Route::get('/laporan/getRisalahs', [LaporanApiController::class, 'getRisalahs']);

    // Notifikasi
    Route::get('/notifikasi', [NotifApiController::class, 'index']);
    Route::get('/notifikasi/unread-count', [NotifApiController::class, 'getUnreadCount']);
    Route::post('/notifikasi/mark-all-read', [NotifApiController::class, 'markAllAsRead']);

    //Arsip
    Route::prefix('arsip')->group(function () {
        Route::get('/', [ArsipApiController::class, 'index']);              // Ambil daftar dokumen terarsip
        Route::post('/', [ArsipApiController::class, 'store']);             // Arsipkan dokumen
        Route::post('/restore', [ArsipApiController::class, 'restore']);    // Unarsipkan dokumen
        Route::get('/undangan', [ArsipApiController::class, 'indexUndangan']);
        Route::get('/memo', [ArsipApiController::class, 'indexMemo']);
        Route::get('/risalah', [ArsipApiController::class, 'indexRisalah']);

    });

    Route::prefix('auth')->group(function () {
        Route::post('/forgot-password', [ForgotPwApiController::class, 'sendVerificationCode']);
        Route::post('/verify-code', [ForgotPwApiController::class, 'verifyCode']);
        Route::post('/reset-password', [ForgotPwApiController::class, 'resetPassword']);
    });
});
