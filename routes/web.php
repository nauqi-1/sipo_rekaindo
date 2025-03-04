<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManageController;
use App\Http\Controllers\ForgotPWController;
use App\Http\Controllers\CetakPDFController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\UndanganController;
use App\Http\Controllers\KirimController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PerusahaanController;
use Illuminate\Support\Facades\Route;

Route::get('/p', function () {
    return view('welcome');
});


Route::get('/user-manage/edit/{id}', [UserController::class, 'edit'])->name('user-manage.edit');
Route::delete('/user-manage/delete/{id}', [UserController::class, 'destroy'])->name('user-manage.destroy');
Route::put('/user-manage/update/{id}', [UserController::class, 'update'])->name('user-manage/update');
Route::get('/user-manage/paginate', [UserManageController::class, 'paginateUsers'])->name('user-manage.paginate');

Route::get('/role-management', [UserController::class, 'showRole'])->name('user.role');

Route::get('/', function () {
    return view('pages.login');
});

Route::get('/user-manage', [UserManageController::class, 'index'])->name('user.manage');

Route::get('/dashboard', function () {
    return view('layouts.superadmin');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('berkas/cetak/memo/{id} ', [CetakPDFController::class, 'cetakmemoPDF'])->name('cetakmemo');
    Route::get('view/memoPDF/{id_memo}', [CetakPDFController::class, 'viewmemoPDF'])->name('view-memoPDF');
    Route::get('berkas/cetak/undangan/{id} ', [CetakPDFController::class, 'cetakundanganPDF'])->name('cetakundangan');
    Route::get('view/undanganPDF/{id_undangan}', [CetakPDFController::class, 'viewundanganPDF'])->name('view-undanganPDF');
});

require __DIR__.'/auth.php';

Route::get('/memo-superadmin',[MemoController::class, 'index'])
->name('memo.superadmin');
Route::get('/memo-admin',[MemoController::class, 'index'])
->name('memo.admin');


Route::get('/add-memoSuperadmin', function() {
    return view('superadmin.memo.add-memo');
})->name('add-memo.superadmin');



Route::get('/memo/edit/{id_memo}', [MemoController::class, 'edit'])->name('memo.edit');
Route::delete('/memo/delete/{id_memo}', [MemoController::class, 'destroy'])->name('memo.destroy');
Route::put('/memo/update/{id_memo}', [MemoController::class, 'update'])->name('memo/update');

Route::get('/undangan/edit/{id_undangan}', [UndanganController::class, 'edit'])->name('undangan.edit');
Route::delete('/undangan/delete/{id_undangan}', [UndanganController::class, 'destroy'])->name('undangan.destroy');
Route::put('/undangan/update/{id_undangan}', [UndanganController::class, 'update'])->name('undangan/update');


Route::get('/dashboard.admin',  [DashboardController::class, 'index']
)->name('admin.dashboard');
Route::get('/dashboard.superadmin',  [DashboardController::class, 'index']
)->name('superadmin.dashboard');
Route::get('dashboard.manager', [DashboardController::class, 'index']
)->name('manager.dashboard');


