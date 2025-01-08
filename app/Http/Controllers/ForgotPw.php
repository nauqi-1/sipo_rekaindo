<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForgotPw extends Controller
{
    public function index()
    {
        $forgot = "Forgot Password";
        return view('forgot_pw', ['forgot' => $forgot]);
    }
}