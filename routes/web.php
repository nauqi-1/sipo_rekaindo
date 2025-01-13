<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/p', function () {
    return view('welcome');
});
Route::get('/admin.role', [UserController::class, 'showRole'])->name('user.role');
Route::get('/', function () {
    return view('pages.login');
});


Route::get('/user-manage', function () {
    return view('superadmin.user-manage');
});

Route::get('/user-manage/add', function() {
    return view('/user-manage.add');
})->name('user.add');

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
