<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManageController;
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

// Route::get('/user-manage/add', function() {
//     return view('user-manage.add');
// })->name('user-manage/add');

Route::get('/dashboard', function () {
    return view('layouts.app');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

Route::get('/memo', function() {
    return view('superadmin.memo.memo-superadmin');
})->name('memo.superadmin');

Route::get('/add-memo', function() {
    return view('superadmin.memo.add-memo');
})->name('add-memo.superadmin');

Route::get('/edit-memo', function() {
    return view('superadmin.memo.edit-memo');
})->name('edit-memo.superadmin');

Route::get('/dashboard.admin', function () {
    return view('admin.index');
})->name('admin.dashboard');
Route::get('/dashboard.superadmin', function () {
    return view('superadmin.dashboard');
})->name('superadmin.dashboard');
Route::get('/dashboard.manager', function () {
    return view('supervisor.index');
})->name('manager.dashboard');

// routes/web.php

Route::get('/verif-email', function () {
    return view('/components/verif-email');
})->name('verif-email');

Route::get('/undangan', function() {
    return view('superadmin.undangan.undangan');
})->name('undangan.superadmin');

Route::get('/add-undangan', function() {
    return view('superadmin.undangan.add-undangan');
})->name('add-undangan.superadmin');

Route::get('/edit-undangan', function() {
    return view('superadmin.undangan.edit-undangan');
})->name('edit-undangan.superadmin');

Route::get('/risalah', function() {
    return view('superadmin.risalah.risalah');
})->name('risalah.superadmin');

Route::get('/add-risalah', function() {
    return view('superadmin.risalah.add-risalah');
})->name('add-risalah.superadmin');

Route::get('/edit-risalah', function() {
    return view('superadmin.risalah.edit-risalah');
})->name('edit-risalah.superadmin');

Route::get('/arsip-risalah', function() {
    return view('superadmin.arsip.arsip-risalah');
})->name('arsip-risalah.superadmin');
Route::get('/arsip-undangan', function() {
    return view('superadmin.arsip.arsip-undangan');
})->name('arsip-undangan.superadmin');
Route::get('/arsip-memo', function() {
    return view('superadmin.arsip.arsip-memo');
})->name('arsip-memo.superadmin');