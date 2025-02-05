<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MemoController;
use App\Http\Controllers\UndanganController;
use Illuminate\Support\Facades\Route;



    Route::get('user-manage/add', [RegisteredUserController::class, 'create'])
        ->name('user-manage/add');

    Route::post('user-manage/add', [RegisteredUserController::class, 'store'])
        ->name('user-manage/add');;

    Route::get('memo-superadmin/add', [MemoController::class, 'create'])
        ->name('memo-superadmin/add');
    Route::post('memo-superadmin/add/doc', [MemoController::class, 'store'])
    ->name('memo-superadmin.store');

    Route::get('undangan-superadmin/add', [UndanganController::class, 'create'])
        ->name('undangan-superadmin/add');
    Route::post('undangan-superadmin/add/doc', [UndanganController::class, 'store'])
    ->name('undangan-superadmin.store');



Route::middleware('guest')->group(function () {
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
