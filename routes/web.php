<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('components/login');
});

Route::get('/index', function () {
    return view('index');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/forgot-password', function () {
    return view('components/forgot-password');
});

