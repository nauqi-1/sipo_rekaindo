<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPwController extends Controller
{
    // public function index()
    // {
    //     $forgot = "Forgot Password";
    //     return view('forgot_pw', ['forgot' => $forgot]);
    // }

    public function showForgotPasswordForm()
    {
        return view('pages.forgot-pw');
    }

    public function sendVerificationCode(Request $request)
    {
        

        $request->validate(['email' => 'required|email|exists:users,email']);

        $email = $request->email;
        $verificationCode = rand(1000, 9999);

        // Simpan kode ke tabel password_resets
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'verification_code' => $verificationCode,
                'created_at' => now(),
            ]
        );

        // Simpan email dalam session
        session()->put('email', $email);

        // Kirim kode ke email
        Mail::raw("Kode verifikasi Anda adalah: $verificationCode", function ($message) use ($email) {
            $message->to($email)->subject('Password Reset Verification Code');
        });
       
        // session(['email' => $email]);
        return redirect()->route('verify-code');
    }

    public function showVerifyCodeForm(Request $request)
    {

    
        $email = session('email');
            if (!$email) {
                return redirect()->route('forgot-password')->withErrors(['email' => 'Session expired. Please try again.']);
            }
            return view('components.verif-email', compact('email'));
    }
    

    public function verifyCode(Request $request)
    {
        

        $request->validate([
            'digit1' => 'required|numeric|digits:1',
            'digit2' => 'required|numeric|digits:1',
            'digit3' => 'required|numeric|digits:1',
            'digit4' => 'required|numeric|digits:1',
        ]);

        // Gabungkan 4 digit menjadi kode
        $verificationCode = $request->digit1 . $request->digit2 . $request->digit3 . $request->digit4;
        
        $email = session('email');
        if (!$email) {
            return redirect()->route('forgot-password')->withErrors(['email' => 'Session expired. Please try again.']);
        }
        
        $resetEntry = DB::table('password_reset_tokens')
            ->where('email', session('email'))
            ->where('verification_code',  $verificationCode)
            ->first();
           
        if (!$resetEntry) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }
        
        return redirect()->route('reset-password')->with('email', $request->email);
    }

    public function resendCode()
    {
        $email = session('email');

        if (!$email) {
            return redirect()->route('forgot-password')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        return $this->sendVerificationCode(new Request(['email' => $email]));
    }


    public function showResetPasswordForm(Request $request)
    {
        
        
        $email = session('email');
        
        $verificationCode = session('verification_code');
        
        if (!$email) {
            return redirect()->route('forgot-password')->withErrors(['email' => 'Session expired. Please try again.']);
        }
        
        return view('components.new-pw', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $user = \App\Models\User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        return redirect('/')->with('status', 'Password successfully reset.');
    }
}