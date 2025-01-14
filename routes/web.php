<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManageController;
use Illuminate\Support\Facades\Route;

Route::get('/p', function () {
    return view('welcome');
});

Route::get('/role-management', [UserController::class, 'showRole'])->name('user.role');

Route::get('/', function () {
    return view('pages.login');
});

Route::get('/user-manage', [UserManageController::class, 'index'])->name('user.manage');

// Route::get('/user-manage/add', function() {
//     return view('user-manage.add');
// })->name('user.add');

Route::get('/dashboard/{role}', [DashboardController::class, 'index'])->name('dashboard.index');
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
});