// routes/web.php
Route::middleware('web')->group(function () {
    Route::get('/forgot-password', [ForgotPwController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('/forgot-password', [ForgotPwController::class, 'sendVerificationCode'])->name('forgot-password.send');
    
    Route::get('/verify-code', [ForgotPwController::class, 'showVerifyCodeForm'])->name('verify-code');
    Route::post('/verify-code', [ForgotPwController::class, 'verifyCode'])->name('verify-code.check');
    
    
    Route::get('/reset-password', [ForgotPwController::class, 'showResetPasswordForm'])->name('reset-password');
    
    Route::post('/reset-password', [ForgotPwController::class, 'resetPassword'])->name('reset-password.update');
    });

// Lampiran memo
Route::get('/memo/{id}/file', [MemoController::class, 'showFile'])->name('memo.file');
Route::get('/memo/download/{id}', [MemoController::class, 'downloadFile'])->name('memo.download');
Route::get('/memo/{id}/preview', [MemoController::class, 'showFile'])->name('memo.preview');

Route::get('/verif-email', function () {
    return view('/components/verif-email');
})->name('verif-email');



Route::get('/add-undanganSuperadmin', function() {
    return view('superadmin.undangan.add-undangan');
})->name('add-undangan.superadmin');
Route::get('/edit-undanganSuperadmin', function() {
    return view('superadmin.undangan.edit-undangan');
})->name('edit-undangan.superadmin');

Route::get('/risalahSuperadmin', function() {
    return view('superadmin.risalah.risalah');
})->name('risalah.superadmin');

Route::get('/add-risalahSuperadmin', function() {
    return view('superadmin.risalah.add-risalah');
})->name('add-risalah.superadmin');

Route::get('/edit-risalahSuperadmin', function() {
    return view('superadmin.risalah.edit-risalah');
})->name('edit-risalah.superadmin');




Route::get('/add-memoAdmin', function() {
    return view('admin.memo.add-memo');
})->name('admin.memo.add-memo');

Route::get('/edit-memoAdmin', function() {
    return view('admin.memo.edit-memo');
})->name('admin.memo.edit-memo');

Route::get('/kirim-memoAdmin/{id}',  
    [KirimController::class, 'index']
)->name('kirim-memoAdmin.admin');

Route::get('/after-kirim', function() {
    return view('manager.after-kirim-memo');
})->name('after-kirim');

Route::get('/catatan-memo', function() {
    return view('admin.memo.catatan-memo');
})->name('catatan-memo.admin');


// laporan
Route::get('/laporan-memo', function() {
    return view('superadmin.laporan.laporan-memo');
})->name('laporan-memo.superadmin');

Route::get('/laporan-risalah', function() {
    return view('superadmin.laporan.laporan-risalah');
})->name('laporan-risalah.superadmin');
Route::get('/laporan-undangan', function() {
    return view('superadmin.laporan.laporan-undangan');
})->name('laporan-undangan.superadmin');

// cetak laporan
Route::get('/cetak-laporan-memo', function() {
    return view('superadmin.laporan.cetak-laporan-memo');
})->name('laporan-memo');
Route::get('/cetak-laporan-memo', [LaporanController::class, 'index'])
    ->name('cetak-laporan-memo.superadmin');

Route::post('/cetak-laporan-memo', [LaporanController::class, 'filterMemosByDate'])
    ->name('cetak-laporan-memo.filter');

Route::get('/cetak-laporan-undangan', [LaporanController::class, 'index'])
->name('cetak-laporan-undangan.superadmin');

Route::post('/cetak-laporan-undangan', [LaporanController::class, 'filterUndanganByDate'])
    ->name('cetak-laporan-undangan.filter');

Route::get('/cetak-laporan-risalah', function() {
    return view('superadmin.laporan.cetak-laporan-risalah');
})->name('laporan-risalah');
Route::get('/cetak-laporan-undangan', function() {
    return view('superadmin.laporan.cetak-laporan-undangan');
})->name('laporan-undangan');



// memo supervisor
// Route::get('/memo-terkirim', function() {
//     return view('manager.memo.memo-terkirim'); })->name('memo.terkirim');
// Route::get('/memo-diterima', function() {
//     return view('manager.memo.memo-diterima'); })->name('memo.diterima');
Route::get('/memo-terkirim', [KirimController::class, 'memoTerkirim'])->name('memo.terkirim');
Route::get('/memo-diterima', [KirimController::class, 'memoDiterima'])->name('memo.diterima');
Route::get('/view-memoTerkirim/{id_memo}', [MemoController::class, 'showTerkirim'])->name('view.memo-terkirim');
Route::get('/view-memoDiterima/{id_memo}', [MemoController::class, 'showDiterima'])->name('view.memo-diterima');


// undangan admin


Route::get('/add-undanganAdmin', function() {
    return view('admin.undangan.add-undangan'); })->name('add-undangan.admin');
Route::get('/edit-undanganAdmin', function() {
    return view('admin.undangan.edit-undangan'); })->name('edit-undangan.admin');
Route::get('/kirim-undanganAdmin/{id}', [KirimController::class, 'index'])->name('kirim-undanganAdmin.admin');

// risalah admin
Route::get('/risalahAdmin', function() {
    return view('admin.risalah.risalah-admin'); })->name('risalah.admin');
Route::get('/add-risalahAdmin', function() {
    return view('admin.risalah.add-risalah'); })->name('add-risalah.admin');
Route::get('/edit-risalahAdmin', function() {
    return view('admin.risalah.edit-risalah'); })->name('edit-risalah.admin');
Route::get('/kirim-risalahAdmin', function() {
    return view('admin.risalah.kirim-risalahAdmin'); })->name('kirim-risalahAdmin.admin');    

// undangan supervisor
Route::get('/persetujuan-undangan/{id}', [KirimController::class,'viewManager'])->name('persetujuan.undangan');

// risalah supervisor
Route::get('/risalahSupervisor', function() {
    return view('manager.risalah.risalah-manager'); })->name('risalah.manager');
Route::get('/approve-risalah', function() {
    return view('manager.risalah.approve-risalah'); })->name('approve.risalah');
Route::get('/view-risalah', function() { 
    return view('manager.risalah.view-risalah'); })->name('view.risalah');   

// Arsip Superadmin


// Arsip Admin


// View Arsip Superadmin
   

// View Arsip Admin
   

Route::get('/superadmin/memo', [MemoController::class, 'index'])->name('memo.superadmin');
Route::get('/superadmin/undangan', [UndanganController::class, 'index'])->name('undangan.superadmin');
Route::get('/admin/undangan', [UndanganController::class, 'index'])->name('undangan.admin');
Route::get('/manager/undangan', [KirimController::class, 'undangan'])->name('undangan.manager');

Route::get('/info', function() {
    return view('info'); })->name('info');


// Format Surat
Route::get('/format-memo', function() {
    return view('format-surat.format-memo');
})->name('format-memo');
Route::get('/format-undangan', function() {
    return view('format-surat.format-undangan');
})->name('format-undangan');
Route::get('/format-risalah', function() {
    return view('format-surat.format-risalah');
})->name('format-risalah');

Route::get('/data-perusahaan', [PerusahaanController::class, 'index'])->name('data-perusahaan');
Route::post('/data-perusahaan/update', [PerusahaanController::class, 'update'])->name('data-perusahaan.update');

// edit profile
Route::get('/edit-profileSuperadmin', [ProfileController::class, 'editProfile'])->name('edit-profile.superadmin');
Route::post('/update-profileSuperadmin', [ProfileController::class, 'updateProfile'])->name('superadmin.updateProfile');
Route::get('/edit-profileAdmin', function() {
    return view('admin.edit-profileAdmin'); })->name('edit-profile.admin');
Route::get('/edit-profileSupervisor', function() {
    return view('manager.edit-profileSupervisor'); })->name('edit-profile.manager');

Route::put('/memo/{id}/update-status', [MemoController::class, 'updateStatus'])->name('memo.updateStatus');
Route::put('/undangan/{id}/update-status', [UndanganController::class, 'updateStatus'])->name('undangan.updateStatus');

// Lampiran memo
Route::get('/memo/{id}/file', [MemoController::class, 'showFile'])->name('memo.file');
Route::get('/memo/download/{id}', [MemoController::class, 'downloadFile'])->name('memo.download');
Route::get('/memo/{id}/preview', [MemoController::class, 'showFile'])->name('memo.preview');

Route::get('/memo/arsip/{id}', [ArsipController::class, 'view'])->name('view.memo-arsip');
Route::get('/undangan/arsip/{id}', [ArsipController::class, 'viewUndangan'])->name('view.undangan-arsip');
Route::get('/risalah/arsip', [ArsipController::class, 'view'])->name('view.risalah-arsip');

Route::get('/memo/{id}', [MemoController::class, 'view'])->name('view.memo');
Route::get('/undangan/manager/{id}', [UndanganController::class, 'view'])->name('view.undangan');
