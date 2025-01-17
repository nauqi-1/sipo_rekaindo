<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManageController;
use Illuminate\Support\Facades\Route;

Route::get('/p', function () {
    return view('welcome');
});

Route::get('/user-manage/edit/{id}', [UserController::class, 'edit'])->name('user-manage.edit');
// Route untuk menangani update data
Route::put('/user-manage/update/{id}', [UserController::class, 'update'])->name('user-manage/update');

Route::get('/role-management', [UserController::class, 'showRole'])->name('user.role');

Route::get('/', function () {
    return view('pages.login');
});

Route::get('/user-manage', [UserManageController::class, 'index'])->name('user.manage');

// Route::get('/user-manage/add', function() {
//     return view('user-manage.add');
// })->name('user-manage/add');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/memo', function() {
    return view('superadmin.memo-superadmin');
})->name('superadmin.memo-superadmin');

Route::get('/add-memo', function() {
    return view('superadmin.add-memo');
})->name('superadmin.add-memo');

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

Route::get('/undangan-rapat', function() {
    return view('superadmin.undangan-rapat');
})->name('superadmin.undangan-rapat');

Route::get('/add-undangan-rapat', function() {
    return view('superadmin.add-undangan-rapat');
})->name('superadmin.add-undangan-rapat');