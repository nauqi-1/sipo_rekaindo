<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('components/login');
}) ->name('login');

Route::get('/index', function () {
    return view('index');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/forgot-pw', function () {
    return view('components/forgot-pw');
})->name('forgot-pw');

Route::get('/verif-email', function () {
    return view('components/verif-email');
})->name('verif-email');

Route::get('/new-pw', function () {
    return view('components/new-pw');
})->name('new-pw');

Route::get('/confirm-success', function () {
    return view('components/confirm-success');
})->name('confirm-success');

