<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManageController;
use App\Http\Controllers\ForgotPWController;
use App\Http\Controllers\CetakPDFController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\UndanganController;
use App\Http\Controllers\KirimController;
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
    return view('layouts.superadmin');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('berkas/cetak', [CetakPDFController::class, 'cetakmemoPDF'])->name('cetakmemo');
});

require __DIR__.'/auth.php';

Route::get('/memo-superadmin',[MemoController::class, 'index'])
Route::get('/memo-superadmin',[MemoController::class, 'index'])
->name('memo.superadmin');
Route::get('/memo-admin',[MemoController::class, 'index'])
->name('memo.admin');

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

Route::get('/kirim-memoSuperadmin', function() {
    return view('superadmin.memo.kirim-memoSuperadmin');
})->name('kirim-memoSuperadmin.superadmin');

Route::get('/dashboard.admin', function () {
    return view('admin.dashboard');
    return view('admin.dashboard');
})->name('admin.dashboard');
Route::get('/dashboard.superadmin', function () {
    return view('superadmin.dashboard');
})->name('superadmin.dashboard');
Route::get('/dashboard.manager', function () {
    return view('manager.dashboard');
    return view('manager.dashboard');
})->name('manager.dashboard');

// routes/web.php
Route::middleware('web')->group(function () {
    Route::get('/forgot-password', [ForgotPwController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('/forgot-password', [ForgotPwController::class, 'sendVerificationCode'])->name('forgot-password.send');
    
    Route::get('/verify-code', [ForgotPwController::class, 'showVerifyCodeForm'])->name('verify-code');
    Route::post('/verify-code', [ForgotPwController::class, 'verifyCode'])->name('verify-code.check');
    
    
    Route::get('/reset-password', [ForgotPwController::class, 'showResetPasswordForm'])->name('reset-password');
    
    Route::post('/reset-password', [ForgotPwController::class, 'resetPassword'])->name('reset-password.update');
    });

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
    return view('manager.after-kirim-memo');
})->name('after-kirim');

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
})->name('cetak-laporan-memo.superadmin');
Route::get('/cetak-laporan-risalah', function() {
    return view('superadmin.laporan.cetak-laporan-risalah');
})->name('cetak-laporan-risalah.superadmin');
Route::get('/cetak-laporan-undangan', function() {
    return view('superadmin.laporan.cetak-laporan-undangan');
})->name('cetak-laporan-undangan.superadmin');



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
Route::get('/kirim-undanganAdmin', function() {
    return view('admin.undangan.kirim-undanganAdmin'); })->name('kirim-undanganAdmin.admin');

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
Route::get('/approve-undangan', function() {
        return view('manager.undangan.approve-undangan'); })->name('approve.undangan');
        return view('manager.undangan.approve-undangan'); })->name('approve.undangan');
Route::get('/view-undangan', function() {
    return view('manager.undangan.view-undangan'); })->name('view.undangan');
    return view('manager.undangan.view-undangan'); })->name('view.undangan');

// risalah supervisor
Route::get('/risalahSupervisor', function() {
    return view('manager.risalah.risalah-manager'); })->name('risalah.manager');
Route::get('/approve-risalah', function() {
    return view('manager.risalah.approve-risalah'); })->name('approve.risalah');
    return view('manager.risalah.approve-risalah'); })->name('approve.risalah');
Route::get('/view-risalah', function() {
    return view('manager.risalah.view-risalah'); })->name('view.risalah');   
    return view('manager.risalah.view-risalah'); })->name('view.risalah');   

// Arsip Superadmin
Route::get('/arsip-risalah', function() {
    return view('superadmin.arsip.arsip-risalah');
})->name('arsip-risalah.superadmin');
Route::get('/arsip-undangan', function() {
    return view('superadmin.arsip.arsip-undangan');
})->name('arsip-undangan.superadmin');
Route::get('/arsip-memoSuperadmin', function() {
    return view('superadmin.arsip.arsip-memo');
})->name('arsip-memo.superadmin');

// Arsip Admin
Route::get('/arsip-risalah-admin', function() {
    return view('admin.arsip.arsip-risalah');
})->name('arsip-risalah.admin');
Route::get('/arsip-undangan-admin', function() {
    return view('admin.arsip.arsip-undangan');
})->name('arsip-undangan.admin');
Route::get('/arsip-memo-admin', function() {
    return view('admin.arsip.arsip-memo');
})->name('arsip-memo.admin');

// View Arsip Superadmin
Route::get('/view-arsipMemo-superadmin', function() {
    return view('superadmin.arsip.view-arsipMemo'); })->name('arsip-viewMemo.superadmin');    
Route::get('/view-arsipUndangan-superadmin', function() {
    return view('superadmin.arsip.view-arsipUndangan'); })->name('arsip-viewUndangan.superadmin');    
Route::get('/view-arsipRisalah-superadmin', function() {
    return view('superadmin.arsip.view-arsipRisalah'); })->name('arsip-viewRisalah.superadmin');    

// View Arsip Admin
Route::get('/view-arsipMemo-admin', function() {
    return view('admin.arsip.view-arsipMemo-admin'); })->name('arsip-viewMemo.admin');    
Route::get('/view-arsipUndangan-admin', function() {
    return view('admin.arsip.view-arsipUndangan-admin'); })->name('arsip-viewUndangan.admin');    
Route::get('/view-arsipRisalah-admin', function() {
    return view('admin.arsip.view-arsipRisalah-admin'); })->name('arsip-viewRisalah.admin');    

Route::get('/superadmin/memo', [MemoController::class, 'index'])->name('memo.superadmin');
Route::get('/superadmin/undangan', [UndanganController::class, 'index'])->name('undangan.superadmin');
Route::get('/admin/undangan', [UndanganController::class, 'index'])->name('undangan.admin');
Route::get('/manager/undangan', [UndanganController::class, 'index'])->name('undangan.manager');
Route::get('/admin/undangan', [UndanganController::class, 'index'])->name('undangan.admin');
Route::get('/manager/undangan', [UndanganController::class, 'index'])->name('undangan.manager');

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
Route::get('/format-cetakLaporan', function() {
    return view('format-surat.format-cetakLaporan');
})->name('format-cetakLaporan');

// data perusahaan 
Route::get('/data-perusahaan', function() {
    return view('superadmin.data-perusahaan');
})->name('data-perusahaan');

// edit profile
Route::get('/edit-profileSuperadmin', function() {
    return view('superadmin.edit-profileSuperadmin'); })->name('edit-profile.superadmin');
Route::get('/edit-profileAdmin', function() {
    return view('admin.edit-profileAdmin'); })->name('edit-profile.admin');
Route::get('/edit-profileSupervisor', function() {
    return view('manager.edit-profileSupervisor'); })->name('edit-profile.manager');



    