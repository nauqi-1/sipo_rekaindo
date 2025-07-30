<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\KirimController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BackupRisalahController;
use App\Http\Controllers\UndanganController;
use App\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;


// Route::get('register', [RegisteredUserController::class, 'create'])
// ->name('register');

// Route::post('register', [RegisteredUserController::class, 'store'])
// ->name('register');

    Route::get('user-manage/add', [RegisteredUserController::class, 'create'])
        ->name('user-manage/add');

    Route::post('user-manage/add', [RegisteredUserController::class, 'store'])
        ->name('user-manage/add');;

    Route::post('organization-manage/add', [OrganizationController::class, 'store'])
        ->name('organization-manage/add');;

    Route::get('memo-superadmin/add', [MemoController::class, 'create'])
        ->name('memo-superadmin/add');
    Route::post('memo-superadmin/add/doc', [MemoController::class, 'store'])
    ->name('memo-superadmin.store');
    Route::get('memo-admin/add', [MemoController::class, 'create'])
        ->name('memo-admin/add');
    Route::post('memo-admin/add/doc', [MemoController::class, 'store'])
    ->name('memo-admin.store');
    Route::get('memo-manager/add', [MemoController::class, 'create'])
        ->name('memo-manager/add');

    Route::get('undangan-superadmin/add', [UndanganController::class, 'create'])
        ->name('undangan-superadmin/add');
    Route::post('undangan-superadmin/add/doc', [UndanganController::class, 'store'])
    ->name('undangan-superadmin.store');
    Route::get('undangan-admin/add', [UndanganController::class, 'create'])
        ->name('undangan-admin/add');
    Route::post('undangan-admin/add/doc', [UndanganController::class, 'store'])
    ->name('undangan-admin.store');



Route::middleware('guest')->group(function () {
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

        Route::post('/documents/send', [KirimController::class, 'sendDocument'])->name('documents.send');
        Route::get('/documents/sent', [KirimController::class, 'sentDocuments'])->name('documents.sent');
        Route::get('/documents/received', [KirimController::class, 'receivedDocuments'])->name('documents.received');
        Route::post('/documents/read/{id}', [KirimController::class, 'markAsRead'])->name('documents.read');

        Route::post('/arsip/{document_id}/{jenis_document}/simpan', [ArsipController::class, 'archiveDocument'])->name('arsip.archive');
        Route::delete('/arsip/{document_id}/{jenis_document}', [ArsipController::class, 'restoreDocument'])->name('arsip.restore');
        Route::get('/arsip/memo', [ArsipController::class, 'indexMemo'])->name('arsip.memo');
        Route::get('/arsip/undangan', [ArsipController::class, 'indexUndangan'])->name('arsip.undangan');
        Route::get('/arsip/risalah', [ArsipController::class, 'indexRisalah'])->name('arsip.risalah');      

        Route::get('/kirim-memoAdmin/{id}',  
            [KirimController::class, 'index']
        )->name('kirim-memoAdmin.admin');

        Route::get('/memo-terkirim', [KirimController::class, 'memoTerkirim'])->name('memo.terkirim');
        Route::get('/memo-diterima', [KirimController::class, 'memoDiterima'])->name('memo.diterima');

        Route::get('/memo-restore', [BackupController::class, 'memo'])->name('memo.backup');
        Route::get('/undangan-restore', [BackupController::class, 'undangan'])->name('undangan.backup');

        
        Route::post('/memo/bulk-restore', [BackupController::class, 'bulkRestoreMemo'])->name('memo.bulk-restore');
        Route::post('/memo/bulk-force-delete', [BackupController::class, 'bulkForceDeleteMemo'])->name('memo.bulk-force-delete');

        Route::post('/undangan/bulk-restore', [BackupController::class, 'bulkRestore'])->name('undangan.bulk-restore');
        Route::post('/undangan/bulk-force-delete', [BackupController::class, 'bulkForceDelete'])->name('undangan.bulk-force-delete');
        // web.php
Route::delete('/undangan-force-delete/{id}', [BackupController::class, 'forceDelete'])->name('undangan.forceDelete');



        Route::get('/risalah-restore', [BackupRisalahController::class, 'risalah'])->name('risalah.backup');
        Route::post('/memo-restore-file/{id}', [BackupController::class, 'RestoreMemo'])->name('memo.restore-file');
        Route::post('/undangan-restore/{id}', [BackupController::class, 'RestoreUndangan'])->name('undangan.restore');
        Route::get('/risalah-restore/{id}', [BackupRisalahController::class, 'RestoreRisalah'])->name('risalah.restore');
});
